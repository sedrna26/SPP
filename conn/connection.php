<?php
// Variables de conexión a la base de datos
$db_host = 'localhost'; // Host de la base de datos
$db_name = 'abm_escuela'; // Nombre de la base de datos
$db_user = 'root'; // Usuario de la base de datos
$db_password = ''; // Contraseña de la base de datos


//Estan las dos formas de generar la coneccion a la base de datos, PDO y mysqli
try {
    $db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Error de conexión: " . $e->getMessage()); // Registro del error
    exit(); // Finaliza el script sin generar salida
}

$conexion = new mysqli($db_host, $db_user, $db_password, $db_name);
if ($conexion->connect_error) {
    error_log("Error de conexión: " . $conexion->connect_error); // Registro del error
    exit(); // Finaliza el script sin generar salida
}

?>