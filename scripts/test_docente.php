<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Models\Docente;

// 1. Listar docentes
echo "=== Docentes ===\n";
print_r(Docente::listar(10));

// 2. Buscar por DNI
echo "\n=== Buscar DNI 12345678 ===\n";
print_r(Docente::buscarPorDni('12345678'));

// 3. Resumen de asistencia
echo "\n=== Resumen mes actual ===\n";
print_r(Docente::resumenAsistencia(1, date('Y-m-01'), date('Y-m-d')));

// 4. Horario
echo "\n=== Horario 2026 ===\n";
print_r(Docente::horario(1, 1));