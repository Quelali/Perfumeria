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
        Schema::create('ct_detalle_entrada', function (Blueprint $table) {
            $table->id('id_detalle_entrada');
            $table->unsignedBigInteger('id_entrada');
            $table->unsignedBigInteger('id_producto');
            $table->unsignedBigInteger('id_ubicacion');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('total_precio', 10, 2);
            $table->foreign('id_entrada')->references('id_entrada')->on('ct_entradas');
            $table->foreign('id_producto')->references('id_producto')->on('ct_productos');
            $table->foreign('id_ubicacion')->references('id_ubicacion')->on('ct_ubicaciones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ct_detalle_entrada');
    }
};
