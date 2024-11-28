<?php
var_dump($_GET);  // Para ver si el parámetro 'pais_id' se recibe correctamente
include('../../conn/connection.php'); // Asegúrate de tener la conexión a la base de datos
include('../admin/navbar.php');
@include('../conn/connection.php');

if (isset($_GET['pais_id']) && $_GET['pais_id'] != '') {
    $pais_id = $_GET['pais_id'];  // Obtener el ID del país

    // Realizamos la consulta para obtener las provincias del país
    $query = "SELECT id, nombre FROM provincias WHERE id_pais = :pais_id ORDER BY nombre ASC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':pais_id', $pais_id, PDO::PARAM_INT);  // Usamos PDO para prevenir inyecciones SQL
    $stmt->execute();

    // Si se encuentran provincias, las mostramos
    echo "<option value=''>-- Seleccione una Provincia --</option>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
    }
} else {
    echo "<option value=''>No se ha seleccionado un país</option>";
}
