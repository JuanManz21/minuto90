<?php
/**
 * index.php - Página principal pública (antes de iniciar sesión)
 * Las "Ofertas Destacadas" salen de la BD: productos con destacado = 1
 */

require_once 'config/db.php';
require_once 'includes/funciones.php';

// Productos marcados como destacados en la base de datos
$destacados = $pdo->query(
    'SELECT * FROM productos WHERE destacado = 1 ORDER BY descuento DESC LIMIT 4'
)->fetchAll();

// Producto con mayor descuento, para el badge del banner
$oferta = $destacados[0] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minuto 90' - Tienda de Camisetas de Fútbol</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/index.css">
</head>
<body>

<header class="header">
    <div class="logo-container">
        <img src="img/logo.png" alt="Minuto 90 logo" class="logo">
        <h1>Minuto 90'</h1>
    </div>

    <div class="header-buttons">
        <?php if (hay_sesion()): ?>
            <a href="tienda.php" class="btn-header">Ir a la tienda</a>
            <a href="logout.php" class="btn-header">Cerrar sesión</a>
        <?php else: ?>
            <a href="login.php" class="btn-header">Iniciar sesión</a>
            <a href="register.php" class="btn-header">Registrarse</a>
        <?php endif; ?>
    </div>
</header>

<!-- Banner principal -->
<section class="hero-banner">
    <div class="banner-container">
        <div class="banner-image">
            <img src="img/neymar.png" alt="Jugador de fútbol">
        </div>
        <div class="banner-content">
            <h2 class="banner-title">Tu pasión, Nuestro equipamiento</h2>
            <p class="banner-description">
                Descubre la mejor selección de uniformes y guayos de fútbol
                de los equipos más prestigiosos del mundo
            </p>

            <?php if ($oferta): ?>
                <a href="producto.php?id=<?= (int)$oferta['id'] ?>" class="offer-badge">
                    <div class="discount-text">
                        <span class="discount-number"><?= (int)$oferta['descuento'] ?>%</span>
                        <span class="discount-off">OFF</span>
                    </div>
                    <div class="offer-product-preview">
                        <img src="img/<?= e($oferta['imagen']) ?>" alt="<?= e($oferta['nombre']) ?>">
                    </div>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Ofertas destacadas traídas de la base de datos -->
<section class="featured-products">
    <h2 class="section-title">Ofertas Destacadas</h2>

    <div class="products-grid">
        <?php foreach ($destacados as $p): ?>
            <article class="product-card">
                <?php if ($p['descuento']): ?>
                    <div class="product-badge"><?= (int)$p['descuento'] ?>%</div>
                <?php endif; ?>

                <div class="product-image">
                    <img src="img/<?= e($p['imagen']) ?>" alt="<?= e($p['nombre']) ?>">
                </div>

                <h3 class="product-name"><?= e($p['nombre']) ?></h3>

                <?php if ($p['precio_antes']): ?>
                    <p class="product-price-before">
                        Antes: <span class="price-old"><?= precio($p['precio_antes']) ?></span>
                    </p>
                <?php endif; ?>

                <p class="product-price-now"><?= precio($p['precio']) ?></p>

                <a href="producto.php?id=<?= (int)$p['id'] ?>" class="btn-buy">Comprar Ahora</a>
            </article>
        <?php endforeach; ?>

        <?php if (empty($destacados)): ?>
            <p style="text-align:center;color:#666;">
                No hay ofertas destacadas. Marca productos con <code>destacado = 1</code> en la BD.
            </p>
        <?php endif; ?>
    </div>
</section>

<footer class="footer">
    <p>&copy; <?= date('Y') ?> Minuto 90' - Todos los derechos reservados</p>
    <p class="footer-contact">Contacto: info@minuto90.com | Tel: +57 300 123 4567</p>
</footer>

</body>
</html>
