<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ubicacion;
use Illuminate\Http\Request;

class UbicacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Ubicacion::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        $ubicacion = Ubicacion::create($request->all());

        return response()->json($ubicacion, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Ubicacion::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $ubicacion = Ubicacion::findOrFail($id);

        $request->validate([
            'nombre' => 'string|max:100',
        ]);

        $ubicacion->update($request->all());

        return response()->json($ubicacion);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ubicacion = Ubicacion::findOrFail($id);
        $ubicacion->delete();

        return response()->json(['message' => 'Ubicación eliminada']);
    }
}
