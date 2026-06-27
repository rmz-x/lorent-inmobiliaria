<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Prueba voz - Reportes</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        button { padding: 10px 16px; font-size: 16px; }
        #log { margin-top: 12px; white-space: pre-wrap; }
    </style>
</head>
<body>
    <h2>Prueba por voz — Reporte de propiedades</h2>
    <p>Di: <strong>"reporte de propiedades registradas"</strong> (o frase similar) y el sistema te responderá por voz.</p>

    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-bottom:12px;">
        <button id="startBtn">Iniciar escucha</button>
        <button id="stopBtn" disabled>Detener</button>
    </div>

    <div id="log"></div>

    <script>
        const logEl = document.getElementById('log');
        function log(msg){ logEl.textContent += msg + '\n'; }

        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) {
            log('Tu navegador no soporta Web Speech API (SpeechRecognition). Prueba Chrome o Edge.');
        }

        let recognition;
        if (SpeechRecognition) {
            recognition = new SpeechRecognition();
            recognition.lang = 'es-ES';
            recognition.interimResults = false;
            recognition.maxAlternatives = 1;

            recognition.onstart = () => { log('Escuchando...'); document.getElementById('startBtn').disabled = true; document.getElementById('stopBtn').disabled = false; };
            recognition.onend = () => { log('Detenido.'); document.getElementById('startBtn').disabled = false; document.getElementById('stopBtn').disabled = true; };
            recognition.onerror = (e) => { log('Error reconocimiento: ' + e.error); };

            recognition.onresult = (event) => {
                const text = event.results[0][0].transcript;
                log('Transcripción: "' + text + '"');
                handleCommand(text.toLowerCase());
            };
        }

        const textQueryInput = document.getElementById('voice-text-query');
        const sendTextBtn = document.getElementById('sendTextBtn');

        document.getElementById('startBtn').addEventListener('click', ()=>{ if(recognition) recognition.start(); });
        document.getElementById('stopBtn').addEventListener('click', ()=>{ if(recognition) recognition.stop(); });
        sendTextBtn.addEventListener('click', ()=>{ const q = textQueryInput.value.trim(); if(q){ log('Consulta escrita: "' + q + '"'); fetchVoiceQuery(q); }});
        textQueryInput.addEventListener('keydown', (e)=>{ if(e.key === 'Enter'){ e.preventDefault(); sendTextBtn.click(); }});

        function handleCommand(text){
            fetchVoiceQuery(text);
        }

        function getCsrfToken() {
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            if (tokenMeta) {
                return tokenMeta.getAttribute('content');
            }
            const match = document.cookie.match(/(^|;)\s*XSRF-TOKEN=([^;]+)/);
            return match ? decodeURIComponent(match[2]) : null;
        }

        async function fetchVoiceQuery(q){
            log('Consultando Gemini...');
            try {
                const token = getCsrfToken();
                if (!token) {
                    log('No se encontró token CSRF en la página.');
                    speak('Error interno: faltó token CSRF.');
                    return;
                }

                const res = await fetch('/voice/gemini', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ q }),
                    credentials: 'same-origin'
                });

                const contentType = res.headers.get('content-type') || '';
                const textBody = await res.text();
                let data = null;
                if (contentType.includes('application/json')) {
                    try { data = JSON.parse(textBody); } catch (_e) { data = null; }
                }

                if (!res.ok) {
                    log('Error HTTP ' + res.status + ': ' + textBody);
                    speak('Error del servidor: ' + res.status + '. Revisa la consola.');
                    return;
                }

                log('Respuesta Gemini: ' + textBody);
                const reply = data && (data.speech || data.text || data.reply || '');
                if (reply) {
                    speak(reply);
                } else {
                    const errorMsg = data && data.error ? data.error : 'No recibí respuesta válida del servidor.';
                    log('Error de Gemini: ' + errorMsg);
                    speak('Error al obtener respuesta de Gemini.');
                }
            } catch (e) {
                console.error(e);
                log('Error: ' + e.message);
                speak('Error al consultar el servidor.');
            }
        }

        function getCsrfToken() {
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            if (tokenMeta) {
                return tokenMeta.getAttribute('content');
            }
            const match = document.cookie.match(/(^|;)\s*XSRF-TOKEN=([^;]+)/);
            return match ? decodeURIComponent(match[2]) : null;
        }

        async function playPollySpeech(text) {
            if (!text) return;
            log('Generando voz Polly...');
            const token = getCsrfToken();
            if (!token) {
                log('No CSRF token available for Polly');
                return;
            }

            try {
                const res = await fetch('/voice/polly', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ text }),
                    credentials: 'same-origin'
                });

                const data = await res.json();
                if (!res.ok || !data.audio) {
                    log('Error Polly: ' + (data.error || res.status));
                    return;
                }

                const audio = new Audio('data:audio/mpeg;base64,' + data.audio);
                audio.onended = () => { log('Polly finalizó.'); };
                audio.onerror = (err) => { log('Error reproducción Polly: ' + err.message); };
                await audio.play();
                log('Reproduciendo Polly.');
            } catch (e) {
                log('Polly request failed: ' + e.message);
            }
        }

        function buildSpeechFromReport(data){
            const total = data.totalProps ?? 0;
            const hoy = data.totalHoy ?? 0;
            let text = `Hay ${total} propiedades registradas.`;
            if (hoy > 0) text += ` Hoy se registraron ${hoy} propiedades.`;
            if (Array.isArray(data.latest) && data.latest.length > 0) {
                text += ' Últimas propiedades registradas: ';
                const items = data.latest.map((r, idx) => {
                    // intentar extraer info breve de la descripción
                    const desc = r.descripcion ? r.descripcion.replace(/\s+/g,' ').slice(0,80) : '';
                    return `${idx+1}: ${desc}`;
                });
                text += items.join('; ');
            }
            return text;
        }

        async function speak(text){
            await playPollySpeech(text);
        }
    </script>
</body>
</html>
