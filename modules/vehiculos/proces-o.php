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
    $categoria_id = (int) $_POST['categoria_id'];
    $modo_ticket = $_POST['modo_ticket'] ?? 'preview';

    $query = mysqli_query($mysqli, "
        INSERT INTO vehiculos
        (patente, fecha_ingreso, hora_ingreso, tarifa_id, en_playa, categoria_id)
        VALUES
        ('$patente', CURDATE(), CURTIME(), '$tarifa_id', 1, '$categoria_id')
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

    /* =========================================================
       DATOS POST
    ========================================================= */

    $id = (int) $_POST['id'];

    $medio_cobro = mysqli_real_escape_string(
        $mysqli,
        trim($_POST['medio_cobro'])
    );

    /* =========================================================
       TRAER VEHÍCULO
    ========================================================= */

    $qVeh = mysqli_query($mysqli, "
        SELECT
            v.*,
            c.nombre AS categoria
        FROM vehiculos v

        LEFT JOIN categorias c
            ON c.id = v.categoria_id

        WHERE v.id = $id
        LIMIT 1
    ");

    $veh = mysqli_fetch_assoc($qVeh);

    if (!$veh) {

        die('Vehículo no encontrado');

    }

    /* =========================================================
       TIEMPO TRANSCURRIDO
    ========================================================= */

    $ingreso = strtotime(
        $veh['fecha_ingreso'] . ' ' . $veh['hora_ingreso']
    );

    $egreso = time();

    $minutos_totales = ceil(
        ($egreso - $ingreso) / 60
    );

    if ($minutos_totales < 1) {
        $minutos_totales = 1;
    }

    $horas_completas = floor(
        $minutos_totales / 60
    );

    $minutos_resto = $minutos_totales % 60;

    /* =========================================================
       VERIFICAR ABONO VIGENTE
    ========================================================= */

    $abonoVigente = false;

    $qAbono = mysqli_query($mysqli, "
        SELECT id
        FROM clientes
        WHERE patente = '".$veh['patente']."'
          AND activo = 1
          AND fecha_fin >= CURDATE()
        LIMIT 1
    ");

    if (mysqli_num_rows($qAbono) > 0) {

        $abonoVigente = true;

    }

    /* =========================================================
       SI TIENE ABONO → TOTAL 0
    ========================================================= */

    $total = 0;

    $detalle_json = [];

    if ($abonoVigente) {

        $detalle_json[] = [

            'descripcion' => 'Abono vigente',
            'cantidad'    => 1,
            'precio'      => 0,
            'subtotal'    => 0

        ];

    }

    /* =========================================================
       CALCULAR TARIFA MÁS CONVENIENTE
    ========================================================= */

   
else {

    $categoria_id = (int) $veh['categoria_id'];

    $qTarifas = mysqli_query($mysqli, "
        SELECT *
        FROM tarifas
        WHERE activo = 1
          AND categoria_id = $categoria_id
        ORDER BY monto ASC
    ");

    $tarifas = [];

    while ($t = mysqli_fetch_assoc($qTarifas)) {

        $tarifas[] = $t;

    }

    $escenarios = [];

    /* =====================================================
       1. ESCENARIOS INDIVIDUALES
    ===================================================== */

    foreach ($tarifas as $t) {

        /* =========================================
           TARIFAS MINUTOS
        ========================================= */

        if ($t['unidad'] == 'minutos') {

            $bloque = (int) $t['valor'];

            if ($bloque <= 0) {
                continue;
            }

            $cantidad = ceil(
                $minutos_totales / $bloque
            );

            $subtotal = (
                $cantidad * $t['monto']
            );

            $escenarios[] = [

                'nombre' => $t['descripcion'],

                'items' => [
                    [
                        'descripcion' => $t['descripcion'],
                        'cantidad'    => $cantidad,
                        'precio'      => $t['monto'],
                        'subtotal'    => $subtotal
                    ]
                ],

                'total' => $subtotal

            ];
        }

        /* =========================================
           TARIFAS HORAS
        ========================================= */

        elseif ($t['unidad'] == 'horas') {

            $bloque = (
                ((int) $t['valor']) * 60
            );

            if ($bloque <= 0) {
                continue;
            }

            $cantidad = ceil(
                $minutos_totales / $bloque
            );

            $subtotal = (
                $cantidad * $t['monto']
            );

            $escenarios[] = [

                'nombre' => $t['descripcion'],

                'items' => [
                    [
                        'descripcion' => $t['descripcion'],
                        'cantidad'    => $cantidad,
                        'precio'      => $t['monto'],
                        'subtotal'    => $subtotal
                    ]
                ],

                'total' => $subtotal

            ];
        }

        /* =========================================
           ESTADÍA COMPLETA / TOPE
        ========================================= */

        elseif (
            $t['unidad'] == 'fijo'
            && $t['valor'] == 1
        ) {

            $escenarios[] = [

                'nombre' => $t['descripcion'],

                'items' => [
                    [
                        'descripcion' => $t['descripcion'],
                        'cantidad'    => 1,
                        'precio'      => $t['monto'],
                        'subtotal'    => $t['monto']
                    ]
                ],

                'total' => $t['monto']

            ];
        }
    }

    /* =====================================================
       2. COMBINACIONES INTELIGENTES
       EJ:
       30 min + 15 min
       1 hora + 15 min
       etc
    ===================================================== */

    $fraccionables = [];

    foreach ($tarifas as $t) {

        if (
            $t['es_tarifa_fraccionable'] == 1
            &&
            (
                $t['unidad'] == 'minutos'
                ||
                $t['unidad'] == 'horas'
            )
        ) {

            $fraccionables[] = $t;

        }
    }

    foreach ($fraccionables as $a) {

        foreach ($fraccionables as $b) {

            $bloqueA = (
                $a['unidad'] == 'horas'
            )
            ? ((int)$a['valor']) * 60
            : (int)$a['valor'];

            $bloqueB = (
                $b['unidad'] == 'horas'
            )
            ? ((int)$b['valor']) * 60
            : (int)$b['valor'];

            if (
                $bloqueA <= 0
                || $bloqueB <= 0
            ) {
                continue;
            }

            for ($cantA = 0; $cantA <= 24; $cantA++) {

                for ($cantB = 0; $cantB <= 24; $cantB++) {

                    if (
                        $cantA == 0
                        && $cantB == 0
                    ) {
                        continue;
                    }

                    $cubierto = (
                        ($cantA * $bloqueA)
                        +
                        ($cantB * $bloqueB)
                    );

                    if (
                        $cubierto < $minutos_totales
                    ) {
                        continue;
                    }

                    $items = [];

                    $subtotal = 0;

                    if ($cantA > 0) {

                        $subA = (
                            $cantA * $a['monto']
                        );

                        $subtotal += $subA;

                        $items[] = [

                            'descripcion' => $a['descripcion'],
                            'cantidad'    => $cantA,
                            'precio'      => $a['monto'],
                            'subtotal'    => $subA

                        ];
                    }

                    if ($cantB > 0) {

                        $subB = (
                            $cantB * $b['monto']
                        );

                        $subtotal += $subB;

                        $items[] = [

                            'descripcion' => $b['descripcion'],
                            'cantidad'    => $cantB,
                            'precio'      => $b['monto'],
                            'subtotal'    => $subB

                        ];
                    }

                    $escenarios[] = [

                        'nombre' =>
                            $a['descripcion']
                            .' + '.
                            $b['descripcion'],

                        'items' => $items,

                        'total' => $subtotal

                    ];
                }
            }
        }
    }

    /* =====================================================
       ORDENAR MÁS BARATO PRIMERO
    ===================================================== */

    usort($escenarios, function($a, $b) {

        return $a['total'] <=> $b['total'];

    });

    /* =====================================================
       TOMAR EL MÁS BARATO
    ===================================================== */

    $mejor = $escenarios[0] ?? null;

    if ($mejor) {

        $total = $mejor['total'];

        $detalle_json = $mejor['items'];

    }

    /* =====================================================
       DEBUG VISUAL
    ===================================================== */

    echo '

    <div class="alert alert-info">

        <h4>
            Debug cálculo tarifario
        </h4>

        <p>
            Minutos totales:
            <b>'.$minutos_totales.'</b>
        </p>

        <table class="table table-bordered">

            <thead>

                <tr>

                    <th>Alternativa</th>
                    <th>Total</th>

                </tr>

            </thead>

            <tbody>

    ';

    foreach ($escenarios as $esc) {

        $bg = (
            $esc['total'] == $total
        )
        ? 'style="background:#dff0d8;font-weight:bold;"'
        : '';

        echo '

        <tr '.$bg.'>

            <td>

        ';

        foreach ($esc['items'] as $i) {

            echo '
                '.$i['cantidad'].' x '.$i['descripcion'].'
                ($'.number_format($i['subtotal'],2).')
                <br>
            ';
        }

        echo '

            </td>

            <td>
                $'.number_format($esc['total'],2).'
            </td>

        </tr>

        ';
    }

    echo '

            </tbody>

        </table>

    </div>

    ';
}

    /* =========================================================
       GUARDAR EN CAJA
    ========================================================= */

    $concepto = (
        $abonoVigente
    )
    ? 'Salida con abono vigente'
    : 'Cobro estacionamiento';

    $detalle = mysqli_real_escape_string(
        $mysqli,
        json_encode(
            $detalle_json,
            JSON_UNESCAPED_UNICODE
        )
    );

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
            '".$veh['id']."',
            '".($veh['cliente_id'] ?? 0)."',
            '".$veh['patente']."',
            '".$veh['categoria_id']."',
            '".$veh['tarifa_id']."',
            '$concepto',
            '$medio_cobro',
            '$total',
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
}
?>