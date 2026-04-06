<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasajero extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
        'documento',
        'email',
        'telefono',
        'vuelo_id',
    ];

    public function vuelo()
    {
        return $this->belongsTo(Vuelo::class);
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }
}
