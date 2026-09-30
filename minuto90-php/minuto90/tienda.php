<?php
/**
 * tienda.php - Página principal del área interna (banner + menú lateral)
 */

require_once 'config/db.php';
require_once 'includes/funciones.php';

$titulo_pagina    = 'Tienda';
$mostrar_buscador = true;
$mostrar_menu     = true;
require 'includes/header_tienda.php';
?>

<div class="tienda-body">
    <?php require 'includes/sidebar.php'; ?>

    <main class="main-content">
        <section class="hero-just" style="background-image:url('img/ChatGPT_Image_14_sept_2026__04_06_28_p_m_.png');">
            <div class="hero-just-content">
                <p class="hero-just-tag">Solo hay una forma de descubrirlo.</p>
                <h1 class="hero-just-title">JUST DO IT.</h1>
                <a href="catalogo.php?cat=hombre" class="btn-equipate">Equípate</a>
            </div>
        </section>
    </main>
</div>

<script src="js/tienda.js"></script>
</body>
</html>
