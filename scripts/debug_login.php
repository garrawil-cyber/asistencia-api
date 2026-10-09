<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;

$db = Database::getConnection();
$stmt = $db->prepare("SELECT username, password_hash FROM usuarios WHERE username = 'admin'");
$stmt->execute();
$user = $stmt->fetch();

if (!$user) {
    echo "❌ No existe usuario 'admin' en la BD\n";
    exit;
}

echo "Usuario: {$user['username']}\n";
echo "Hash en BD: {$user['password_hash']}\n";
echo "Longitud del hash: " . strlen($user['password_hash']) . " caracteres\n";
echo "¿Es un hash bcrypt válido? " . (password_get_info($user['password_hash'])['algoName'] === 'bcrypt' ? 'SÍ' : 'NO') . "\n";
echo "¿'admin123' coincide? " . (password_verify('admin123', $user['password_hash']) ? 'SÍ ✅' : 'NO ❌') . "\n";