<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $con = new Database();
    $pdo = $con->conectar();

    // Datos comunes para todos los vínculos
    $id_ppl = $_POST['id_ppl'];
    $vinculo = $_POST['vinculo'];
    $vive = $_POST['vive'];
    $apellido = $_POST['apellido'];
    $nombre = $_POST['nombre'];
    $edad = $_POST['edad'];

    // Insertar primero en la tabla persona
    $sql_persona = "INSERT INTO persona (Apellidos, Nombres, Edad) VALUES (?, ?, ?)";
    $stmt_persona = $pdo->prepare($sql_persona);
    $stmt_persona->execute([$apellido, $nombre, $edad]);
    $id_persona = $pdo->lastInsertId();

    // Preparar la consulta SQL para la tabla familia
    $sql_familia = "INSERT INTO familia (Ppl, Vinculo, Datos, Vive";
    $params = [$id_ppl, $vinculo, $id_persona, $vive];

    if ($vive == 'NO') {
        $sql_familia .= ", Fecha_fall, Causa_fall";
        $params[] = $_POST['fecha_muerte'];
        $params[] = $_POST['causa_muerte'];
    }

    // Campos adicionales según el vínculo
    if (in_array($vinculo, ['PADRE', 'MADRE', 'CONCUBINO', 'ESPOSO'])) {
        $sql_familia .= ", Nacional, Instruc, Ocupacion";
        $params[] = $_POST['nacionalidad'];
        $params[] = $_POST['gradoInstruccion'];
        $params[] = $_POST['ocupacion'];
    }

    if (in_array($vinculo, ['PADRE', 'MADRE'])) {
        $sql_familia .= ", Ffaa, Fam_detenida";
        $params[] = $_POST['esFFAA'] == 'SI' ? 1 : 0;
        $params[] = $_POST['estaDetenido'] == 'SI' ? 1 : 0;
    }

    $sql_familia .= ") VALUES (" . str_repeat("?,", count($params) - 1) . "?)";

    $stmt_familia = $pdo->prepare($sql_familia);
    
    if ($stmt_familia->execute($params)) {
        echo json_encode(["status" => "success", "message" => "Familiar agregado correctamente"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al agregar familiar"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método no permitido"]);
}
