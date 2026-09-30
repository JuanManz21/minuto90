<?php
/**
 * admin/header_admin.php - Encabezado común para las páginas del Panel Admin
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/funciones.php';

// Bloquea el acceso a cualquier usuario que no sea admin
requiere_admin();

$pagina_actual = $pagina_actual ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Minuto 90'</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<header class="admin-header">
    <a href="index.php" class="admin-header-brand">
        <img src="../img/logo.png" alt="Minuto 90 logo">
        <h1>Minuto 90'</h1>
        <span class="admin-badge">Admin</span>
    </a>

    <nav class="admin-nav">
        <a href="index.php" class="<?= $pagina_actual === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
        <a href="pedidos.php" class="<?= $pagina_actual === 'pedidos' ? 'active' : '' ?>">Pedidos</a>
        <a href="usuarios.php" class="<?= $pagina_actual === 'usuarios' ? 'active' : '' ?>">Usuarios</a>
        <a href="../tienda.php" class="btn-back-shop">← Ir a la Tienda</a>
        <a href="../logout.php" style="color:#ff8080;">Cerrar sesión</a>
    </nav>
</header>
