<?php

session_start();

require_once "../../config/database.php";

if (
    empty($_SESSION['username'])
    &&
    empty($_SESSION['password'])
) {

    echo "
    <meta http-equiv='refresh'
          content='0;
          url=index.php?alert=1'>";

    exit;
}

/* =========================================================
   INSERTAR VEHÍCULO
========================================================= */

if ($_GET['act'] == 'insert') {

    $patente = mysqli_real_escape_string(
        $mysqli,
        strtoupper(trim($_POST['patente']))
    );

    $tarifa_id = (int) $_POST['tarifa_id'];

    $categoria_id = (int) $_POST['categoria_id'];

    $modo_ticket = $_POST['modo_ticket'] ?? 'preview';

    mysqli_query($mysqli, "

        INSERT INTO vehiculos
        (
            patente,
            fecha_ingreso,
            hora_ingreso,
            tarifa_id,
            categoria_id,
            en_playa
        )

        VALUES
        (
            '$patente',
            CURDATE(),
            CURTIME(),
            '$tarifa_id',
            '$categoria_id',
            1
        )

    ") or die(mysqli_error($mysqli));

    $vehiculo_id = mysqli_insert_id($mysqli);

    if ($modo_ticket == 'preview') {

        header("Location: ../../modules/vehiculos/ticket_ingreso.php?id=".$vehiculo_id);

    } else {

        header("Location:../../modules/vehiculos/ticket_ingreso.php?id=".$vehiculo_id."&auto=1");
    }

    exit;
}

/* =========================================================
   EDITAR VEHÍCULO
========================================================= */

elseif ($_GET['act'] == 'update') {

    $id = (int) $_POST['id'];

    $patente = mysqli_real_escape_string(
        $mysqli,
        strtoupper(trim($_POST['patente']))
    );

    mysqli_query($mysqli, "

        UPDATE vehiculos

        SET patente = '$patente'

        WHERE id = '$id'

    ") or die(mysqli_error($mysqli));

    header("Location: ../../main.php?module=vehiculos&alert=2");

    exit;
}

/* =========================================================
   ELIMINAR VEHÍCULO
========================================================= */

elseif ($_GET['act'] == 'delete') {

    $id = (int) $_GET['id'];

    mysqli_query($mysqli, "

        DELETE FROM vehiculos

        WHERE id = '$id'

    ") or die(mysqli_error($mysqli));

    header("Location: ../../main.php?module=vehiculos&alert=3");

    exit;
}



elseif ($_GET['act'] == 'cobrar') {

    if (!isset($_POST['id'])) {
          die('ID no recibido');
    }

      $id = (int) $_POST['id'];
      $patente  =  $_POST['patente'];
      $categoria_hidden = (int) $_POST['categoria_hidden'];
      $medio_cobro = $_POST['medio_cobro'];
      $total_hidden = (float) $_POST['total_hidden'];
      $tarifa_id_hidden = (int) $_POST['tarifa_id_hidden'];

      echo "<script>

        console.log('ID:', ".json_encode($id).");
        console.log('PATENTE:', ".json_encode($patente).");
        console.log('CATEGORIA_ID:', ".json_encode($categoria_hidden).");
        console.log('MEDIO_COBRO:', ".json_encode($medio_cobro).");
        console.log('total_hidden:', ".json_encode($total_hidden).");
        console.log('TARIFA_ID:', ".json_encode($tarifa_id_hidden).");

        </script>";

    
      mysqli_query($mysqli, "
        INSERT INTO caja
        (
            vehiculo_id,
            cliente_id,
            patente,
            categoria_id,
            tarifa_id,
            concepto,
            medio_cobro,
            monto,
            detalle,
            fecha_movimiento
        )
        VALUES
        (
            '".$_POST['id']."',
            0,
            '".$patente."',
            '".$categoria_hidden."',
            '".$tarifa_id_hidden."',
            '$concepto',
            '$medio_cobro',
            '".$total_hidden."',
            '$detalle',
            NOW()
        )
    ") or die(mysqli_error($mysqli));

    /* =========================================================
       REGISTRAR EGRESO
    ========================================================= */

    mysqli_query($mysqli, "
        UPDATE vehiculos
        SET
            fecha_egreso = CURDATE(),
            hora_egreso = CURTIME(),
            estado = 'finalizado'
        WHERE id = $id
    ") or die(mysqli_error($mysqli));

    /* =========================================================
       REDIRECCIONAR
    ========================================================= */

    header("Location: ../../main.php?module=vehiculos&alert=salida_ok");
    exit;



}
?>