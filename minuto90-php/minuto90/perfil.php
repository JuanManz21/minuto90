<?php
/**
 * perfil.php - Datos del usuario e historial de compras reales
 */

require_once 'config/db.php';
require_once 'includes/funciones.php';
requiere_login();

$usuario = usuario_actual($pdo);

// Historial de compras del usuario
$stmt = $pdo->prepare(
    'SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY creado_en DESC LIMIT 10'
);
$stmt->execute([$_SESSION['usuario_id']]);
$pedidos = $stmt->fetchAll();

$titulo_pagina    = 'Tu perfil';
$mostrar_buscador = true;
require 'includes/header_tienda.php';
?>

<section class="profile-page">
    <div class="profile-info">
        <div class="profile-field">
            <svg viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/></svg>
            <span class="value"><?= e($usuario['nombre']) ?></span>
        </div>

        <div class="profile-field">
            <svg viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 6l10 7 10-7"/></svg>
            <span class="value"><?= e($usuario['email']) ?></span>
        </div>

        <div class="profile-field">
            <svg viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.1-8.7A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.8.6 2.7a2 2 0 0 1-.5 2.1L7.9 9.9a16 16 0 0 0 6 6l1.4-1.4a2 2 0 0 1 2.1-.5c.9.3 1.8.5 2.7.6a2 2 0 0 1 1.9 2.3Z"/></svg>
            <span class="value"><?= e($usuario['telefono'] ?: 'Sin teléfono registrado') ?></span>
        </div>

        <div class="profile-block">
            <h4>Dirección de envío</h4>
            <p><?= e($usuario['direccion'] ?: 'Sin dirección registrada') ?></p>
        </div>

        <div class="profile-block">
            <h4>Historial de compras</h4>
            <?php if (empty($pedidos)): ?>
                <p style="font-size:14px;color:#666;">Todavía no has hecho ningún pedido.</p>
            <?php else: ?>
                <?php foreach ($pedidos as $ped): ?>
                    <p style="font-size:14px;">
                        <strong>#<?= e($ped['numero_pedido']) ?></strong> ·
                        <?= precio($ped['total']) ?> ·
                        <?= e(ucfirst($ped['estado'])) ?> ·
                        <?= e(date('d/m/Y', strtotime($ped['creado_en']))) ?>
                    </p>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <a href="logout.php" class="profile-edit-link">Cerrar sesión</a>
    </div>

    <div class="profile-banner" style="background-image:url('img/Ney.png');">
        <p>GREATNESS IS NOT<br>BORN, IT IS MADE.</p>
    </div>
</section>

<script src="js/tienda.js"></script>
</body>
</html>
