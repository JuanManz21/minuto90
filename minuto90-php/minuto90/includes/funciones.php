<?php
/**
 * includes/funciones.php - Funciones auxiliares de todo el sitio
 * Se incluye al principio de cada página junto con config/db.php
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ============================================================
   SEGURIDAD / TEXTO
   ============================================================ */

/** Escapa texto antes de imprimirlo en HTML (evita inyección de código) */
function e($texto) {
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

/** Formatea un precio: 524550 -> "$ 524.550" */
function precio($valor) {
    return '$ ' . number_format((float)$valor, 0, ',', '.');
}

/* ============================================================
   SESIÓN DE USUARIO
   ============================================================ */

/** ¿Hay alguien con la sesión iniciada? */
function hay_sesion() {
    return isset($_SESSION['usuario_id']);
}

/** Devuelve los datos del usuario logueado (o null) */
function usuario_actual($pdo) {
    if (!hay_sesion()) {
        return null;
    }
    $sql = 'SELECT * FROM usuarios WHERE id = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['usuario_id']]);
    return $stmt->fetch() ?: null;
}

/** Bloquea la página si no hay sesión iniciada */
function requiere_login() {
    if (!hay_sesion()) {
        header('Location: login.php');
        exit;
    }
}

/** ¿El usuario logueado es administrador? */
function es_admin() {
    return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin';
}

/** Bloquea la página si el usuario no es administrador */
function requiere_admin() {
    requiere_login();
    if (!es_admin()) {
        header('Location: ../tienda.php');
        exit;
    }
}

/* ============================================================
   CARRITO (guardado en la sesión)
   ============================================================ */

/** Devuelve el carrito actual como array */
function obtener_carrito() {
    if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }
    return $_SESSION['carrito'];
}

/** Agrega un producto al carrito (o suma cantidad si ya estaba) */
function agregar_al_carrito($producto, $talla, $cantidad = 1) {
    $carrito = obtener_carrito();
    $clave   = $producto['id'] . '-' . $talla;   // mismo producto + misma talla

    if (isset($carrito[$clave])) {
        $carrito[$clave]['cantidad'] += $cantidad;
    } else {
        $carrito[$clave] = [
            'producto_id' => $producto['id'],
            'nombre'      => $producto['nombre'],
            'precio'      => (int)$producto['precio'],
            'imagen'      => $producto['imagen'],
            'talla'       => $talla,
            'cantidad'    => $cantidad,
        ];
    }

    $_SESSION['carrito'] = $carrito;
}

/** Elimina una línea del carrito */
function eliminar_del_carrito($clave) {
    $carrito = obtener_carrito();
    unset($carrito[$clave]);
    $_SESSION['carrito'] = $carrito;
}

/** Vacía el carrito completo */
function vaciar_carrito() {
    $_SESSION['carrito'] = [];
}

/** Cantidad total de unidades en el carrito (para el ícono del header) */
function contar_carrito() {
    $total = 0;
    foreach (obtener_carrito() as $item) {
        $total += $item['cantidad'];
    }
    return $total;
}

/** Suma de precio * cantidad de todo el carrito */
function subtotal_carrito() {
    $subtotal = 0;
    foreach (obtener_carrito() as $item) {
        $subtotal += $item['precio'] * $item['cantidad'];
    }
    return $subtotal;
}

/** Costo fijo de envío (0 si el carrito está vacío) */
function costo_envio() {
    return contar_carrito() > 0 ? 5000 : 0;
}

/* ============================================================
   CONSULTAS REUTILIZABLES
   ============================================================ */

/** Trae todas las categorías para el menú lateral */
function obtener_categorias($pdo) {
    return $pdo->query('SELECT * FROM categorias ORDER BY id')->fetchAll();
}

/** Busca una categoría por su slug (ej: "hombre") */
function obtener_categoria_por_slug($pdo, $slug) {
    $stmt = $pdo->prepare('SELECT * FROM categorias WHERE slug = ?');
    $stmt->execute([$slug]);
    return $stmt->fetch() ?: null;
}

/** Busca un producto por su id */
function obtener_producto($pdo, $id) {
    $stmt = $pdo->prepare('SELECT * FROM productos WHERE id = ?');
    $stmt->execute([(int)$id]);
    return $stmt->fetch() ?: null;
}

/** Genera un número de pedido único tipo "004521" */
function generar_numero_pedido($pdo) {
    do {
        $numero = str_pad((string)random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM pedidos WHERE numero_pedido = ?');
        $stmt->execute([$numero]);
        $existe = $stmt->fetchColumn() > 0;
    } while ($existe);

    return $numero;
}
