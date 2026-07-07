# CU26 - Gestionar asistente de voz con IA

## Objetivo

Permitir que el administrador o asistente consulte información del sistema mediante comandos de voz y reciba respuestas habladas sobre reportes, actividad, propiedades y tendencias.

## Actores

- Administrador
- Asistente

## Descripción

El sistema integra un asistente de voz en el módulo de reportes. El usuario activa el micrófono, realiza una consulta por voz y el frontend convierte el audio a texto con la Web Speech API.

A partir de ese texto, el servidor decide si responde localmente o si debe enviar la consulta a Gemini para generar una respuesta más natural. Luego la respuesta puede reproducirse como audio usando Amazon Polly.

## Flujo principal

1. El actor abre el módulo de reportes.
2. Presiona el botón de asistente de voz.
3. El navegador solicita permiso para usar el micrófono.
4. El actor habla y la voz se transforma a texto con Web Speech API.
5. El frontend procesa el texto.
6. Si la consulta corresponde a una pregunta común, el servidor responde localmente.
7. Si no hay coincidencia local, el backend llama a Gemini.
8. El sistema reproduce la respuesta por audio con Polly si es necesario.
9. El actor escucha la respuesta y ve el texto en pantalla.

## Detección local vs IA

### Respuestas locales

El método `localVoiceFallback()` en `app/Http/Controllers/ReporteController.php` resuelve consultas frecuentes sin llamar a Gemini. Esto hace que la experiencia sea rápida y reduce la latencia.

Consultas que ya se resuelven localmente:

- `¿Cuántas propiedades hay hoy?`
- `¿Cuántos inicios de sesión hubo?`
- `Mostrar actividad de hoy`
- `Mostrar el dashboard`
- `¿Cuál es la tendencia más fuerte?`
- `¿Qué filtros están aplicados?`
- `¿Hay propiedades nuevas?`
- `Descargar reporte en PDF/Excel/CSV`

También responde preguntas de mercado con datos de la tendencia más fuerte.

### Consulta con Gemini

Cuando la consulta no encaja con el fallback local, el backend usa `voiceQuery()` para enviar el texto a Gemini.

Este flujo es más flexible, pero también puede ser más lento porque depende de la llamada remota a la API.

## Implementación en el proyecto

### Frontend

Archivos clave:

- `resources/views/compartido/reportes.blade.php`
- `resources/views/compartido/voice.blade.php`

Elementos principales:

- `SpeechRecognition` captura audio y genera texto.
- `handleCommand()` decide si es un saludo, un comando de descarga o una pregunta para Gemini.
- `detectExportCommand()` identifica peticiones de exportación en PDF, Excel o CSV.
- `downloadReportByVoice()` abre la descarga correspondiente.
- `playPollySpeech()` envía texto a `POST /voice/polly` y reproduce el audio retornado.

### Backend

Controlador: `app/Http/Controllers/ReporteController.php`

Métodos principales:

- `voiceQuery(Request $request)`
  - Recibe `q` con la pregunta por voz.
  - Prueba `localVoiceFallback()` primero.
  - Si hay respuesta local, la devuelve con `source: local`.
  - Si no, valida la configuración de Gemini y llama a la API.
  - Devuelve texto en JSON.

- `voicePolly(Request $request)`
  - Recibe `text` para convertir a audio.
  - Usa AWS Polly para sintetizar voz.
  - Devuelve audio en Base64.

- `voiceReportProperties(Request $request)`
  - Devuelve datos simples de propiedades para la UI de voz.

- `collectVoiceContext(Request $request)`
  - Construye el contexto con totales, filtros, últimos eventos y tendencias.
  - Ese contexto alimenta a Gemini.

- `localVoiceFallback(string $query, array $context, bool $allowGeneric = true)`
  - Detecta patrones en la consulta.
  - Responde localmente a consultas de sesión, fallos, propiedades, actividad, tendencias, filtros y descargas.

- `formatVoiceContext(array $context)`
  - Convierte el contexto en texto para enviar a Gemini.

## Rutas

Las rutas relevantes de `routes/web.php` son:

- `GET /voice`
  - Página de prueba de voz.
- `GET /voice/report/propiedades`
  - Endpoint JSON usado por la UI de voz.
- `POST /voice/gemini`
  - Endpoint para procesar preguntas con Gemini.
- `POST /voice/polly`
  - Endpoint para sintetizar audio con Amazon Polly.

## Conexión a la API

### Configuración necesaria en `.env`

Para Gemini:

- `GEMINI_API_KEY`
- `GEMINI_API_URL`
- `GEMINI_MODEL`

Para Amazon Polly:

- `AWS_ACCESS_KEY_ID`
- `AWS_SECRET_ACCESS_KEY`
- `AWS_POLLY_REGION`
- `AWS_POLLY_VOICE`
- `AWS_POLLY_ENGINE`

### Ejemplo de valores

Gemini:

- `GEMINI_API_URL=https://generativelanguage.googleapis.com/v1beta`
- `GEMINI_MODEL=models/gemini-3.5-flash`

Polly:

- `AWS_POLLY_REGION=eu-west-1`
- `AWS_POLLY_VOICE=Lucia`
- `AWS_POLLY_ENGINE=neural`

### Endpoint bien configurado

`POST /voice/gemini` debe aceptar JSON correctamente y recibir el token CSRF.

La petición que hace el frontend incluye:

- `X-Requested-With: XMLHttpRequest`
- `X-CSRF-TOKEN`
- `Accept: application/json`
- `Content-Type: application/json`

Si la ruta no responde bien, el asistente de voz puede reconocer tu pregunta pero no dar respuesta.

## Importante: rendimiento y experiencia

### Qué responde rápido

- Las consultas comunes se resuelven localmente y son casi instantáneas.
- El sistema evita usar Gemini cuando no hace falta.

### Qué usa IA

- Gemini se usa solo cuando no hay respuesta local clara.
- Estas consultas pueden tardar más por la red y la respuesta del servicio.
- Por eso conviene mantener el conjunto de comandos locales ampliado.

### Recomendación

- Mantener las preguntas de datos comunes en `localVoiceFallback()`.
- Reservar Gemini para preguntas más abiertas.
- Seguir usando Polly para la voz, pero solo después de generar el texto.

## Dónde va cada parte

- Lógica de voz y AI: `app/Http/Controllers/ReporteController.php`
- Interfaz dentro del módulo de reportes: `resources/views/compartido/reportes.blade.php`
- Página de prueba de voz simple: `resources/views/compartido/voice.blade.php`
- Rutas: `routes/web.php`

## Conclusión

CU26 combina:

- reconocimiento de voz en el navegador,
- respuestas locales rápidas en el servidor,
- Gemini para consultas abiertas,
- Amazon Polly para reproducir voz.

Así se logra una experiencia que funciona rápido en lo común y sigue siendo inteligente cuando el usuario pregunta algo más complejo.
