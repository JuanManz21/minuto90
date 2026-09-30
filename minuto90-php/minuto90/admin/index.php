<?php
/**
 * admin/index.php - Panel Principal de Administración (Dashboard)
 */

$pagina_actual = 'dashboard';
require_once __DIR__ . '/header_admin.php';

// Estadísticas
$total_usuarios = $pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
$total_pedidos  = $pdo->query('SELECT COUNT(*) FROM pedidos')->fetchColumn();
$ingresos       = $pdo->query("SELECT SUM(total) FROM pedidos WHERE estado NOT IN ('cancelado', 'reembolsado')")->fetchColumn() ?: 0;
$pendientes     = $pdo->query("SELECT COUNT(*) FROM pedidos WHERE estado IN ('bodega', 'enviado')")->fetchColumn();

// Últimos 5 pedidos con datos del usuario
$sql = "SELECT p.*, u.email as email_usuario, u.nombre as nombre_usuario
        FROM pedidos p
        LEFT JOIN usuarios u ON p.usuario_id = u.id
        ORDER BY p.id DESC
        LIMIT 5";
$ultimos_pedidos = $pdo->query($sql)->fetchAll();
?>

<div class="admin-container">
    <div class="admin-title-bar">
        <h2>Panel de Administración</h2>
        <span>Bienvenido, <?= e($_SESSION['usuario_nombre']) ?></span>
    </div>

    <div class="stats-grid">
        <div class="stat-card card-users">
            <div class="stat-label">Usuarios Registrados</div>
            <div class="stat-value"><?= number_format($total_usuarios) ?></div>
        </div>

        <div class="stat-card card-orders">
            <div class="stat-label">Total Pedidos</div>
            <div class="stat-value"><?= number_format($total_pedidos) ?></div>
        </div>

        <div class="stat-card card-pending">
            <div class="stat-label">Pedidos Pendientes</div>
            <div class="stat-value"><?= number_format($pendientes) ?></div>
        </div>

        <div class="stat-card card-sales">
            <div class="stat-label">Ingresos Totales</div>
            <div class="stat-value"><?= precio($ingresos) ?></div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h3>Últimos Pedidos Realizados</h3>
            <a href="pedidos.php" class="btn-action btn-secondary">Ver Todos los Pedidos →</a>
        </div>

        <?php if (empty($ultimos_pedidos)): ?>
            <p style="color: var(--admin-text-muted);">Aún no se han realizado pedidos en la tienda.</p>
        <?php else: ?>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nº Pedido</th>
                            <th>Cliente</th>
                            <th>Ciudad / Dirección</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ultimos_pedidos as $p): ?>
                            <tr>
                                <td><strong>#<?= e($p['numero_pedido']) ?></strong></td>
                                <td>
                                    <strong><?= e($p['nombre_envio']) ?></strong><br>
                                    <small style="color:#666;"><?= e($p['email_usuario'] ?? 'Usuario eliminado') ?></small>
                                </td>
                                <td>
                                    <?= e($p['ciudad']) ?><br>
                                    <small style="color:#666;"><?= e($p['direccion']) ?></small>
                                </td>
                                <td><strong><?= precio($p['total']) ?></strong></td>
                                <td>
                                    <span class="badge-status badge-<?= e($p['estado']) ?>">
                                        <?= ucfirst(e($p['estado'])) ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($p['creado_en'])) ?></td>
                                <td>
                                    <a href="pedidos.php?id=<?= $p['id'] ?>" class="btn-action btn-secondary">Gestionar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
