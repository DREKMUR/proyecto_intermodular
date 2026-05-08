<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarController extends Controller
{
    /**
     * Lista todos los coches con filtros opcionales.
     */
    public function index(Request $request)
    {
        $query = Car::query();

        $query->when($request->state, fn($q) => $q->where('state', $request->state));
        $query->when($request->name, fn($q) => $q->where('name', 'like', '%' . $request->name . '%'));
        $query->when($request->brand_id, fn($q) => $q->where('brand_id', $request->brand_id));
        $query->when($request->year, fn($q) => $q->where('year', $request->year));

        $perPage = (int) $request->get('per_page', 8);
        $cars = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends($request->query());

        return response()->json($cars, 200);
    }

    /**
     * Crea un nuevo registro.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'state' => 'required|string',
            'year'  => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'price' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $car = Car::create($validator->validated());

        return response()->json($car, 201);
    }

    /**
     * Muestra un coche específico por ID.
     */
    public function show($id)
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json(['message' => 'Coche no encontrado'], 404);
        }

        return response()->json($car, 200);
    }

    /**
     * Actualiza un registro existente.
     */
    public function update(Request $request, $id)
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json(['message' => 'Coche no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'  => 'sometimes|string|max:255',
            'brand' => 'sometimes|string|max:255',
            'state' => 'sometimes|string',
            'year'  => 'sometimes|integer|min:1900',
            'price' => 'sometimes|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $car->update($validator->validated());

        return response()->json($car, 200);
    }

    /**
     * Elimina un registro.
     */
    public function destroy($id)
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json(['message' => 'Coche no encontrado'], 404);
        }

        $car->delete();

        return response()->json(['message' => 'Coche eliminado correctamente'], 200);
    }
}
