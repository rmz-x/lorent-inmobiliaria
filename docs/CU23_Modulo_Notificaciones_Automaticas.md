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
