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

### Funcionamiento del backend

1. El método `tendencias(Request $request)` recibe filtros opcionales `tipo` y `zona`.
2. Llama a `buildMarketTrendRows($filtroTipo, $filtroZona)` para generar las filas de predicción.
3. Recupera las zonas únicas disponibles desde `Propiedad` para el filtro del UI.
4. Calcula el resumen de segmentos y agrega totales de tendencias y solicitudes.
5. Devuelve la vista `resources/views/compartido/tendencias.blade.php` con los datos.

### Lógica de cálculo en `buildMarketTrendRows()`

- Obtiene todas las propiedades disponibles en `propiedades` con el contador `solicitudes_count`.
- Filtra por tipo y zona solo si se proporcionan en la URL.
- Agrupa las propiedades por segmento: `zona + tipo de propiedad`.
- Para cada segmento calcula:
  - `propiedades`: total de inmuebles en el segmento.
  - `solicitudes`: suma de solicitudes de visita (`solicitudes_count`).
  - `disponibles`: cantidad de propiedades con `estado = Disponible`.
  - `noDisponibles`: propiedades fuera de stock o vendidas/alquiladas.
  - `conMapa`: número de propiedades con latitud y longitud.
  - `precio_promedio`: promedio redondeado del `precio` del segmento.

### Fórmula de puntaje

- `demandaScore = min(35, solicitudes * 7)`
- `conversionScore = total > 0 ? min(25, round((noDisponibles / total) * 25)) : 0`
- `disponibilidadScore = disponibles > 0 ? 10 : 0`
- `mapaScore = total > 0 ? round((conMapa / total) * 10) : 0`
- `volumenScore = min(15, total * 3)`
- `probabilidad_venta = min(95, 15 + demandaScore + conversionScore + disponibilidadScore + mapaScore + volumenScore)`

### Clasificación de tendencia

- `Alta` si `probabilidad_venta >= 75`
- `Media` si `probabilidad_venta >= 55`
- `Estable` si no hay solicitudes (`solicitudes === 0`) y hay propiedades disponibles
- `Baja` en todos los demás casos

### Salida de cada segmento

Cada fila de predicción incluye:

- `zona`
- `tipo_propiedad`
- `propiedades`
- `disponibles`
- `solicitudes`
- `precio_promedio`
- `probabilidad_venta`
- `dias_estimados_venta` (calculado como `max(15, 125 - probabilidad)`) 
- `tendencia`

## Nota de base de datos

No se agregaron migraciones nuevas para este caso de uso. La prediccion se calcula con tablas existentes: `propiedades`, `solicitudes_visita` y `registro_actividad`. La tabla `predicciones` puede mantenerse como propuesta documental si se desea guardar historicos en una version futura.
