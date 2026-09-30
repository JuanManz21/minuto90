<?php
/**
 * seguimiento.php - Estado del último pedido del usuario
 */

require_once 'config/db.php';
require_once 'includes/funciones.php';
requiere_login();

// Último pedido (o uno específico si viene ?pedido=)
if (isset($_GET['pedido'])) {
    $stmt = $pdo->prepare('SELECT * FROM pedidos WHERE numero_pedido = ? AND usuario_id = ?');
    $stmt->execute([$_GET['pedido'], $_SESSION['usuario_id']]);
} else {
    $stmt = $pdo->prepare('SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY creado_en DESC LIMIT 1');
    $stmt->execute([$_SESSION['usuario_id']]);
}
$pedido = $stmt->fetch();

// Qué pasos van completados según el estado guardado en la BD
$estados = ['bodega' => 1, 'enviado' => 2, 'recibido' => 3];
$paso_actual = $pedido ? ($estados[$pedido['estado']] ?? 1) : 0;

$titulo_pagina    = 'Seguimiento';
$mostrar_buscador = true;
require 'includes/header_tienda.php';
?>

<section class="seguimiento-page">
    <div class="seguimiento-topbar">
        <a href="perfil.php" class="seguimiento-back">&larr; Back</a>
        <?php if ($pedido): ?>
            <div class="seguimiento-ids">
                <span class="completed">PEDIDO N°: <?= e($pedido['numero_pedido']) ?></span>
                <span>Estado: <?= e(strtoupper($pedido['estado'])) ?> ·
                      <?= e(date('d/m/Y H:i', strtotime($pedido['creado_en']))) ?></span>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!$pedido): ?>
        <p style="text-align:center;color:#666;padding:40px;">
            Todavía no tienes pedidos para hacer seguimiento.
            <a href="tienda.php" style="color:#007bff;">Ir a la tienda</a>
        </p>
    <?php else: ?>
        <?php if (in_array($pedido['estado'], ['cancelado', 'reembolsado'], true)): ?>
            <div style="max-width:500px; margin:20px auto; padding:15px; border-radius:8px; text-align:center; background-color:#f8d7da; color:#721c24;">
                <h3 style="margin-top:0;">Pedido <?= ucfirst(e($pedido['estado'])) ?></h3>
                <p>Este pedido ha sido marcado como <strong><?= e($pedido['estado']) ?></strong> por la administración.</p>
            </div>
        <?php else: ?>
            <div class="seguimiento-steps">
                <div class="step">
                    <div class="step-icon" style="<?= $paso_actual >= 1 ? '' : 'background:#ccc;' ?>">
                        <svg viewBox="0 0 24 24" stroke="#fff" stroke-width="1.8" fill="none"><path d="M3 9.5 12 4l9 5.5V20a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1Z"/><path d="M9 21v-7h6v7"/></svg>
                    </div>
                    <p>Bodega</p>
                </div>
                <div class="step-line"></div>
                <div class="step">
                    <div class="step-icon" style="<?= $paso_actual >= 2 ? '' : 'background:#ccc;' ?>">
                        <svg viewBox="0 0 24 24" stroke="#fff" stroke-width="1.8" fill="none"><rect x="1" y="7" width="14" height="10" rx="1"/><path d="M15 10h4l3 3v4h-7z"/><circle cx="6" cy="19" r="1.6"/><circle cx="17.5" cy="19" r="1.6"/></svg>
                    </div>
                    <p>Enviado</p>
                </div>
                <div class="step-line"></div>
                <div class="step">
                    <div class="step-icon" style="<?= $paso_actual >= 3 ? '' : 'background:#ccc;' ?>">
                        <svg viewBox="0 0 24 24" stroke="#fff" stroke-width="1.8" fill="none"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 6l10 7 10-7"/></svg>
                    </div>
                    <p>Recibido</p>
                </div>
            </div>
        <?php endif; ?>

        <div style="max-width:600px;margin:0 auto;padding:20px;">
            <h3 style="margin-bottom:12px;">Productos de este pedido</h3>
            <?php
            $stmt = $pdo->prepare('SELECT * FROM pedido_items WHERE pedido_id = ?');
            $stmt->execute([$pedido['id']]);
            foreach ($stmt->fetchAll() as $item):
            ?>
                <div class="order-item">
                    <img src="img/<?= e($item['imagen']) ?>" alt="<?= e($item['nombre_producto']) ?>">
                    <div class="order-item-info">
                        <h4><?= e($item['nombre_producto']) ?></h4>
                        <span>Talla: <?= e($item['talla']) ?> · Cant: <?= (int)$item['cantidad'] ?></span>
                        <span class="price"><?= precio($item['precio_unitario'] * $item['cantidad']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<script src="js/tienda.js"></script>
</body>
</html>
