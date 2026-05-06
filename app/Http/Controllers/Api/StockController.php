<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Stock::with(['producto', 'ubicacion'])->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_producto' => 'required|exists:ct_productos,id_producto',
            'id_ubicacion' => 'required|exists:ct_ubicaciones,id_ubicacion',
            'cantidad' => 'integer|min:0',
        ]);

        $stock = Stock::create($request->all());

        return response()->json($stock, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Stock::with(['producto', 'ubicacion'])->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $stock = Stock::findOrFail($id);

        $request->validate([
            'cantidad' => 'integer|min:0',
        ]);

        $stock->update($request->only(['cantidad']));

        return response()->json($stock);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $stock = Stock::findOrFail($id);
        $stock->delete();

        return response()->json(['message' => 'Stock eliminado']);
    }
}
