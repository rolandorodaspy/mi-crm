<?php
// config.php - Configuración de la Base de Datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'nombre_de_tu_base_de_datos'); // CAMBIAR POR TU DB
define('DB_USER', 'usuario_de_tu_base_de_datos'); // CAMBIAR POR TU USUARIO
define('DB_PASS', 'tu_contraseña'); // CAMBIAR POR TU CONTRASEÑA

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
