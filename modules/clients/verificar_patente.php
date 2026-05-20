<?php

require '../../config/database.php';

header('Content-Type: application/json');

$patente = trim($_POST['patente'] ?? '');

if ($patente == '') {

    echo json_encode([
        'error' => true
    ]);

    exit;
}

/* =========================================
   VERIFICAR ABONO VIGENTE
========================================= */

$sql = "

SELECT
    id,
    fecha_fin

FROM clientes

WHERE patente = ?
AND activo = 1
AND fecha_fin >= CURDATE()

LIMIT 1

";

$stmt = mysqli_prepare($mysqli, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $patente
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$cliente = mysqli_fetch_assoc($result);

echo json_encode([

    'existe' => !!$cliente,

    'fecha_fin' => $cliente['fecha_fin'] ?? null

]);