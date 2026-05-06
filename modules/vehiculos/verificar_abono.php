<?php
session_start();
require_once "../../config/database.php";

header('Content-Type: application/json');

$patente = trim($_POST['patente'] ?? '');

$response = [
  'tiene_abono' => false
];

if ($patente === '') {
  echo json_encode($response);
  exit;
}

$sql = "
SELECT
  a.tarifa_id,
  a.categoria_id,
  t.descripcion AS tarifa,
  a.patente,
  a.fecha_fin
FROM clientes a
INNER JOIN tarifas t ON t.id = a.tarifa_id
WHERE 
  a.activo = 1
  AND a.patente = ?
ORDER BY a.fecha_fin DESC, a.id DESC
LIMIT 1
";



$stmt = $mysqli->prepare($sql);
$stmt->bind_param('s', $patente);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {

  $fechaFinRaw = $row['fecha_fin']; // viene de la DB: Y-m-d o Y-m-d H:i:s
  $hoy = date('Y-m-d');

$vigente = ($fechaFinRaw >= $hoy);

  $response = [
  'tiene_abono'  => true,
  'tarifa_id'    => (int)$row['tarifa_id'],
  'categoria_id' => (int)$row['categoria_id'],
  'tarifa'       => $row['tarifa'],
  'fecha_egreso' => date('d/m/Y', strtotime($fechaFinRaw)),
  'vigente'      => $vigente
];
}

echo json_encode($response);