<?php
require __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$db = Database::getConnection();

$usuarios = [
    ['admin',       'admin123',   1, null],
    ['coordinador', 'coord123',   2, null],
    ['jperez',      'docente123', 3, [
        'dni' => '12345678',
        'nombres' => 'Juan',
        'apellidos' => 'Pérez Gómez',
        'email' => 'jperez@colegio.edu.pe',
        'telefono' => '987654321',
        'especialidad' => 'Matemática'
    ]],
];

foreach ($usuarios as [$username, $password, $rolId, $docenteData]) {
    // Verificar si ya existe
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo "⚠️  Usuario '$username' ya existe, se omite.\n";
        continue;
    }

    // Insertar usuario
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $db->prepare("INSERT INTO usuarios (username, password_hash, rol_id) VALUES (?, ?, ?)");
    $stmt->execute([$username, $hash, $rolId]);
    $usuarioId = $db->lastInsertId();
    echo "✅ Usuario '$username' creado (id: $usuarioId)\n";

    // Si es docente, insertar en tabla docentes
    if ($docenteData) {
        $stmt = $db->prepare("
            INSERT INTO docentes (usuario_id, dni, nombres, apellidos, email, telefono, especialidad)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $usuarioId,
            $docenteData['dni'],
            $docenteData['nombres'],
            $docenteData['apellidos'],
            $docenteData['email'],
            $docenteData['telefono'],
            $docenteData['especialidad']
        ]);
        echo "   └─ Perfil de docente creado (id: {$db->lastInsertId()})\n";
    }
}

echo "\n🎉 Listo. Credenciales:\n";
echo "   admin / admin123\n";
echo "   coordinador / coord123\n";
echo "   jperez / docente123\n";