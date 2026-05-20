<?php

function calcularTarifaVehiculo($mysqli, $vehiculo_id)
{
    /* =========================================
       VEHÍCULO
    ========================================= */

    $qVeh = mysqli_query($mysqli, "

        SELECT
            v.*,

            COALESCE(
                cli.categoria_id,
                v.categoria_id
            ) AS categoria_final_id,

            cat.nombre AS categoria,

            t.descripcion AS tarifa

        FROM vehiculos v

        LEFT JOIN clientes cli
            ON cli.patente COLLATE utf8mb4_unicode_ci =
               v.patente COLLATE utf8mb4_unicode_ci
           AND cli.activo = 1

        LEFT JOIN categorias cat
            ON cat.id = COALESCE(
                cli.categoria_id,
                v.categoria_id
            )

        LEFT JOIN tarifas t
            ON t.id = v.tarifa_id

        WHERE v.id = $vehiculo_id

        LIMIT 1
    ");

    $veh = mysqli_fetch_assoc($qVeh);

    if (!$veh) {
        return null;
    }

    /* =========================================
       TIEMPO
    ========================================= */

    $ingreso = strtotime(
        $veh['fecha_ingreso'].' '.$veh['hora_ingreso']
    );

    $egreso = time();

    $minutos_totales = ceil(
        ($egreso - $ingreso) / 60
    );

    if ($minutos_totales < 1) {
        $minutos_totales = 1;
    }

    /* =========================================
       ABONO VIGENTE
    ========================================= */

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

    /* =========================================
       SI TIENE ABONO
    ========================================= */

    if ($abonoVigente) {

        return [

            'vehiculo' => $veh,

            'minutos_totales' => $minutos_totales,

            'total' => 0,

            'detalle' => [
                [
                    'descripcion' => 'Abono vigente',
                    'cantidad' => 1,
                    'precio' => 0,
                    'subtotal' => 0
                ]
            ],

            'alternativas' => [
                [
                    'nombre' => 'Abono vigente',

                    'abono_base' => $veh['tarifa'],

                    'total' => 0,

                    'items' => [
                        [
                            'descripcion' => 'Abono vigente',
                            'cantidad' => 1,
                            'precio' => 0,
                            'subtotal' => 0
                        ]
                    ]
                ]
            ]

        ];
    }

    /* =========================================
       TARIFAS POR CATEGORÍA
    ========================================= */

    $categoria_id = (int) $veh['categoria_final_id'];

    $qTarifas = mysqli_query($mysqli, "
        SELECT *
        FROM tarifas
        WHERE activo = 1
          AND categoria_id = $categoria_id
    ");

    $tarifas = [];

    while ($t = mysqli_fetch_assoc($qTarifas)) {

        $tarifas[] = $t;

    }

    $escenarios = [];

    $hashes = [];

    $totalesExistentes = [];

    /* =========================================
       TARIFAS DIRECTAS
    ========================================= */

    foreach ($tarifas as $t) {

        $unidad = $t['unidad'];

        $valor = (int) $t['valor'];

        $monto = (float) $t['monto'];

        $subtotal = 0;

        $cantidad = 1;

        if ($unidad == 'minutos') {

            $cantidad = ceil(
                $minutos_totales / $valor
            );

            $subtotal = $cantidad * $monto;

        }

        elseif ($unidad == 'horas') {

            $bloque = $valor * 60;

            $cantidad = ceil(
                $minutos_totales / $bloque
            );

            $subtotal = $cantidad * $monto;

        }

        elseif ($unidad == 'fijo') {

            $subtotal = $monto;

        }

        else {

            continue;

        }

        /* EVITAR TOTALES REPETIDOS */

        if (isset($totalesExistentes[$subtotal])) {
            continue;
        }

        $totalesExistentes[$subtotal] = true;

        $escenarios[] = [

            'nombre' => $t['descripcion'],

            'abono_base' => $veh['tarifa'],

            'total' => $subtotal,

            'items' => [
                [
                    'descripcion' => $t['descripcion'],
                    'cantidad' => $cantidad,
                    'precio' => $monto,
                    'subtotal' => $subtotal
                ]
            ]
        ];
    }

    /* =========================================
   MOTOR INTELIGENTE
   TARIFA BASE + FRACCIÓN
========================================= */

$tarifasBase = [];
$tarifasFraccion = [];
$topeDiario = null;

/* =========================================
   SEPARAR TARIFAS
========================================= */

foreach ($tarifas as $t) {

    /* TOPE */

    if ((int)$t['es_tope_diario'] === 1) {

        $topeDiario = $t;

    }

    /* FRACCIONABLES */

    if (
        (int)$t['es_tarifa_fraccionable'] === 1
        &&
        (
            $t['unidad'] == 'minutos'
            ||
            $t['unidad'] == 'horas'
        )
    ) {

        $tarifasFraccion[] = $t;

    }

    /* BASE */

    else {

        $tarifasBase[] = $t;

    }
}

/* =========================================
   ANALIZAR TARIFAS BASE
========================================= */

foreach ($tarifasBase as $base) {

    $unidad = $base['unidad'];

    $valor = (int)$base['valor'];

    $monto = (float)$base['monto'];

    $bloqueMinutos = 0;

    if ($unidad == 'horas') {

        $bloqueMinutos = $valor * 60;

    }

    elseif ($unidad == 'minutos') {

        $bloqueMinutos = $valor;

    }

    else {

        continue;
    }

    /* =========================================
       CANTIDAD ENTERA
    ========================================= */

    $cantidadBase = floor(
        $minutos_totales / $bloqueMinutos
    );

    $remanente =
        $minutos_totales
        -
        ($cantidadBase * $bloqueMinutos);

    $subtotal =
        $cantidadBase * $monto;

    $items = [];

    if ($cantidadBase > 0) {

        $items[] = [

            'descripcion' => $base['descripcion'],

            'cantidad' => $cantidadBase,

            'precio' => $monto,

            'subtotal' => $subtotal
        ];
    }

    /* =========================================
       BUSCAR MEJOR FRACCIÓN
    ========================================= */

    if ($remanente > 0) {

        $mejorFraccion = null;

        foreach ($tarifasFraccion as $frac) {

            $bloqueFrac =
                ($frac['unidad'] == 'horas')
                ? ((int)$frac['valor'] * 60)
                : (int)$frac['valor'];

            $cantFrac = ceil(
                $remanente / $bloqueFrac
            );

            $subFrac =
                $cantFrac * $frac['monto'];

            if (
                !$mejorFraccion
                ||
                $subFrac < $mejorFraccion['subtotal']
            ) {

                $mejorFraccion = [

                    'descripcion' =>
                        $frac['descripcion'],

                    'cantidad' => $cantFrac,

                    'precio' => $frac['monto'],

                    'subtotal' => $subFrac
                ];
            }
        }

        if ($mejorFraccion) {

            $subtotal +=
                $mejorFraccion['subtotal'];

            $items[] =
                $mejorFraccion;
        }
    }

    /* =========================================
       TOPE DIARIO
    ========================================= */

    if ($topeDiario) {

        $subtotal = min(
            $subtotal,
            (float)$topeDiario['monto']
        );
    }

    /* =========================================
       EVITAR DUPLICADOS
    ========================================= */

    if (isset($totalesExistentes[$subtotal])) {
        continue;
    }

    $totalesExistentes[$subtotal] = true;

    /* =========================================
       GUARDAR ESCENARIO
    ========================================= */

    $escenarios[] = [

        'nombre' => $base['descripcion'],

        'abono_base' => $veh['tarifa'],

        'total' => $subtotal,

        'items' => $items

    ];
}

/* =========================================
   SI SOLO HAY FRACCIONES
========================================= */

if (empty($escenarios)) {

    foreach ($tarifasFraccion as $frac) {

        $bloque =
            ($frac['unidad'] == 'horas')
            ? ((int)$frac['valor'] * 60)
            : (int)$frac['valor'];

        $cantidad = ceil(
            $minutos_totales / $bloque
        );

        $subtotal =
            $cantidad * $frac['monto'];

        if (isset($totalesExistentes[$subtotal])) {
            continue;
        }

        $totalesExistentes[$subtotal] = true;

        $escenarios[] = [

            'nombre' => $frac['descripcion'],

            'abono_base' => $veh['tarifa'],

            'total' => $subtotal,

            'items' => [
                [
                    'descripcion' =>
                        $frac['descripcion'],

                    'cantidad' => $cantidad,

                    'precio' => $frac['monto'],

                    'subtotal' => $subtotal
                ]
            ]
        ];
    }
}

    usort($escenarios, function($a, $b){

        return $a['total'] <=> $b['total'];

    });

    $mejor = $escenarios[0];

    return [

        'vehiculo' => $veh,

        'minutos_totales' => $minutos_totales,

        'total' => $mejor['total'],

        'detalle' => $mejor['items'],

        'alternativas' => $escenarios

    ];
}