public function up(): void
{
    Schema::table('propiedades', function (Blueprint $table) {
        $table->decimal('latitud', 10, 8)->nullable();
        $table->decimal('longitud', 11, 8)->nullable();
    });
}

public function down(): void
{
    Schema::table('propiedades', function (Blueprint $table) {
        $table->dropColumn(['latitud', 'longitud']);
    });
}