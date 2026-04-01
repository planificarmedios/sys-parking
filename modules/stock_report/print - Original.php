<?php
session_start();
ob_start();
require_once "../../config/database.php";
include "../../config/fungsi_tanggal.php";
include "../../config/fungsi_rupiah.php";

function convertir ($fecha) {
      
    $meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
    $meses_EN = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
    $nombreMes = str_replace($meses_EN, $meses_ES, $fecha);
    return $nombreMes;

}


$hari_ini = date("d-m-Y");
$tgl1     = $_GET['tgl_awal'];
$explode  = explode('-',$tgl1);


$tgl_awal = convertir ($explode[1])."-".$explode[0];

$tgl2      = $_GET['tgl_akhir'];
$explode   = explode('-',$tgl2);
$tgl_akhir = $explode[2]."-".$explode[1]."-".$explode[0];

if (isset($_GET['tgl_awal'])) {
    $no    = 1;
    
    /*$query = mysqli_query($mysqli, 
    "SELECT prod.id, prod.nombre, prod.precio, cl.id, cl.denominacion as 'DENOMINACION'
    FROM agendas ag
    JOIN clientes cl ON ag.cliente_id = cl.id 
    JOIN productos prod on prod.id = ag.producto_id
    ORDER BY ag.cliente_id asc") 
    or die('error: '.mysqli_error($mysqli));
    $count  = mysqli_num_rows($query);*/


    $query = mysqli_query($mysqli, 
    "SELECT id, nombre, precio FROM productos") 
    or die('error: '.mysqli_error($mysqli));
    $count  = mysqli_num_rows($query);
}
?>
<html xmlns="http://www.w3.org/1999/xhtml"> 
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
       
        <link rel="stylesheet" type="text/css" href="../../assets/css/laporan.css" />
 

    </head>
    <body>

    <!-- Columns start at 50% wide on mobile and bump up to 33.3% wide on desktop -->



    <div class="wrapper">

   


           
            <div class="row bottom-15">
                <div class="col-xs-24">
                    <div class="col-xs-6 text-center">
                        <span class="empresa"> 
                            <strong>CENTRAL DE MONITOREO</strong>                                            
                        </span>
                        <h6> 
                            7600 - Mar del Plata - Pcia. de Bs. As.
                        </h6>
                        
                        <h5> Fecha <?php echo(($tgl_awal)); ?> </h5>
                    </div> 
                    
                   
                    
                   

                </div>
            </div>


            
        
    </div>

    <div class="col-xs-6 text-center">
                         
                        
                            <h4>
                            <strong>RECIBO <?php $count ?></strong><br>
                                <strong>X</strong><br>
                                <h6>No valido como factura</h6>
                            </h4>   
                        
                        
    </div>
                    
     
        <br>

        <div class="row">
                <div class="col-sm-12 left-15">
                    <div class="row bordered bottom-5">
                        <strong>ABONADO: </strong>
                        <?php echo $nombre ?>
                    </div>
                    <div class="row bordered bottom-5">
                        <strong>DOMICILIO: </strong>
                       
                    </div>
                    <div class="row bordered bottom-5">
                        <strong>LOCALIDAD: </strong>
                        
                    </div>
                </div>
            </div><br><br>

            

        <div id="isi">
            <table width="100%" border="0.3" cellpadding="0" cellspacing="0">
                <thead style="background:#e8ecee">
                    <tr class="tr-title">
                        
                    <th width='200' height="20" align="center" valign="middle"><small>ID </small></th><br>
                        <th width='200' height="20" align="center" valign="middle"><small>CONCEPTO </small></th><br>
                        <th width='200' height="20" align="center" valign="middle"><small>SUBTOTAL</small></th>
                        </tr>
                </thead>
                <tbody>
<?php
    
   
   
        while ($data = mysqli_fetch_assoc($query)) {
            
            echo "  <tr>
                        <td width='40' height='13' align='center' valign='middle'>$data[id]</td>
                        <td width='120' height='13' align='center' valign='middle'>$data[nombre]</td>
                        <td width='80' height='13' align='center' valign='middle'>$data[precio]</td>
               
                    </tr>";
            $no++;
        }
    
?>	
                </tbody>
            </table>

        </div><br><br><br><br><br>

        <div class="row top-10 bottom-10">
                    
                    <div class="col-xs-7 text-center">
                        <br>
						
                        ________________________________
						<br>
                         <strong>Firma</strong> 
						
                    </div>
                </div>
       

        
 
    


    </body>
</html>
<?php
$filename="Recibos.pdf"; 
$content = ob_get_clean();
$content = '<page style="font-family: freeserif">'.($content).'</page>';

require_once('../../assets/plugins/html2pdf_v4.03/html2pdf.class.php');
try
{
    $html2pdf = new HTML2PDF('L','F4','en', false, 'ISO-8859-15',array(10, 10, 10, 10));
    $html2pdf->setDefaultFont('Arial');
    $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
    $html2pdf->Output($filename);
}
catch(HTML2PDF_exception $e) { echo $e; }
?>