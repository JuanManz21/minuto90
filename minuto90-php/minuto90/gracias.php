<?php
/**
 * gracias.php - Confirmación de compra
 */

require_once 'config/db.php';
require_once 'includes/funciones.php';

$numero = $_GET['pedido'] ?? '';

// Verificamos que el pedido exista de verdad y sea de este usuario
$pedido = null;
if ($numero !== '' && hay_sesion()) {
    $stmt = $pdo->prepare('SELECT * FROM pedidos WHERE numero_pedido = ? AND usuario_id = ?');
    $stmt->execute([$numero, $_SESSION['usuario_id']]);
    $pedido = $stmt->fetch();
}

$titulo_pagina = 'Gracias por tu compra';
require 'includes/header_tienda.php';
?>

<section class="gracias-page">
    <h1>¡Gracias por tu compra!</h1>

    <?php if ($pedido): ?>
        <p>Número de pedido: <strong>#<?= e($pedido['numero_pedido']) ?></strong></p>
        <p>Total pagado: <strong><?= precio($pedido['total']) ?></strong></p>
        <p>Te hemos enviado un correo de confirmación a tu bandeja de entrada.</p>
        <p style="margin-top:20px;">
            <a href="seguimiento.php" style="color:#007bff;">Ver el seguimiento de tu pedido</a>
        </p>
    <?php else: ?>
        <p>Tu pedido fue registrado correctamente.</p>
    <?php endif; ?>

    <a href="tienda.php" class="btn-volver">Volver a la tienda</a>
</section>

<script src="js/tienda.js"></script>
</body>
</html>
