/**
 * muestra un mensaje de error debajo del campo
 * @param {HTMLElement} campo
 * @param {string}      mensaje
 */
function mostrarError(campo, mensaje) {
    limpiarError(campo); // evitar mensajes duplicados

    campo.style.border = "1.5px solid #e53935";
    campo.style.background = "#fff8f8";

    const span = document.createElement("span");
    span.className   = "error-msg";
    span.textContent = mensaje;
    span.style.cssText = `
        color: #e53935;
        font-size: 11px;
        margin-top: 4px;
        display: block;
        font-family: Roboto, sans-serif;
    `;
    campo.parentNode.appendChild(span);
}

/* limpia el error visual de un campo */
function limpiarError(campo) {
    campo.style.border    = "";
    campo.style.background = "";
    const viejo = campo.parentNode.querySelector(".error-msg");
    if (viejo) viejo.remove();
}

/* limpia todos los errores de un formulario */
function limpiarTodosLosErrores(form) {
    form.querySelectorAll(".error-msg").forEach(e => e.remove());
    form.querySelectorAll("input, select, textarea").forEach(c => {
        c.style.border     = "";
        c.style.background = "";
    });
}

/* verifica si un correo tiene formato válido */
function esCorreoValido(correo) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo);
}

/**
 * Valida que una contraseña cumpla con los criterios de seguridad
 * @param {string} contrasena La contraseña a validar
 * @returns {object} { valida: boolean, errores: array }
 */
function validarFormatoContrasena(contrasena) {
    const errores = [];
    
    // Verificar longitud mínima
    if (contrasena.length < 8) {
        errores.push("La contraseña debe tener al menos 8 caracteres.");
    }
    
    // Verificar que contenga al menos una letra mayúscula
    if (!/[A-Z]/.test(contrasena)) {
        errores.push("La contraseña debe contener al menos una letra mayúscula.");
    }
    
    // Verificar que contenga al menos una letra minúscula
    if (!/[a-z]/.test(contrasena)) {
        errores.push("La contraseña debe contener al menos una letra minúscula.");
    }
    
    // Verificar que contenga al menos un número
    if (!/[0-9]/.test(contrasena)) {
        errores.push("La contraseña debe contener al menos un número.");
    }
    
    return {
        valida: errores.length === 0,
        errores: errores
    };
}

/**
 * Configura el toggle de mostrar/ocultar contraseña para todos los campos
 */
function inicializarTogglePassword() {
    document.querySelectorAll('.password-toggle-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const input = this.previousElementSibling;
            
            if (input && (input.type === 'password' || input.type === 'text')) {
                // Alternar tipo de input
                input.type = input.type === 'password' ? 'text' : 'password';
                
                // Cambiar el texto del botón
                this.textContent = input.type === 'password' ? '👁️' : '🙈';
                this.title = input.type === 'password' ? 'Mostrar contraseña' : 'Ocultar contraseña';
            }
        });
    });
}

// Función para validar el formulario de login
function validarLogin(e) {
    const form    = document.querySelector(".formulario_login");
    const correo  = form.querySelector('input[name="correo"]');
    const pass    = form.querySelector('input[name="contrasena"]');
    let   esValido = true;
 
    limpiarTodosLosErrores(form);
 
    if (correo.value.trim() === "") {
        mostrarError(correo, "El correo electrónico es obligatorio.");
        esValido = false;
    } else if (!esCorreoValido(correo.value.trim())) {
        mostrarError(correo, "Ingresa un correo con formato válido (ej: nombre@correo.com).");
        esValido = false;
    }
 
    if (pass.value === "") {
        mostrarError(pass, "La contraseña es obligatoria.");
        esValido = false;
    } else if (pass.value.length < 6) {
        mostrarError(pass, "La contraseña debe tener al menos 6 caracteres.");
        esValido = false;
    }
 
    if (!esValido) e.preventDefault();
}
 
