<?php

require '../../config/database.php';

/* =========================================================
   FILTROS
========================================================= */

$fecha_desde = $_GET['fecha_desde'] ?? date('Y-m-d');
$fecha_hasta = $_GET['fecha_hasta'] ?? date('Y-m-d');
$medio_cobro = $_GET['medio_cobro'] ?? '';

$where = [];

$where[] = "DATE(fecha_movimiento) >= '$fecha_desde'";
$where[] = "DATE(fecha_movimiento) <= '$fecha_hasta'";

if ($medio_cobro != '') {

    $medio_cobro = mysqli_real_escape_string(
        $mysqli,
        $medio_cobro
    );

    $where[] = "medio_cobro = '$medio_cobro'";
}

$where_sql = implode(' AND ', $where);

/* =========================================================
   CONSULTA
========================================================= */

$query = mysqli_query($mysqli, "

SELECT
    c.*,
    cat.nombre AS categoria,
    t.descripcion AS tarifa

FROM caja c

LEFT JOIN categorias cat
    ON cat.id = c.categoria_id

LEFT JOIN tarifas t
    ON t.id = c.tarifa_id

WHERE $where_sql

ORDER BY c.fecha_movimiento DESC

") or die(mysqli_error($mysqli));

/* =========================================================
   HEADERS EXCEL
========================================================= */

$filename =
    'reporte_caja_' .
    date('Y-m-d') .
    '.xls';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Expires: 0");

/* =========================================================
   TABLA
========================================================= */

$total_general = 0;

echo "

<table border='1'>

<tr style='background:#ddd;'>

    <th>Fecha</th>
    <th>Patente</th>
    <th>Categoria</th>
    <th>Tarifa</th>
    <th>Medio</th>
    <th>Monto</th>

</tr>

";

while ($row = mysqli_fetch_assoc($query)) {

    $total_general += (float)$row['monto'];

    echo "

    <tr>

        <td>
            ".date(
                'd/m/Y H:i',
                strtotime($row['fecha_movimiento'])
            )."
        </td>

        <td>
            ".htmlspecialchars(
                strtoupper($row['patente'])
            )."
        </td>

        <td>
            ".htmlspecialchars($row['categoria'])."
        </td>

        <td>
            ".htmlspecialchars($row['tarifa'])."
        </td>

        <td>
            ".htmlspecialchars($row['medio_cobro'])."
        </td>

        <td>
            ".number_format(
                $row['monto'],
                2,
                ',',
                '.'
            )."
        </td>

    </tr>

    ";
}

echo "

<tr>

    <td colspan='5'>
        <strong>TOTAL</strong>
    </td>

    <td>
        <strong>
            ".number_format(
                $total_general,
                2,
                ',',
                '.'
            )."
        </strong>
    </td>

</tr>

</table>

";

exit;