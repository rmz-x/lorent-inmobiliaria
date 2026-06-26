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

        let selectedVoice = null;
        if (window.speechSynthesis) {
            window.speechSynthesis.onvoiceschanged = loadVoices;
            loadVoices();
            setTimeout(loadVoices, 500);
        }

        function loadVoices() {
            const voices = window.speechSynthesis.getVoices() || [];
            selectedVoice = findFemaleSpanishVoice(voices) || findAnySpanishVoice(voices);
        }

        function findFemaleSpanishVoice(voices) {
            const normalized = (text) => (text || '').toLowerCase();
            const femaleHint = ['female', 'woman', 'mujer', 'feminine', 'maria', 'sofia', 'lucia', 'silvia', 'ines', 'laura', 'emilia', 'valentina', 'alejandra', 'carmen', 'angela', 'marina', 'ana', 'paola', 'adriana', 'helena', 'carla', 'natalia', 'isabel'];
            return voices.find(voice => {
                const name = normalized(voice.name);
                const uri = normalized(voice.voiceURI || '');
                const lang = normalized(voice.lang);
                const isSpanish = lang.startsWith('es');
                return isSpanish && femaleHint.some(hint => name.includes(hint) || uri.includes(hint));
            });
        }

        function findAnySpanishVoice(voices) {
            return voices.find(voice => (voice.lang || '').toLowerCase().startsWith('es'));
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

        function speak(text){
            if (!window.speechSynthesis) { log('SpeechSynthesis no disponible en este navegador.'); return; }
            const u = new SpeechSynthesisUtterance(text);
            if (selectedVoice) {
                u.voice = selectedVoice;
                u.lang = selectedVoice.lang || 'es-ES';
            } else {
                u.lang = 'es-ES';
            }
            u.rate = 1.15;
            u.pitch = 1.1;
            u.volume = 1.0;
            window.speechSynthesis.cancel();
            window.speechSynthesis.speak(u);
            log('Hablando: ' + text);
        }
    </script>
</body>
</html>
