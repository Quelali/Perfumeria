<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ubicacion extends Model
{
    protected $table = 'ct_ubicaciones';
    protected $primaryKey = 'id_ubicacion';
    protected $fillable = ['nombre'];
    public $timestamps = false;

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'id_ubicacion', 'id_ubicacion');
    }

    public function detalleEntradas()
    {
        return $this->hasMany(DetalleEntrada::class, 'id_ubicacion', 'id_ubicacion');
    }

    public function detalleSalidas()
    {
        return $this->hasMany(DetalleSalida::class, 'id_ubicacion', 'id_ubicacion');
    }
}
