<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pasajero;
use App\Models\Reserva;
use App\Models\Vuelo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ReservaController extends Controller
{
    public function index(Request $request)
    {
        $query = Reserva::with(['pasajero', 'vuelo.aerolinea', 'vuelo.ruta.origen', 'vuelo.ruta.destino']);

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('codigo', 'like', "%{$search}%")
                    ->orWhere('asiento', 'like', "%{$search}%")
                    ->orWhereHas('pasajero', function ($passengerQuery) use ($search) {
                        $passengerQuery
                            ->where('nombre', 'like', "%{$search}%")
                            ->orWhere('apellido', 'like', "%{$search}%")
                            ->orWhere('documento', 'like', "%{$search}%");
                    })
                    ->orWhereHas('vuelo', function ($flightQuery) use ($search) {
                        $flightQuery->where('codigo', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('clase')) {
            $query->where('clase', $request->string('clase')->value());
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->string('estado')->value());
        }

        if ($request->filled('vuelo_id')) {
            $query->where('vuelo_id', $request->integer('vuelo_id'));
        }

        return $query->latest()->get();
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $reserva = $this->persist(new Reserva(), $data);

        return response()->json($this->loadReserva($reserva), 201);
    }

    public function show(Reserva $reserva)
    {
        return $this->loadReserva($reserva);
    }

    public function update(Request $request, Reserva $reserva)
    {
        $data = $this->validateData($request, $reserva);
        $this->persist($reserva, $data);

        return $this->loadReserva($reserva);
    }

    public function destroy(Reserva $reserva)
    {
        $reserva->delete();

        return response()->json(['message' => 'Reserva eliminada']);
    }

    private function validateData(Request $request, ?Reserva $reserva = null): array
    {
        $payload = $request->all();

        foreach (['asiento', 'precio'] as $field) {
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
                Rule::unique('reservas', 'codigo')->ignore($reserva?->id),
            ],
            'pasajero_id' => ['required', 'exists:pasajeros,id'],
            'vuelo_id' => ['required', 'exists:vuelos,id'],
            'clase' => ['required', 'string', Rule::in(Reserva::CLASES)],
            'estado' => ['required', 'string', Rule::in(Reserva::ESTADOS)],
            'asiento' => ['nullable', 'string', 'max:50'],
            'precio' => ['nullable', 'numeric', 'min:0'],
        ]);
    }

    private function persist(Reserva $reserva, array $data): Reserva
    {
        $vuelo = Vuelo::findOrFail($data['vuelo_id']);
        $pasajero = Pasajero::findOrFail($data['pasajero_id']);

        $this->ensureCapacity($vuelo, $reserva->exists ? $reserva->id : null);
        $this->ensureSeatAvailability($data, $reserva->exists ? $reserva->id : null);

        if ((int) $pasajero->vuelo_id !== (int) $data['vuelo_id']) {
            $pasajero->update(['vuelo_id' => $data['vuelo_id']]);
        }

        $reserva->fill($data);
        $reserva->save();

        return $reserva;
    }

    private function ensureCapacity(Vuelo $vuelo, ?int $ignoreReservaId = null): void
    {
        $count = $vuelo->reservas()
            ->when($ignoreReservaId, fn ($query) => $query->where('id', '!=', $ignoreReservaId))
            ->count();

        if ($count >= $vuelo->asientos) {
            throw ValidationException::withMessages([
                'vuelo_id' => 'El vuelo ya alcanzo su capacidad maxima.',
            ]);
        }
    }

    private function ensureSeatAvailability(array $data, ?int $ignoreReservaId = null): void
    {
        if (empty($data['asiento'])) {
            return;
        }

        $exists = Reserva::query()
            ->where('vuelo_id', $data['vuelo_id'])
            ->where('asiento', $data['asiento'])
            ->when($ignoreReservaId, fn ($query) => $query->where('id', '!=', $ignoreReservaId))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'asiento' => 'Ese asiento ya fue asignado en el vuelo seleccionado.',
            ]);
        }
    }

    private function loadReserva(Reserva $reserva): Reserva
    {
        return $reserva->load(['pasajero', 'vuelo.aerolinea', 'vuelo.ruta.origen', 'vuelo.ruta.destino']);
    }
}
