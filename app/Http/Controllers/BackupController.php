<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BackupController extends Controller
{
    public function index()
    {
        // Verificar se usuário é proprietária
        if (!auth()->user()->isProprietaria()) {
            abort(403, 'Acesso negado. Apenas a proprietária pode acessar esta página.');
        }

        // Buscar informações do último backup
        $lastBackupInfo = $this->getLastBackupInfo();
        
        // Estatísticas do sistema
        $stats = [
            'total_users' => DB::table('users')->count(),
            'total_pagamentos' => DB::table('pagamentos')->count(),
            'total_agendamentos' => DB::table('agendamentos')->count(),
            'total_clientes' => DB::table('clientes')->count(),
            'total_profissionais' => DB::table('profissionais')->count(),
            'total_servicos' => DB::table('servicos')->count(),
        ];

        // Verificar se a view existe, senão retornar JSON
        if (view()->exists('backup.index')) {
            return view('backup.index', [
                'lastBackup' => $lastBackupInfo,
                'stats' => [
                    'usuarios' => $stats['total_users'],
                    'agendamentos' => $stats['total_agendamentos'],
                    'clientes' => $stats['total_clientes'],
                    'profissionais' => $stats['total_profissionais'],
                ]
            ]);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Sistema de backup funcionando',
                'last_backup' => $lastBackupInfo,
                'stats' => $stats
            ]);
        }
    }

    public function create()
    {
        // Verificar se usuário é proprietária
        if (!auth()->user()->isProprietaria()) {
            abort(403, 'Acesso negado.');
        }

        try {
            // Criar diretório de backup se não existir
            $backupDir = storage_path('app/backups');
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            // Nome do arquivo de backup
            $filename = 'backup_completo_' . date('Y-m-d_H-i-s') . '.sql';
            $filepath = $backupDir . '/' . $filename;

            // Criar backup do banco de dados
            $this->createDatabaseBackup($filepath);

            // Salvar informações do backup
            $this->saveBackupInfo($filename, $filepath);

            Log::info('Backup criado com sucesso', [
                'filename' => $filename,
                'size' => filesize($filepath),
                'user_id' => auth()->id()
            ]);
            
            return redirect()->route('backup.index')
                ->with('success', 'Backup criado com sucesso! Arquivo: ' . $filename);
                
        } catch (\Exception $e) {
            Log::error('Erro ao criar backup', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return redirect()->route('backup.index')
                ->with('error', 'Erro ao criar backup: ' . $e->getMessage());
        }
    }

    public function download()
    {
        // Verificar se usuário é proprietária
        if (!auth()->user()->isProprietaria()) {
            abort(403, 'Acesso negado.');
        }

        $lastBackupInfo = $this->getLastBackupInfo();
        
        if (!$lastBackupInfo || !$lastBackupInfo['filename']) {
            return redirect()->route('backup.index')
                ->with('error', 'Nenhum backup encontrado. Crie um backup primeiro.');
        }

        $filePath = storage_path("app/backups/{$lastBackupInfo['filename']}");
        
        if (!file_exists($filePath)) {
            return redirect()->route('backup.index')
                ->with('error', 'Arquivo de backup não encontrado.');
        }

        return response()->download($filePath, $lastBackupInfo['filename']);
    }

    public function status()
    {
        // Verificar se usuário é proprietária
        if (!auth()->user()->isProprietaria()) {
            abort(403, 'Acesso negado.');
        }

        $lastBackupInfo = $this->getLastBackupInfo();
        
        return response()->json([
            'success' => true,
            'data' => $lastBackupInfo
        ]);
    }

    public function resetDatabase()
    {
        // Verificar se usuário é proprietária
        if (!auth()->user()->isProprietaria()) {
            abort(403, 'Acesso negado.');
        }

        try {
            // Fazer backup antes de resetar
            $this->create();
            
            // Desabilitar verificações de chave estrangeira
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // Limpar dados específicos (manter estrutura)
            DB::table('agendamento_servico')->truncate();
            DB::table('agendamentos')->truncate();
            DB::table('pagamentos')->truncate();
            DB::table('clientes')->truncate();
            DB::table('profissionais')->truncate();
            
            // Manter apenas usuários admin e proprietária
            DB::table('users')->whereNotIn('tipo', ['proprietaria', 'admin'])->delete();
            
            // Reabilitar verificações de chave estrangeira
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            // Executar seeders (que vão recriar os dados necessários)
            Artisan::call('db:seed', ['--force' => true]);
            
            Log::warning('Banco de dados resetado', [
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name,
                'ip' => request()->ip()
            ]);
            
            return redirect()->route('backup.index')
                ->with('success', 'Banco de dados resetado com sucesso! Todos os dados foram restaurados para o estado inicial.');
                
        } catch (\Exception $e) {
            // Reabilitar verificações de chave estrangeira em caso de erro
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            Log::error('Erro ao resetar banco de dados', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return redirect()->route('backup.index')
                ->with('error', 'Erro ao resetar banco de dados: ' . $e->getMessage());
        }
    }

    private function getLastBackupInfo()
    {
        try {
            if (Storage::disk('local')->exists('backups/last_backup.json')) {
                $content = Storage::disk('local')->get('backups/last_backup.json');
                $info = json_decode($content, true);
                
                // Verificar se o arquivo ainda existe
                $filePath = storage_path("app/backups/{$info['filename']}");
                if (file_exists($filePath)) {
                    $info['exists'] = true;
                    $info['file_size'] = $this->formatBytes(filesize($filePath));
                    $info['last_modified'] = date('d/m/Y H:i:s', filemtime($filePath));
                } else {
                    $info['exists'] = false;
                }
                
                return $info;
            }
        } catch (\Exception $e) {
            // Log error
        }

        return null;
    }

    private function formatBytes($size, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }

    private function createDatabaseBackup($filepath)
    {
        // Exportar dados do banco de dados
        $tables = ['users', 'profissionais', 'clientes', 'servicos', 'agendamentos', 'pagamentos', 'formas_pagamento'];
        
        $sqlContent = "-- Backup do banco de dados - " . date('Y-m-d H:i:s') . "\n";
        $sqlContent .= "-- Sistema: Vida Maria Esmalteria\n";
        $sqlContent .= "-- Usuário: " . auth()->user()->name . "\n\n";
        
        foreach ($tables as $table) {
            $sqlContent .= "-- Tabela: {$table}\n";
            $sqlContent .= "DROP TABLE IF EXISTS `{$table}`;\n";
            
            // Obter estrutura da tabela
            $createTable = DB::select("SHOW CREATE TABLE `{$table}`");
            if (!empty($createTable)) {
                $sqlContent .= $createTable[0]->{'Create Table'} . ";\n\n";
            }
            
            // Obter dados da tabela
            $rows = DB::table($table)->get();
            if ($rows->count() > 0) {
                $sqlContent .= "INSERT INTO `{$table}` VALUES\n";
                $values = [];
                foreach ($rows as $row) {
                    $rowArray = (array) $row;
                    $escapedValues = array_map(function($value) {
                        return is_null($value) ? 'NULL' : "'" . addslashes($value) . "'";
                    }, $rowArray);
                    $values[] = '(' . implode(',', $escapedValues) . ')';
                }
                $sqlContent .= implode(",\n", $values) . ";\n\n";
            }
        }
        
        // Adicionar informações do sistema
        $sqlContent .= "-- Informações do Sistema\n";
        $sqlContent .= "-- Total de usuários: " . DB::table('users')->count() . "\n";
        $sqlContent .= "-- Total de agendamentos: " . DB::table('agendamentos')->count() . "\n";
        $sqlContent .= "-- Total de clientes: " . DB::table('clientes')->count() . "\n";
        $sqlContent .= "-- Total de profissionais: " . DB::table('profissionais')->count() . "\n";
        $sqlContent .= "-- Total de serviços: " . DB::table('servicos')->count() . "\n";
        $sqlContent .= "-- Total de pagamentos: " . DB::table('pagamentos')->count() . "\n";
        
        // Salvar arquivo
        file_put_contents($filepath, $sqlContent);
    }

    private function saveBackupInfo($filename, $filepath)
    {
        $info = [
            'filename' => $filename,
            'filepath' => $filepath,
            'created_at' => now()->toISOString(),
            'size' => filesize($filepath),
            'user_id' => auth()->id()
        ];
        
        Storage::disk('local')->put('backups/last_backup.json', json_encode($info));
    }
}