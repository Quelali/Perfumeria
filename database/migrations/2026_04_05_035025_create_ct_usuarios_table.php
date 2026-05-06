<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ct_usuarios', function (Blueprint $table) {
            $table->string('Email', 100)->primary();
            $table->string('password', 255);
            $table->string('nombre_usuario', 100);
            $table->string('nivel_permisos', 20)->default('user');
            $table->timestamp('fecha_inicio')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ct_usuarios');
    }
};
