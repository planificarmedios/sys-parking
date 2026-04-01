<?php

$host = "localhost";
$user = "root";
$pass = "";            // tu password
$db   = "nombre_bd";   // TU base de datos

$fecha = date("Y-m-d_H-i-s");
$archivo = "backup_{$db}_{$fecha}.sql";

// Ruta temporal en el servidor
$ruta = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $archivo;

// Comando mysqldump (ruta típica XAMPP)
$mysqldump = '"C:\xampp\mysql\bin\mysqldump.exe"';

$cmd = "$mysqldump -h $host -u $user " . ($pass ? "-p$pass " : "") . "$db > \"$ruta\"";

// Ejecutar
system($cmd);

// Forzar descarga
header("Content-Type: application/sql");
header("Content-Disposition: attachment; filename=$archivo");
header("Content-Length: " . filesize($ruta));

readfile($ruta);

// Limpiar
unlink($ruta);
exit;
