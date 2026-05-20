<?php

session_start();

require_once "../../config/database.php";

if ($_GET['act'] == 'insert_manual') {

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $tipo_ingreso = mysqli_real_escape_string(
            $mysqli,
            trim($_POST['tipo_ingreso'])
        );

        $patente = isset($_POST['patente'])
            ? strtoupper(
                mysqli_real_escape_string(
                    $mysqli,
                    trim($_POST['patente'])
                )
            )
            : null;

        $categoria_id = !empty($_POST['categoria_id'])
            ? (int) $_POST['categoria_id']
            : null;

        $tarifa_id = !empty($_POST['tarifa_id'])
            ? (int) $_POST['tarifa_id']
            : null;

        $concepto_manual = isset($_POST['concepto'])
            ? mysqli_real_escape_string(
                $mysqli,
                trim($_POST['concepto'])
            )
            : '';

        $medio_cobro = mysqli_real_escape_string(
            $mysqli,
            trim($_POST['medio_cobro'])
        );

        $monto = (float) $_POST['monto'];
        $monto = str_replace('.', '', $monto);
        $monto = str_replace(',', '.', $monto);
        $monto = (float) $monto;

        $detalle = isset($_POST['detalle'])
            ? mysqli_real_escape_string(
                $mysqli,
                trim($_POST['detalle'])
            )
            : '';

        $concepto = '';

        /* =========================================
           CONCEPTO AUTOMÁTICO DESDE TARIFA
        ========================================= */

        if (
            $tipo_ingreso == 'abono'
            &&
            !empty($tarifa_id)
        ) {

            $qTarifa = mysqli_query(
                $mysqli,
                "SELECT descripcion
                 FROM tarifas
                 WHERE id = '$tarifa_id'
                 LIMIT 1"
            );

            if ($rowTarifa = mysqli_fetch_assoc($qTarifa)) {

                $concepto =
                    'Ingreso manual - '
                    .$rowTarifa['descripcion'];

            } else {

                $concepto =
                    'Ingreso manual';

            }

        }

        /* =========================================
           CONCEPTO MANUAL
        ========================================= */

        else {

            $concepto = $concepto_manual;

        }

        /* =========================================
           VALIDACIONES
        ========================================= */

        if ($monto < 0) {

            header(
                "location: ../../main.php?module=cajas&alert=error"
            );

            exit;

        }

        if (
            $tipo_ingreso == 'manual'
            &&
            empty($concepto)
        ) {

            header(
                "location: ../../main.php?module=form_cajas&form=manual&alert=concepto"
            );

            exit;

        }

        $tipo_movimiento = $_POST['tipo_movimiento'];
        if ($tipo_movimiento == 'egreso') {    $monto = $monto * -1;

}

        /* =========================================
           INSERT
        ========================================= */

        $query = mysqli_query($mysqli, "

            INSERT INTO caja (

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

            VALUES (

                0,
                NULL,
                ".($patente ? "'$patente'" : "NULL").",
                ".($categoria_id ? "'$categoria_id'" : "NULL").",
                ".($tarifa_id ? "'$tarifa_id'" : "NULL").",
                '$concepto',
                '$medio_cobro',
                '$monto',
                NOW(),
                '$detalle'

            )

        ");

        /* =========================================
           REDIRECCIÓN
        ========================================= */

        if ($query) {

            header("location: ../../main.php?module=caja&alert=1");

        } else {

            header("location: ../../main.php?module=cajas&alert=2");

        }

    }

}

?>