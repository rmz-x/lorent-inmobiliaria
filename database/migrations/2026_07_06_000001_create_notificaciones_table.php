<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('notificaciones')) {
            return;
        }

        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('propiedad_id')->nullable()->constrained('propiedades')->nullOnDelete();
            $table->string('tipo', 50);
            $table->text('mensaje');
            $table->boolean('leida')->default(false);
            $table->timestamp('fecha_envio')->useCurrent();
            $table->timestamp('created_at')->useCurrent();

            $table->index('usuario_id');
            $table->index('leida');
            $table->index('fecha_envio');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
