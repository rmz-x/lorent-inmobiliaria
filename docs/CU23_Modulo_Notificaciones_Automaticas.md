# CU23 - Gestionar notificaciones automáticas

## Objetivo

Permitir que el sistema genere avisos automáticos para los usuarios cuando ocurren eventos relevantes del proceso inmobiliario.

## Módulo agregado

Se agrega el módulo **Notificaciones automáticas** para todos los roles del sistema.

- Ruta: `GET /notificaciones`
- Controlador: `NotificacionController`
- Vista: `resources/views/notificaciones/index.blade.php`
- Modelo: `Notificacion`
- Tabla: `notificaciones`

## Eventos que generan notificaciones

- Registro de una nueva propiedad disponible.
- Registro de una nueva solicitud de visita.
- Cambio de estado de una solicitud de visita.
- Cancelación de una visita.
- Reagendamiento de una visita.

## Flujo principal

1. Ocurre un evento dentro del sistema.
2. El sistema identifica a los usuarios involucrados.
3. El sistema registra una notificación en la tabla `notificaciones`.
4. El usuario ingresa al panel y ve el contador de notificaciones pendientes.
5. El usuario abre el centro de notificaciones y puede marcar una o todas como leídas.

## Relación con diagramas

- Diagrama de comunicación: `docs/3.2_Diagramas_Comunicacion_Ciclo5/CU23_gestionar_notificaciones_automaticas.svg`
- Diagrama de clase análisis: `docs/3.3_Diagramas_Clase_Analisis_Ciclo5/CU23_clase_analisis_notificaciones.svg`
- Diagrama de secuencia: `docs/4.2.3_Diagrama_Secuencia_Ciclo#5/CU23-GESTIONAR NOTIFICACIONES AUTOMATICAS.png`

## Criterios de aceptación

- Las notificaciones aparecen dentro del sistema.
- Cada usuario ve únicamente sus propias notificaciones.
- Las notificaciones no leídas se muestran con contador en el menú.
- El usuario puede marcar una notificación como leída.
- El usuario puede marcar todas sus notificaciones como leídas.

## Implementación en el proyecto

- Modelo: [app/Models/Notificacion.php](app/Models/Notificacion.php)
	- Método principal: `crearPara($usuarioId, $tipo, $mensaje, $propiedadId = null)` — inserta un registro en la tabla `notificaciones`.

- Migración / estructura de la tabla: [database/migrations/2026_07_06_000001_create_notificaciones_table.php](database/migrations/2026_07_06_000001_create_notificaciones_table.php)
	- Columnas clave: `usuario_id`, `propiedad_id` (nullable), `tipo`, `mensaje`, `leida` (boolean), `fecha_envio`.

- Puntos del código donde se generan notificaciones automáticas:
	- `app/Http/Controllers/PropiedadController.php` — en los métodos `store()` y `update()` se invoca `Notificacion::crearPara(...)` para avisar a clientes y usuarios relevantes cuando se crea o cambia una propiedad.
	- `app/Http/Controllers/SolicitudController.php` — en `store()`, `actualizarEstado()`, `cambiarEstado()`, `cancelar()` y `reagendar()` se usan llamadas a `Notificacion::crearPara(...)` para notificar a agentes, asistentes o clientes sobre solicitudes y cambios de estado.

- Visualización y gestión:
	- `app/Http/Controllers/NotificacionController.php` — `index()`, `marcarLeida()`, `marcarTodas()` gestionan la vista y marcación de notificaciones.
	- `app/Http/Controllers/DashboardController.php` — carga las últimas notificaciones para mostrarlas en el dashboard.

## Cómo explicarlo en la defensa

- Di qué hace el método `crearPara`: es una función centralizada que recibe `usuario_id`, `tipo`, `mensaje` y opcionalmente `propiedad_id`, y crea la fila en la tabla `notificaciones` (ver [app/Models/Notificacion.php](app/Models/Notificacion.php)).
- Menciona los eventos que lo disparan: creación/actualización de propiedades y acciones sobre solicitudes de visita. Señala los controladores exactos donde ocurre (`PropiedadController` y `SolicitudController`).
- Muestra la migración para evidenciar la estructura de la tabla y los campos relevantes (ver [database/migrations/2026_07_06_000001_create_notificaciones_table.php](database/migrations/2026_07_06_000001_create_notificaciones_table.php)).
- Explica el flujo: evento → llamada a `Notificacion::crearPara` → registro en BD → lectura por `NotificacionController` y/o dashboard → marcado como leído.

Si quieres, a continuación se incluyen fragmentos de código listos para mostrar en la defensa.

### Ejemplos de código (backend)

Modelo: [app/Models/Notificacion.php](app/Models/Notificacion.php)

```php
public static function crearPara(?int $usuarioId, string $tipo, string $mensaje, ?int $propiedadId = null): void
{
	if (!$usuarioId) {
		return;
	}

	self::create([
		'usuario_id' => $usuarioId,
		'propiedad_id' => $propiedadId,
		'tipo' => $tipo,
		'mensaje' => $mensaje,
	]);
}
```

Ejemplo de llamada en `app/Http/Controllers/PropiedadController.php` (al crear una propiedad):

```php
$this->usuariosActivosPorRol(['cliente'])
	->pluck('id')
	->each(fn ($clienteId) => Notificacion::crearPara(
		$clienteId,
		'nueva_propiedad',
		"Nueva propiedad disponible: {$prop->titulo} en {$prop->zona}.",
		$prop->id
	));
```

Ejemplo de llamadas en `app/Http/Controllers/PropiedadController.php` (al actualizar estado):

```php
$destinatarios = $this->usuariosActivosPorRol(['cliente', 'administrador', 'asistente'])
	->pluck('id')
	->push($propiedad->agente_id)
	->filter()
	->unique();

foreach ($destinatarios as $destinatarioId) {
	Notificacion::crearPara(
		(int) $destinatarioId,
		$tipoNotificacion,
		$mensaje,
		$propiedad->id
	);
}
```

Estos fragmentos evidencian el punto exacto del backend que dispara las notificaciones.
