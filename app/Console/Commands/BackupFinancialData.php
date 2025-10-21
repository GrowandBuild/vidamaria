<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupFinancialData extends Command
{
    protected $signature = 'backup:complete';
    protected $description = 'Cria backup completo do sistema (banco + arquivos)';

    public function handle()
    {
        $this->info('Iniciando backup completo do sistema...');

        $timestamp = Carbon::now()->format('Y-m-d');
        $filename = "backup_completo_{$timestamp}.zip";
        
        // Criar arquivo ZIP único (substitui o anterior)
        $zipPath = storage_path("app/backups/{$filename}");
        
        // Remover backup anterior se existir
        if (file_exists($zipPath)) {
            unlink($zipPath);
        }

        // Criar arquivo de backup simples (JSON com todos os dados)
        $this->info('Criando arquivo de backup...');
        
        // 1. Backup do banco de dados
        $this->info('Fazendo backup do banco de dados...');
        $sqlContent = $this->backupDatabase();

        // 2. Backup dos dados financeiros
        $this->info('Fazendo backup dos dados financeiros...');
        $financialData = $this->getFinancialData();

        // 3. Informações do sistema
        $systemInfo = [
            'backup_date' => now()->format('Y-m-d H:i:s'),
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'total_records' => [
                'users' => DB::table('users')->count(),
                'pagamentos' => DB::table('pagamentos')->count(),
                'agendamentos' => DB::table('agendamentos')->count(),
                'clientes' => DB::table('clientes')->count(),
                'profissionais' => DB::table('profissionais')->count(),
                'servicos' => DB::table('servicos')->count(),
            ]
        ];

        // Criar backup completo em JSON
        $completeBackup = [
            'system_info' => $systemInfo,
            'database_sql' => $sqlContent,
            'financial_data' => $financialData,
            'backup_metadata' => [
                'created_at' => now()->format('Y-m-d H:i:s'),
                'version' => '1.0',
                'type' => 'complete_backup'
            ]
        ];

        // Salvar backup
        $jsonContent = json_encode($completeBackup, JSON_PRETTY_PRINT);
        file_put_contents($zipPath, $jsonContent);

        // Salvar informações do backup
        $this->saveBackupInfo($filename, $systemInfo);

        $this->info("Backup completo concluído: {$filename}");
        $this->info("Tamanho: " . $this->formatBytes(filesize($zipPath)));
    }

    private function backupDatabase()
    {
        // Tentar mysqldump primeiro
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --single-transaction --routines --triggers %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.host'),
            config('database.connections.mysql.database')
        );

        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            return implode("\n", $output);
        }

        // Fallback: usar Laravel para exportar dados
        $this->info('mysqldump não disponível, usando exportação via Laravel...');
        return $this->exportDatabaseViaLaravel();
    }

    private function exportDatabaseViaLaravel()
    {
        $sql = "-- Backup do banco de dados gerado em " . now() . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        $tables = [
            'users', 'profissionais', 'clientes', 'servicos', 'formas_pagamento',
            'agendamentos', 'agendamento_servico', 'pagamentos', 'audit_logs'
        ];

        foreach ($tables as $table) {
            $sql .= "-- Estrutura da tabela `{$table}`\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            
            // Estrutura da tabela
            $createTable = DB::select("SHOW CREATE TABLE `{$table}`")[0];
            $sql .= $createTable->{'Create Table'} . ";\n\n";

            // Dados da tabela
            $rows = DB::table($table)->get();
            if ($rows->count() > 0) {
                $sql .= "-- Dados da tabela `{$table}`\n";
                $sql .= "INSERT INTO `{$table}` VALUES\n";
                
                $values = [];
                foreach ($rows as $row) {
                    $rowArray = (array) $row;
                    $escapedValues = array_map(function($value) {
                        return $value === null ? 'NULL' : "'" . addslashes($value) . "'";
                    }, $rowArray);
                    $values[] = "(" . implode(',', $escapedValues) . ")";
                }
                
                $sql .= implode(",\n", $values) . ";\n\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        return $sql;
    }


    private function getFinancialData()
    {
        return [
            'pagamentos' => DB::table('pagamentos')->get(),
            'agendamentos' => DB::table('agendamentos')->get(),
            'clientes' => DB::table('clientes')->get(),
            'profissionais' => DB::table('profissionais')->get(),
            'servicos' => DB::table('servicos')->get(),
            'users' => DB::table('users')->get(),
            'audit_logs' => DB::table('audit_logs')->get(),
            'backup_date' => now()->format('Y-m-d H:i:s'),
        ];
    }

    private function saveBackupInfo($filename, $systemInfo)
    {
        $info = [
            'filename' => $filename,
            'created_at' => now(),
            'size' => $this->formatBytes(filesize(storage_path("app/backups/{$filename}"))),
            'system_info' => $systemInfo
        ];

        Storage::disk('local')->put('backups/last_backup.json', json_encode($info, JSON_PRETTY_PRINT));
    }

    private function formatBytes($size, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }

    private function cleanOldBackups()
    {
        $backupPath = storage_path('app/backups');
        
        if (!is_dir($backupPath)) {
            return;
        }

        $files = glob($backupPath . '/*/*.json');
        $cutoff = Carbon::now()->subDays(30);

        foreach ($files as $file) {
            if (filemtime($file) < $cutoff->timestamp) {
                unlink($file);
                $this->info("Backup antigo removido: " . basename($file));
            }
        }
    }
}
