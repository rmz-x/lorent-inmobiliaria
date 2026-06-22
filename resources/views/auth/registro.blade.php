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
    />

    <button
        type="button"
        class="toggle-password"
        aria-label="Mostrar contraseña"
    >
        <span class="eye-icon"></span>
    </button>

</div>

</main>

<!-- debug banner removed -->

<style>
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
