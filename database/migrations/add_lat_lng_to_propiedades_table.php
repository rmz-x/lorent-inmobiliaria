<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('propiedades', 'latitud')) {
            Schema::table('propiedades', function (Blueprint $table) {
                $table->decimal('latitud', 10, 8)->nullable();
            });
        }

        if (!Schema::hasColumn('propiedades', 'longitud')) {
            Schema::table('propiedades', function (Blueprint $table) {
                $table->decimal('longitud', 11, 8)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('propiedades', 'latitud')) {
            Schema::table('propiedades', function (Blueprint $table) {
                $table->dropColumn('latitud');
            });
        }

        if (Schema::hasColumn('propiedades', 'longitud')) {
            Schema::table('propiedades', function (Blueprint $table) {
                $table->dropColumn('longitud');
            });
        }
    }
};