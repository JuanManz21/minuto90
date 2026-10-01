<?php
/**
 * procesar_pedido.php - Guarda el pedido en la base de datos
 *
 * Usa una transacción: o se guardan el pedido Y todos sus productos,
 * o no se guarda nada. Así nunca queda un pedido a medias.
 */

require_once 'config/db.php';
require_once 'includes/funciones.php';

// Solo se llega aquí por POST desde el formulario del carrito
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: carrito.php');
    exit;
}

// Para comprar hay que tener cuenta (así el pedido queda asociado al usuario)
requiere_login();

$carrito = obtener_carrito();
if (empty($carrito)) {
    header('Location: carrito.php');
    exit;
}

/* ---------- Datos del formulario ---------- */
$nombre_envio = trim($_POST['nombre_envio'] ?? '');
$direccion    = trim($_POST['direccion']    ?? '');
$ciudad       = trim($_POST['ciudad']       ?? '');
$telefono     = trim($_POST['telefono']     ?? '');
$metodo_pago  = trim($_POST['metodo_pago']  ?? 'Tarjeta de crédito/débito');

if ($nombre_envio === '' || $direccion === '' || $ciudad === '' || $telefono === '') {
    header('Location: carrito.php?error=datos');
    exit;
}

$subtotal = subtotal_carrito();
$envio    = costo_envio();
$total    = $subtotal + $envio;
$numero   = generar_numero_pedido($pdo);

try {
    $pdo->beginTransaction();

    /* ---------- 1. Cabecera del pedido ---------- */
    $sql = 'INSERT INTO pedidos
            (usuario_id, numero_pedido, nombre_envio, direccion, ciudad,
             telefono, metodo_pago, subtotal, envio, total)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_SESSION['usuario_id'],
        $numero,
        $nombre_envio,
        $direccion,
        $ciudad,
        $telefono,
        $metodo_pago,
        $subtotal,
        $envio,
        $total,
    ]);

    $pedido_id = (int)$pdo->lastInsertId();

    /* ---------- 2. Cada producto del carrito ---------- */
    $sql = 'INSERT INTO pedido_items
            (pedido_id, producto_id, nombre_producto, imagen, talla, cantidad, precio_unitario)
            VALUES (?, ?, ?, ?, ?, ?, ?)';

    $stmt = $pdo->prepare($sql);

    foreach ($carrito as $item) {
        $stmt->execute([
            $pedido_id,
            $item['producto_id'],
            $item['nombre'],
            $item['imagen'],
            $item['talla'],
            $item['cantidad'],
            $item['precio'],
        ]);
    }

    $pdo->commit();

    /* ---------- 3. Limpiar y redirigir ---------- */
    vaciar_carrito();
    header('Location: gracias.php?pedido=' . urlencode($numero));
    exit;

} catch (PDOException $e) {
    $pdo->rollBack();
    die(
        '<div style="font-family:sans-serif;padding:40px;">'
        . '<h2>No se pudo procesar el pedido</h2>'
        . '<p>' . htmlspecialchars($e->getMessage()) . '</p>'
        . '<a href="carrito.php">Volver al carrito</a>'
        . '</div>'
    );
}
