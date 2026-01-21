<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// TABLA PUNTOS DE ENTREGA

return new class extends Migration
{
    /**
     * Run the migrations
     * Ejecutar la migración
     */
    public function up(): void
{
    Schema::create('delivery_points', function (Blueprint $table) {
        $table->increments('id_delivery_point');
        $table->unsignedInteger('id_user');
        $table->string('name', 255);
        $table->string('direction', 255);
        $table->double('latitude');
        $table->double('length');

        $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations
     * Eliminar la migración
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_points');
    }
};
