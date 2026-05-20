<?php

require '../../config/database.php';

$categoria_id = (int) ($_GET['categoria_id'] ?? 0);

$todas = (int) ($_GET['todas'] ?? 0);

/* =========================================
   QUERY
========================================= */

$sql = "

SELECT *

FROM tarifas

WHERE activo = 1
AND categoria_id = $categoria_id

";

$sql .= "
ORDER BY monto ASC
";

$query = mysqli_query($mysqli, $sql);

$data = [];

while ($row = mysqli_fetch_assoc($query)) {

    $data[] = $row;
}

header('Content-Type: application/json');

echo json_encode($data);