<?php

include "../../config/database.php";

$id = (int)$_GET['id'];

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

$veh=mysqli_fetch_assoc($q);

$patente=strtoupper($veh['patente']);
$fecha=date("d/m/Y",strtotime($veh['fecha_ingreso']));
$hora=substr($veh['hora_ingreso'],0,5);

$width=384;
$height=500;

$img=imagecreate($width,$height);

$white=imagecolorallocate($img,255,255,255);
$black=imagecolorallocate($img,0,0,0);

$y=20;

imagestring($img,5,110,$y,"ESTACIONAMIENTO",$black);
$y+=40;

imagestring($img,4,140,$y,"INGRESO",$black);
$y+=50;

imagestring($img,5,140,$y,$patente,$black);
$y+=60;

imagestring($img,4,120,$y,"Fecha: $fecha",$black);
$y+=30;

imagestring($img,4,130,$y,"Hora: $hora",$black);
$y+=50;

imagestring($img,4,150,$y,"Ticket",$black);
$y+=30;

imagestring($img,5,170,$y,$id,$black);
$y+=60;

imagestring($img,3,110,$y,"Conserve este ticket",$black);

header("Content-Type:image/png");
imagepng($img);
imagedestroy($img);

?>