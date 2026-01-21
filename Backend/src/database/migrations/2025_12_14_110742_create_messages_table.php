<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// TABLA MENSAJES

return new class extends Migration
{
    /**
     * Run the migrations
     * Ejecutar la migración
     */
    public function up(): void
{
    Schema::create('messages', function (Blueprint $table) {
        $table->increments('id_message');
        $table->unsignedInteger('id_product');
        $table->unsignedInteger('id_transmitter'); 
        $table->unsignedInteger('id_receiver'); 
        $table->text('content');
        $table->date('shipping_date');

        $table->foreign('id_product')->references('id_product')->on('products')->onDelete('cascade');
        $table->foreign('id_transmitter')->references('id_user')->on('users')->onDelete('cascade');
        $table->foreign('id_receiver')->references('id_user')->on('users')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations
     * Eliminar la migración
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
