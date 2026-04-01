<?php
session_start();
require_once "../../config/database.php";

if (empty($_SESSION['username']) && empty($_SESSION['password'])){
	echo "<meta http-equiv='refresh' content='0; url=index.php?alert=1'>";
}

else {


if ($_GET['act'] == 'insert') {

    $patente   = mysqli_real_escape_string($mysqli, strtoupper($_POST['patente']));
    $tarifa_id = (int) $_POST['tarifa_id'];
    $modo_ticket = $_POST['modo_ticket'] ?? 'preview';

    $query = mysqli_query($mysqli, "
        INSERT INTO vehiculos
        (patente, fecha_ingreso, hora_ingreso, tarifa_id, en_playa)
        VALUES
        ('$patente', CURDATE(), CURTIME(), '$tarifa_id', 1)
    ") or die(mysqli_error($mysqli));

    $vehiculo_id = mysqli_insert_id($mysqli);

    if ($modo_ticket == 'preview') {

        header("Location: ../../modules/vehiculos/ticket_ingreso.php?id=".$vehiculo_id);

    } else {

        header("Location: ../../modules/vehiculos/ticket_ingreso.php?id=".$vehiculo_id."&auto=1");

    }

    exit;
}

elseif ($_GET['act'] == 'update') {
   
        $id      = (int) $_POST['id'];
        $patente = mysqli_real_escape_string($mysqli, strtoupper($_POST['patente']));

        mysqli_query($mysqli, "
        UPDATE vehiculos
        SET patente='$patente'
        WHERE id='$id'
        ") or die(mysqli_error($mysqli));

        header("location: ../../main.php?module=vehiculos&alert=2");
    
}

elseif ($_GET['act'] == 'delete') {

    if (!isset($_GET['id'])) {
        die('ID no recibido');
    }

    $id = (int) $_GET['id'];

    mysqli_query($mysqli, "
        DELETE FROM vehiculos 
        WHERE id='$id'
    ") or die(mysqli_error($mysqli));

    error_log("DELETE vehiculo id=$id");

    header("location: ../../main.php?module=vehiculos&alert=3");
    exit;
}
    
elseif ($_GET['act'] == 'cobrar') {

  $vehiculo_id = (int)$_POST['id'];
  $total       = (float)$_POST['total'];
  $medio_cobro = mysqli_real_escape_string($mysqli, $_POST['medio_cobro']);

  $fecha_egreso = date('Y-m-d');
  $hora_egreso  = date('H:i:s');

  echo $_POST['id'];
  echo $_POST['total'];
  echo $_POST['medio_cobro'];
  echo $fecha_egreso;
  echo $hora_egreso;



  // ===============================
  // ACTUALIZAR VEHÍCULO
  // ===============================
  $update = mysqli_query($mysqli, "
    UPDATE vehiculos
    SET
      fecha_egreso = '$fecha_egreso',
      hora_egreso  = '$hora_egreso',
      monto_total  = '$total',
      medio_cobro  = '$medio_cobro',
      en_playa     = 0
    WHERE id = '$vehiculo_id'
  ");

  if ($update) {
    
    header("Location: ../../main.php?module=vehiculos&alert=success_cobro");
  } else {
    header("Location: ../../main.php?module=vehiculos&alert=error_cobro");
  }
}

}
?>