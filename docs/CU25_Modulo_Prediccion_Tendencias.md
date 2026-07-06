# CU25 - Predecir tendencias del mercado inmobiliario

## Objetivo

Permitir que el administrador o asistente analice el comportamiento del mercado inmobiliario mediante una prediccion basada en los datos registrados en el sistema.

## Actores

- Administrador
- Asistente

## Descripcion

El sistema calcula tendencias agrupando propiedades por zona y tipo de operacion. Para cada segmento se analiza la cantidad de propiedades, disponibilidad, solicitudes de visita, precio promedio y ubicacion registrada en el mapa. Con esos datos se genera una probabilidad estimada de venta o alquiler, dias aproximados de cierre y una clasificacion de tendencia.

## Flujo principal

1. El actor ingresa al modulo de reportes.
2. Selecciona la opcion `Ver tendencias`.
3. El sistema obtiene las propiedades registradas y sus solicitudes de visita.
4. El sistema agrupa la informacion por zona y tipo de propiedad.
5. El sistema calcula la probabilidad de venta, dias estimados y tendencia.
6. El actor visualiza los resultados en tarjetas y tabla.
7. El actor puede filtrar por tipo de propiedad o zona.

## Criterios de calculo

- Demanda: cantidad de solicitudes de visita asociadas al segmento.
- Conversion: propiedades que ya no se encuentran disponibles.
- Disponibilidad: existencia de propiedades activas para ofertar.
- Ubicacion: propiedades con latitud y longitud registradas.
- Volumen: cantidad total de propiedades en el segmento.

## Resultado esperado

El sistema muestra una tabla con zona, tipo de propiedad, cantidad de propiedades, solicitudes, precio promedio, probabilidad estimada, dias estimados y tendencia: Alta, Media, Baja o Estable.

## Implementacion en el proyecto

- Controlador: `app/Http/Controllers/ReporteController.php`
- Metodo principal: `tendencias()`
- Motor de calculo: `buildMarketTrendRows()`
- Vista: `resources/views/compartido/tendencias.blade.php`
- Rutas:
  - `GET /admin/reportes/tendencias`
  - `GET /asistente/reportes/tendencias`

## Nota de base de datos

No se agregaron migraciones nuevas para este caso de uso. La prediccion se calcula con tablas existentes: `propiedades`, `solicitudes_visita` y `registro_actividad`. La tabla `predicciones` puede mantenerse como propuesta documental si se desea guardar historicos en una version futura.
