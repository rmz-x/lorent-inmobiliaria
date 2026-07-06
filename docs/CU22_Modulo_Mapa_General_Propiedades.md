# CU22 - Visualizar mapa general de propiedades

## Objetivo

Permitir que el cliente visualice en un mapa general todas las propiedades disponibles que tienen coordenadas registradas, facilitando la exploración geográfica de inmuebles antes de revisar el detalle.

## Módulo agregado

Se agrega el módulo **Mapa general de propiedades** dentro del menú del cliente.

- Ruta: `GET /cliente/mapa-general`
- Nombre de ruta: `cliente.mapa`
- Controlador: `PropiedadController@mapaGeneral`
- Vista: `resources/views/cliente/mapa-general.blade.php`
- Datos usados: tabla `propiedades`

## Criterio importante de base de datos

Para este caso de uso **no se usa la tabla `ubicaciones`**, porque el sistema ya registra la ubicación necesaria directamente en `propiedades` mediante:

- `zona`
- `latitud`
- `longitud`

Esto coincide con el formulario actual de registro/edición de propiedades, donde el usuario selecciona la ubicación en el mapa y se guardan las coordenadas en la propiedad.

## Flujo principal

1. El cliente ingresa al menú **Mapa general**.
2. El sistema consulta las propiedades disponibles con `latitud` y `longitud`.
3. El sistema muestra un mapa con marcadores por cada propiedad encontrada.
4. El cliente puede filtrar por tipo de propiedad: venta, alquiler o anticrético.
5. El cliente selecciona un marcador y puede abrir el detalle de la propiedad.

## Relación con diagramas

- Diagrama de comunicación: `docs/3.2_Diagramas_Comunicacion_Ciclo5/CU22_visualizar_mapa_general_propiedades.svg`
- Diagrama de clase análisis: `docs/3.3_Diagramas_Clase_Analisis_Ciclo5/CU22_clase_analisis_visualizar_mapa.svg`
- Diagrama de secuencia: `docs/4.2.3_Diagrama_Secuencia_Ciclo#5/CU22-VISUALIZAR MAPA GENERAL DE PROPIEDADES.png`

## Validación funcional

El módulo debe mostrar únicamente propiedades que cumplan:

- `estado = Disponible`
- `latitud IS NOT NULL`
- `longitud IS NOT NULL`

Si no existen propiedades con coordenadas, se muestra un mensaje informativo.
