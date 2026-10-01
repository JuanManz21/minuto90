<?php
/**
 * includes/header_tienda.php - Encabezado del área interna
 *
 * Antes de incluirlo puedes definir:
 *   $titulo_pagina   -> título del <title>
 *   $titulo_centro   -> texto que va en el centro del header (ej: "Hombre")
 *   $mostrar_buscador-> true para mostrar la barra de búsqueda en vez del título
 *   $mostrar_menu    -> true para mostrar el botón hamburguesa
 */

$titulo_pagina    = $titulo_pagina    ?? "Minuto 90'";
$titulo_centro    = $titulo_centro    ?? '';
$mostrar_buscador = $mostrar_buscador ?? false;
$mostrar_menu     = $mostrar_menu     ?? false;
$total_carrito    = contar_carrito();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo_pagina) ?> - Minuto 90'</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/tienda.css">
</head>
<body>

<header class="tienda-header">
    <?php if ($mostrar_menu): ?>
        <button class="menu-toggle" id="menu-toggle" aria-label="Abrir menú">
            <svg viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
    <?php endif; ?>

    <a href="tienda.php" class="logo-container">
        <img src="img/logo.png" alt="Minuto 90 logo" class="logo-img">
        <span class="logo-text">Minuto 90'</span>
    </a>

    <?php if ($mostrar_buscador): ?>
        <form class="tienda-search" action="buscar.php" method="get">
            <input type="text" name="q" placeholder="Sigue y mira sin compromiso"
                   value="<?= e($_GET['q'] ?? '') ?>">
            <button type="submit" aria-label="Buscar">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </button>
        </form>
    <?php else: ?>
        <span class="page-title"><?= e($titulo_centro) ?></span>
    <?php endif; ?>

    <div class="tienda-icons">
        <div class="user-dropdown-wrapper">
            <button class="icon-btn" id="user-icon-btn" aria-label="Cuenta">
                <svg viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/></svg>
            </button>
            <div class="user-dropdown" id="user-dropdown">
                <?php if (hay_sesion()): ?>
                    <?php if (es_admin()): ?>
                        <a href="admin/index.php" style="font-weight:bold; color:#e63946;">⚡ Panel Admin</a>
                    <?php endif; ?>
                    <a href="perfil.php">Tu perfil</a>
                    <a href="ayuda.php">Ayuda</a>
                    <a href="seguimiento.php">Seguimiento</a>
                    <a href="logout.php">Cerrar sesión</a>
                <?php else: ?>
                    <a href="login.php">Iniciar sesión</a>
                    <a href="register.php">Registrarse</a>
                    <a href="ayuda.php">Ayuda</a>
                <?php endif; ?>
            </div>
        </div>

        <a href="carrito.php" class="icon-btn" aria-label="Carrito" style="position:relative;">
            <svg viewBox="0 0 24 24" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/></svg>
            <span class="cart-count" id="cart-count"
                  style="<?= $total_carrito > 0 ? '' : 'display:none;' ?>"><?= $total_carrito ?></span>
        </a>
    </div>
</header>
