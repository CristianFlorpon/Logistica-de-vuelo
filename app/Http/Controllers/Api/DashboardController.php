<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aerolinea;
use App\Models\Aeropuerto;
use App\Models\Flota;
use App\Models\Pasajero;
use App\Models\Reserva;
use App\Models\Ruta;
use App\Models\Vuelo;

class DashboardController extends Controller
{
    public function index()
    {
        $vuelos = Vuelo::with(['aerolinea', 'ruta.origen', 'ruta.destino'])->withCount('reservas')->get();
        $reservas = Reserva::all();

        return [
            'totales' => [
                'vuelos' => $vuelos->count(),
                'pasajeros' => Pasajero::count(),
                'reservas' => $reservas->count(),
                'rutas' => Ruta::count(),
                'aeropuertos' => Aeropuerto::count(),
                'flota' => Flota::count(),
                'aerolineas' => Aerolinea::count(),
            ],
            'vuelos_por_estado' => collect(Vuelo::ESTADOS)->map(function ($estado) use ($vuelos) {
                return [
                    'estado' => $estado,
                    'total' => $vuelos->where('estado', $estado)->count(),
                ];
            })->values(),
            'reservas_por_clase' => collect(Reserva::CLASES)->map(function ($clase) use ($reservas) {
                return [
                    'clase' => $clase,
                    'total' => $reservas->where('clase', $clase)->count(),
                ];
            })->values(),
            'ocupacion_vuelos' => $vuelos
                ->map(function ($vuelo) {
                    $ocupacion = $vuelo->asientos > 0
                        ? round(($vuelo->reservas_count / $vuelo->asientos) * 100, 1)
                        : 0;

                    return [
                        'id' => $vuelo->id,
                        'codigo' => $vuelo->codigo,
                        'ruta' => $this->routeLabel($vuelo),
                        'estado' => $vuelo->estado,
                        'asientos' => $vuelo->asientos,
                        'reservas' => $vuelo->reservas_count,
                        'ocupacion' => $ocupacion,
                    ];
                })
                ->sortByDesc('ocupacion')
                ->take(6)
                ->values(),
            'rutas_mas_usadas' => $vuelos
                ->groupBy(fn ($vuelo) => $this->routeLabel($vuelo))
                ->map(function ($group, $label) {
                    return [
                        'ruta' => $label,
                        'vuelos' => $group->count(),
                        'reservas' => $group->sum('reservas_count'),
                    ];
                })
                ->sortByDesc('reservas')
                ->take(6)
                ->values(),
            'proximos_vuelos' => $vuelos
                ->filter(fn ($vuelo) => !empty($vuelo->salida_programada))
                ->sortBy('salida_programada')
                ->take(6)
                ->values()
                ->map(function ($vuelo) {
                    return [
                        'id' => $vuelo->id,
                        'codigo' => $vuelo->codigo,
                        'ruta' => $this->routeLabel($vuelo),
                        'aerolinea' => $vuelo->aerolinea?->nombre,
                        'estado' => $vuelo->estado,
                        'salida_programada' => optional($vuelo->salida_programada)->format('Y-m-d H:i'),
                    ];
                }),
        ];
    }

    private function routeLabel(Vuelo $vuelo): string
    {
        if ($vuelo->ruta && $vuelo->ruta->origen && $vuelo->ruta->destino) {
            return sprintf(
                '%s (%s) -> %s (%s)',
                $vuelo->ruta->origen->ciudad,
                $vuelo->ruta->origen->codigo_iata,
                $vuelo->ruta->destino->ciudad,
                $vuelo->ruta->destino->codigo_iata
            );
        }

        return "{$vuelo->origen} -> {$vuelo->destino}";
    }
}
