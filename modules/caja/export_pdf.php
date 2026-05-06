<?php

ob_start();

require '../../vendor/autoload.php';
require '../../config/database.php';

use Dompdf\Dompdf;
use Dompdf\Options;

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
    $medio_cobro = mysqli_real_escape_string($mysqli, $medio_cobro);
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

$total_general = 0;

/* =========================================================
   HTML PDF
========================================================= */

$html = '

<style>

body {
    font-family: Arial, sans-serif;
    font-size: 12px;
}

h2 {
    text-align: center;
    margin-bottom: 5px;
}

.info {
    margin-bottom: 15px;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th {
    background: #eaeaea;
}

.table th,
.table td {
    border: 1px solid #ccc;
    padding: 6px;
    text-align: left;
}

.total {
    margin-top: 15px;
    text-align: right;
    font-size: 16px;
    font-weight: bold;
}

</style>

<h2>Reporte de Caja</h2>

<div class="info">
    <strong>Desde:</strong> '.date('d/m/Y', strtotime($fecha_desde)).'<br>
    <strong>Hasta:</strong> '.date('d/m/Y', strtotime($fecha_hasta)).'<br>
</div>

<table class="table">

<thead>
<tr>
    <th>Fecha</th>
    <th>Patente</th>
    <th>Categoria</th>
    <th>Tarifa</th>
    <th>Medio</th>
    <th>Monto</th>
</tr>
</thead>

<tbody>
';

while ($row = mysqli_fetch_assoc($query)) {

    $total_general += (float)$row['monto'];

    $html .= '
    <tr>
        <td>'.date('d/m/Y H:i', strtotime($row['fecha_movimiento'])).'</td>
        <td>'.htmlspecialchars(strtoupper($row['patente'])).'</td>
        <td>'.htmlspecialchars($row['categoria']).'</td>
        <td>'.htmlspecialchars($row['tarifa']).'</td>
        <td>'.htmlspecialchars($row['medio_cobro']).'</td>
        <td>$ '.number_format($row['monto'], 2, ',', '.').'</td>
    </tr>
    ';
}

$html .= '
</tbody>
</table>

<div class="total">
    TOTAL: $ '.number_format($total_general, 2, ',', '.').'
</div>
';

/* =========================================================
   DOMPDF
========================================================= */

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'landscape');

$dompdf->render();

/* =========================================================
   DESCARGA AUTOMATICA
========================================================= */

ob_end_clean();
$today = date('Y-m-d H:i:s');

$dompdf->stream(
    'reporte_caja_'.date('Y-m-d', strtotime($today)).'.pdf',
    [
        'Attachment' => true
    ]
);

exit;