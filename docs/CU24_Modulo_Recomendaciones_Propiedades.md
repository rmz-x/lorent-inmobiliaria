# CU24 - Recomendar propiedades

## Objetivo

Sugerir propiedades disponibles al cliente de acuerdo con su comportamiento dentro del sistema, como visitas realizadas, solicitudes y propiedades consultadas.

## Módulo agregado

Se agrega el módulo **Recomendaciones** dentro del menú del cliente.

- Ruta: `GET /cliente/recomendaciones`
- Controlador: `RecomendacionController@index`
- Vista: `resources/views/cliente/recomendaciones.blade.php`
- Modelos: `Recomendacion`, `HistorialCliente`, `Propiedad`
- Tablas: `recomendaciones`, `historial_cliente`, `propiedades`

## Backend y lógica actual

La lógica de recomendaciones se ejecuta en `app/Http/Controllers/RecomendacionController.php`.

- `index()` obtiene el cliente autenticado y reconstruye sus preferencias desde el historial.
- Si no hay historial suficiente, entrega propiedades disponibles ordenadas por popularidad (`solicitudes_count`).
- Si hay historial, calcula un puntaje para cada propiedad usando `calcularPuntaje()`.
- El puntaje se guarda o actualiza en la tabla `recomendaciones` y luego se ordenan las propiedades por `puntaje_recomendacion`.
- La vista muestra las 8 propiedades con mayor puntaje.

### Reglas de puntaje actuales

- Base inicial: `45`
- Coincidencia de tipo: `+25`
- Coincidencia de zona: `+20`
- Propiedad con coordenadas: `+10`
- Propiedad con imagen: `+10`
- Precio cercano al promedio del cliente:
  - Si la diferencia es <= 10%: `+10`
  - Si la diferencia es <= 25%: `+5`
- Popularidad: `+4` por solicitud, hasta `+12`
- Feedback del cliente:
  - `like`: `+20`
  - `dislike`: `-40`

El resultado final se limita entre `0` y `100`.

## Criterio de recomendación

El sistema calcula un puntaje de afinidad usando reglas simples:

- Coincidencia con tipos de propiedad consultados.
- Coincidencia con zonas consultadas.
- Propiedades con coordenadas registradas.
- Propiedades con imagen disponible.

## Flujo principal

1. El cliente consulta propiedades o solicita visitas.
2. El sistema registra acciones en `historial_cliente`.
3. El cliente ingresa a **Recomendaciones**.
4. El sistema analiza preferencias recientes.
5. El sistema muestra propiedades disponibles ordenadas por puntaje de afinidad.

## Relación con diagramas

- Diagrama de comunicación: `docs/3.2_Diagramas_Comunicacion_Ciclo5/CU24_recomendar_propiedades_ia.svg`
- Diagrama de clase análisis: `docs/3.3_Diagramas_Clase_Analisis_Ciclo5/CU24_clase_analisis_recomendar_propiedades.svg`
- Diagrama de secuencia: `docs/4.2.3_Diagrama_Secuencia_Ciclo#5/CU24-RECOMENDAR PROPIEDADES (IA).png`

## Criterios de aceptación

- El cliente puede ingresar al módulo de recomendaciones.
- El sistema muestra propiedades disponibles ordenadas por afinidad.
- El sistema usa datos reales de `propiedades`.
- El sistema registra historial al ver detalles y solicitar visitas.
- Las recomendaciones pueden actualizarse automáticamente al cambiar el comportamiento del cliente.
