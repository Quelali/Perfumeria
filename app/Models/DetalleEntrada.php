<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleEntrada extends Model
{
    protected $table = 'ct_detalle_entrada';
    protected $primaryKey = 'id_detalle_entrada';
    protected $fillable = ['id_entrada', 'id_producto', 'id_ubicacion', 'cantidad', 'precio_unitario', 'total_precio'];
    public $timestamps = false;

    public function entrada()
    {
        return $this->belongsTo(Entrada::class, 'id_entrada', 'id_entrada');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class, 'id_ubicacion', 'id_ubicacion');
    }
}
