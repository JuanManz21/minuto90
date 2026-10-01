<?php
/**
 * includes/sidebar.php - Menú lateral de categorías
 * Las categorías se leen de la base de datos, así que si agregas una
 * categoría nueva en MySQL aparece sola aquí.
 *
 * Define $categoria_activa con el slug para resaltar la opción actual.
 */

$categoria_activa = $categoria_activa ?? '';
$categorias = obtener_categorias($pdo);

// Ícono SVG correspondiente a cada categoría
$iconos = [
    'novedades' => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 6l10 7 10-7"/>',
    'hombre'    => '<circle cx="12" cy="7" r="4"/><path d="M5 21c0-4 3-7 7-7s7 3 7 7"/>',
    'ninos'     => '<circle cx="8" cy="7" r="3"/><circle cx="16" cy="7" r="3"/><path d="M2 21c0-3.5 2.7-6 6-6s6 2.5 6 6"/><path d="M10 21c0-3.5 2.7-6 6-6s6 2.5 6 6"/>',
    'guayos'    => '<path d="M2 18h20"/><path d="M4 18c0-4 2-9 9-9 3 0 5 2 5 4 0 3-4 3-4 5h6"/>',
    'descuento' => '<path d="M20.6 12.2 12 20.8a2 2 0 0 1-2.8 0L2 13.6V4h9.6l9 9a2 2 0 0 1 0 2.8Z"/><circle cx="7" cy="8.5" r="1.5"/>',
];
?>
<aside class="sidebar" id="sidebar">
    <button class="sidebar-close" id="sidebar-close" aria-label="Cerrar menú">&times;</button>

    <?php foreach ($categorias as $cat): ?>
        <div class="sidebar-item">
            <svg viewBox="0 0 24 24" stroke-width="1.6" fill="none">
                <?= $iconos[$cat['slug']] ?? '<circle cx="12" cy="12" r="9"/>' ?>
            </svg>
            <a href="catalogo.php?cat=<?= e($cat['slug']) ?>"
               class="sidebar-link <?= $categoria_activa === $cat['slug'] ? 'active' : '' ?>">
                <?= e($cat['nombre']) ?>
            </a>
        </div>
    <?php endforeach; ?>
</aside>
