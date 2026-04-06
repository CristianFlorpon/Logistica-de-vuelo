<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aeropuerto;
use App\Models\Ruta;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RutaController extends Controller
{
    public function index(Request $request)
    {
        $query = Ruta::with(['origen', 'destino']);

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('codigo', 'like', "%{$search}%")
                    ->orWhere('pais', 'like', "%{$search}%")
                    ->orWhereHas('origen', function ($airportQuery) use ($search) {
                        $airportQuery
                            ->where('nombre', 'like', "%{$search}%")
                            ->orWhere('codigo_iata', 'like', "%{$search}%")
                            ->orWhere('ciudad', 'like', "%{$search}%");
                    })
                    ->orWhereHas('destino', function ($airportQuery) use ($search) {
                        $airportQuery
                            ->where('nombre', 'like', "%{$search}%")
                            ->orWhere('codigo_iata', 'like', "%{$search}%")
                            ->orWhere('ciudad', 'like', "%{$search}%");
                    });
            });
        }

        return $query->orderBy('codigo')->get();
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $ruta = Ruta::create($this->completeDistanceData($data));

        return response()->json($ruta->load(['origen', 'destino']), 201);
    }

    public function show(Ruta $ruta)
    {
        return $ruta->load(['origen', 'destino']);
    }

    public function update(Request $request, Ruta $ruta)
    {
        $data = $this->validateData($request, $ruta);
        $ruta->update($this->completeDistanceData($data));

        return $ruta->load(['origen', 'destino']);
    }

    public function destroy(Ruta $ruta)
    {
        $ruta->delete();

        return response()->json(['message' => 'Ruta eliminada']);
    }

    private function validateData(Request $request, ?Ruta $ruta = null): array
    {
        $payload = $request->all();

        foreach (['pais', 'distancia_km', 'tiempo_estimado_min'] as $field) {
            if (($payload[$field] ?? null) === '') {
                $payload[$field] = null;
            }
        }

        $request->replace($payload);

        return $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:255',
                Rule::unique('rutas', 'codigo')->ignore($ruta?->id),
            ],
            'pais' => ['nullable', 'string', 'max:255'],
            'origen_airport_id' => ['required', 'exists:aeropuertos,id', 'different:destino_airport_id'],
            'destino_airport_id' => ['required', 'exists:aeropuertos,id'],
            'distancia_km' => ['nullable', 'integer', 'min:1'],
            'tiempo_estimado_min' => ['nullable', 'integer', 'min:1'],
        ]);
    }

    private function completeDistanceData(array $data): array
    {
        $origen = Aeropuerto::find($data['origen_airport_id']);
        $destino = Aeropuerto::find($data['destino_airport_id']);

        if (empty($data['pais']) && $origen) {
            $data['pais'] = $origen->pais;
        }

        if (empty($data['distancia_km']) && $origen && $destino) {
            $data['distancia_km'] = $this->calculateDistance($origen, $destino);
        }

        if (empty($data['tiempo_estimado_min']) && !empty($data['distancia_km'])) {
            $data['tiempo_estimado_min'] = (int) max(45, round(($data['distancia_km'] / 750) * 60));
        }

        return $data;
    }

    private function calculateDistance(Aeropuerto $origen, Aeropuerto $destino): int
    {
        $earthRadius = 6371;
        $latFrom = deg2rad((float) $origen->latitud);
        $lonFrom = deg2rad((float) $origen->longitud);
        $latTo = deg2rad((float) $destino->latitud);
        $lonTo = deg2rad((float) $destino->longitud);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2)
            + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
        ));

        return (int) round($earthRadius * $angle);
    }
}
