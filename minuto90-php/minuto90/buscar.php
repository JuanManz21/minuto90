<?php
/**
 * buscar.php - Buscador de productos
 * Usa LIKE con parámetro preparado (no concatena texto del usuario en el SQL)
 */

require_once 'config/db.php';
require_once 'includes/funciones.php';

$q = trim($_GET['q'] ?? '');
$resultados = [];

if ($q !== '') {
    $sql = 'SELECT * FROM productos
            WHERE nombre LIKE :busqueda OR descripcion LIKE :busqueda
            ORDER BY nombre
            LIMIT 30';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':busqueda' => '%' . $q . '%']);
    $resultados = $stmt->fetchAll();
}

$titulo_pagina    = 'Buscar';
$mostrar_buscador = true;
require 'includes/header_tienda.php';
?>

<div class="catalog-header">
    <span class="page-title" style="font-size:18px;">
        <?php if ($q === ''): ?>
            Escribe algo en el buscador
        <?php else: ?>
            <?= count($resultados) ?> resultado(s) para "<?= e($q) ?>"
        <?php endif; ?>
    </span>
    <span></span>
</div>

<div class="catalog-grid">
    <?php foreach ($resultados as $p): ?>
        <a class="catalog-card" href="producto.php?id=<?= (int)$p['id'] ?>">
            <div class="catalog-card-img">
                <img src="img/<?= e($p['imagen']) ?>" alt="<?= e($p['nombre']) ?>">
            </div>
            <div class="catalog-card-info">
                <h3><?= e($p['nombre']) ?></h3>
                <?php if ($p['descuento']): ?>
                    <p class="catalog-card-discount"><?= (int)$p['descuento'] ?>% OFF</p>
                <?php endif; ?>
                <p class="catalog-card-price"><?= precio($p['precio']) ?></p>
            </div>
        </a>
    <?php endforeach; ?>

    <?php if ($q !== '' && empty($resultados)): ?>
        <p style="padding:20px;color:#666;">
            No encontramos productos con ese nombre. Prueba con "camiseta", "real", "guayos"...
        </p>
    <?php endif; ?>
</div>

<script src="js/tienda.js"></script>
</body>
</html>
