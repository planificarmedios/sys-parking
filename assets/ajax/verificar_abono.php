<?php
// ajax/verificar_abono.php

require_once '../config/database.php';

header('Content-Type: application/json');

$patente = $_POST['patente'] ?? '';
$patente = trim($patente);

$response = [
    'tiene_abono' => false
];

if ($patente === '') {
    echo json_encode($response);
    exit;
}

// Buscar el último abono de esa patente
$sql = "
    SELECT 
        a.tarifa_id,
        t.descripcion AS tarifa,
        a.fecha_fin
    FROM abonos a
    INNER JOIN tarifas t ON t.id = a.tarifa_id
    WHERE a.patente = ?
      AND a.activo = 1
    ORDER BY a.fecha_fin DESC
    LIMIT 1
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param('s', $patente);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {

    $hoy = date('Y-m-d');
    $vigente = ($row['fecha_fin'] >= $hoy);

    $response = [
        'tiene_abono' => true,
        'tarifa_id'   => (int)$row['tarifa_id'],
        'tarifa'      => $row['tarifa'],
        'fecha_fin'   => $row['fecha_fin'],
        'vigente'     => $vigente
    ];
}

echo json_encode($response);