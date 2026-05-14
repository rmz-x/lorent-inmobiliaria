<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registro_actividad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('nombre')->nullable();
            $table->string('correo')->nullable();
            $table->string('rol')->nullable();
            $table->string('accion');
            $table->text('descripcion')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamp('fecha_hora')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registro_actividad');
    }
};