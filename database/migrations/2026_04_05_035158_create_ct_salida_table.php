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
        Schema::create('ct_salida', function (Blueprint $table) {
            $table->id('id_salida');
            $table->string('usuario_email', 100);
            $table->timestamp('fecha_salida')->useCurrent();
            $table->decimal('total_salida', 10, 2);
            $table->foreign('usuario_email')->references('Email')->on('ct_usuarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ct_salidas');
    }
};
