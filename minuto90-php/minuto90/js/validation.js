/**
 * validation.js - Validación de formularios en el navegador (Minuto 90')
 *
 * IMPORTANTE: esta validación es solo para dar respuesta rápida al usuario.
 * La validación de verdad está en PHP (login.php y register.php), porque el
 * JavaScript se puede desactivar o saltar desde el navegador.
 *
 * Aquí, si todo está bien, se DEJA que el formulario se envíe al servidor.
 */

const EMAIL_REGEX = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

function mostrarError(input, mensaje) {
    const errorSpan = document.getElementById(input.id + "-error");
    if (errorSpan) {
        errorSpan.textContent = mensaje;
    }
    input.classList.add("input-invalido");
}

function limpiarError(input) {
    const errorSpan = document.getElementById(input.id + "-error");
    if (errorSpan) {
        errorSpan.textContent = "";
    }
    input.classList.remove("input-invalido");
}

/* ---------- LOGIN ---------- */
function validarLogin(e) {
    let esValido = true;

    const email    = document.getElementById("email");
    const password = document.getElementById("password");

    limpiarError(email);
    limpiarError(password);

    if (email.value.trim() === "") {
        mostrarError(email, "El correo es obligatorio.");
        esValido = false;
    } else if (!EMAIL_REGEX.test(email.value.trim())) {
        mostrarError(email, "Ingresa un correo válido.");
        esValido = false;
    }

    if (password.value.trim() === "") {
        mostrarError(password, "La contraseña es obligatoria.");
        esValido = false;
    } else if (password.value.length < 6) {
        mostrarError(password, "La contraseña debe tener al menos 6 caracteres.");
        esValido = false;
    }

    // Si algo falla, se detiene el envío. Si todo está bien, PHP lo recibe.
    if (!esValido) {
        e.preventDefault();
    }
}

/* ---------- REGISTRO ---------- */
function validarRegistro(e) {
    let esValido = true;

    const nombre    = document.getElementById("nombre");
    const email     = document.getElementById("reg-email");
    const password  = document.getElementById("reg-password");
    const confirmar = document.getElementById("reg-confirmar");
    const direccion = document.getElementById("direccion");

    [nombre, email, password, confirmar, direccion].forEach(limpiarError);

    if (nombre.value.trim() === "") {
        mostrarError(nombre, "El nombre completo es obligatorio.");
        esValido = false;
    }

    if (email.value.trim() === "") {
        mostrarError(email, "El correo es obligatorio.");
        esValido = false;
    } else if (!EMAIL_REGEX.test(email.value.trim())) {
        mostrarError(email, "Ingresa un correo válido.");
        esValido = false;
    }

    if (password.value.trim() === "") {
        mostrarError(password, "La contraseña es obligatoria.");
        esValido = false;
    } else if (password.value.length < 6) {
        mostrarError(password, "La contraseña debe tener al menos 6 caracteres.");
        esValido = false;
    }

    if (confirmar.value.trim() === "") {
        mostrarError(confirmar, "Debes confirmar la contraseña.");
        esValido = false;
    } else if (confirmar.value !== password.value) {
        mostrarError(confirmar, "Las contraseñas no coinciden.");
        esValido = false;
    }

    if (direccion.value.trim() === "") {
        mostrarError(direccion, "La dirección es obligatoria.");
        esValido = false;
    }

    if (!esValido) {
        e.preventDefault();
    }
}

/* ---------- CONEXIÓN DE LOS FORMULARIOS ---------- */
document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("login-form");
    if (loginForm) {
        loginForm.addEventListener("submit", validarLogin);
    }

    const registerForm = document.getElementById("register-form");
    if (registerForm) {
        registerForm.addEventListener("submit", validarRegistro);
    }
});
