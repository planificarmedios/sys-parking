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

    $tarifa_id =
        (int) $_POST['tarifa_id'];

    $categoria_id =
        (int) $_POST['categoria_id'];

    $modo_ticket =
        $_POST['modo_ticket'] ?? 'preview';

    $medio_cobro =
        mysqli_real_escape_string(
            $mysqli,
            $_POST['medio_cobro'] ?? ''
        );

    /* =========================================
       OBTENER TARIFA
    ========================================= */

    $queryTarifa = mysqli_query($mysqli, "

        SELECT
            descripcion,
            monto,
            es_tope_diario

        FROM tarifas

        WHERE id = '$tarifa_id'

        LIMIT 1

    ");

    $tarifa = mysqli_fetch_assoc($queryTarifa);

    if (!$tarifa) {

        die('Tarifa inexistente');
    }

    $descripcionTarifa =
        $tarifa['descripcion'];

    $montoTarifa =
        (float)$tarifa['monto'];

    $esEstadia =
        (int)$tarifa['es_tope_diario'];

    /* =========================================
       INSERT VEHICULO
    ========================================= */

    mysqli_query($mysqli, "

        INSERT INTO vehiculos
        (
            patente,
            fecha_ingreso,
            hora_ingreso,
            tarifa_id,
            categoria_id,
            en_playa,
            pagado,
            estado
        )

        VALUES
        (
            '$patente',
            CURDATE(),
            CURTIME(),
            '$tarifa_id',
            '$categoria_id',
            1,
            ".($esEstadia ? 1 : 0).",
            'activo'
        )

    ") or die(mysqli_error($mysqli));

    $vehiculo_id =
        mysqli_insert_id($mysqli);

    /* =========================================
       SI ES ESTADIA:
       REGISTRAR COBRO EN CAJA
    ========================================= */

    if ($esEstadia) {

        $concepto =
            'Ingreso estadía';

        $detalle =
            'Cobro automático al ingreso';

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
                fecha_movimiento,
                detalle
            )

            VALUES
            (
                '$vehiculo_id',
                NULL,
                '$patente',
                '$categoria_id',
                '$tarifa_id',
                '$concepto',
                '$medio_cobro',
                '$montoTarifa',
                NOW(),
                '$detalle'
            )

        ") or die(mysqli_error($mysqli));
    }

    /* =========================================
       REDIRECCION
    ========================================= */

    if ($modo_ticket == 'preview') {

        header(
            "Location: ../../modules/vehiculos/ticket_ingreso.php?id=".$vehiculo_id
        );

    } else {

        header(
            "Location: ../../modules/vehiculos/ticket_ingreso.php?id=".$vehiculo_id."&auto=1"
        );
    }

    exit;
}

/* =========================================================
   EDITAR VEHÍCULO
========================================================= */

elseif ($_GET['act'] == 'update') {

    $id =
        (int) $_POST['id'];

    $patente =
        mysqli_real_escape_string(
            $mysqli,
            strtoupper(trim($_POST['patente']))
        );

    mysqli_query($mysqli, "

        UPDATE vehiculos

        SET patente = '$patente'

        WHERE id = '$id'

    ") or die(mysqli_error($mysqli));

    header(
        "Location: ../../main.php?module=vehiculos&alert=2"
    );

    exit;
}

/* =========================================================
   ELIMINAR VEHÍCULO
========================================================= */

elseif ($_GET['act'] == 'delete') {

    $id =
        (int) $_GET['id'];

    mysqli_query($mysqli, "

        DELETE FROM vehiculos

        WHERE id = '$id'

    ") or die(mysqli_error($mysqli));

    header(
        "Location: ../../main.php?module=vehiculos&alert=3"
    );

    exit;
}

/* =========================================================
   COBRAR / EGRESAR
========================================================= */

elseif ($_GET['act'] == 'cobrar') {

    if (!isset($_POST['id'])) {

        die('ID no recibido');
    }

    $id =
        (int) $_POST['id'];

    $patente =
        mysqli_real_escape_string(
            $mysqli,
            $_POST['patente']
        );

    $categoria_hidden =
        (int) $_POST['categoria_hidden'];

    $medio_cobro =
    mysqli_real_escape_string(
        $mysqli,
        $_POST['medio_cobro'] ?? ''
    );

    $total_hidden =
        (float) $_POST['total_hidden'];

    $tarifa_id_hidden =
        (int) $_POST['tarifa_id_hidden'];

    /* =========================================
       OBTENER VEHICULO
    ========================================= */

    $queryVehiculo = mysqli_query($mysqli, "

        SELECT *

        FROM vehiculos

        WHERE id = '$id'

        LIMIT 1

    ");

    $vehiculo =
        mysqli_fetch_assoc($queryVehiculo);

    if (!$vehiculo) {

        die('Vehículo inexistente');
    }

    /* =========================================
       VEHICULO PREPAGADO
       SOLO EGRESA
    ========================================= */

    if ((int)$vehiculo['pagado'] === 1) {

        mysqli_query($mysqli, "

            UPDATE vehiculos

            SET

                fecha_egreso = CURDATE(),
                hora_egreso = CURTIME(),
                en_playa = 0,
                estado = 'finalizado'

            WHERE id = '$id'

        ") or die(mysqli_error($mysqli));

        header(
            "Location: ../../main.php?module=vehiculos&alert=salida_ok"
        );

        exit;
    }

    /* =========================================
       REGISTRAR COBRO EN CAJA
    ========================================= */

    $concepto =
        'Cobro estacionamiento';

    $detalle =
        'Cobro generado al egreso';

    var_dump($medio_cobro);

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
            '$id',
            0,
            '$patente',
            '$categoria_hidden',
            '$tarifa_id_hidden',
            '$concepto',
            '$medio_cobro',
            '$total_hidden',
            '$detalle',
            NOW()
        )

    ") or die(mysqli_error($mysqli));

    /* =========================================
       REGISTRAR EGRESO
    ========================================= */

    mysqli_query($mysqli, "

        UPDATE vehiculos

        SET

            fecha_egreso = CURDATE(),
            hora_egreso = CURTIME(),
            en_playa = 0,
            estado = 'finalizado'

        WHERE id = '$id'

    ") or die(mysqli_error($mysqli));

    /* =========================================
       REDIRECCION
    ========================================= */

    header(
        "Location: ../../main.php?module=vehiculos&alert=salida_ok"
    );

    exit;
}

?>