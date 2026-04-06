<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruta extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'pais',
        'origen_airport_id',
        'destino_airport_id',
        'distancia_km',
        'tiempo_estimado_min',
    ];

    public function origen()
    {
        return $this->belongsTo(Aeropuerto::class, 'origen_airport_id');
    }

    public function destino()
    {
        return $this->belongsTo(Aeropuerto::class, 'destino_airport_id');
    }

    public function vuelos()
    {
        return $this->hasMany(Vuelo::class);
    }
}
