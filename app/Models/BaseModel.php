<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * Automáticamente convierte timestamps a timezone local
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Obtener fecha formateada para mostrar
     */
    public function getFechaFormateadaAttribute()
    {
        return $this->created_at?->format('d/m/Y');
    }

    /**
     * Obtener hora formateada para mostrar
     */
    public function getHoraFormateadaAttribute()
    {
        return $this->created_at?->format('H:i');
    }

    /**
     * Obtener fecha y hora formateadas
     */
    public function getFechaHoraFormateadaAttribute()
    {
        return $this->created_at?->format('d/m/Y H:i');
    }

    /**
     * Obtener hace cuánto tiempo se creó
     */
    public function getHaceTimepoAttribute()
    {
        return $this->created_at?->diffForHumans();
    }
}
