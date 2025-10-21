<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTE DE URL ===\n";

// Testar diferentes formas de gerar a URL
$profissional = App\Models\Profissional::with('user')->find(1);

echo "1. asset() simples:\n";
echo "   " . asset('storage/avatars/EPTAgAQJMIZptM6eHlsz6SJWFpqY00AokQMZswZs.jpg') . "\n";

echo "\n2. url() simples:\n";
echo "   " . url('storage/avatars/EPTAgAQJMIZptM6eHlsz6SJWFpqY00AokQMZswZs.jpg') . "\n";

echo "\n3. Storage::url():\n";
echo "   " . \Storage::disk('public')->url('avatars/EPTAgAQJMIZptM6eHlsz6SJWFpqY00AokQMZswZs.jpg') . "\n";

echo "\n4. Profissional avatar_url:\n";
echo "   " . $profissional->avatar_url . "\n";

echo "\n5. User avatar_url:\n";
echo "   " . $profissional->user->avatar_url . "\n";

// Testar se a URL é acessível
echo "\n6. Testando acessibilidade:\n";
$url = asset('storage/avatars/EPTAgAQJMIZptM6eHlsz6SJWFpqY00AokQMZswZs.jpg');
$headers = @get_headers($url);
if ($headers) {
    echo "   Status: " . $headers[0] . "\n";
} else {
    echo "   ERRO: URL não acessível!\n";
}
?>