function validarRegistro(e) {
    const form    = document.querySelector(".formulario_register");
    const nombre  = form.querySelector('input[name="nombre"]');
    const correo  = form.querySelector('input[name="correo"]');
    const usuario = form.querySelector('input[name="usuario"]');
    const pass    = form.querySelector('input[name="contrasena"]');
    let   esValido = true;
 
    limpiarTodosLosErrores(form);
 
    if (nombre.value.trim() === "") {
        mostrarError(nombre, "El nombre completo es obligatorio.");
        esValido = false;
    } else if (nombre.value.trim().length < 3) {
        mostrarError(nombre, "El nombre debe tener al menos 3 caracteres.");
        esValido = false;
    }
 
    if (correo.value.trim() === "") {
        mostrarError(correo, "El correo electrónico es obligatorio.");
        esValido = false;
    } else if (!esCorreoValido(correo.value.trim())) {
        mostrarError(correo, "Ingresa un correo con formato válido (ej: nombre@correo.com).");
        esValido = false;
    }
 
    if (usuario.value.trim() === "") {
        mostrarError(usuario, "El nombre de usuario es obligatorio.");
        esValido = false;
    } else if (usuario.value.trim().length < 3) {
        mostrarError(usuario, "El usuario debe tener al menos 3 caracteres.");
        esValido = false;
    } else if (/\s/.test(usuario.value)) {
        mostrarError(usuario, "El usuario no puede contener espacios.");
        esValido = false;
    }
 
    if (pass.value === "") {
        mostrarError(pass, "La contraseña es obligatoria.");
        esValido = false;
    } else {
        const validacion = validarFormatoContrasena(pass.value);
        if (!validacion.valida) {
            // Mostrar el primer error
            mostrarError(pass, validacion.errores[0]);
            esValido = false;
        }
    }
 
    if (!esValido) e.preventDefault();
}
 
function validarPropiedad(e) {
    const form        = document.getElementById("formPropiedad");
    const titulo      = document.getElementById("propTitulo");
    const tipo        = document.getElementById("propTipo");
    const zona        = document.getElementById("propZona");
    const precio      = document.getElementById("propPrecio");
    const area        = document.getElementById("propArea");
    const descripcion = document.getElementById("propDescripcion");
    const estado      = document.getElementById("propEstado");
    let   esValido    = true;

    limpiarTodosLosErrores(form);

    if (titulo.value.trim() === "") {
        mostrarError(titulo, "El título de la propiedad es obligatorio.");
        esValido = false;
    } else if (titulo.value.trim().length < 5) {
        mostrarError(titulo, "El título debe tener al menos 5 caracteres.");
        esValido = false;
    }

    if (tipo.value === "") {
        mostrarError(tipo, "Selecciona un tipo de propiedad.");
        esValido = false;
    }

    if (zona.value.trim() === "") {
        mostrarError(zona, "La zona o ubicación es obligatoria.");
        esValido = false;
    }

    if (precio.value === "" || precio.value === null) {
        mostrarError(precio, "El precio es obligatorio.");
        esValido = false;
    } else if (parseFloat(precio.value) <= 0) {
        mostrarError(precio, "El precio debe ser mayor a cero.");
        esValido = false;
    }

    if (area.value === "" || area.value === null) {
        mostrarError(area, "El área es obligatoria.");
        esValido = false;
    } else if (parseFloat(area.value) <= 0) {
        mostrarError(area, "El área debe ser mayor a cero.");
        esValido = false;
    }

    if (descripcion.value.trim() === "") {
        mostrarError(descripcion, "La descripción es obligatoria.");
        esValido = false;
    } else if (descripcion.value.trim().length < 10) {
        mostrarError(descripcion, "La descripción debe tener al menos 10 caracteres.");
        esValido = false;
    }

    if (estado.value === "") {
        mostrarError(estado, "Selecciona el estado de la propiedad.");
        esValido = false;
    }

    if (!esValido) e.preventDefault();
}
 
