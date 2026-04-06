<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Flota;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FlotaController extends Controller
{
    public function index(Request $request)
    {
        $query = Flota::with('aerolinea');

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('matricula', 'like', "%{$search}%")
                    ->orWhere('modelo', 'like', "%{$search}%")
                    ->orWhere('tipo', 'like', "%{$search}%")
                    ->orWhereHas('aerolinea', function ($airlineQuery) use ($search) {
                        $airlineQuery
                            ->where('nombre', 'like', "%{$search}%")
                            ->orWhere('codigo', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('aerolinea_id')) {
            $query->where('aerolinea_id', $request->integer('aerolinea_id'));
        }

        return $query->orderBy('matricula')->get();
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        return response()->json(Flota::create($data)->load('aerolinea'), 201);
    }

    public function show(Flota $flota)
    {
        return $flota->load('aerolinea');
    }

    public function update(Request $request, Flota $flota)
    {
        $data = $this->validateData($request, $flota);
        $flota->update($data);

        return $flota->load('aerolinea');
    }

    public function destroy(Flota $flota)
    {
        $flota->delete();

        return response()->json(['message' => 'Avion eliminado']);
    }

    private function validateData(Request $request, ?Flota $flota = null): array
    {
        $payload = $request->all();

        if (($payload['alcance_km'] ?? null) === '') {
            $payload['alcance_km'] = null;
        }

        $request->replace($payload);

        return $request->validate([
            'aerolinea_id' => ['required', 'exists:aerolineas,id'],
            'matricula' => [
                'required',
                'string',
                'max:255',
                Rule::unique('flotas', 'matricula')->ignore($flota?->id),
            ],
            'modelo' => ['required', 'string', 'max:255'],
            'capacidad' => ['required', 'integer', 'min:1'],
            'alcance_km' => ['nullable', 'integer', 'min:1'],
            'tipo' => ['required', 'string', 'max:255'],
        ]);
    }
}
