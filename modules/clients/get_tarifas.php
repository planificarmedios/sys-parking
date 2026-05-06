<?php
require_once "../../config/database.php";

$categoria_id = (int) $_GET['categoria_id'];

$q = mysqli_query($mysqli, "
  SELECT id, descripcion, monto
  FROM tarifas
  WHERE categoria_id = $categoria_id
    AND activo = 1
");

$data = [];

while ($row = mysqli_fetch_assoc($q)) {
  $data[] = $row;
}

echo json_encode($data);