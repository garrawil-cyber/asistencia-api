<?php
namespace App\Core;

class DateHelper
{
    private static array $dias = [
        'Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'
    ];

    private static array $meses = [
        '', 'Enero','Febrero','Marzo','Abril','Mayo','Junio',
        'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'
    ];

    /**
     * Día de la semana en español.
     */
    public static function diaSemana(string $fecha): string
    {
        return self::$dias[(int)date('w', strtotime($fecha))];
    }

    /**
     * Mes en español.
     */
    public static function mes(string $fecha): string
    {
        return self::$meses[(int)date('n', strtotime($fecha))];
    }

    /**
     * Fecha completa: "Viernes, 09 de Octubre de 2026".
     */
    public static function fechaLarga(string $fecha): string
    {
        return self::diaSemana($fecha)
            . ', ' . date('d', strtotime($fecha))
            . ' de ' . self::mes($fecha)
            . ' de ' . date('Y', strtotime($fecha));
    }
}