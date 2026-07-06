# Diagrama logico actualizado - Ciclo 5

Archivo principal:

- `diagrama_logico_vertical.mmd`

Este diagrama esta hecho en Mermaid como `flowchart TB`, no como `classDiagram`, porque Mermaid no respeta bien la verticalidad cuando hay muchas clases y relaciones. Visualmente funciona mejor para pegarlo como diseno logico actualizado.

Importante:

- Tablas tomadas en cuenta desde `docs/Database.md`: `usuarios`, `propiedades`, `categorias`, `propietarios`, `contratos`, `prospectos`, `seguimientos`, `resenas`, `solicitudes_visita`, `registro_actividad`, `notificaciones`, `recomendaciones`, `predicciones` e `historial_cliente`.
- Se omiten tablas tecnicas de Laravel como `cache`, `jobs`, `sessions`, `migrations`, `users` y `password_reset_tokens` porque no forman parte del modelo logico principal del negocio.

Para CU22 no se usa `ubicaciones`; la ubicación necesaria para el mapa se representa directamente en `propiedades` mediante `zona`, `latitud` y `longitud`.