function validarUsuario(e) {
    const form     = document.getElementById("formUsuario");
    const nombre   = document.getElementById("userName");
    const correo   = document.getElementById("userCorreo");
    const usuario  = document.getElementById("userUsuario");
    const pass     = document.getElementById("userPass");
    const rol      = document.getElementById("userRol");
    const esEditar = document.getElementById("userAccion").value === "editar";
    let   esValido = true;
 
    limpiarTodosLosErrores(form);
 
    if (nombre.value.trim() === "") {
        mostrarError(nombre, "El nombre completo es obligatorio.");
        esValido = false;
    } else if (nombre.value.trim().length < 3) {
        mostrarError(nombre, "El nombre debe tener al menos 3 caracteres.");
        esValido = false;
    }
 
    if (correo.value.trim() === "") {
        mostrarError(correo, "El correo electrónico es obligatorio.");
        esValido = false;
    } else if (!esCorreoValido(correo.value.trim())) {
        mostrarError(correo, "Ingresa un correo con formato válido (ej: nombre@correo.com).");
        esValido = false;
    }
 
    if (usuario.value.trim() === "") {
        mostrarError(usuario, "El nombre de usuario es obligatorio.");
        esValido = false;
    } else if (usuario.value.trim().length < 3) {
        mostrarError(usuario, "El usuario debe tener al menos 3 caracteres.");
        esValido = false;
    } else if (/\s/.test(usuario.value)) {
        mostrarError(usuario, "El usuario no puede contener espacios.");
        esValido = false;
    }
 
    // Validar contraseña solo si es requerida o si se proporciona
    if (!esEditar && pass.value === "") {
        mostrarError(pass, "La contraseña es obligatoria al crear un usuario.");
        esValido = false;
    } else if (pass.value !== "") {
        const validacion = validarFormatoContrasena(pass.value);
        if (!validacion.valida) {
            // Mostrar el primer error
            mostrarError(pass, validacion.errores[0]);
            esValido = false;
        }
    }
 
    if (rol.value === "") {
        mostrarError(rol, "Selecciona un rol para el usuario.");
        esValido = false;
    }
 
    if (!esValido) e.preventDefault();
}

/**
 * Valida el formulario de perfil de usuario
 * @param {Event} e El evento del formulario
 */
function validarPerfil(e) {
    const form            = e.target;
    const nombre          = form.querySelector('input[name="nombre"]');
    const usuario         = form.querySelector('input[name="usuario"]');
    const contrasenaNueva = form.querySelector('input[name="contrasena_nueva"]');
    let   esValido        = true;

    limpiarTodosLosErrores(form);

    if (nombre.value.trim() === "") {
        mostrarError(nombre, "El nombre completo es obligatorio.");
        esValido = false;
    } else if (nombre.value.trim().length < 3) {
        mostrarError(nombre, "El nombre debe tener al menos 3 caracteres.");
        esValido = false;
    }

    if (usuario.value.trim() === "") {
        mostrarError(usuario, "El nombre de usuario es obligatorio.");
        esValido = false;
    } else if (usuario.value.trim().length < 3) {
        mostrarError(usuario, "El usuario debe tener al menos 3 caracteres.");
        esValido = false;
    } else if (/\s/.test(usuario.value)) {
        mostrarError(usuario, "El usuario no puede contener espacios.");
        esValido = false;
    }

    // Validar contraseña solo si se proporciona
    if (contrasenaNueva && contrasenaNueva.value !== "") {
        const validacion = validarFormatoContrasena(contrasenaNueva.value);
        if (!validacion.valida) {
            mostrarError(contrasenaNueva, validacion.errores[0]);
            esValido = false;
        }
    }

    if (!esValido) e.preventDefault();
}

document.addEventListener("DOMContentLoaded", function () {
 
    // Inicializar toggle de contraseña
    inicializarTogglePassword();

    //Login
    const fLogin = document.querySelector(".formulario_login");
    if (fLogin) {
        fLogin.addEventListener("submit", validarLogin);
        // Limpiar error al escribir
        fLogin.querySelectorAll("input").forEach(input => {
            input.addEventListener("input", () => limpiarError(input));
        });
    }
 
    //Registro
    const fRegister = document.querySelector(".formulario_register");
    if (fRegister) {
        fRegister.addEventListener("submit", validarRegistro);
        fRegister.querySelectorAll("input").forEach(input => {
            input.addEventListener("input", () => limpiarError(input));
        });
    }
 
    //Propiedades
    const fProp = document.getElementById("formPropiedad");
    if (fProp) {
        fProp.addEventListener("submit", validarPropiedad);
        fProp.querySelectorAll("input, select, textarea").forEach(campo => {
            campo.addEventListener("input",  () => limpiarError(campo));
            campo.addEventListener("change", () => limpiarError(campo));
        });
    }
 
    //Usuarios
    const fUser = document.getElementById("formUsuario");
    if (fUser) {
        fUser.addEventListener("submit", validarUsuario);
        fUser.querySelectorAll("input, select").forEach(campo => {
            campo.addEventListener("input",  () => limpiarError(campo));
            campo.addEventListener("change", () => limpiarError(campo));
        });
    }

    // Perfil de usuario
    const formPerfil = document.querySelector('form[action="mi_perfil.php"]');
    if (formPerfil) {
        formPerfil.addEventListener("submit", validarPerfil);
        formPerfil.querySelectorAll("input").forEach(input => {
            input.addEventListener("input", () => limpiarError(input));
        });
    }
 
});