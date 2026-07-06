<?php

namespace Tests\Feature;

use App\Models\Propiedad;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PropiedadNotificacionTest extends TestCase
{
    use RefreshDatabase;

    public function test_cuando_se_vende_una_propiedad_se_crea_notificacion_para_clientes(): void
    {
        $agente = Usuario::create([
            'nombre' => 'Agente test',
            'correo' => 'agente@test.com',
            'usuario' => 'agente_test',
            'contrasena' => Hash::make('password'),
            'rol' => 'agente',
            'estado' => 'activo',
        ]);

        $cliente = Usuario::create([
            'nombre' => 'Cliente test',
            'correo' => 'cliente@test.com',
            'usuario' => 'cliente_test',
            'contrasena' => Hash::make('password'),
            'rol' => 'cliente',
            'estado' => 'activo',
        ]);

        $propiedad = Propiedad::create([
            'titulo' => 'Casa test',
            'tipo' => 'Venta',
            'zona' => 'Centro',
            'precio' => 150000,
            'area' => 120,
            'descripcion' => 'Propiedad de prueba',
            'estado' => 'Disponible',
            'agente_id' => $agente->id,
        ]);

        $this->actingAs($agente);

        $response = $this->put('/agente/propiedades/' . $propiedad->id, [
            'titulo' => 'Casa nueva',
            'tipo' => 'Venta',
            'zona' => 'Centro',
            'precio' => 150000,
            'area' => 120,
            'descripcion' => 'Hermosa propiedad actualizada',
            'estado' => 'Vendido',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('notificaciones', [
            'usuario_id' => $cliente->id,
            'propiedad_id' => $propiedad->id,
            'tipo' => 'cambio_estado',
        ]);
    }
}
