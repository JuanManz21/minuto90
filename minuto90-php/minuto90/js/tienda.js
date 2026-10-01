/**
 * tienda.js - Interacciones del lado del cliente (Minuto 90')
 *
 * Con PHP el carrito y los productos ya viven en el servidor, así que aquí
 * solo queda lo visual: menús, dropdowns y selección de talla.
 */

document.addEventListener("DOMContentLoaded", function () {

    /* ---------- MENÚ LATERAL (hamburguesa) ---------- */
    const menuToggle   = document.getElementById("menu-toggle");
    const sidebar      = document.getElementById("sidebar");
    const sidebarClose = document.getElementById("sidebar-close");

    if (menuToggle && sidebar) {
        menuToggle.addEventListener("click", function () {
            sidebar.classList.toggle("closed");
        });
    }

    if (sidebarClose && sidebar) {
        sidebarClose.addEventListener("click", function () {
            sidebar.classList.add("closed");
        });
    }

    /* ---------- DROPDOWN DE USUARIO ---------- */
    const userIcon     = document.getElementById("user-icon-btn");
    const userDropdown = document.getElementById("user-dropdown");

    if (userIcon && userDropdown) {
        userIcon.addEventListener("click", function (e) {
            e.stopPropagation();
            userDropdown.classList.toggle("open");
        });
    }

    /* ---------- MENÚ "ORDENAR POR" ---------- */
    // Las opciones son enlaces a catalogo.php?orden=...
    // PHP hace el ORDER BY en la consulta SQL.
    const sortBtn  = document.getElementById("sort-btn");
    const sortMenu = document.getElementById("sort-menu");

    if (sortBtn && sortMenu) {
        sortBtn.addEventListener("click", function (e) {
            e.stopPropagation();
            sortMenu.classList.toggle("open");
        });
    }

    // Un solo listener cierra los dos menús al hacer clic fuera
    document.addEventListener("click", function () {
        if (userDropdown) userDropdown.classList.remove("open");
        if (sortMenu)     sortMenu.classList.remove("open");
    });

    /* ---------- SELECCIÓN DE TALLA ---------- */
    // Al hacer clic en una talla se marca visualmente y se copia su valor
    // al input oculto que viaja con el formulario hacia carrito.php
    const sizeButtons = document.querySelectorAll(".size-btn");
    const tallaInput  = document.getElementById("talla-input");

    sizeButtons.forEach(function (btn) {
        btn.addEventListener("click", function () {
            sizeButtons.forEach(function (b) {
                b.classList.remove("selected");
            });
            btn.classList.add("selected");

            if (tallaInput) {
                tallaInput.value = btn.textContent.trim();
            }
        });
    });

    /* ---------- VALIDAR TALLA ANTES DE ENVIAR ---------- */
    const addCartBtn = document.getElementById("add-to-cart-btn");

    if (addCartBtn && sizeButtons.length > 0) {
        addCartBtn.addEventListener("click", function (e) {
            if (!tallaInput || tallaInput.value === "") {
                e.preventDefault();   // no envía el formulario
                const msg = document.getElementById("add-cart-msg");
                if (msg) {
                    msg.style.color   = "#e63946";
                    msg.textContent   = "Selecciona una talla antes de continuar.";
                }
            }
        });
    }
});
