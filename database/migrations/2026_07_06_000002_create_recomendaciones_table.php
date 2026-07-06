<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('recomendaciones')) {
            return;
        }

        Schema::create('recomendaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('propiedad_id')->constrained('propiedades')->cascadeOnDelete();
            $table->decimal('puntuacion_recomendacion', 5, 2)->default(0);
            $table->boolean('vista')->default(false);
            $table->timestamp('fecha_recomendacion')->useCurrent();
            $table->timestamps();

            $table->unique(['cliente_id', 'propiedad_id']);
            $table->index('cliente_id');
            $table->index('fecha_recomendacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recomendaciones');
    }
};
