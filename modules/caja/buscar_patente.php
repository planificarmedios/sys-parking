<?php

require '../../config/database.php';

header('Content-Type: application/json');

$patente = trim($_POST['patente'] ?? '');

if ($patente == '') {

    echo json_encode([
        'ok' => false
    ]);

    exit;
}

/* =========================================
   BUSCAR PATENTE
========================================= */

$sql = "

SELECT

    COALESCE(
        cli.categoria_id,
        v.categoria_id
    ) AS categoria_id,

    COALESCE(
        cli.tarifa_id,
        v.tarifa_id
    ) AS tarifa_id,

    COALESCE(
        catCli.nombre,
        catVeh.nombre
    ) AS categoria

FROM vehiculos v

LEFT JOIN clientes cli
    ON cli.patente COLLATE utf8mb4_general_ci =
       v.patente COLLATE utf8mb4_general_ci
   AND cli.activo = 1
   AND cli.fecha_fin >= CURDATE()

LEFT JOIN categorias catCli
    ON catCli.id = cli.categoria_id

LEFT JOIN categorias catVeh
    ON catVeh.id = v.categoria_id

WHERE v.patente = ?

ORDER BY v.id DESC

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

$data = mysqli_fetch_assoc($result);

if ($data) {

    echo json_encode([

    'ok' => true,

    'categoria_id' =>
        (int)$data['categoria_id'],

    'tarifa_id' =>
        (int)$data['tarifa_id'],

    'categoria' =>
        $data['categoria']

]);

} else {

    echo json_encode([

        'ok' => false

    ]);

}