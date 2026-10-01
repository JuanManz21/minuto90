<?php
/**
 * catalogo.php - Catálogo de productos por categoría
 *
 * Reemplaza los 10 archivos HTML estáticos anteriores.
 * URL de ejemplo: catalogo.php?cat=hombre&pagina=2&orden=precio-asc
 */

require_once 'config/db.php';
require_once 'includes/funciones.php';

/* ---------- 1. Categoría solicitada ---------- */
$slug      = $_GET['cat'] ?? 'novedades';
$categoria = obtener_categoria_por_slug($pdo, $slug);

if (!$categoria) {
    header('Location: tienda.php');
    exit;
}

/* ---------- 2. Ordenamiento ---------- */
// Lista blanca: nunca se mete texto del usuario directo en el SQL
$ordenes_validos = [
    'destacados'  => 'id ASC',
    'fecha'       => 'creado_en DESC, id DESC',
    'precio-desc' => 'precio DESC',
    'precio-asc'  => 'precio ASC',
];

$orden_actual = $_GET['orden'] ?? 'destacados';
if (!array_key_exists($orden_actual, $ordenes_validos)) {
    $orden_actual = 'destacados';
}
$order_by = $ordenes_validos[$orden_actual];

/* ---------- 3. Paginación ---------- */
$por_pagina = 3;
$pagina     = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;

// Total de productos de esta categoría
$stmt = $pdo->prepare('SELECT COUNT(*) FROM productos WHERE categoria_id = ?');
$stmt->execute([$categoria['id']]);
$total_productos = (int)$stmt->fetchColumn();
$total_paginas   = max(1, (int)ceil($total_productos / $por_pagina));

if ($pagina > $total_paginas) {
    $pagina = $total_paginas;
}
$offset = ($pagina - 1) * $por_pagina;

/* ---------- 4. Traer los productos de esta página ---------- */
$sql = "SELECT * FROM productos
        WHERE categoria_id = :categoria
        ORDER BY $order_by
        LIMIT :limite OFFSET :offset";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':categoria', $categoria['id'], PDO::PARAM_INT);
$stmt->bindValue(':limite',    $por_pagina,      PDO::PARAM_INT);
$stmt->bindValue(':offset',    $offset,          PDO::PARAM_INT);
$stmt->execute();
$productos = $stmt->fetchAll();

/* ---------- 5. Pintar la página ---------- */
$titulo_pagina = $categoria['nombre'];
$titulo_centro = $categoria['nombre'];
require 'includes/header_tienda.php';

/** Construye una URL del catálogo conservando los filtros actuales */
function url_catalogo($slug, $pagina, $orden) {
    return 'catalogo.php?' . http_build_query([
        'cat'    => $slug,
        'pagina' => $pagina,
        'orden'  => $orden,
    ]);
}
?>

<div class="catalog-header">
    <span></span>
    <div class="sort-wrapper">
        <button class="sort-btn" id="sort-btn" type="button">
            Ordenar por
            <svg width="12" height="12" viewBox="0 0 24 24" stroke="#000" stroke-width="2" fill="none"><path d="M6 9l6 6 6-6"/></svg>
        </button>
        <div class="sort-menu" id="sort-menu">
            <a href="<?= e(url_catalogo($slug, 1, 'destacados')) ?>">Destacados</a>
            <a href="<?= e(url_catalogo($slug, 1, 'fecha')) ?>">Fecha de subida</a>
            <a href="<?= e(url_catalogo($slug, 1, 'precio-desc')) ?>">Precio: Alto a Bajo</a>
            <a href="<?= e(url_catalogo($slug, 1, 'precio-asc')) ?>">Precio: Bajo a Alto</a>
        </div>
    </div>
</div>

<div class="catalog-grid">
    <?php if (empty($productos)): ?>
        <p style="padding:20px;color:#666;">No hay productos en esta categoría todavía.</p>
    <?php endif; ?>

    <?php foreach ($productos as $p): ?>
        <a class="catalog-card" href="producto.php?id=<?= (int)$p['id'] ?>">
            <div class="catalog-card-img">
                <img src="img/<?= e($p['imagen']) ?>" alt="<?= e($p['nombre']) ?>">
            </div>
            <div class="catalog-card-info">
                <h3><?= e($p['nombre']) ?></h3>

                <?php if ($p['descuento']): ?>
                    <p class="catalog-card-discount"><?= (int)$p['descuento'] ?>% OFF</p>
                <?php endif; ?>

                <?php if ($p['precio_antes']): ?>
                    <p class="catalog-card-sub">
                        Antes: <span class="price-old"><?= precio($p['precio_antes']) ?></span>
                    </p>
                <?php endif; ?>

                <p class="catalog-card-price"><?= precio($p['precio']) ?></p>
            </div>
        </a>
    <?php endforeach; ?>
</div>

<!-- Paginación calculada según cuántos productos haya en la BD -->
<nav class="pagination">
    <?php if ($pagina > 1): ?>
        <a href="<?= e(url_catalogo($slug, $pagina - 1, $orden_actual)) ?>">&larr; Anterior</a>
    <?php else: ?>
        <span class="page-disabled">&larr; Anterior</span>
    <?php endif; ?>

    <span class="page-numbers">
        <?php for ($n = 1; $n <= $total_paginas; $n++): ?>
            <a href="<?= e(url_catalogo($slug, $n, $orden_actual)) ?>"
               class="<?= $n === $pagina ? 'current' : '' ?>"><?= $n ?></a>
        <?php endfor; ?>
    </span>

    <?php if ($pagina < $total_paginas): ?>
        <a href="<?= e(url_catalogo($slug, $pagina + 1, $orden_actual)) ?>">Siguiente &rarr;</a>
    <?php else: ?>
        <span class="page-disabled">Siguiente &rarr;</span>
    <?php endif; ?>
</nav>

<p style="text-align:center;color:#888;font-size:13px;padding-bottom:40px;">
    Mostrando <?= count($productos) ?> de <?= $total_productos ?> productos
</p>

<script src="js/tienda.js"></script>
</body>
</html>
