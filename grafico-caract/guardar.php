<?php
// Conexión a la base de datos
$host = "localhost"; // Cambia por el host de tu servidor
$dbname = "SPP"; // Nombre de tu base de datos
$username = "root"; // Usuario de la base de datos
$password = ""; // Contraseña de la base de datos

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos enviados por el formulario
    $parte = $_POST['parte']; // La parte del cuerpo seleccionada
    $tipoMarca = $_POST['tipoMarca']; // El tipo de marca
    $observaciones = $_POST['observaciones']; // Observaciones
    
    // Preparar la consulta SQL para insertar los datos en la tabla 'caracteristicas'
    $sql = "INSERT INTO caracteristicas (parte, tipo_marca, observaciones) VALUES (:parte, :tipoMarca, :observaciones)";
    
    $stmt = $pdo->prepare($sql);
    
    // Ejecutar la consulta con los valores del formulario
    $stmt->execute([
        ':parte' => $parte,
        ':tipoMarca' => $tipoMarca,
        ':observaciones' => $observaciones,
    ]);
    
    echo "Datos insertados correctamente.";
}
?>

