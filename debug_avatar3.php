<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUG AVATAR NA AGENDA ===\n";

// Verificar o agendamento específico
$agendamento = App\Models\Agendamento::with(['profissional.user'])->first();
if ($agendamento) {
    echo "Agendamento ID: " . $agendamento->id . "\n";
    echo "Profissional ID: " . $agendamento->profissional_id . "\n";
    echo "Profissional Nome: " . $agendamento->profissional->nome . "\n";
    echo "Profissional Avatar URL: " . $agendamento->profissional->avatar_url . "\n";
    
    if ($agendamento->profissional->user) {
        echo "User ID: " . $agendamento->profissional->user->id . "\n";
        echo "User Avatar: " . $agendamento->profissional->user->avatar . "\n";
        echo "User Avatar URL: " . $agendamento->profissional->user->avatar_url . "\n";
    }
    
    // Testar se a URL está acessível
    $avatarUrl = $agendamento->profissional->avatar_url;
    echo "\nTestando URL: " . $avatarUrl . "\n";
    
    // Verificar se é uma URL externa (ui-avatars) ou local
    if (strpos($avatarUrl, 'ui-avatars.com') !== false) {
        echo "PROBLEMA: Está usando fallback (ui-avatars) em vez da foto real!\n";
    } else {
        echo "OK: Está usando foto local\n";
    }
}
?>
