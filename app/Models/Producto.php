<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'ct_productos';
    protected $primaryKey = 'id_producto';
    protected $fillable = ['codigo', 'nombre_producto', 'descripcion', 'precio_producto'];
    public $timestamps = false;

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'id_producto', 'id_producto');
    }

    public function detalleEntradas()
    {
        return $this->hasMany(DetalleEntrada::class, 'id_producto', 'id_producto');
    }

    public function detalleSalidas()
    {
        return $this->hasMany(DetalleSalida::class, 'id_producto', 'id_producto');
    }
}
