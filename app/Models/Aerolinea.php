<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aerolinea extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'codigo',
        'pais',
    ];

    public function vuelos()
    {
        return $this->hasMany(Vuelo::class);
    }

    public function flotas()
    {
        return $this->hasMany(Flota::class);
    }
}
