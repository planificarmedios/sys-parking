<?php

require_once "../../config/database.php";

$categoria_id = (int) $_GET['categoria_id'];

$query = mysqli_query($mysqli, "
    SELECT id, descripcion, es_default
    FROM tarifas
    WHERE activo = 1
      AND categoria_id = $categoria_id
    ORDER BY descripcion
");

$datos = [];

while($row = mysqli_fetch_assoc($query)) {

    $datos[] = $row;

}

header('Content-Type: application/json');

echo json_encode($datos);