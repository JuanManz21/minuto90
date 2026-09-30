<?php
/**
 * admin/pedidos.php - Gestión de Pedidos y Reembolsos
 */

$pagina_actual = 'pedidos';
require_once __DIR__ . '/header_admin.php';

$mensaje = '';
$error   = '';

// Procesar cambio de estado de pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'cambiar_estado') {
    $pedido_id   = (int)($_POST['pedido_id'] ?? 0);
    $nuevo_estado = trim($_POST['estado'] ?? '');

    $estados_permitidos = ['bodega', 'enviado', 'recibido', 'cancelado', 'reembolsado'];

    if ($pedido_id > 0 && in_array($nuevo_estado, $estados_permitidos, true)) {
        $stmt = $pdo->prepare('UPDATE pedidos SET estado = ? WHERE id = ?');
        $stmt->execute([$nuevo_estado, $pedido_id]);

        $textos_estado = [
            'bodega'      => 'En proceso / Bodega',
            'enviado'     => 'Enviado / En camino',
            'recibido'    => 'Entregado / Recibido',
            'cancelado'   => 'Cancelado',
            'reembolsado' => 'Reembolsado al cliente'
        ];

        $mensaje = "El estado del pedido #{$pedido_id} se actualizó a '" . ($textos_estado[$nuevo_estado] ?? $nuevo_estado) . "'.";
    } else {
        $error = 'Parámetros o estado inválido.';
    }
}

// Filtros y búsqueda
$filtro_estado = trim($_GET['estado'] ?? 'todos');
$busqueda      = trim($_GET['q'] ?? '');

$sql = "SELECT p.*, u.email as email_usuario, u.nombre as nombre_usuario_reg
        FROM pedidos p
        LEFT JOIN usuarios u ON p.usuario_id = u.id
        WHERE 1=1";
$params = [];

if ($filtro_estado !== 'todos' && in_array($filtro_estado, ['bodega', 'enviado', 'recibido', 'cancelado', 'reembolsado'], true)) {
    $sql .= " AND p.estado = ?";
    $params[] = $filtro_estado;
}

if ($busqueda !== '') {
    $sql .= " AND (p.numero_pedido LIKE ? OR p.nombre_envio LIKE ? OR p.ciudad LIKE ? OR u.email LIKE ?)";
    $term = "%{$busqueda}%";
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
}

$sql .= " ORDER BY p.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$pedidos = $stmt->fetchAll();

// Obtener items para cada pedido cargado
$pedidos_items = [];
if (!empty($pedidos)) {
    $ids = array_column($pedidos, 'id');
    $in  = str_repeat('?,', count($ids) - 1) . '?';
    $stmt_items = $pdo->prepare("SELECT * FROM pedido_items WHERE pedido_id IN ($in)");
    $stmt_items->execute($ids);
    $all_items = $stmt_items->fetchAll();

    foreach ($all_items as $item) {
        $pedidos_items[$item['pedido_id']][] = $item;
    }
}
?>

