<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    public const CLASES = ['economica', 'ejecutiva', 'primera'];
    public const ESTADOS = ['confirmada', 'pendiente', 'cancelada'];

    protected $fillable = [
        'codigo',
        'pasajero_id',
        'vuelo_id',
        'clase',
        'estado',
        'asiento',
        'precio',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
    ];

    public function pasajero()
    {
        return $this->belongsTo(Pasajero::class);
    }

    public function vuelo()
    {
        return $this->belongsTo(Vuelo::class);
    }
}
