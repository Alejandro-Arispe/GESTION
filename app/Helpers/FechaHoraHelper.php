<?php

namespace App\Helpers;

use Carbon\Carbon;

class FechaHoraHelper
{
    /**
     * Obtener fecha actual en formato Y-m-d
     */
    public static function fechaHoy(): string
    {
        return Carbon::now()->format('Y-m-d');
    }

    /**
     * Obtener hora actual en formato H:i
     */
    public static function horaActual(): string
    {
        return Carbon::now()->format('H:i');
    }

    /**
     * Obtener hora actual en formato H:i:s
     */
    public static function horaActualConSegundos(): string
    {
        return Carbon::now()->format('H:i:s');
    }

    /**
     * Obtener fecha y hora completa
     */
    public static function ahora(): Carbon
    {
        return Carbon::now();
    }

    /**
     * Obtener fecha en formato legible (ej: 12 de noviembre de 2025)
     */
    public static function fechaLegible(): string
    {
        return Carbon::now()->locale('es')->isoFormat('DD [de] MMMM [de] YYYY');
    }

    /**
     * Obtener día de la semana
     */
    public static function diaSemana(): string
    {
        return Carbon::now()->locale('es')->dayName;
    }

    /**
     * Verificar si es laborable
     */
    public static function esLaborable(): bool
    {
        $dia = Carbon::now()->dayOfWeek;
        return $dia !== 0 && $dia !== 6; // 0 = domingo, 6 = sábado
    }

    /**
     * Obtener minutos transcurridos desde una hora
     */
    public static function minutosDesde($hora): int
    {
        $ahora = Carbon::now();
        $horaObj = Carbon::parse($hora);
        return $ahora->diffInMinutes($horaObj);
    }

    /**
     * Verificar si una hora ha pasado
     */
    public static function horaPasada($hora): bool
    {
        return Carbon::now()->greaterThan(Carbon::parse($hora));
    }

    /**
     * Tiempo hasta una hora (en minutos)
     */
    public static function minutosHasta($hora): int
    {
        return abs(Carbon::now()->diffInMinutes(Carbon::parse($hora)));
    }
}
