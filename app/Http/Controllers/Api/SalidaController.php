<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Salida;
use App\Models\DetalleSalida;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalidaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Salida::with(['usuario', 'detalles'])->get();
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
            // Validar stock
            foreach ($request->detalles as $det) {
                $stock = Stock::where('id_producto', $det['id_producto'])
                    ->where('id_ubicacion', $det['id_ubicacion'])
                    ->first();
                if (!$stock || $stock->cantidad < $det['cantidad']) {
                    throw new \Exception('Stock insuficiente para producto ' . $det['id_producto'] . ' en ubicación ' . $det['id_ubicacion']);
                }
            }

            $total = 0;
            foreach ($request->detalles as $det) {
                $total += $det['cantidad'] * $det['precio_unitario'];
            }

            $salida = Salida::create([
                'usuario_email' => $request->usuario_email,
                'total_salida' => $total,
            ]);

            foreach ($request->detalles as $det) {
                DetalleSalida::create([
                    'id_salida' => $salida->id_salida,
                    'id_producto' => $det['id_producto'],
                    'id_ubicacion' => $det['id_ubicacion'],
                    'cantidad' => $det['cantidad'],
                    'precio_unitario' => $det['precio_unitario'],
                    'total_precio' => $det['cantidad'] * $det['precio_unitario'],
                ]);

                // Actualizar stock
                $stock = Stock::where('id_producto', $det['id_producto'])
                    ->where('id_ubicacion', $det['id_ubicacion'])
                    ->first();
                $stock->decrement('cantidad', $det['cantidad']);
            }
        });

        return response()->json(['message' => 'Salida creada'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Salida::with(['usuario', 'detalles'])->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return response()->json(['message' => 'Update no implementado'], 501);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $salida = Salida::findOrFail($id);

        DB::transaction(function () use ($salida) {
            foreach ($salida->detalles as $detalle) {
                $stock = Stock::where('id_producto', $detalle->id_producto)
                    ->where('id_ubicacion', $detalle->id_ubicacion)
                    ->first();
                if ($stock) {
                    $stock->increment('cantidad', $detalle->cantidad);
                }
            }
            $salida->delete();
        });

        return response()->json(['message' => 'Salida eliminada']);
    }
}
