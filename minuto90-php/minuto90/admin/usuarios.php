<?php
/**
 * admin/usuarios.php - Gestión de Usuarios Registrados
 */

$pagina_actual = 'usuarios';
require_once __DIR__ . '/header_admin.php';

$mensaje = '';
$error   = '';

// Eliminar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar_usuario') {
    $usuario_id = (int)($_POST['usuario_id'] ?? 0);

    // Evitar que el admin se elimine a sí mismo
    if ($usuario_id === (int)$_SESSION['usuario_id']) {
        $error = 'No puedes eliminar tu propia cuenta de administrador mientras tienes la sesión abierta.';
    } elseif ($usuario_id > 0) {
        $stmt_check = $pdo->prepare('SELECT nombre FROM usuarios WHERE id = ?');
        $stmt_check->execute([$usuario_id]);
        $u_nombre = $stmt_check->fetchColumn();

        if ($u_nombre) {
            $stmt_del = $pdo->prepare('DELETE FROM usuarios WHERE id = ?');
            $stmt_del->execute([$usuario_id]);
            $mensaje = "El usuario '{$u_nombre}' (ID #{$usuario_id}) ha sido eliminado correctamente.";
        } else {
            $error = "El usuario no existe.";
        }
    }
}

// Búsqueda
$busqueda = trim($_GET['q'] ?? '');

$sql = "SELECT u.*, COUNT(p.id) as total_pedidos
        FROM usuarios u
        LEFT JOIN pedidos p ON u.id = p.usuario_id";

$params = [];
if ($busqueda !== '') {
    $sql .= " WHERE u.nombre LIKE ? OR u.email LIKE ? OR u.direccion LIKE ?";
    $term = "%{$busqueda}%";
    $params = [$term, $term, $term];
}

$sql .= " GROUP BY u.id ORDER BY u.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$usuarios = $stmt->fetchAll();
?>

<div class="admin-container">
    <div class="admin-title-bar">
        <h2>Usuarios Registrados</h2>
    </div>

    <?php if ($mensaje): ?>
        <div class="alert-flash alert-success"><?= e($mensaje) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert-flash alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <!-- Buscador -->
    <div class="admin-card">
        <form method="get" action="usuarios.php" style="display:flex; gap:12px; align-items:center;">
            <input type="text" name="q" value="<?= e($busqueda) ?>" placeholder="Buscar usuario por nombre, correo o dirección..." class="form-select-sm" style="width:100%; max-width:400px;">
            <button type="submit" class="btn-action btn-secondary">Buscar</button>
            <?php if ($busqueda !== ''): ?>
                <a href="usuarios.php" class="btn-action btn-secondary">Limpiar</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3>Lista de Usuarios (<?= count($usuarios) ?>)</h3>
        </div>

        <?php if (empty($usuarios)): ?>
            <p style="color: var(--admin-text-muted);">No se encontraron usuarios registrados.</p>
        <?php else: ?>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo Electrónico</th>
                            <th>Dirección Guardada</th>
                            <th>Teléfono</th>
                            <th>Rol</th>
                            <th>Pedidos</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td>#<?= $u['id'] ?></td>
                                <td><strong><?= e($u['nombre']) ?></strong></td>
                                <td><?= e($u['email']) ?></td>
                                <td><?= e($u['direccion'] ?: 'No registrada') ?></td>
                                <td><?= e($u['telefono'] ?: 'No registrado') ?></td>
                                <td>
                                    <span class="badge-status badge-rol-<?= e($u['rol']) ?>">
                                        <?= strtoupper(e($u['rol'])) ?>
                                    </span>
                                </td>
                                <td><strong><?= (int)$u['total_pedidos'] ?></strong></td>
                                <td><?= date('d/m/Y', strtotime($u['creado_en'])) ?></td>
                                <td>
                                    <?php if ((int)$u['id'] === (int)$_SESSION['usuario_id']): ?>
                                        <small style="color:#888;">(Tu cuenta)</small>
                                    <?php else: ?>
                                        <form method="post" action="usuarios.php" onsubmit="return confirm('¿Estás seguro de que deseas eliminar a este usuario?');">
                                            <input type="hidden" name="accion" value="eliminar_usuario">
                                            <input type="hidden" name="usuario_id" value="<?= $u['id'] ?>">
                                            <button type="submit" class="btn-action btn-danger">Eliminar</button>
                                        </form>
                                    <?php endif; ?>
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
