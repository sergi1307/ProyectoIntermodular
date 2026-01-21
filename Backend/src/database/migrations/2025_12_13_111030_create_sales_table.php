<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// TABLA VENTAS

return new class extends Migration
{
    /**
     * Run the migrations
     * Ejecutar la migración
     */
    public function up(): void
{
    Schema::create('sales', function (Blueprint $table) {
        $table->increments('id_sale');
        $table->unsignedInteger('id_product');
        $table->unsignedInteger('id_buyer');
        $table->unsignedInteger('id_seller');
        $table->unsignedInteger('id_delivery_point');
        $table->date('sale_date');
        $table->double('total');
        $table->date('collection_date');
        $table->enum('state', ['Pendiente', 'En Curso', 'Terminado']);

        $table->foreign('id_product')->references('id_product')->on('products')->onDelete('cascade');
        $table->foreign('id_buyer')->references('id_user')->on('users')->onDelete('cascade');
        $table->foreign('id_seller')->references('id_user')->on('users')->onDelete('cascade');
        $table->foreign('id_delivery_point')->references('id_delivery_point')->on('delivery_points')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations
     * Eliminar la migración
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
