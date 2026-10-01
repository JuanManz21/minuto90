<?php
/**
 * bienvenida.php - Pantalla de bienvenida tras iniciar sesión
 */

require_once 'config/db.php';
require_once 'includes/funciones.php';
requiere_login();

$usuario = usuario_actual($pdo);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - Minuto 90'</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/tienda.css">
</head>
<body>

<div class="bienvenida-hero">
    <div class="bienvenida-header">Minuto 90'</div>

    <div class="bienvenida-content">
        <div class="bienvenida-text">
            <h1>Bienvenido<?= $usuario ? ', ' . e(explode(' ', $usuario['nombre'])[0]) : '' ?></h1>
            <p>
                En Minuto 90 somos fanáticos del fútbol tanto como tú. Nos especializamos
                en ofrecer camisetas originales y de la mejor calidad, inspiradas en los
                equipos y selecciones más importantes del mundo.
            </p>
            <a href="tienda.php" class="btn-entra">Entra</a>
        </div>

        <div class="bienvenida-visual">
            <img src="img/CR7.png" alt="Jugador de fútbol">
        </div>
    </div>
</div>

</body>
</html>
