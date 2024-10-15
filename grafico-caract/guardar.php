<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "user";
$password = "";
$dbname = "spp";

// Recibir los datos JSON
$datos = json_decode(file_get_contents('php://input'), true);

// Conectar a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => "Conexión fallida: " . $conn->connect_error]));
}

// Preparar la consulta SQL
$stmt = $conn->prepare("INSERT INTO marcas (parte_cuerpo, tipo_marca, observaciones) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $datos['parte'], $datos['tipo'], $datos['observaciones']);

// Ejecutar la consulta
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => "Marca guardada con éxito"]);
} else {
    echo json_encode(['success' => false, 'message' => "Error al guardar la marca: " . $stmt->error]);
}

$stmt->close();
$conn->close();
