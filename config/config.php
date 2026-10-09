<?php
return [
    'db' => [
        'host'    => 'localhost',
        'name'    => 'control_asistencia_docentes',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4'
    ],
    'jwt' => [
        'secret' => 'CAMBIA_ESTO_POR_UNA_CLAVE_LARGA_Y_ALEATORIA_2026',
        'expira' => 60 * 60 * 8  // 8 horas
    ],
    'app' => [
        'debug'    => true,
        'timezone' => 'America/Lima'   // ← FIX #3
    ]
];