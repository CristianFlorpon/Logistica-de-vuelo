<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aeropuerto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AeropuertoController extends Controller
{
    public function index(Request $request)
    {
        $query = Aeropuerto::query();

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('nombre', 'like', "%{$search}%")
                    ->orWhere('codigo_iata', 'like', "%{$search}%")
                    ->orWhere('ciudad', 'like', "%{$search}%")
                    ->orWhere('pais', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('pais')->orderBy('ciudad')->get();
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        return response()->json(Aeropuerto::create($data), 201);
    }

    public function show(Aeropuerto $aeropuerto)
    {
        return $aeropuerto;
    }

    public function update(Request $request, Aeropuerto $aeropuerto)
    {
        $data = $this->validateData($request, $aeropuerto);
        $aeropuerto->update($data);

        return $aeropuerto;
    }

    public function destroy(Aeropuerto $aeropuerto)
    {
        $aeropuerto->delete();

        return response()->json(['message' => 'Aeropuerto eliminado']);
    }

    private function validateData(Request $request, ?Aeropuerto $aeropuerto = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'codigo_iata' => [
                'required',
                'string',
                'max:10',
                Rule::unique('aeropuertos', 'codigo_iata')->ignore($aeropuerto?->id),
            ],
            'ciudad' => ['required', 'string', 'max:255'],
            'pais' => ['required', 'string', 'max:255'],
            'latitud' => ['required', 'numeric', 'between:-90,90'],
            'longitud' => ['required', 'numeric', 'between:-180,180'],
        ]);
    }
}
