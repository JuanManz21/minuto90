<?php
/**
 * config/db.php - Conexión a la base de datos MySQL
 *
 * Estos son los datos por defecto de XAMPP. Si cambiaste la contraseña
 * de root en MySQL, ajústala aquí.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'minuto90');
define('DB_USER', 'root');
define('DB_PASS', '');          // XAMPP por defecto no tiene contraseña
define('DB_CHARSET', 'utf8mb4');

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

    $opciones = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $opciones);

} catch (PDOException $e) {
    die(
        '<div style="font-family:sans-serif;padding:40px;">'
        . '<h2>Error de conexión a la base de datos</h2>'
        . '<p>' . htmlspecialchars($e->getMessage()) . '</p>'
        . '<p>Revisa que MySQL esté encendido en XAMPP y que hayas importado '
        . '<strong>sql/minuto90.sql</strong> en phpMyAdmin.</p>'
        . '</div>'
    );
}
