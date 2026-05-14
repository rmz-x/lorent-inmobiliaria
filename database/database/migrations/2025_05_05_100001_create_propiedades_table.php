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
        Schema::create('propiedades', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->enum('tipo', ['Venta', 'Alquiler', 'Anticretico']);
            $table->string('zona');
            $table->decimal('precio', 12, 2);
            $table->decimal('area', 8, 2);
            $table->text('descripcion');
            $table->enum('estado', ['Disponible', 'Reservado', 'Vendido'])->default('Disponible');
            $table->foreignId('agente_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('propiedades');
    }
};
