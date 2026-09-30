<?php
/**
 * producto.php - Detalle de un producto
 * URL: producto.php?id=5
 */

require_once 'config/db.php';
require_once 'includes/funciones.php';

$id       = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$producto = obtener_producto($pdo, $id);

if (!$producto) {
    header('Location: tienda.php');
    exit;
}

// Las tallas vienen guardadas como "M,L,XL" -> las convertimos en array
$tallas = array_filter(array_map('trim', explode(',', $producto['tallas'])));

// Mensaje que llega desde carrito.php después de agregar
$mensaje = $_GET['msg'] ?? '';

$titulo_pagina = $producto['nombre'];
$titulo_centro = '';
require 'includes/header_tienda.php';
?>

<section class="product-page">
    <div class="product-page-img">
        <img src="img/<?= e($producto['imagen']) ?>" alt="<?= e($producto['nombre']) ?>">
    </div>

    <div class="product-page-info">
        <h2><?= e($producto['nombre']) ?></h2>
        <p class="product-page-desc"><?= e($producto['descripcion']) ?></p>

        <?php if ($producto['precio_antes']): ?>
            <p class="product-price-before">
                Antes: <span class="price-old"><?= precio($producto['precio_antes']) ?></span>
                <?php if ($producto['descuento']): ?>
                    <strong style="color:#e63946;"> · <?= (int)$producto['descuento'] ?>% OFF</strong>
                <?php endif; ?>
            </p>
        <?php endif; ?>

        <p class="product-page-price"><?= precio($producto['precio']) ?></p>

        <!-- El formulario envía la talla y el id a carrito.php -->
        <form action="carrito.php" method="post">
            <input type="hidden" name="accion" value="agregar">
            <input type="hidden" name="producto_id" value="<?= (int)$producto['id'] ?>">
            <input type="hidden" name="talla" id="talla-input" value="">

            <p class="size-label">Seleccione la talla</p>
            <div class="size-options" id="size-options">
                <?php foreach ($tallas as $talla): ?>
                    <button type="button" class="size-btn"><?= e($talla) ?></button>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="btn-add-cart" id="add-to-cart-btn">Agregar a tu carrito</button>
            <p class="add-cart-msg" id="add-cart-msg">
                <?= $mensaje === 'agregado' ? '¡Producto agregado a tu carrito!' : '' ?>
            </p>
        </form>
    </div>
</section>

<script src="js/tienda.js"></script>
</body>
</html>
