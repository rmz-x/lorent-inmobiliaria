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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('correo')->unique();
            $table->string('usuario')->unique();
            $table->string('contrasena'); // Hash de contraseña
            $table->enum('rol', ['administrador', 'agente', 'asistente', 'cliente'])->default('cliente');
            $table->timestamps();
            $table->softDeletes(); // Soft delete para auditoría
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
