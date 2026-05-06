<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    protected $table = 'ct_entradas';
    protected $primaryKey = 'id_entrada';
    protected $fillable = ['usuario_email', 'fecha_entrada', 'total_entrada'];
    public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_email', 'Email');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleEntrada::class, 'id_entrada', 'id_entrada');
    }
}
