<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('historial_cliente')) {
            return;
        }

        Schema::create('historial_cliente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('propiedad_id')->constrained('propiedades')->cascadeOnDelete();
            $table->string('accion', 50);
            $table->timestamp('fecha_accion')->useCurrent();
            $table->timestamp('created_at')->useCurrent();

            $table->index('cliente_id');
            $table->index('fecha_accion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_cliente');
    }
};
