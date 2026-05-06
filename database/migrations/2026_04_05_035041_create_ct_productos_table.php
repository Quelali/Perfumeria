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
        Schema::create('ct_productos', function (Blueprint $table) {
            $table->id('id_producto');
            $table->string('codigo', 50)->unique();
            $table->string('nombre_producto', 50);
            $table->string('descripcion', 100);
            $table->decimal('precio_producto', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ct_productos');
    }
};
