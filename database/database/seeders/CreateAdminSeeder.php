<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario administrador por defecto
        Usuario::create([
            'nombre'     => 'Administrador',
            'correo'     => 'admin@lorent.com',
            'usuario'    => 'admin',
            'contrasena' => Hash::make('admin123'), // Cambiar esto en producción
            'rol'        => 'administrador',
        ]);

        // Crear usuario agente de prueba
        Usuario::create([
            'nombre'     => 'Agente Ejemplo',
            'correo'     => 'agente@lorent.com',
            'usuario'    => 'agente',
            'contrasena' => Hash::make('agente123'),
            'rol'        => 'agente',
        ]);

        // Crear usuario cliente de prueba
        Usuario::create([
            'nombre'     => 'Cliente Ejemplo',
            'correo'     => 'cliente@lorent.com',
            'usuario'    => 'cliente',
            'contrasena' => Hash::make('cliente123'),
            'rol'        => 'cliente',
        ]);

        echo "✅ Usuarios de prueba creados correctamente.\n";
    }
}
