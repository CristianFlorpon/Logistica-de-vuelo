<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aeropuerto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'codigo_iata',
        'ciudad',
        'pais',
        'latitud',
        'longitud',
    ];

    protected $casts = [
        'latitud' => 'float',
        'longitud' => 'float',
    ];

    public function rutasOrigen()
    {
        return $this->hasMany(Ruta::class, 'origen_airport_id');
    }

    public function rutasDestino()
    {
        return $this->hasMany(Ruta::class, 'destino_airport_id');
    }
}