<div class="admin-container">
    <div class="admin-title-bar">
        <h2>Gestión de Pedidos</h2>
    </div>

    <?php if ($mensaje): ?>
        <div class="alert-flash alert-success"><?= e($mensaje) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert-flash alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <!-- Filtros de búsqueda -->
    <div class="admin-card">
        <form method="get" action="pedidos.php" style="display:flex; flex-wrap:wrap; gap:15px; align-items:center;">
            <div>
                <label style="font-weight:600; font-size:0.9rem; margin-right:8px;">Estado:</label>
                <select name="estado" class="form-select-sm" onchange="this.form.submit()">
                    <option value="todos" <?= $filtro_estado === 'todos' ? 'selected' : '' ?>>Todos los estados</option>
                    <option value="bodega" <?= $filtro_estado === 'bodega' ? 'selected' : '' ?>>En Bodega (En proceso)</option>
                    <option value="enviado" <?= $filtro_estado === 'enviado' ? 'selected' : '' ?>>Enviado (En camino)</option>
                    <option value="recibido" <?= $filtro_estado === 'recibido' ? 'selected' : '' ?>>Entregado (Recibido)</option>
                    <option value="cancelado" <?= $filtro_estado === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                    <option value="reembolsado" <?= $filtro_estado === 'reembolsado' ? 'selected' : '' ?>>Reembolsado</option>
                </select>
            </div>

            <div style="flex-grow:1; display:flex; gap:8px;">
                <input type="text" name="q" value="<?= e($busqueda) ?>" placeholder="Buscar por Nº pedido, cliente, ciudad o email..." class="form-select-sm" style="width:100%; max-width:350px;">
                <button type="submit" class="btn-action btn-secondary">Buscar</button>
                <?php if ($busqueda !== '' || $filtro_estado !== 'todos'): ?>
                    <a href="pedidos.php" class="btn-action btn-secondary">Limpiar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Tabla de Pedidos -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3>Listado de Pedidos (<?= count($pedidos) ?>)</h3>
        </div>

        <?php if (empty($pedidos)): ?>
            <p style="color: var(--admin-text-muted);">No se encontraron pedidos con los criterios seleccionados.</p>
        <?php else: ?>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nº Pedido</th>
                            <th>Cliente / Datos Envio</th>
                            <th>Productos Comprados</th>
                            <th>Pago y Totales</th>
                            <th>Estado Actual</th>
                            <th>Cambiar Estado / Reembolso</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $p): ?>
                            <tr id="pedido-<?= $p['id'] ?>">
                                <td>
                                    <strong>#<?= e($p['numero_pedido']) ?></strong><br>
                                    <small style="color:#888;"><?= date('d/m/Y H:i', strtotime($p['creado_en'])) ?></small>
                                </td>
                                <td>
                                    <strong><?= e($p['nombre_envio']) ?></strong><br>
                                    <small style="color:#555;">Email: <?= e($p['email_usuario'] ?? 'N/A') ?></small><br>
                                    <small style="color:#555;">Dirección: <?= e($p['direccion']) ?>, <?= e($p['ciudad']) ?></small><br>
                                    <small style="color:#555;">Teléfono: <?= e($p['telefono']) ?></small>
                                </td>
                                <td>
                                    <?php $items = $pedidos_items[$p['id']] ?? []; ?>
                                    <ul class="order-items-list">
                                        <?php foreach ($items as $item): ?>
                                            <li>
                                                <strong><?= e($item['nombre_producto']) ?></strong>
                                                (Talla: <?= e($item['talla']) ?>, Cant: <?= (int)$item['cantidad'] ?>) - <?= precio($item['precio_unitario']) ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </td>
                                <td>
                                    <small style="color:#666;">Método: <?= e($p['metodo_pago']) ?></small><br>
                                    <small style="color:#666;">Envío: <?= precio($p['envio']) ?></small><br>
                                    <strong>Total: <?= precio($p['total']) ?></strong>
                                </td>
                                <td>
                                    <span class="badge-status badge-<?= e($p['estado']) ?>">
                                        <?php
                                        $map_badge = [
                                            'bodega' => 'En Bodega',
                                            'enviado' => 'Enviado',
                                            'recibido' => 'Entregado',
                                            'cancelado' => 'Cancelado',
                                            'reembolsado' => 'Reembolsado'
                                        ];
                                        echo e($map_badge[$p['estado']] ?? $p['estado']);
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="post" action="pedidos.php" style="display:flex; flex-direction:column; gap:6px;">
                                        <input type="hidden" name="accion" value="cambiar_estado">
                                        <input type="hidden" name="pedido_id" value="<?= $p['id'] ?>">
                                        <select name="estado" class="form-select-sm">
                                            <option value="bodega" <?= $p['estado'] === 'bodega' ? 'selected' : '' ?>>📦 En Bodega (En proceso)</option>
                                            <option value="enviado" <?= $p['estado'] === 'enviado' ? 'selected' : '' ?>>🚚 Enviado (En camino)</option>
                                            <option value="recibido" <?= $p['estado'] === 'recibido' ? 'selected' : '' ?>>✅ Entregado</option>
                                            <option value="cancelado" <?= $p['estado'] === 'cancelado' ? 'selected' : '' ?>>❌ Cancelado</option>
                                            <option value="reembolsado" <?= $p['estado'] === 'reembolsado' ? 'selected' : '' ?>>💸 Reembolsado</option>
                                        </select>
                                        <button type="submit" class="btn-action btn-secondary" style="justify-content:center;">
                                            Actualizar
                                        </button>
                                    </form>
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
