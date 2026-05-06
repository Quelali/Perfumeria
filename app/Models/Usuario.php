<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'ct_usuarios';
    protected $primaryKey = 'Email';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['Email', 'password', 'nombre_usuario', 'nivel_permisos', 'fecha_inicio'];
    protected $hidden = ['password'];
    public $timestamps = false;

    public function entradas()
    {
        return $this->hasMany(Entrada::class, 'usuario_email', 'Email');
    }

    public function salidas()
    {
        return $this->hasMany(Salida::class, 'usuario_email', 'Email');
    }
}
