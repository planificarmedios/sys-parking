<?php

require '../../config/database.php';

header('Content-Type: application/json');

/* =========================================
   VALIDAR PATENTE
========================================= */

$patente = trim(
    strtoupper($_POST['patente'] ?? '')
);

if ($patente == '') {

    echo json_encode([
        'error' => true
    ]);

    exit;
}

/* =========================================
   VERIFICAR VEHÍCULO ABIERTO
========================================= */

$sqlVehiculo = "

SELECT id

FROM vehiculos

WHERE patente COLLATE utf8mb4_general_ci = ?
AND fecha_egreso IS NULL

LIMIT 1

";

$stmtVehiculo = mysqli_prepare(
    $mysqli,
    $sqlVehiculo
);

mysqli_stmt_bind_param(
    $stmtVehiculo,
    "s",
    $patente
);

mysqli_stmt_execute($stmtVehiculo);

$resultVehiculo =
    mysqli_stmt_get_result($stmtVehiculo);

$vehiculoAbierto =
    mysqli_num_rows($resultVehiculo) > 0;

/* =========================================
   BUSCAR CLIENTE
========================================= */

$sqlCliente = "

SELECT

    c.id,

    c.patente,

    c.tarifa_id,

    c.categoria_id,

    c.fecha_fin,

    c.activo,

    t.descripcion AS tarifa

FROM clientes c

LEFT JOIN tarifas t
    ON t.id = c.tarifa_id

WHERE c.patente COLLATE utf8mb4_general_ci = ?
AND c.activo = 1

ORDER BY c.fecha_fin DESC,
         c.id DESC

LIMIT 1

";

$stmtCliente = mysqli_prepare(
    $mysqli,
    $sqlCliente
);

mysqli_stmt_bind_param(
    $stmtCliente,
    "s",
    $patente
);

mysqli_stmt_execute($stmtCliente);

$resultCliente =
    mysqli_stmt_get_result($stmtCliente);

$cliente =
    mysqli_fetch_assoc($resultCliente);

/* =========================================
   RESPUESTA
========================================= */

if ($cliente) {

    $vigente =
        strtotime($cliente['fecha_fin'])
        >= strtotime(date('Y-m-d'));

    echo json_encode([

        'tiene_abono' => true,

        'vigente' => $vigente,

        'categoria_id' =>
            $cliente['categoria_id'],

        'tarifa_id' =>
            $cliente['tarifa_id'],

        'tarifa' =>
            $cliente['tarifa'],

        'fecha_egreso' =>
            date(
                'd/m/Y',
                strtotime($cliente['fecha_fin'])
            ),

        'vehiculo_abierto' =>
            $vehiculoAbierto
    ]);

} else {

    echo json_encode([

        'tiene_abono' => false,

        'vehiculo_abierto' =>
            $vehiculoAbierto
    ]);
}