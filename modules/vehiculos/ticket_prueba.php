<?php

require __DIR__ . '/../../vendor/autoload.php';
include "../../config/database.php";

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

/* ==============================
   OBTENER ID
============================== */

if (!isset($_GET['id'])) {
    die("ID no especificado");
}

$id = (int)$_GET['id'];

/* ==============================
   CONSULTA
============================== */

$q = mysqli_query($mysqli, "
SELECT
    v.patente,
    v.fecha_ingreso,
    v.hora_ingreso,
    t.descripcion tarifa
FROM vehiculos v
LEFT JOIN tarifas t ON t.id = v.tarifa_id
WHERE v.id = '$id'
");

$veh = mysqli_fetch_assoc($q);

if (!$veh) {
    die("Vehículo no encontrado");
}

/* ==============================
   DATOS
============================== */

$patente = strtoupper($veh['patente']);
$fecha   = date("d/m/Y", strtotime($veh['fecha_ingreso']));
$hora    = date("H:i", strtotime($veh['hora_ingreso']));
$tarifa  = $veh['tarifa'];

$ticket = str_pad($id, 6, "0", STR_PAD_LEFT);

/* =====================================================
   PREVIEW HTML / PDF NAVEGADOR
===================================================== */

if (isset($_GET['preview'])) {
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Ticket</title>

<style>

body{
    background:#e9ecef;
    font-family: monospace;
    margin:0;
    padding:30px;
}

.ticket-wrapper{
    display:flex;
    justify-content:center;
}

.ticket{

    width:320px;
    background:#fff;
    padding:20px;

    box-shadow:
        0 0 10px rgba(0,0,0,.15);

    border-radius:8px;

    text-align:center;
}

.logo{
    font-size:28px;
    font-weight:bold;
    letter-spacing:2px;
}

.sub{
    font-size:14px;
    margin-top:4px;
    color:#555;
}

hr{
    border:none;
    border-top:1px dashed #999;
    margin:15px 0;
}

.label{
    font-size:12px;
    color:#666;
    margin-bottom:4px;
}

.patente{
    font-size:42px;
    font-weight:bold;
    letter-spacing:3px;
    margin:10px 0;
}

.hora{
    font-size:30px;
    font-weight:bold;
}

.fecha{
    font-size:16px;
    margin-top:5px;
}

.tarifa{
    font-size:16px;
    font-weight:bold;
}

.ticket-id{
    margin-top:10px;
    font-size:16px;
}

.footer{
    margin-top:20px;
    font-size:12px;
    color:#666;
}

</style>

</head>

<body>

<div class="ticket-wrapper">

    <div class="ticket">

        <div class="logo">
            MARMAX
        </div>

        <div class="sub">
            ESTACIONAMIENTO
        </div>

        <hr>

        <div class="ticket-id">
            Ticket #<?= $ticket ?>
        </div>

        <div class="tarifa">
            <?= htmlspecialchars($tarifa) ?>
        </div>

        <hr>

        <div class="label">
            PATENTE
        </div>

        <div class="patente">
            <?= htmlspecialchars($patente) ?>
        </div>

        <hr>

        <div class="label">
            INGRESO
        </div>

        <div class="hora">
            <?= $hora ?>
        </div>

        <div class="fecha">
            <?= $fecha ?>
        </div>

        <hr>

        <div class="footer">
            Conserve este ticket<br>
            Presente al retirar el vehículo
        </div>

    </div>

</div>

</body>
</html>




<?php
exit;
}

/* =====================================================
   IMPRESIÓN TÉRMICA ESC/POS
===================================================== */

try {

    $connector = new WindowsPrintConnector("POS-58");
    $printer = new Printer($connector);

    $printer->setJustification(Printer::JUSTIFY_CENTER);

    $printer->text("MARMAX\n");
    $printer->text("ESTACIONAMIENTO\n");
    $printer->text("--------------------------\n");

    $printer->text("Ticket: $ticket\n");
    $printer->text("$tarifa\n");

    $printer->text("--------------------------\n");

    $printer->setTextSize(2,2);
    $printer->text("$patente\n");

    $printer->setTextSize(1,1);

    $printer->text("\nIngreso\n");
    $printer->text("$hora\n");
    $printer->text("$fecha\n");

    $printer->text("--------------------------\n");

    $printer->text("Conserve este ticket\n");

    $printer->feed(4);
    $printer->cut();

    $printer->close();

    header("Location: ../../main.php?module=vehiculos");

} catch (Exception $e) {

    echo "Error al imprimir: " . $e->getMessage();

}