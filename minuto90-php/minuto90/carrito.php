<?php
/**
 * carrito.php - Carrito de compras y formulario de checkout
 *
 * Recibe por POST:
 *   accion=agregar   + producto_id + talla
 *   accion=eliminar  + clave
 *   accion=vaciar
 */

require_once 'config/db.php';
require_once 'includes/funciones.php';

/* ---------- Acciones sobre el carrito ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'agregar') {
        $producto_id = (int)($_POST['producto_id'] ?? 0);
        $talla       = trim($_POST['talla'] ?? '');
        $producto    = obtener_producto($pdo, $producto_id);

        if ($producto) {
            // Si no eligió talla, se usa la primera disponible
            $tallas_validas = array_map('trim', explode(',', $producto['tallas']));
            if ($talla === '' || !in_array($talla, $tallas_validas, true)) {
                $talla = $tallas_validas[0];
            }

            agregar_al_carrito($producto, $talla);
            header('Location: producto.php?id=' . $producto_id . '&msg=agregado');
            exit;
        }
    }

    if ($accion === 'eliminar') {
        eliminar_del_carrito($_POST['clave'] ?? '');
        header('Location: carrito.php');
        exit;
    }

    if ($accion === 'vaciar') {
        vaciar_carrito();
        header('Location: carrito.php');
        exit;
    }
}

$carrito  = obtener_carrito();
$subtotal = subtotal_carrito();
$envio    = costo_envio();
$total    = $subtotal + $envio;

// Si hay sesión, precargamos los datos del usuario en el formulario
$usuario = usuario_actual($pdo);

$titulo_pagina    = 'Tu carrito';
$mostrar_buscador = true;
require 'includes/header_tienda.php';
?>

<form class="checkout-page" action="procesar_pedido.php" method="post">
    <div class="order-summary">
        <h3>Resumen del pedido</h3>

        <div class="order-box">
            <?php if (empty($carrito)): ?>
                <p style="font-size:14px;color:#666;">
                    Tu carrito está vacío.
                    <a href="tienda.php" style="color:#007bff;">Vuelve a la tienda</a> para agregar productos.
                </p>
            <?php endif; ?>

            <?php foreach ($carrito as $clave => $item): ?>
                <div class="order-item">
                    <img src="img/<?= e($item['imagen']) ?>" alt="<?= e($item['nombre']) ?>">
                    <div class="order-item-info">
                        <h4><?= e($item['nombre']) ?></h4>
                        <span>Talla: <?= e($item['talla']) ?> · Cant: <?= (int)$item['cantidad'] ?></span>
                        <span class="price"><?= precio($item['precio'] * $item['cantidad']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (!empty($carrito)): ?>
                <div class="order-totals">
                    <div><span>Subtotal</span><span><?= precio($subtotal) ?></span></div>
                    <div><span>Envío</span><span><?= precio($envio) ?></span></div>
                    <div class="grand-total"><span>Total</span><span><?= precio($total) ?></span></div>
                </div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn-confirm" <?= empty($carrito) ? 'disabled' : '' ?>>
            Confirmar la Compra
        </button>
    </div>

    <div class="shipping-form">
        <h3>Información de envío</h3>

        <input type="text" name="nombre_envio" placeholder="Nombre completo"
               value="<?= e($usuario['nombre'] ?? '') ?>" required>
        <input type="text" name="direccion" placeholder="Dirección"
               value="<?= e($usuario['direccion'] ?? '') ?>" required>
        <input type="text" name="ciudad" placeholder="Ciudad / Departamento" required>
        <input type="tel" name="telefono" placeholder="Teléfono"
               value="<?= e($usuario['telefono'] ?? '') ?>" required>

        <h3 style="margin-top:20px;">Método de pago</h3>
        <div class="payment-method">
            <label class="payment-option">
                <input type="radio" name="metodo_pago" value="Tarjeta de crédito/débito" checked>
                Tarjeta de crédito/débito
            </label>
            <label class="payment-option">
                <input type="radio" name="metodo_pago" value="Transferencia / Nequi / Daviplata">
                Transferencia / Nequi / Daviplata
            </label>
        </div>

        <input type="text" name="num_tarjeta" placeholder="Número de tarjeta">
        <input type="text" name="expiracion" placeholder="Fecha de expiración">
    </div>
</form>

<?php if (!empty($carrito)): ?>
<div style="text-align:center;padding-bottom:40px;">
    <form action="carrito.php" method="post" style="display:inline;">
        <input type="hidden" name="accion" value="vaciar">
        <button type="submit" style="background:#eee;color:#666;font-size:13px;">Vaciar carrito</button>
    </form>
</div>
<?php endif; ?>

<script src="js/tienda.js"></script>
</body>
</html>
