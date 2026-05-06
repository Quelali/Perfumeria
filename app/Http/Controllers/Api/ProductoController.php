<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Producto::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:50|unique:ct_productos,codigo',
            'nombre_producto' => 'required|string|max:50',
            'descripcion' => 'required|string|max:100',
            'precio_producto' => 'required|numeric|min:0',
        ], [
            'codigo.unique' => 'El código ya está en uso.',
            'codigo.required' => 'El código es obligatorio.',
            'nombre_producto.required' => 'El nombre del producto es obligatorio.',
            'descripcion.required' => 'La descripción del producto es obligatoria.',
            'precio_producto.required' => 'El precio del producto es obligatorio.',
        ]);

        $producto = Producto::create($request->only(['codigo', 'nombre_producto', 'descripcion', 'precio_producto']));

        return response()->json($producto, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Producto::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $producto = Producto::findOrFail($id);

        $request->validate([
            'codigo' => 'nullable|string|max:50|unique:ct_productos,codigo,' . $producto->id_producto . ',id_producto',
            'nombre_producto' => 'nullable|string|max:50',
            'descripcion' => 'nullable|string|max:100',
            'precio_producto' => 'nullable|numeric|min:0',
        ], [
            'codigo.unique' => 'El código ya está en uso.',
        ]);

        $producto->update($request->only(['codigo', 'nombre_producto', 'descripcion', 'precio_producto']));

        return response()->json($producto);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $producto = Producto::findOrFail($id);
        $producto->delete();

        return response()->json(['message' => 'Producto eliminado']);
    }
}
