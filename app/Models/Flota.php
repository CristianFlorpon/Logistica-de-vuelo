<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flota extends Model
{
    use HasFactory;

    protected $fillable = [
        'aerolinea_id',
        'matricula',
        'modelo',
        'capacidad',
        'alcance_km',
        'tipo',
    ];

    public function aerolinea()
    {
        return $this->belongsTo(Aerolinea::class);
    }

    public function vuelos()
    {
        return $this->hasMany(Vuelo::class);
    }
}
