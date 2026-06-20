<!DOCTYPE html>
...
<div class="pw-wrapper" style="position:relative; display:block; width:100%; max-width:420px;">
    <input
        id="registerPassword"
        name="contrasena"
        type="password"
        class="pw-input"
        data-pw-enable="1"
        placeholder="Contraseña"
        required
        autocomplete="new-password"
        aria-describedby="pw-tooltip"
    />

    <button
        type="button"
        class="toggle-password"
        aria-label="Mostrar contraseña"
    >
        <span class="eye-icon"></span>
    </button>

    <div id="pw-tooltip" class="pw-tooltip" role="status" aria-live="polite" style="display:none">
        <div class="pw-tooltip-inner">
                    <ul class="pw-list">
                        <li data-rule="length" class="pw-item"><span class="pw-icon" aria-hidden="true"></span><span class="pw-text">Mínimo 8 caracteres</span></li>
                        <li data-rule="upper" class="pw-item"><span class="pw-icon" aria-hidden="true"></span><span class="pw-text">Al menos una letra mayúscula (A-Z)</span></li>
                        <li data-rule="number" class="pw-item"><span class="pw-icon" aria-hidden="true"></span><span class="pw-text">Al menos un número (0-9)</span></li>
                        <li data-rule="special" class="pw-item"><span class="pw-icon" aria-hidden="true"></span><span class="pw-text">Al menos un carácter especial (# ? ! @)</span></li>
                    </ul>
        </div>
    </div>
</div>

</main>

<!-- debug banner removed -->

<style>
.pw-tooltip {
    position: absolute;
    left: 50%;
    transform: translateX(-50%) translateY(-8px);
    bottom: calc(100% + 8px);
    min-width: 260px;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    box-shadow: 0 6px 16px rgba(0,0,0,0.08);
    border-radius: 8px;
    padding: 8px;
    z-index: 1200;
    font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
    font-size: 13px;
    color: #111827;
}
.pw-tooltip::after{
    content: "";
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    bottom: -6px;
    border-width: 6px 6px 0 6px;
    border-style: solid;
    border-color: #ffffff transparent transparent transparent;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.06));
}
.pw-tooltip.pw-fixed { position: fixed; transform: none; }
.pw-tooltip.pw-fixed::after{ bottom: auto; }
.pw-tooltip.pw-below.pw-fixed::after{ top: -6px; bottom: auto; transform: translateX(-50%); border-width: 0 6px 6px 6px; border-color: transparent transparent #ffffff transparent; }
.pw-tooltip.pw-fixed:not(.pw-below)::after{ top: 100%; bottom: auto; transform: translateX(-50%); border-width: 6px 6px 0 6px; border-color: #ffffff transparent transparent transparent; }
.pw-tooltip-inner { padding: 6px 10px; }
.pw-title { font-weight:600; margin-bottom:6px; font-size:13px; color:#0f172a; }
.pw-list { list-style:none; margin:0; padding:0; }
.pw-item { display:flex; align-items:center; gap:8px; color:#6b7280; margin:6px 0; }
.pw-icon { width:18px; display:inline-flex; justify-content:center; align-items:center; color:#6b7280; }
.pw-item.satisfied { color:#166534; }
.pw-item.satisfied .pw-icon { color:#16a34a; }
.pw-input { width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:6px; font-size:14px; }
.pw-input:focus { outline: none; border-color:#60a5fa; box-shadow:0 0 0 3px rgba(96,165,250,0.12); }
@media (max-width:420px){
    .pw-tooltip{ left: 8px; transform: none; right: 8px; bottom: calc(100% + 8px); }
    .pw-tooltip::after{ left: 24px; transform: none; }
}

/* Helper box under password for reliable, always-visible feedback */
.pw-helper {
    background: #f8fafc; /* soft tint */
    border: 1px solid #e6e9ee;
    border-radius: 12px;
    padding: 6px 8px;
    margin-top: 4px;
    font-size: 13px;
    color: #374151;
    max-width:420px;
    box-shadow: 0 1px 2px rgba(16,24,40,0.03);
}
.pw-helper.pw-hidden{ opacity:0; max-height:0; overflow:hidden; transform:translateY(-6px); transition:opacity .16s ease, transform .16s ease, max-height .16s ease }
.pw-helper.pw-visible{ opacity:1; max-height:400px; transform:translateY(0); transition:opacity .16s ease, transform .16s ease, max-height .2s ease }
.pw-helper .pw-list { margin:0; padding:0; list-style:none; font-size:13px }
.pw-helper .pw-item { display:flex; gap:8px; align-items:center; color:#6b7280; margin:3px 0; font-size:13px; padding:4px 6px; border-radius:8px }
.pw-helper .pw-icon { width:18px; display:inline-flex; justify-content:center; align-items:center; color:#ef4444; font-weight:700 }
.pw-helper .pw-item.satisfied { color:#16A34A; /* bright green */ }
.pw-helper .pw-item.satisfied .pw-icon { color:#16A34A }
.pw-helper .pw-item, .pw-helper .pw-icon { transition: color .12s ease, background .12s ease }
/* SVG icon sizing and satisfied highlight */
.pw-icon svg { width:18px; height:18px; display:block }
/* remove background on satisfied items to avoid layout shift; color handled by .pw-helper .pw-item.satisfied */
.pw-item.satisfied .pw-text { color:#16A34A; font-weight:600 }
</style>

<script src="{{ asset('js/compartido/pw-tooltip.js') }}"></script>
<script src="{{ asset('js/compartido/pw-helper.js') }}"></script>


<script>
    // Si hay errores de validación, activar automáticamente el formulario de registro
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            const registerPanel = document.getElementById('register-panel');
            const loginPanel = document.getElementById('login-panel');
            const registerTab = document.querySelector('.tab:nth-child(2)');
            const loginTab = document.querySelector('.tab:nth-child(1)');
            
            if (registerPanel) {
                registerPanel.classList.add('active');
            }
            if (loginPanel) {
                loginPanel.classList.remove('active');
            }
            if (registerTab) {
                registerTab.classList.add('active');
            }
            if (loginTab) {
                loginTab.classList.remove('active');
            }
        });
    @endif
</script>

<script src="{{ asset('js/scrip.js') }}"></script>
<script src="{{ asset('js/compartido/password-toggle.js') }}"></script>
<script src="{{ asset('js/auth/login.js') }}"></script>
