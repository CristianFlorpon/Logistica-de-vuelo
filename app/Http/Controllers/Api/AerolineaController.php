<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aerolinea;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AerolineaController extends Controller
{
    public function index(Request $request)
    {
        $query = Aerolinea::query()->withCount(['vuelos', 'flotas']);

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('nombre', 'like', "%{$search}%")
                    ->orWhere('codigo', 'like', "%{$search}%")
                    ->orWhere('pais', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('nombre')->get();
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        return response()->json(Aerolinea::create($data), 201);
    }

    public function show(Aerolinea $aerolinea)
    {
        return $aerolinea->loadCount(['vuelos', 'flotas']);
    }

    public function update(Request $request, Aerolinea $aerolinea)
    {
        $data = $this->validateData($request, $aerolinea);
        $aerolinea->update($data);

        return $aerolinea->loadCount(['vuelos', 'flotas']);
    }

    public function destroy(Aerolinea $aerolinea)
    {
        $aerolinea->delete();

        return response()->json(['message' => 'Aerolínea eliminada']);
    }

    private function validateData(Request $request, ?Aerolinea $aerolinea = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'codigo' => [
                'required',
                'string',
                'max:255',
                Rule::unique('aerolineas', 'codigo')->ignore($aerolinea?->id),
            ],
            'pais' => ['required', 'string', 'max:255'],
        ]);
    }
}
