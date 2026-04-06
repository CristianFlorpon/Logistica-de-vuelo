<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Flota;
use App\Models\Ruta;
use App\Models\Vuelo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class VueloController extends Controller
{
    public function index(Request $request)
    {
        $query = Vuelo::with([
            'aerolinea',
            'ruta.origen',
            'ruta.destino',
            'flota',
        ])->withCount('reservas');

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('codigo', 'like', "%{$search}%")
                    ->orWhere('origen', 'like', "%{$search}%")
                    ->orWhere('destino', 'like', "%{$search}%")
                    ->orWhereHas('aerolinea', function ($airlineQuery) use ($search) {
                        $airlineQuery
                            ->where('nombre', 'like', "%{$search}%")
                            ->orWhere('codigo', 'like', "%{$search}%");
                    })
                    ->orWhereHas('ruta.origen', function ($airportQuery) use ($search) {
                        $airportQuery
                            ->where('ciudad', 'like', "%{$search}%")
                            ->orWhere('codigo_iata', 'like', "%{$search}%");
                    })
                    ->orWhereHas('ruta.destino', function ($airportQuery) use ($search) {
                        $airportQuery
                            ->where('ciudad', 'like', "%{$search}%")
                            ->orWhere('codigo_iata', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->string('estado')->value());
        }

        if ($request->filled('aerolinea_id')) {
            $query->where('aerolinea_id', $request->integer('aerolinea_id'));
        }

        if ($request->filled('ruta_id')) {
            $query->where('ruta_id', $request->integer('ruta_id'));
        }

        return $query
            ->orderByRaw('salida_programada IS NULL')
            ->orderBy('salida_programada')
            ->latest('id')
            ->get();
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $vuelo = $this->persist(new Vuelo(), $data);

        return response()->json($this->loadVuelo($vuelo), 201);
    }

    public function show(Vuelo $vuelo)
    {
        return $this->loadVuelo($vuelo);
    }

    public function update(Request $request, Vuelo $vuelo)
    {
        $data = $this->validateData($request, $vuelo);
        $this->persist($vuelo, $data);

        return $this->loadVuelo($vuelo);
    }

    public function destroy(Vuelo $vuelo)
    {
        $vuelo->delete();

        return response()->json(['message' => 'Vuelo eliminado']);
    }

    public function estado(Request $request, Vuelo $vuelo)
    {
        $data = $request->validate([
            'estado' => ['required', 'string', Rule::in(Vuelo::ESTADOS)],
        ]);

        $vuelo->update(['estado' => $data['estado']]);

        return $this->loadVuelo($vuelo);
    }

    private function validateData(Request $request, ?Vuelo $vuelo = null): array
    {
        $payload = $request->all();

        foreach ([
            'origen',
            'destino',
            'estado',
            'ruta_id',
            'flota_id',
            'salida_programada',
            'llegada_programada',
            'asientos',
        ] as $field) {
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
                Rule::unique('vuelos', 'codigo')->ignore($vuelo?->id),
            ],
            'origen' => ['nullable', 'string', 'max:255'],
            'destino' => ['nullable', 'string', 'max:255'],
            'asientos' => ['nullable', 'integer', 'min:1'],
            'estado' => ['nullable', 'string', Rule::in(Vuelo::ESTADOS)],
            'aerolinea_id' => ['required', 'exists:aerolineas,id'],
            'ruta_id' => ['nullable', 'exists:rutas,id'],
            'flota_id' => ['nullable', 'exists:flotas,id'],
            'salida_programada' => ['nullable', 'date'],
            'llegada_programada' => ['nullable', 'date', 'after_or_equal:salida_programada'],
        ]);
    }

    private function persist(Vuelo $vuelo, array $data): Vuelo
    {
        $ruta = !empty($data['ruta_id'])
            ? Ruta::with(['origen', 'destino'])->findOrFail($data['ruta_id'])
            : null;

        $flota = !empty($data['flota_id'])
            ? Flota::findOrFail($data['flota_id'])
            : null;

        if ($flota && (int) $flota->aerolinea_id !== (int) $data['aerolinea_id']) {
            throw ValidationException::withMessages([
                'flota_id' => 'La flota seleccionada no pertenece a la aerolínea elegida.',
            ]);
        }

        if ($ruta) {
            $data['origen'] = $data['origen'] ?: sprintf('%s (%s)', $ruta->origen->ciudad, $ruta->origen->codigo_iata);
            $data['destino'] = $data['destino'] ?: sprintf('%s (%s)', $ruta->destino->ciudad, $ruta->destino->codigo_iata);
        }

        if ($flota && empty($data['asientos'])) {
            $data['asientos'] = $flota->capacidad;
        }

        if (empty($data['origen']) || empty($data['destino'])) {
            throw ValidationException::withMessages([
                'ruta_id' => 'Selecciona una ruta o completa origen y destino manualmente.',
            ]);
        }

        if (empty($data['asientos'])) {
            throw ValidationException::withMessages([
                'asientos' => 'Ingresa los asientos o selecciona una flota para heredar la capacidad.',
            ]);
        }

        $data['estado'] = $data['estado'] ?? 'programado';

        $vuelo->fill($data);
        $vuelo->save();

        return $vuelo;
    }

    private function loadVuelo(Vuelo $vuelo): Vuelo
    {
        return $vuelo->load([
            'aerolinea',
            'ruta.origen',
            'ruta.destino',
            'flota',
        ])->loadCount('reservas');
    }
}
