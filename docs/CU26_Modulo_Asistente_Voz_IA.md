# CU26 - Gestionar asistente de voz con IA

## Objetivo

Permitir que el administrador o asistente consulte informacion del sistema mediante comandos de voz y reciba respuestas habladas sobre reportes, actividad, propiedades y tendencias.

## Actores

- Administrador
- Asistente

## Descripcion

El sistema incorpora un asistente de voz dentro del modulo de reportes. El usuario activa el microfono, realiza una consulta y el sistema procesa el texto reconocido. La respuesta puede generarse con datos locales o con Gemini, y luego reproducirse como audio mediante Amazon Polly.

## Flujo principal

1. El actor ingresa al modulo de reportes.
2. Presiona el boton `Asistente IA`.
3. El sistema solicita o usa el permiso de microfono del navegador.
4. El actor realiza una pregunta por voz.
5. El sistema convierte la voz a texto desde el navegador.
6. El sistema consulta los datos internos del reporte.
7. Si corresponde, envia el contexto a Gemini para generar una respuesta natural.
8. El sistema convierte la respuesta a audio mediante Amazon Polly.
9. El actor escucha la respuesta desde la interfaz.

## Comandos soportados

- Consultar propiedades registradas.
- Consultar inicios de sesion e intentos fallidos.
- Consultar actividad del dia.
- Consultar tendencias del mercado.
- Descargar reportes por voz.

## Mejora agregada: descarga de reportes por voz

El asistente reconoce comandos como:

- `Descargar reporte en PDF`.
- `Exportar reporte en Excel`.
- `Generar reporte CSV`.

Cuando detecta el comando, abre la descarga correspondiente usando los filtros actuales del reporte.

## Implementacion en el proyecto

- Controlador: `app/Http/Controllers/ReporteController.php`
- Metodos:
  - `voiceQuery()`
  - `voicePolly()`
  - `voiceReportProperties()`
  - `collectVoiceContext()`
  - `localVoiceFallback()`
- Vista principal: `resources/views/compartido/reportes.blade.php`
- Rutas:
  - `POST /voice/gemini`
  - `POST /voice/polly`
  - `GET /voice/report/propiedades`
  - `GET /admin/reportes/export/{type}`
  - `GET /asistente/reportes/export/{type}`

## Servicios externos

- Gemini API: genera respuestas naturales con contexto del sistema.
- Amazon Polly: convierte las respuestas a audio.
- Web Speech API del navegador: reconoce la voz del usuario.

## Requisitos

- Navegador compatible con reconocimiento de voz, preferentemente Chrome o Edge.
- Variables `.env` configuradas para Gemini y Amazon Polly si se desea usar IA y voz sintetizada.
- El sistema mantiene respuesta local para consultas basicas, incluso si Gemini no responde.
