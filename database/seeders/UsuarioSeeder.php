<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Usuario::create([
            'Email' => 'admin@perfumeria.com',
            'password' => Hash::make('password'),
            'nombre_usuario' => 'Admin',
            'nivel_permisos' => 'admin',
        ]);
    }
}
