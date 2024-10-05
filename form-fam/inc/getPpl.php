<?php
require 'db.php';

$con = new Database();
$pdo = $con->conectar();

$campo = $_POST["campo"];

$sql = "SELECT ppl.id as id_ppl, persona.nombres, persona.apellidos, persona.dni
        FROM ppl
        INNER JOIN persona ON ppl.idpersona = persona.id 
        WHERE ppl.id LIKE ? 
        OR persona.nombres LIKE ? 
        OR persona.apellidos LIKE ?
        OR persona.dni LIKE ?
        ORDER BY ppl.id ASC";

$query = $pdo->prepare($sql);
$params = array_fill(0, 4, "%$campo%");
$query->execute($params);

$html = "";

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $html .= "<li onclick=\"mostrar('" . $row["id_ppl"] . "', '" .
        $row["nombres"] . "', '" . $row["apellidos"] . "', '" .
        $row["dni"] . "')\">" .
        $row["id_ppl"] . " - " .
        $row["nombres"] . " " .
        $row["apellidos"] . " - DNI: " .
        $row["dni"] . "</li>";
}

echo json_encode($html, JSON_UNESCAPED_UNICODE);
