<?php
// Requerimos las dependencias
require_once "../../config/database.php";
require_once "../../vendor/autoload.php";

use Mpdf\Mpdf;

/* =========================
   FUNCIONES
========================= */
function convertirMes($mesNum) {
    $meses = ["01"=>"Enero", "02"=>"Febrero", "03"=>"Marzo", "04"=>"Abril", "05"=>"Mayo", "06"=>"Junio", "07"=>"Julio", "08"=>"Agosto", "09"=>"Septiembre", "10"=>"Octubre", "11"=>"Noviembre", "12"=>"Diciembre"];
    return $meses[$mesNum] ?? $mesNum;
}

/* =========================
   1. OBTENER Y VALIDAR DATOS DE ENTRADA (GET)
========================= */
$id_cliente = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_cliente) {
    die("Error: Se requiere un ID de cliente válido.");
}

$tgl1 = $_GET['tgl_awal'] ?? date('Y-m');
$explode = explode('-', $tgl1);
$fecha_recibo = convertirMes($explode[1]) . " - " . $explode[0];

/* =========================
   2. OBTENER DATOS DE LA BASE DE DATOS (DE FORMA SEGURA)
========================= */
// --- Datos del Cliente ---
$sql_cliente = "SELECT id, denominacion, direccion, localidad, nro_abonado FROM clientes WHERE id = ?";
$stmt_cliente = mysqli_prepare($mysqli, $sql_cliente);
mysqli_stmt_bind_param($stmt_cliente, "i", $id_cliente);
mysqli_stmt_execute($stmt_cliente);
$data_cliente = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_cliente));

if (!$data_cliente) {
    die("Error: No se encontró ningún cliente con el ID proporcionado.");
}

// --- Productos del Cliente ---
$sql_productos = "SELECT p.nombre, p.precio FROM agendas a JOIN productos p ON p.id = a.producto_id WHERE a.cliente_id = ?";
$stmt_productos = mysqli_prepare($mysqli, $sql_productos);
mysqli_stmt_bind_param($stmt_productos, "i", $id_cliente);
mysqli_stmt_execute($stmt_productos);
$resultado_productos = mysqli_stmt_get_result($stmt_productos);

$filas_productos = '';
$total = 0;
while ($p = mysqli_fetch_assoc($resultado_productos)) {
    $total += $p['precio'];
    $precioFormateado = number_format($p['precio'], 2, ',', '.');
    $nombreProducto = htmlspecialchars($p['nombre']);
    $filas_productos .= "<tr><td>{$nombreProducto}</td><td align='right'>$ {$precioFormateado}</td></tr>";
}
$totalFormateado = number_format($total, 2, ',', '.');

// Sanitizar datos del cliente para el HTML
foreach ($data_cliente as $key => $value) {
    $data_cliente[$key] = htmlspecialchars($value);
}

/* =====================================================================
   3. CONSTRUIR EL HTML DE LA PÁGINA (Un recibo y un espacio en blanco)
===================================================================== */

// Plantilla para el recibo con datos
$html_un_recibo_lleno = "
<td class='recibo'>
    <table width='100%' height='100%' cellpadding='0' cellspacing='0'>
        <tr>
            <td>
                <table width='100%'>
                    <tr><td style='font-size:18px; font-weight:bold; padding-bottom:10px;'>CENTRAL DE MONITOREO</td></tr>
                    <tr><td style='font-size:8px; padding-bottom:30px;'>7600 - Mar del Plata - Pcia. de Bs. As.</td></tr>
                    <tr><td height='10'></td></tr>
                    <tr><td align='center' style='font-size:18px; font-weight:bold; padding-bottom:10px;'>RECIBO X</td></tr>
                    <tr><td align='center' style='font-size:10px; padding-bottom:20px;'>Documento no válido como factura</td></tr>
                </table>
            </td>
        </tr>
        <tr>
            <td valign='top'>
                <p><strong>Fecha:</strong> {$fecha_recibo}</p>
                <p><strong>Cliente:</strong> {$data_cliente['denominacion']}</p>
                <p><strong>Cuenta:</strong> {$data_cliente['nro_abonado']}</p>
                <p><strong>Domicilio:</strong> {$data_cliente['direccion']}</p>
                <p><strong>Localidad:</strong> {$data_cliente['localidad']}</p>
                <table width='100%'><tr><td height='40'></td></tr></table>
                <table class='tabla' width='100%' cellpadding='4' cellspacing='0'>
                    <tr><th>Concepto</th><th>Subtotal</th></tr>
                    {$filas_productos}
                    <tr><th>Total</th><th align='right'>$ {$totalFormateado}</th></tr>
                </table>
            </td>
        </tr>
        <tr valign='bottom'>
            <td style='padding-top:50px'>
                <table width='100%'>
                    <tr><td style='font-size:18px; padding-top:30px; padding-bottom:30px;'>RECIBÍ CONFORME TOTAL <strong> $ {$totalFormateado} </strong></td></tr>
                    <tr>
                        <td style='font-size:20px; padding-bottom:10px;'>
                            <img src='firma.jpg' width='205'>
                        </td>
                    </tr>
                    <tr><td><table width='40%'><tr><td style='border-top:1px solid #000; padding-top:6px;'>&nbsp;</td></tr></table></td></tr>
                    <tr><td style='font-size:20px; padding-top:4px;'><strong>FIRMA</strong></td></tr>
                </table>
            </td>
        </tr>
    </table>
</td>";

// Plantilla para el espacio en blanco (mantiene la estructura y el borde)
$html_recibo_vacio = "<td class='recibo'>&nbsp;</td>";

// Unimos todo en la estructura de la hoja original
$html_final = "
<table class='hoja'>
    <tr>
        {$html_un_recibo_lleno}
        <td class='separador'></td>
        {$html_recibo_vacio}
    </tr>
</table>";

/* =========================
   4. GENERAR Y MOSTRAR EL PDF CON LA CONFIGURACIÓN ORIGINAL
========================= */
try {
    // RESTAURAMOS TU CONFIGURACIÓN ORIGINAL EXACTA
    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4-L',
        'margin_left' => 5,
        'margin_right' => 5,
        'margin_top' => 5,
        'margin_bottom' => 5,
        'tempDir' => __DIR__ . '/../../tmp/mpdf'
    ]);

    // RESTAURAMOS TU CSS ORIGINAL EXACTO
    $css = '
        html, body {height:100%; margin:0;}
        table.hoja {width:100%; height:100%; border-collapse: collapse;}
        td.recibo {width:13.85cm; height:19cm; vertical-align: top; border:1px solid #fdfcfc; padding:10px;}
        td.separador {width:2cm;}
        .recibo-contenido {height:100%; display:flex; flex-direction:column; justify-content:space-between;}
        .tabla {width:100%; border-collapse:collapse;}
        .tabla th, .tabla td {border:1px solid #000; padding:3px;}
        .firma {text-align:center;}
    ';
    $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
    $mpdf->WriteHTML($html_final);

    ob_clean();
    $nombre_archivo = "recibo_" . $data_cliente['nro_abonado'] . ".pdf";
    $mpdf->Output($nombre_archivo, 'I');

} catch (\Mpdf\MpdfException $e) {
    die("Error al generar el PDF: " . $e->getMessage());
}
?>