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
