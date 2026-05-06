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

    /* =========================================
       TARIFAS FIJAS DIRECTAS
    ========================================= */

    foreach ($tarifas as $t) {

        $unidad = $t['unidad'];

        $valor = (int) $t['valor'];

        $monto = (float) $t['monto'];

        if ($unidad == 'minutos') {

            $cantidad = ceil(
                $minutos_totales / $valor
            );

            $subtotal = $cantidad * $monto;

            $escenarios[] = [

                'nombre' => $t['descripcion'],

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

        elseif ($unidad == 'horas') {

            $bloque = $valor * 60;

            $cantidad = ceil(
                $minutos_totales / $bloque
            );

            $subtotal = $cantidad * $monto;

            $escenarios[] = [

                'nombre' => $t['descripcion'],

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

        elseif ($unidad == 'fijo') {

            $escenarios[] = [

                'nombre' => $t['descripcion'],

                'total' => $monto,

                'items' => [
                    [
                        'descripcion' => $t['descripcion'],
                        'cantidad' => 1,
                        'precio' => $monto,
                        'subtotal' => $monto
                    ]
                ]
            ];
        }
    }

    /* =========================================
       COMBINACIONES FRACCIONABLES
    ========================================= */

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

            $bloqueA =
                ($a['unidad'] == 'horas')
                ? ((int)$a['valor'] * 60)
                : (int)$a['valor'];

            $bloqueB =
                ($b['unidad'] == 'horas')
                ? ((int)$b['valor'] * 60)
                : (int)$b['valor'];

            for ($cantA = 0; $cantA <= 6; $cantA++) {

                for ($cantB = 0; $cantB <= 6; $cantB++) {

                    /* =========================================
                    EVITAR ESCENARIO VACÍO
                    ========================================= */

                    if (
                        $cantA == 0
                        &&
                        $cantB == 0
                    ) {
                        continue;
                    }

                    /* =========================================
                    EVITAR DUPLICADOS
                    EJ:
                    15+30 y 30+15
                    ========================================= */

                    if (
                        $a['id'] == $b['id']
                    ) {
                        continue;
                    }

                    /* =========================================
                    MINUTOS CUBIERTOS
                    ========================================= */

                    $cubierto =
                        ($cantA * $bloqueA)
                        +
                        ($cantB * $bloqueB);

                    if (
                        $cubierto < $minutos_totales
                    ) {
                        continue;
                    }

                    /* =========================================
                    GENERAR ITEMS
                    ========================================= */

                    $subtotal = 0;

                    $items = [];

                    if ($cantA > 0) {

                        $subA = (
                            $cantA * $a['monto']
                        );

                        $subtotal += $subA;

                        $items[] = [

                            'descripcion' => $a['descripcion'],

                            'cantidad' => $cantA,

                            'precio' => $a['monto'],

                            'subtotal' => $subA

                        ];
                    }

                    if ($cantB > 0) {

                        $subB = (
                            $cantB * $b['monto']
                        );

                        $subtotal += $subB;

                        $items[] = [

                            'descripcion' => $b['descripcion'],

                            'cantidad' => $cantB,

                            'precio' => $b['monto'],

                            'subtotal' => $subB

                        ];
                    }

                    /* =========================================
                    HASH ÚNICO
                    ========================================= */

                    $hash = md5(
                        json_encode($items)
                    );

                    if (
                        isset($hashes[$hash])
                    ) {
                        continue;
                    }

                    $hashes[$hash] = true;

                    /* =========================================
                    GUARDAR ESCENARIO
                    ========================================= */

                    $escenarios[] = [

                        'nombre' =>

                            $a['descripcion']
                            .' + '.
                            $b['descripcion'],

                        'total' => $subtotal,

                        'items' => $items

                    ];
                }
            }
        }
    }

    /* =========================================
       ORDENAR MÁS BARATO
    ========================================= */

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