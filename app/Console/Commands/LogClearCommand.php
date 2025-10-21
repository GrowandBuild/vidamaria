<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class LogClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:clear';
    protected $description = 'Limpa logs antigos para economizar espaço';

    public function handle()
    {
        $this->info('Iniciando limpeza de logs antigos...');

        $cutoff = now()->subDays(30); // Manter apenas 30 dias
        $cleaned = 0;

        // Limpar logs do Laravel
        $logPath = storage_path('logs');
        if (is_dir($logPath)) {
            $files = glob($logPath . '/*.log*');
            foreach ($files as $file) {
                if (filemtime($file) < $cutoff->timestamp) {
                    unlink($file);
                    $cleaned++;
                    $this->info("Log removido: " . basename($file));
                }
            }
        }

        // Limpar backups antigos (manter 30 dias)
        $backupPath = storage_path('app/backups');
        if (is_dir($backupPath)) {
            $files = glob($backupPath . '/*/*');
            foreach ($files as $file) {
                if (filemtime($file) < $cutoff->timestamp) {
                    unlink($file);
                    $cleaned++;
                    $this->info("Backup antigo removido: " . basename($file));
                }
            }
        }

        $this->info("Limpeza concluída. {$cleaned} arquivos removidos.");
        return Command::SUCCESS;
    }
}
