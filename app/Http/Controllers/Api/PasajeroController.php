<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pasajero;
use Illuminate\Http\Request;

class PasajeroController extends Controller
{
    public function index(Request $request)
    {
        $query = Pasajero::with(['vuelo.aerolinea', 'reservas']);

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('nombre', 'like', "%{$search}%")
                    ->orWhere('apellido', 'like', "%{$search}%")
                    ->orWhere('documento', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('vuelo_id')) {
            $query->where('vuelo_id', $request->integer('vuelo_id'));
        }

        return $query->latest()->get();
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        return response()->json(Pasajero::create($data)->load(['vuelo.aerolinea', 'reservas']), 201);
    }

    public function show(Pasajero $pasajero)
    {
        return $pasajero->load(['vuelo.aerolinea', 'reservas']);
    }

    public function update(Request $request, Pasajero $pasajero)
    {
        $data = $this->validateData($request);
        $pasajero->update($data);

        return $pasajero->load(['vuelo.aerolinea', 'reservas']);
    }

    public function destroy(Pasajero $pasajero)
    {
        $pasajero->delete();

        return response()->json(['message' => 'Pasajero eliminado']);
    }

    private function validateData(Request $request): array
    {
        $payload = $request->all();

        foreach (['documento', 'email', 'telefono'] as $field) {
            if (($payload[$field] ?? null) === '') {
                $payload[$field] = null;
            }
        }

        $request->replace($payload);

        return $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'documento' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:255'],
            'vuelo_id' => ['required', 'exists:vuelos,id'],
        ]);
    }
}
