<?php
require_once "../../config/database.php";
require_once "../../vendor/autoload.php";

use Mpdf\Mpdf;

/* =========================
   FUNCIONES
========================= */
function convertirMes($mesNum) {
    $meses = [
        "01"=>"Enero","02"=>"Febrero","03"=>"Marzo","04"=>"Abril",
        "05"=>"Mayo","06"=>"Junio","07"=>"Julio","08"=>"Agosto",
        "09"=>"Septiembre","10"=>"Octubre","11"=>"Noviembre","12"=>"Diciembre"
    ];
    return $meses[$mesNum] ?? $mesNum;
}

/* =========================
   DATOS GET
========================= */
$tgl1 = $_GET['tgl_awal'];
$opcionImpresion = $_GET['opcion_impresion'];
$mesAnual = $_GET['mes_renovacion_anual'];

$explode = explode('-', $tgl1);
$tgl_awal = convertirMes($explode[1]) . "-" . $explode[0];

/* =========================
   CONSULTA CLIENTES
========================= */
if ($opcionImpresion == 1) {
    $where = "WHERE m.periodo = 0";
} elseif ($opcionImpresion == 2) {
    $where = "WHERE m.periodo = $mesAnual";
} else {
    $where = "WHERE m.periodo = 0 OR m.periodo = $mesAnual";
}

