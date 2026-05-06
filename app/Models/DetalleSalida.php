<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleSalida extends Model
{
    protected $table = 'ct_detalle_salida';
    protected $primaryKey = 'id_detalle_salida';
    protected $fillable = ['id_salida', 'id_producto', 'id_ubicacion', 'cantidad', 'precio_unitario', 'total_precio'];
    public $timestamps = false;

    public function salida()
    {
        return $this->belongsTo(Salida::class, 'id_salida', 'id_salida');
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
