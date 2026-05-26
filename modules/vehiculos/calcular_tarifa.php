<?php

/*
|--------------------------------------------------------------------------
| CONFIGURACION POR CATEGORIA
|--------------------------------------------------------------------------
*/

function obtenerConfiguracionCategoria(
    $mysqli,
    $categoria_id
) {

    $query = mysqli_query($mysqli, "

        SELECT *

        FROM configuracion_tarifas

        WHERE categoria_id = '$categoria_id'
        AND activo = 1

        LIMIT 1

    ");

    return mysqli_fetch_assoc($query);

}

/*
|--------------------------------------------------------------------------
| CALCULAR MINUTOS FACTURABLES
|--------------------------------------------------------------------------
|
| EJEMPLOS:
|
| 1h 03m => 60
| 1h 07m => 90
| 1h 35m => 90
| 1h 36m => 120
| 2h 05m => 120
| 2h 06m => 150
|
*/

function calcularMinutosFacturables(
    $minutosTotales,
    $tolerancia,
    $fraccion,
    $cobraHoraMinima = 1
) {

    /*
        SI NO COBRA HORA MINIMA
    */

    if (!$cobraHoraMinima) {

        return ceil(
            $minutosTotales / $fraccion
        ) * $fraccion;
    }

    /*
        HASTA 60 MIN
    */

    if ($minutosTotales <= 60) {

        return 60;
    }

    /*
        RESTANTE DESPUES
        DE PRIMERA HORA
    */

    $restante =
        $minutosTotales - 60;

    /*
        TOLERANCIA
    */

    if ($restante <= $tolerancia) {

        return 60;
    }

    /*
        DESCONTAR TOLERANCIA
    */

    $restante =
        $restante - $tolerancia;

    /*
        BLOQUES
    */

    $bloques =
        ceil(
            $restante / $fraccion
        );

    /*
        TOTAL
    */

    return
        60
        +
        ($bloques * $fraccion);
}

/*
|--------------------------------------------------------------------------
| MOTOR NUEVO
|--------------------------------------------------------------------------
*/

function calcularTarifaVehiculoV2(
    $mysqli,
    $vehiculo_id
) {

    /*
    |--------------------------------------------------------------------------
    | 1. OBTENER VEHICULO
    |--------------------------------------------------------------------------
    */

    $queryVehiculo = mysqli_query($mysqli, "

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

        WHERE v.id = '$vehiculo_id'

        LIMIT 1

    ");

    $vehiculo =
        mysqli_fetch_assoc(
            $queryVehiculo
        );

    if (!$vehiculo) {

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | 2. CATEGORIA
    |--------------------------------------------------------------------------
    */

    $categoria_id =
        (int)$vehiculo['categoria_final_id'];

    /*
    |--------------------------------------------------------------------------
    | 3. CONFIGURACION
    |--------------------------------------------------------------------------
    */

    $config =
        obtenerConfiguracionCategoria(
            $mysqli,
            $categoria_id
        );

    $tolerancia =
        (int)(
            $config['minutos_tolerancia']
            ?? 5
        );

    $fraccion =
        (int)(
            $config['minutos_fraccion']
            ?? 30
        );

    $cobraHoraMinima =
        (int)(
            $config['cobrar_hora_minima']
            ?? 1
        );

    /*
    |--------------------------------------------------------------------------
    | 4. TIEMPO
    |--------------------------------------------------------------------------
    */

    $fechaIngreso = strtotime(

        $vehiculo['fecha_ingreso']
        . ' ' .
        $vehiculo['hora_ingreso']

    );

    $fechaEgreso = time();

    $minutosTotales = ceil(

        ($fechaEgreso - $fechaIngreso)
        / 60

    );

    if ($minutosTotales < 1) {

        $minutosTotales = 1;
    }

    /*
    |--------------------------------------------------------------------------
    | 5. BUSCAR TARIFAS
    |--------------------------------------------------------------------------
    */

    $queryTarifas = mysqli_query($mysqli, "

        SELECT *

        FROM tarifas

        WHERE categoria_id = '$categoria_id'
        AND activo = 1

    ");

    
    $tarifaHora = null;

    $tarifaFraccion = null;

    $tarifaTope = null;

    while ($t = mysqli_fetch_assoc($queryTarifas)) {

        echo "<script>";
        echo "console.log(" . json_encode($t, JSON_UNESCAPED_UNICODE) . ")";
        echo "</script>";

        if (
            (int)$t['es_tope_diario'] === 1
        ) {

            $tarifaTope = $t;
        }

        /*
            IGNORAR ESTADIAS
        */

        elseif (
            (int)$t['es_tarifa_estadia'] === 1
        ) {

            continue;
        }

        /*
            FRACCION
        */

        elseif (
            (int)$t['es_tarifa_fraccionable'] === 1
        ) {

            $tarifaFraccion = $t;
        }

        /*
            TARIFA BASE
        */

        elseif (
            (int)$t['es_default'] === 1
        ) {

            $tarifaHora = $t;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 6. VALIDACIONES
    |--------------------------------------------------------------------------
    */

    if (!$tarifaHora) {

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | 7. VALORES
    |--------------------------------------------------------------------------
    */

    $valorHora =
        (float)$tarifaHora['monto'];

    $valorFraccion =
        $tarifaFraccion
        ? (float)$tarifaFraccion['monto']
        : 0;

    $valorTope =
        $tarifaTope
        ? (float)$tarifaTope['monto']
        : 0;

    /*
    |--------------------------------------------------------------------------
    | 8. MINUTOS FACTURABLES
    |--------------------------------------------------------------------------
    */

    $minutosFacturables =
        calcularMinutosFacturables(

            $minutosTotales,

            $tolerancia,

            $fraccion,

            $cobraHoraMinima

        );

    /*
    |--------------------------------------------------------------------------
    | 9. CALCULAR
    |--------------------------------------------------------------------------
    */

    $total = 0;

    $detalle = [];

    /*
        HORAS COMPLETAS
    */

    $horas =
        floor(
            $minutosFacturables / 60
        );

    /*
        RESTO
    */

    $resto =
        $minutosFacturables % 60;

    /*
    |--------------------------------------------------------------------------
    | COBRAR HORAS
    |--------------------------------------------------------------------------
    */

    if ($horas > 0) {

        $subtotalHoras =
            $horas * $valorHora;

        $total += $subtotalHoras;

        $detalle[] = [

            'descripcion' => $horas . ' hora(s) - ' . $tarifaHora['descripcion'],                

            'cantidad' =>
                $horas,

            'precio' =>
                $valorHora,

            'subtotal' =>
                $subtotalHoras
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | COBRAR FRACCION
    |--------------------------------------------------------------------------
    */

    $cantidadFracciones = 0;

    if (
        $resto > 0
        &&
        $tarifaFraccion
    ) {

        $cantidadFracciones =
            ceil(
                $resto / $fraccion
            );

        $subtotalFraccion =
            $cantidadFracciones
            * $valorFraccion;

        $total += $subtotalFraccion;

        $detalle[] = [

            'descripcion' =>

                (
                    $cantidadFracciones == 1
                    ? '1/2 hora'
                    : $cantidadFracciones . ' fracciones'
                )

                . ' - ' .

                $tarifaFraccion['descripcion'],

            'cantidad' =>
                $cantidadFracciones,

            'precio' =>
                $valorFraccion,

            'subtotal' =>
                $subtotalFraccion
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | TOPE DIARIO
    |--------------------------------------------------------------------------
    */

    if (

        $tarifaTope
        &&
        $total > $valorTope

    ) {

        $total = $valorTope;

        $detalle[] = [

            'descripcion' =>
                $tarifaTope['descripcion'],

            'cantidad' => 1,

            'precio' =>
                $valorTope,

            'subtotal' =>
                $valorTope
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RETURN
    |--------------------------------------------------------------------------
    */

    return [

        'vehiculo' => $vehiculo,

        'total' => $total,

        'detalle' => $detalle,

        'minutos_totales' =>
            $minutosTotales,

        'minutos_facturables' =>
            $minutosFacturables,

        'tolerancia' =>
            $tolerancia,

        'fraccion' =>
            $fraccion,

        'cantidad_fracciones' =>
            $cantidadFracciones,

        'alternativas' => []

    ];
}

/*
|--------------------------------------------------------------------------
| FUNCION ORIGINAL
|--------------------------------------------------------------------------
|
| Para compatibilidad.
|
*/

function calcularTarifaVehiculo(
    $mysqli,
    $vehiculo_id
) {

    return calcularTarifaVehiculoV2(
        $mysqli,
        $vehiculo_id
    );
}