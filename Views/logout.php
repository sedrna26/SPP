<?php
require '../conn/connection.php';
session_start();

if (isset($_SESSION['id_usuario'])) {
    
    $sqlUpdateHoraCierre = "UPDATE registro_acceso SET hora_cierre = NOW() WHERE id_usuario = :id_usuario AND hora_cierre IS NULL ORDER BY hora_inicio DESC LIMIT 1";
    $stmtUpdateHoraCierre = $db->prepare($sqlUpdateHoraCierre);
    $stmtUpdateHoraCierre->bindParam(':id_usuario', $_SESSION['id_usuario'], PDO::PARAM_INT);
    $stmtUpdateHoraCierre->execute();
    
    
    session_unset();
    session_destroy();
}


header("Location: home.php");
exit;
