<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('recomendaciones')) {
            return;
        }

        Schema::table('recomendaciones', function (Blueprint $table) {
            $table->string('feedback', 20)->nullable()->after('puntuacion_recomendacion')->comment('like|dislike|null');
            $table->index('feedback');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('recomendaciones')) {
            return;
        }

        Schema::table('recomendaciones', function (Blueprint $table) {
            $table->dropIndex(['feedback']);
            $table->dropColumn('feedback');
        });
    }
};