/* Total de clientes */
$queryTotal = mysqli_query($mysqli, "
    SELECT COUNT(DISTINCT m.id) as total
    FROM clientes m
    JOIN agendas e ON e.cliente_id = m.id
    $where
");


$rowTotal = mysqli_fetch_assoc($queryTotal);
$totalClientes = $rowTotal['total'];

/* =========================
   CONFIGURACION LOTES
========================= */
$recibosPorHoja = 2;          // 2 recibos por hoja
$hojasPorLote = 50;           // 50 hojas por lote
$recibosPorLote = $recibosPorHoja * $hojasPorLote;

$totalHojas = ceil($totalClientes / $recibosPorHoja);
$totalLotes = ceil($totalHojas / $hojasPorLote);

/* =========================
   GENERAR LOTES
========================= */
$folderLotes = __DIR__ . '/tmp/recibos_lotes/';
if (!file_exists($folderLotes)) mkdir($folderLotes, 0777, true);

for ($lote=1; $lote<=$totalLotes; $lote++) {
    $offset = ($lote-1) * $recibosPorLote;

    $query = mysqli_query($mysqli, "
        SELECT DISTINCT m.id, m.denominacion, m.direccion, m.localidad, m.nro_abonado
        FROM clientes m
        JOIN agendas e ON e.cliente_id = m.id
        $where
        ORDER BY m.id ASC
        LIMIT $offset, $recibosPorLote
    ");

    
  echo "<script>console.log(" . json_encode($where) . ");</script>";

    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4-L',
        'margin_left' => 5,
        'margin_right' => 5,
        'margin_top' => 5,
        'margin_bottom' => 5,
        'tempDir' => __DIR__ . '/../../tmp/mpdf'
    ]);

    /* CSS */
    $css = '
        html, body {height:100%; margin:0;}
        table.hoja {width:100%; height:100%; border-collapse: collapse;}
        td.recibo {width:13.85cm; height:19cm; vertical-align: top; border:1px solid #000; padding:10px;}
        td.separador {width:2cm;}
        .recibo-contenido {height:100%; display:flex; flex-direction:column; justify-content:space-between;}
        .titulo {font-size:16pt; font-weight:bold; text-align:center; margin-bottom:2mm;}
        .tabla {width:100%; border-collapse:collapse;}
        .tabla th, .tabla td {border:1px solid #000; padding:3px;}
        .firma {text-align:center;}
    ';
    $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);

    /* Generar HTML de los recibos */
    $html = '';
    $contador = 0;

    while ($data = mysqli_fetch_assoc($query)) {
        if ($contador % 2 == 0) $html .= '<table class="hoja"><tr>';

        $id_cliente = $data['id'];

        $qProd = mysqli_query($mysqli, "
            SELECT p.nombre, p.precio
            FROM agendas a
            JOIN productos p ON p.id = a.producto_id
            WHERE a.cliente_id = $id_cliente
        ");

        $rows = '';
        $total = 0;

        while ($p = mysqli_fetch_assoc($qProd)) {
            $total += $p['precio'];
            $totalFormateado = number_format($total, 2, ',', '.');
            $precioFormateado = number_format($p['precio'], 2, ',', '.');

            $rows .= "
                <tr>
                    <td>{$p['nombre']}</td>
                    <td align='right'>$ $precioFormateado</td>
                </tr>
            ";
        }

        $html .= "
<td class='recibo'>
<table width='100%' height='100%' cellpadding='0' cellspacing='0'>

    <!-- ENCABEZADO EMPRESA -->
    <tr>
        <td>
            <table width='100%' cellpadding='0' cellspacing='0'>
                <tr>
                    <td style='font-size:18px; font-weight:bold; padding-bottom:10px;'>
                        CENTRAL DE MONITOREO
                    </td>
                </tr>
                <tr>
                    <td style='font-size:8px; padding-bottom:30px;'>
                        7600 - Mar del Plata - Pcia. de Bs. As.
                    </td>
                </tr>
                <tr>
                    <td height='10'></td>
                </tr>
                <tr>
                    <td align='center' style='font-size:18px; font-weight:bold; padding-bottom:10px;'>
                        RECIBO X
                    </td>
                </tr>
                <tr>
                    <td align='center' style='font-size:10px; padding-bottom:20px;'>
                        Documento no válido como factura
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- DATOS -->
    <tr>
        <td valign='top'>
            <p><strong>Fecha:</strong> $tgl_awal</p>
            <p><strong>Cliente:</strong> {$data['denominacion']}</p>
            <p><strong>Cuenta:</strong> {$data['nro_abonado']}</p>
            <p><strong>Domicilio:</strong> {$data['direccion']}</p>
            <p><strong>Localidad:</strong> {$data['localidad']}</p>

            <!-- ESPACIO -->
            <table width='100%' cellpadding='0' cellspacing='0'>
                <tr><td height='40'></td></tr>
            </table>

            <!-- CONCEPTOS -->
            <table class='tabla' width='100%' cellpadding='4' cellspacing='0'>
                <tr>
                    <th>Concepto</th>
                    <th>Subtotal</th>
                </tr>
                $rows
                <tr>
                    <th>Total</th>
                    <th align='right'>$ $totalFormateado</th>
                </tr>
            </table>
        </td>
    </tr>

    <!-- BAJAR PIE -->
    <tr>
        <td>
            <table width='100%' cellpadding='0' cellspacing='0'>
                <tr><td height='35'></td></tr>
            </table>
        </td>
    </tr>

    <!-- FIRMA -->
    <tr>
        <td valign='bottom' style='padding-top:50px'>
            <table width='100%' cellpadding='0' cellspacing='0'>
                <tr>
                    <td style='font-size:18px; padding-top:30px; padding-bottom:30px;'>
                        RECIBÍ CONFORME TOTAL <strong> $ $totalFormateado </strong>

                    </td>
                </tr>
                <tr>
                    <td style='font-size:20px; padding-bottom:10px;'>
                        <img src='firmaoriginal.jpg' width='205'>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table width='40%' cellpadding='0' cellspacing='0'>
                            <tr>
                                <td style='border-top:1px solid #000; padding-top:6px;'>&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style='font-size:20px; padding-top:4px;'>
                        <strong>FIRMA</strong>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

</table>
</td>
";

        $contador++;
        if ($contador % 2 == 1) $html .= '<td class="separador"></td>';
        else $html .= '</tr></table><pagebreak />';
    }

    if ($contador % 2 != 0) $html .= '<td class="recibo"></td></tr></table>';

    $mpdf->WriteHTML($html);

    $fileLote = $folderLotes.'recibos_lote'.$lote.'.pdf';
    $mpdf->Output($fileLote, 'F');
}

/* =========================
   MOSTRAR BOTONES
========================= */
echo "<h2>Recibos generados en $totalLotes lote(s)</h2>";

echo "
<style>
  .btn-lote {
    padding: 8px 14px;
    margin-right: 6px;
    margin-bottom: 6px;
    border: none;
    border-radius: 4px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    color: #fff;
    transition: transform .1s ease, box-shadow .1s ease, opacity .1s ease;
  }

  .btn-lote:hover {
    opacity: 0.9;
    transform: translateY(-1px);
    box-shadow: 0 3px 6px rgba(0,0,0,.2);
  }

  .lote-color-1 { background: #28a745; } /* verde */
  .lote-color-2 { background: #007bff; } /* azul */
  .lote-color-3 { background: #17a2b8; } /* celeste */
  .lote-color-4 { background: #fd7e14; } /* naranja */
  .lote-color-5 { background: #6f42c1; } /* violeta */

</style>
";

echo "<div style='margin-bottom:15px'>";

for ($i = 1; $i <= $totalLotes; $i++) {

    // Ciclo de colores 1 a 5
    $colorClass = 'lote-color-' . (($i - 1) % 5 + 1);

    echo "<button
            class='btn-lote $colorClass'
            onclick=\"document.getElementById('preview').src='tmp/recibos_lotes/recibos_lote$i.pdf'\">
            📄 Ver Lote $i
          </button>";
}

echo "</div>";


echo "
<iframe
    id='preview'
    src='tmp/recibos_lotes/recibos_lote1.pdf'
    style='width:100%; height:700px; border:1px solid #ccc;'>
</iframe>
";

echo "<p style='margin-top:10px'>
        Tip: desde la vista previa podés imprimir directamente.
      </p>";
