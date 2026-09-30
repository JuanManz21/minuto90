<?php
/**
 * login.php - Inicio de sesión
 * Verifica el correo en la BD y compara la contraseña con password_verify()
 */

require_once 'config/db.php';
require_once 'includes/funciones.php';

if (hay_sesion()) {
    header('Location: tienda.php');
    exit;
}

$errores = [];
$email   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '') {
        $errores['email'] = 'El correo es obligatorio.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = 'Ingresa un correo válido.';
    }

    if ($password === '') {
        $errores['password'] = 'La contraseña es obligatoria.';
    }

    if (empty($errores)) {
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = ?');
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        // Mensaje genérico a propósito: no revela si el correo existe o no
        if (!$usuario || !password_verify($password, $usuario['password'])) {
            $errores['password'] = 'Correo o contraseña incorrectos.';
        } else {
            session_regenerate_id(true);   // evita robo de sesión
            $_SESSION['usuario_id']     = (int)$usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];

            header('Location: bienvenida.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Minuto 90'</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <header>
        <div class="logo-container">
            <a href="index.php" style="display:flex;align-items:center;gap:12px;">
                <img src="img/logo.png" alt="Minuto 90'" class="logo-img">
                <h1 class="logo-text">Minuto 90'</h1>
            </a>
        </div>
        <nav class="nav-buttons">
            <a href="register.php" class="btn-register">Registrarse</a>
        </nav>
    </header>

    <div class="login-container">
        <form class="login-form" id="login-form" action="login.php" method="post" novalidate>
            <h2 class="form-title">Iniciar Sesión</h2>

            <div class="form-group">
                <input type="email" id="email" name="email" class="form-input"
                       placeholder="Correo electrónico" value="<?= e($email) ?>" required>
                <span class="error-message" id="email-error"><?= e($errores['email'] ?? '') ?></span>
            </div>

            <div class="form-group">
                <input type="password" id="password" name="password" class="form-input"
                       placeholder="Contraseña" required>
                <span class="error-message" id="password-error"><?= e($errores['password'] ?? '') ?></span>
            </div>

            <div class="forgot-password">
                <a href="ayuda.php" class="forgot-link">¿Olvidaste tu contraseña?</a>
            </div>

            <button type="submit" class="btn-submit">Enviar</button>

            <div class="form-footer">
                <p>¿No tienes cuenta? <a href="register.php" class="link-register">Regístrate aquí</a></p>
                <a href="index.php" class="link-back">← Volver a la tienda</a>
            </div>
        </form>
    </div>

    <script src="js/validation.js"></script>
</body>
</html>
