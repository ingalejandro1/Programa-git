<?php
$host = 'localhost';
$usuario = 'root';
$contrasena = '';
$base_datos = 'biblioteca_db';

$conn = new mysqli($host, $usuario, $contrasena, $base_datos);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>