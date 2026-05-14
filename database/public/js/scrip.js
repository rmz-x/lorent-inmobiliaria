// Agrega event listeners a los botones de login y registro
document.getElementById("btn_iniciar-sesion").addEventListener("click", iniciarSesion);
document.getElementById("btn_registrarse").addEventListener("click", register);

// Agrega event listener para redimensionar la ventana
window.addEventListener("resize", anchopagina);

/* Declaración de variables para elementos del DOM */
var contenedor_login_register=document.querySelector(".contenedor_login-register");
var formulario_login=document.querySelector(".formulario_login");
var formulario_register=document.querySelector(".formulario_register");
var caja_trasera_login=document.querySelector(".caja_trasera_login");
var caja_trasera_register=document.querySelector(".caja_trasera_register");

// Función que ajusta el layout según el ancho de la ventana
function anchopagina(){
    if(window.innerWidth>850){
        // Para pantallas grandes, muestra ambas cajas traseras
        caja_trasera_login.style.display = "block";
        caja_trasera_register.style.display = "block";
    }else{
        // Para pantallas pequeñas, oculta la caja de login y muestra la de registro
        caja_trasera_register.style.display = "block";
        caja_trasera_register.style.opacity = "1";
        caja_trasera_login.style.display = "none";
        formulario_login.style.display = "block";
        formulario_register.style.display = "none";
        contenedor_login_register.style.left = "0px";
    }
}

// Llama a la función al cargar la página
anchopagina();

// Función para mostrar el formulario de login
function iniciarSesion(){
    if(window.innerWidth>850){
        // Para pantallas grandes, anima el contenedor hacia la izquierda
        formulario_register.style.display="none";
        contenedor_login_register.style.left = "10px";
        formulario_login.style.display = "block";
        caja_trasera_register.style.opacity = "1";
        caja_trasera_login.style.opacity = "0";
    }else{
        // Para pantallas pequeñas, cambia la visibilidad
        formulario_register.style.display="none";
        contenedor_login_register.style.left = "0px";
        formulario_login.style.display = "block"
        caja_trasera_register.style.display = "block";
        caja_trasera_login.style.display = "none";
    }
}

// Función para mostrar el formulario de registro
function register(){
    if(window.innerWidth>850){
        // Para pantallas grandes, anima el contenedor hacia la derecha
        formulario_register.style.display="block";
        contenedor_login_register.style.left = "410px";
        formulario_login.style.display = "none"
        caja_trasera_register.style.opacity = "0";
        caja_trasera_login.style.opacity = "1";
    }else{
        // Para pantallas pequeñas, cambia la visibilidad
        formulario_register.style.display="block";
        contenedor_login_register.style.left = "0px";
        formulario_login.style.display = "none"
        caja_trasera_register.style.display = "none";
        caja_trasera_login.style.display = "block";
        caja_trasera_login.style.opacity = "1";
    }
}