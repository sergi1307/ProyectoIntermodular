<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// TABLA RESEÑAS

return new class extends Migration
{
    /**
     * Run the migrations
     * Ejecutar la migración
     */
    public function up(): void
{
    Schema::create('reviews', function (Blueprint $table) {
        $table->increments('id_review');
        $table->unsignedInteger('id_sale');
        $table->decimal('calification', 2, 1)->unsigned();
        $table->text('comment');
        $table->date('review_date');

        $table->foreign('id_sale')->references('id_sale')->on('sales')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations
     * Eliminar la migración
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
