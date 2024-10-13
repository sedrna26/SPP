<?php
require '../../conn/connection.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Obtén el ID de la persona

    try {
        // Consulta para obtener la huella dactilar
        $stmt = $db->prepare("SELECT huella FROM ppl WHERE idpersona = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && $row['huella']) {
            // Mostrar la imagen de huella dactilar
            header("Content-Type: image/png");
            echo $row['huella'];
        } else {
            // Mostrar una imagen de placeholder si no hay huella
            header("Content-Type: image/png");
            readfile("path/to/placeholder.png"); // Cambia a la ruta de una imagen de ejemplo si no hay huella
        }
    } catch (PDOException $e) {
        error_log("Error al mostrar la huella: " . $e->getMessage());
    }
}
?>