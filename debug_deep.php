<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ANÁLISE PROFUNDA DO AVATAR ===\n";

// 1. Verificar se o servidor está servindo arquivos estáticos
echo "1. Testando se o servidor serve arquivos estáticos:\n";
$url = 'http://localhost:8000/storage/avatars/EPTAgAQJMIZptM6eHlsz6SJWFpqY00AokQMZswZs.jpg';
echo "   URL: " . $url . "\n";

// 2. Verificar se o arquivo é acessível via HTTP
$context = stream_context_create([
    'http' => [
        'method' => 'HEAD',
        'timeout' => 5
    ]
]);

$headers = @get_headers($url, 1, $context);
if ($headers) {
    echo "   Status: " . $headers[0] . "\n";
    if (isset($headers['Content-Type'])) {
        echo "   Content-Type: " . $headers['Content-Type'] . "\n";
    }
} else {
    echo "   ERRO: Não conseguiu acessar a URL!\n";
}

// 3. Verificar se o problema está no componente
echo "\n2. Testando componente avatar:\n";
$profissional = App\Models\Profissional::with('user')->find(1);
if ($profissional) {
    echo "   Nome: " . $profissional->nome . "\n";
    echo "   User Avatar: " . ($profissional->user ? $profissional->user->avatar : 'N/A') . "\n";
    echo "   Avatar URL: " . $profissional->avatar_url . "\n";
    
    // Verificar se a URL contém ui-avatars
    if (strpos($profissional->avatar_url, 'ui-avatars.com') !== false) {
        echo "   PROBLEMA: Está usando fallback!\n";
    } else {
        echo "   OK: Está usando foto local\n";
    }
}

// 4. Verificar se o problema está no relacionamento
echo "\n3. Verificando relacionamento:\n";
if ($profissional && $profissional->user) {
    echo "   User ID: " . $profissional->user->id . "\n";
    echo "   User Avatar: " . $profissional->user->avatar . "\n";
    echo "   User Avatar URL: " . $profissional->user->avatar_url . "\n";
    
    // Verificar se o user tem avatar
    if (empty($profissional->user->avatar)) {
        echo "   PROBLEMA: User não tem avatar!\n";
    } else {
        echo "   OK: User tem avatar\n";
    }
}

// 5. Verificar se o arquivo existe no caminho correto
echo "\n4. Verificando arquivo:\n";
$filePath = storage_path('app/public/avatars/EPTAgAQJMIZptM6eHlsz6SJWFpqY00AokQMZswZs.jpg');
echo "   Arquivo existe: " . (file_exists($filePath) ? 'SIM' : 'NÃO') . "\n";
echo "   Caminho: " . $filePath . "\n";

// 6. Verificar se o link simbólico está correto
echo "\n5. Verificando link simbólico:\n";
$linkPath = public_path('storage/avatars/EPTAgAQJMIZptM6eHlsz6SJWFpqY00AokQMZswZs.jpg');
echo "   Link existe: " . (file_exists($linkPath) ? 'SIM' : 'NÃO') . "\n";
echo "   Caminho: " . $linkPath . "\n";

// 7. Verificar se é um link simbólico válido
if (file_exists($linkPath)) {
    $isLink = is_link($linkPath);
    echo "   É link simbólico: " . ($isLink ? 'SIM' : 'NÃO') . "\n";
    if ($isLink) {
        $target = readlink($linkPath);
        echo "   Aponta para: " . $target . "\n";
    }
}
?>
