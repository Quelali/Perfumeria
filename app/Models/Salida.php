<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{
    protected $table = 'ct_salidas';
    protected $primaryKey = 'id_salida';
    protected $fillable = ['usuario_email', 'fecha_salida', 'total_salida'];
    public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_email', 'Email');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleSalida::class, 'id_salida', 'id_salida');
    }
}
