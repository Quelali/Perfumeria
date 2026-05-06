<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entrada;
use App\Models\DetalleEntrada;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntradaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Entrada::with(['usuario', 'detalles'])->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'usuario_email' => 'required|exists:ct_usuarios,Email',
            'detalles' => 'required|array|min:1',
            'detalles.*.id_producto' => 'required|exists:ct_productos,id_producto',
            'detalles.*.id_ubicacion' => 'required|exists:ct_ubicaciones,id_ubicacion',
            'detalles.*.cantidad' => 'required|integer|min:1',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $total = 0;
            foreach ($request->detalles as $det) {
                $total += $det['cantidad'] * $det['precio_unitario'];
            }

            $entrada = Entrada::create([
                'usuario_email' => $request->usuario_email,
                'total_entrada' => $total,
            ]);

            foreach ($request->detalles as $det) {
                DetalleEntrada::create([
                    'id_entrada' => $entrada->id_entrada,
                    'id_producto' => $det['id_producto'],
                    'id_ubicacion' => $det['id_ubicacion'],
                    'cantidad' => $det['cantidad'],
                    'precio_unitario' => $det['precio_unitario'],
                    'total_precio' => $det['cantidad'] * $det['precio_unitario'],
                ]);

                // Actualizar stock
                $stock = Stock::firstOrCreate([
                    'id_producto' => $det['id_producto'],
                    'id_ubicacion' => $det['id_ubicacion'],
                ], ['cantidad' => 0]);
                $stock->increment('cantidad', $det['cantidad']);
            }
        });

        return response()->json(['message' => 'Entrada creada'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Entrada::with(['usuario', 'detalles'])->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Para simplicidad, no implementar update completo, ya que requiere revertir stock
        return response()->json(['message' => 'Update no implementado'], 501);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $entrada = Entrada::findOrFail($id);

        DB::transaction(function () use ($entrada) {
            foreach ($entrada->detalles as $detalle) {
                $stock = Stock::where('id_producto', $detalle->id_producto)
                    ->where('id_ubicacion', $detalle->id_ubicacion)
                    ->first();
                if ($stock) {
                    $stock->decrement('cantidad', $detalle->cantidad);
                }
            }
            $entrada->delete();
        });

        return response()->json(['message' => 'Entrada eliminada']);
    }
}
