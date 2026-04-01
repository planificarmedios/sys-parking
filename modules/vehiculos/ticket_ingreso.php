<?php

require __DIR__ . '/../../vendor/autoload.php';
include "../../config/database.php";

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

/* ==============================
   OBTENER ID
============================== */

if(!isset($_GET['id'])){
    die("ID no especificado");
}

$id = (int)$_GET['id'];

/* ==============================
   CONSULTA
============================== */

$q = mysqli_query($mysqli,"
SELECT
v.patente,
v.fecha_ingreso,
v.hora_ingreso,
t.descripcion tarifa
FROM vehiculos v
LEFT JOIN tarifas t ON t.id=v.tarifa_id
WHERE v.id='$id'
");

$veh = mysqli_fetch_assoc($q);

if(!$veh){
    die("Vehículo no encontrado");
}

/* ==============================
   DATOS
============================== */

$patente = strtoupper($veh['patente']);
$fecha = date("d/m/y", strtotime($veh['fecha_ingreso']));
$hora = date("H:i", strtotime($veh['hora_ingreso']));
$tarifa  = $veh['tarifa'];

$ticket = str_pad($id,6,"0",STR_PAD_LEFT);

/* ==============================
   IMPRESION
============================== */

try {

    $connector = new WindowsPrintConnector("Ticketeadora");
    $printer = new Printer($connector);

    /* ==============================
       ENCABEZADO
    ============================== */

    $printer->setJustification(Printer::JUSTIFY_CENTER);

    $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH | Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_EMPHASIZED);
    $printer->text("MARMAX\n");

    $printer->selectPrintMode(); // volver a modo normal

    $printer->feed(1);

    $printer->setEmphasis(true);
    $printer->text("ESTACIONAMIENTO\n");
    $printer->setEmphasis(false);

    $printer->text("------------------------------\n");

    /* ==============================
       DATOS
    ============================== */

    $printer->setJustification(Printer::JUSTIFY_LEFT);

    $printer->text("Ticket : ".$ticket."\n");
    $printer->text("Tarifa : ".$tarifa."\n");

    $printer->text("------------------------------\n");

    /* ==============================
       PATENTE
    ============================== */

    $printer->setJustification(Printer::JUSTIFY_CENTER);

    $printer->setEmphasis(true);
    $printer->text("PATENTE\n");
    $printer->setEmphasis(false);

    $printer->setTextSize(2,2);
    $printer->text($patente."\n");

    $printer->setTextSize(1,1);

    $printer->feed();

    /* ==============================
       INGRESO
    ============================== */

    $printer->text("Ingreso\n");

    $printer->setTextSize(2,2);
    $printer->text($hora."\n");

    $printer->setTextSize(1,1);
    $printer->text($fecha."\n");

    $printer->feed();

    $printer->text("------------------------------\n");

    /* ==============================
       CODIGO DE BARRAS
    ============================== */

    $printer->setBarcodeHeight(65);
    $printer->setBarcodeWidth(2);

    $printer->barcode($ticket, Printer::BARCODE_CODE39);

    $printer->text($ticket."\n");

    $printer->feed();

    /* ==============================
       MENSAJE
    ============================== */

    $printer->text("Conserve este ticket\n");
    $printer->text("Presente al retirar el vehiculo\n");

    $printer->feed(3);

    /* ==============================
       CORTE
    ============================== */

    $printer->cut();
    $printer->close();

} catch (Exception $e) {

    echo "Error al imprimir: " . $e->getMessage();
    exit;

}

/* ==============================
   REDIRECCION
============================== */

header("Location: /sys_parking/main.php?module=vehiculos");
exit;