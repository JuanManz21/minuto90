<?php
/**
 * logout.php - Cierra la sesión del usuario
 */

require_once 'includes/funciones.php';

$_SESSION = [];
session_destroy();

header('Location: index.php');
exit;
