<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;

// [username, password]
$usuarios = [
    ['admin',       'admin123'],
    ['coordinador', 'coord123'],
    ['jperez',      'docente123'],
];

$db = Database::getConnection();

foreach ($usuarios as [$username, $password]) {
    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
    $stmt = $db->prepare("UPDATE usuarios SET password_hash = ? WHERE username = ?");
    $stmt->execute([$hash, $username]);

    if ($stmt->rowCount() > 0) {
        echo "✅ $username → password actualizado a '$password'\n";
    } else {
        echo "⚠️  $username → no encontrado o el hash era idéntico\n";
    }
}

echo "\n🎉 Listo. Ahora prueba el login en Postman.\n";