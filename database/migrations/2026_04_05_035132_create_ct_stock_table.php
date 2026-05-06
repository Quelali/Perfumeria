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
        Schema::create('ct_stock', function (Blueprint $table) {
            $table->id('id_stock');
            $table->unsignedBigInteger('id_producto');
            $table->unsignedBigInteger('id_ubicacion');
            $table->integer('cantidad')->default(0);
            $table->unique(['id_producto', 'id_ubicacion']);
            $table->foreign('id_producto')->references('id_producto')->on('ct_productos');
            $table->foreign('id_ubicacion')->references('id_ubicacion')->on('ct_ubicaciones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ct_stock');
    }
};
