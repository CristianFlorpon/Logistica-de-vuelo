<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vuelo extends Model
{
    use HasFactory;

    public const ESTADOS = [
        'programado',
        'embarcando',
        'en vuelo',
        'demorado',
        'aterrizado',
        'cancelado',
    ];

    protected $fillable = [
        'codigo',
        'origen',
        'destino',
        'asientos',
        'estado',
        'aerolinea_id',
        'ruta_id',
        'flota_id',
        'salida_programada',
        'llegada_programada',
    ];

    protected $casts = [
        'salida_programada' => 'datetime',
        'llegada_programada' => 'datetime',
    ];

    public function pasajeros()
    {
        return $this->hasMany(Pasajero::class);
    }

    public function aerolinea()
    {
        return $this->belongsTo(Aerolinea::class);
    }

    public function ruta()
    {
        return $this->belongsTo(Ruta::class);
    }

    public function flota()
    {
        return $this->belongsTo(Flota::class);
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }
}
