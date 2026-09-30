<?php
/**
 * ayuda.php - Centro de ayuda / preguntas frecuentes
 */

require_once 'config/db.php';
require_once 'includes/funciones.php';

// Las preguntas se guardan en un array de PHP (fácil de pasar a una tabla después)
$faq = [
    'Devoluciones y cambios' => [
        '¿Cuál es la política de devoluciones de Minuto 90?',
        '¿Cómo puedo cambiar o devolver mi pedido de Minuto 90?',
        '¿Dónde está mi reembolso?',
    ],
    'Envío y entrega' => [
        '¿Cuáles son las opciones de envío de Minuto 90?',
        '¿Cómo puedo obtener envíos gratuitos en pedidos Minuto 90?',
        '¿Puedo hacer un pedido online y recogerlo en tienda?',
    ],
    'Pedidos y pagos' => [
        '¿Dónde está mi pedido Minuto 90?',
        '¿Puedo cancelar o cambiar mi pedido de Minuto 90?',
        '¿Cuáles son las opciones de pago de Minuto 90?',
    ],
];

$titulo_pagina    = 'Obtener ayuda';
$mostrar_buscador = true;
require 'includes/header_tienda.php';
?>

<section class="ayuda-page">
    <h1>OBTENER AYUDA</h1>

    <form class="ayuda-search" action="buscar.php" method="get">
        <input type="text" name="q" placeholder="¿Con qué podemos ayudarte?">
        <button type="submit" aria-label="Buscar">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </button>
    </form>

    <h2>Ayuda rápida</h2>
    <p>Accede a nuestras preguntas más frecuentes con un solo clic.</p>

    <div class="ayuda-grid">
        <?php foreach ($faq as $titulo => $preguntas): ?>
            <div class="ayuda-col">
                <h3><?= e($titulo) ?></h3>
                <?php foreach ($preguntas as $pregunta): ?>
                    <p><?= e($pregunta) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<script src="js/tienda.js"></script>
</body>
</html>
