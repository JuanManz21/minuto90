<?php
/**
 * register.php - Registro de usuarios
 * Valida en el servidor y guarda la contraseña cifrada con password_hash()
 */

require_once 'config/db.php';
require_once 'includes/funciones.php';

// Si ya inició sesión, no tiene sentido registrarse
if (hay_sesion()) {
    header('Location: tienda.php');
    exit;
}

$errores = [];
$datos   = ['nombre' => '', 'email' => '', 'direccion' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $datos['nombre']    = trim($_POST['nombre'] ?? '');
    $datos['email']     = trim($_POST['email'] ?? '');
    $datos['direccion'] = trim($_POST['direccion'] ?? '');
    $password           = $_POST['password'] ?? '';
    $confirmar          = $_POST['confirmar'] ?? '';

    /* ---------- Validaciones ---------- */
    if ($datos['nombre'] === '') {
        $errores['nombre'] = 'El nombre completo es obligatorio.';
    }

    if ($datos['email'] === '') {
        $errores['email'] = 'El correo es obligatorio.';
    } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = 'Ingresa un correo válido.';
    } else {
        // ¿Ya existe ese correo?
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM usuarios WHERE email = ?');
        $stmt->execute([$datos['email']]);
        if ($stmt->fetchColumn() > 0) {
            $errores['email'] = 'Ese correo ya está registrado.';
        }
    }

    if ($password === '') {
        $errores['password'] = 'La contraseña es obligatoria.';
    } elseif (strlen($password) < 6) {
        $errores['password'] = 'La contraseña debe tener al menos 6 caracteres.';
    }

    if ($confirmar === '') {
        $errores['confirmar'] = 'Debes confirmar la contraseña.';
    } elseif ($confirmar !== $password) {
        $errores['confirmar'] = 'Las contraseñas no coinciden.';
    }

    if ($datos['direccion'] === '') {
        $errores['direccion'] = 'La dirección es obligatoria.';
    }

    /* ---------- Guardar ---------- */
    if (empty($errores)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = 'INSERT INTO usuarios (nombre, email, password, direccion)
                VALUES (?, ?, ?, ?)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $datos['nombre'],
            $datos['email'],
            $hash,
            $datos['direccion'],
        ]);

        // Inicia sesión automáticamente
        $_SESSION['usuario_id']     = (int)$pdo->lastInsertId();
        $_SESSION['usuario_nombre'] = $datos['nombre'];
        $_SESSION['usuario_rol']    = 'usuario';

        header('Location: bienvenida.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - Minuto 90'</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
    <header class="header">
        <div class="logo-container">
            <a href="index.php" style="display:flex;align-items:center;gap:10px;">
                <img src="img/logo.png" alt="Minuto 90'" class="logo">
                <h1>Minuto 90'</h1>
            </a>
        </div>
        <a href="login.php" class="btn-header">Iniciar sesión</a>
    </header>

    <main class="register-container">
        <div class="register-box">
            <h2>Registrarse</h2>

            <form action="register.php" method="post" id="register-form" novalidate>
                <input type="text" id="nombre" name="nombre" placeholder="Nombre completo"
                       value="<?= e($datos['nombre']) ?>" required>
                <span class="error-message" id="nombre-error"><?= e($errores['nombre'] ?? '') ?></span>

                <input type="email" id="reg-email" name="email" placeholder="Correo electrónico"
                       value="<?= e($datos['email']) ?>" required>
                <span class="error-message" id="reg-email-error"><?= e($errores['email'] ?? '') ?></span>

                <input type="password" id="reg-password" name="password" placeholder="Crea una contraseña" required>
                <span class="error-message" id="reg-password-error"><?= e($errores['password'] ?? '') ?></span>

                <input type="password" id="reg-confirmar" name="confirmar" placeholder="Confirma contraseña" required>
                <span class="error-message" id="reg-confirmar-error"><?= e($errores['confirmar'] ?? '') ?></span>

                <input type="text" id="direccion" name="direccion" placeholder="Dirección"
                       value="<?= e($datos['direccion']) ?>" required>
                <span class="error-message" id="direccion-error"><?= e($errores['direccion'] ?? '') ?></span>

                <button type="submit">Enviar</button>
            </form>

            <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
            <a href="index.php" class="volver">← Volver a la tienda</a>
        </div>
    </main>

    <script src="js/validation.js"></script>
</body>
</html>
