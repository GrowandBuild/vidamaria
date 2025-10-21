<?php
echo "Laravel está funcionando!<br>";
echo "Data: " . date('Y-m-d H:i:s') . "<br>";
echo "PHP Version: " . phpversion() . "<br>";

// Testar conexão com banco
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=u834385447_vidamaria', 'u834385447_vidamaria', '@2412Ale');
    echo "Banco de dados: OK<br>";
    
    // Verificar se usuário existe
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE email = 'admin@esmalteria.com'");
    $result = $stmt->fetch();
    echo "Usuário admin existe: " . ($result['total'] > 0 ? 'SIM' : 'NÃO') . "<br>";
    
} catch (Exception $e) {
    echo "Erro no banco: " . $e->getMessage() . "<br>";
}
?>





