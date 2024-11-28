<?php
// Para ver si el parámetro 'pais_id' se recibe correctamente
var_dump($_GET);  // Elimina esta línea una vez confirmes que se recibe el parámetro correctamente

include('../../conn/connection.php'); // Asegúrate de tener la conexión a la base de datos
include('../admin/navbar.php');
@include('../conn/connection.php');

// Verifica si se recibió el parámetro 'provincia_id'
if (isset($_GET['provincia_id']) && $_GET['provincia_id'] != '') {
    $provincia_id = $_GET['provincia_id'];  // Obtener el ID de la provincia

    // Realizar la consulta para obtener las ciudades de la provincia
    $query = "SELECT id, nombre FROM ciudades WHERE id_prov = :provincia_id ORDER BY nombre ASC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':provincia_id', $provincia_id, PDO::PARAM_INT);  // Usamos PDO para prevenir inyecciones SQL
    $stmt->execute();

    // Si se encuentran ciudades, las mostramos
    echo "<option value=''>-- Seleccione una Ciudad --</option>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
    }
} else {
    // Si no se recibe el 'provincia_id', mostramos un mensaje en el select
    echo "<option value=''>No se ha seleccionado una provincia</option>";
}
