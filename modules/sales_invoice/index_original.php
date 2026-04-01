<?php

require_once "../../config/database.php";
$n = 0;
function convertir ($fecha) {
      
  $meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
  $meses_EN = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
  $nombreMes = str_replace($meses_EN, $meses_ES, $fecha);
  return $nombreMes;

}


$hari_ini = date("d-m-Y");
$tgl1     = $_GET['tgl_awal'];
$opcionImpresion     = $_GET['opcion_impresion'];
$mesAnual     = $_GET['mes_renovacion_anual'];
$explode  = explode('-',$tgl1);
$tgl_awal = convertir ($explode[1])."-".$explode[0];

if ($opcionImpresion == 1 ) { 

$query = mysqli_query($mysqli, "SELECT DISTINCT (m.id), m.denominacion, m.direccion, m.localidad, m.nro_abonado 
                                FROM clientes m 
                                JOIN agendas e ON e.cliente_id = m.id 
								WHERE m.periodo = 0 ORDER BY m.id ASC") or die('error: '.mysqli_error($mysqli));
} else if ($opcionImpresion == 3 ) { 

$query = mysqli_query($mysqli, "SELECT DISTINCT (m.id), m.denominacion, m.direccion, m.localidad, m.nro_abonado 
                                FROM clientes m 
                                JOIN agendas e ON e.cliente_id = m.id 
								WHERE m.periodo = 0 or m.periodo = $mesAnual ORDER BY m.id ASC")
                                            or die('error: '.mysqli_error($mysqli));

} else if ($opcionImpresion == 2 ) { 

$query = mysqli_query($mysqli, "SELECT DISTINCT (m.id), m.denominacion, m.direccion, m.localidad, m.nro_abonado 
                                FROM clientes m 
                                JOIN agendas e ON e.cliente_id = m.id 
								WHERE m.periodo = $mesAnual ORDER BY m.id ASC")
                                            or die('error: '.mysqli_error($mysqli));

}
                                            
$n=0;
while ($data = mysqli_fetch_assoc($query)) { 
                                            $id_cliente = $data["id"];
                                            $username = $data["denominacion"];
                                            $direccion = $data["direccion"];
                                            $localidad = $data["localidad"];
                                            $nro_abonado = $data["nro_abonado"];
                                            $n++;




                                          
echo "

<!DOCTYPE html>

<head>
    <meta charset='utf-8'>
    <title>Impresión de Recibos</title>
    <link rel='stylesheet' href='{{ asset('/css/bootstrap.min.css') }}'>
    <link rel='stylesheet' href='{{ asset('/css/panel.min.css') }}'>
    <style>
        @page { margin: 0; }
        @media print {
        @page { margin: 0; }
        body { margin-bottom: 0;}
        }
        .wrapper { width:48%; display: inline-block; margin: 0.25cm; }
        .bordered { border: 1px solid; padding: 5px; width: 95%; }
        .top-10 { margin-top: 10px; }
        .left-15 { margin-left: 20px; }
        .right-15 { margin-right: 15px; }
        .bottom-5 { margin-bottom: 5px; }
        .bottom-10 { margin-bottom: 10px; }
        .bottom-15 { margin-bottom: 15px; }
        .razonable { font-size: 18px; }
        .empresa { font-size: 18px; }
    </style>
</head>


<body onload='window.print();'>
    <div class='wrapper'>
        <section class='invoice'>
            <div class='row bottom-15'>
                <div class='col-xs-12'>
                    <div class='col-xs-6 text-center'>
                        <div>.</div>
                        <span class='empresa'> 
                            <strong>CENTRAL DE MONITOREO</strong>
                        </span>
                        <h6> 
                            7600 - Mar del Plata - Pcia. de Bs. As.
                        </h6>
                    </div>
                    <div class='col-xs-10'>
                        <h2> <center><strong>RECIBO X</strong></center> </h2>
                    </div>
                    <div class='col-xs-10'>
                        <div class='pull-left text-left'>
                            <small>
                                <center>Documento no válido como factura</center>
                            </small>
                           
                        </div>
                    </div>
                </div>
            </div>
         
            <div class='row'>
                <div class='col-xs-10 table-responsive'>
                    <div class='row bordered bottom-5'>
                        <strong>FECHA: </strong> $tgl_awal</strong> 
                    </div>
                    <div class='row bordered bottom-5'>
                        <strong>SEÑOR (ES): </strong>
                        $username </strong> Cuenta : $nro_abonado </strong>
                    </div>
                    <div class='row bordered bottom-5'>
                        <strong>DOMICILIO: </strong>
                        $direccion  </strong>
                    </div>
                    <div class='row bordered bottom-5'>
                        <strong>LOCALIDAD: </strong>
                        $localidad ($n)
                    </div>
                </div>
            </div>
            <div class='bordered'>
                <div class='row'>
                    <div class='col-xs-10 table-responsive'>
                        <table class='table table-striped'>
                            <thead>
                                <tr>
                                    <th>CONCEPTO.</th>
                                    <th>SUBTOTAL.</th>
                                    
                                </tr>
                                
                            </thead>
                            
                            <tbody>
                            
";

$query2 = mysqli_query($mysqli, "SELECT m.nombre as 'CODIGO', m.precio as 'PRECIO'
                                 FROM agendas tm
                                 JOIN productoS m ON m.id = tm.producto_id
                                 JOIN clientes u ON tm.cliente_id = u.id
                                 WHERE tm.cliente_id = $id_cliente "
                                 ) or die('error: '.mysqli_error($mysqli));
                                $cont  = 0;
                                $TOTAL = 0;
                                    while ($data2 = mysqli_fetch_assoc($query2)) { 
                                    $CODIGO = $data2['CODIGO'];
                                    $PRECIO = $data2['PRECIO'];
                                    $TOTAL = $TOTAL + $PRECIO;
                                    $cont++; 

                                    echo "  
                                
                                <tr>
                                    <td width='80%'>&nbsp;</td>
                                    <td width='20%'>&nbsp;</td>
                                </tr>
                                
                                <tr>
                                    <td width='80%'> <center> $CODIGO</center> </td>
                                    <td width='20%'> <center> $PRECIO </center> </td>
                                </tr>
                               
                                
                                ";
                              }

                              
                                for ($i = 0; $i < 8-$cont; $i++){
                                echo "
                                    <td width='80%'>&nbsp;</td>
                                    <td width='20%'>&nbsp;</td>
                                </tr>
                                ";
                            }

                              echo "   
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class='bordered'>
                <div class='row top-10 bottom-10'>
                    <div class='col-xs-5 text-center'>
                        <br>
                        RECIBI CONFORME TOTAL <strong> $ $TOTAL </strong>
                        <br>
                        
                    </div>
                    <div class='col-xs-7 text-center'>
                        <br>
                        <img src ='firmaoriginal.jpg'><br>
                        <strong>_____________________________</strong> 
                        <br>
                    </div>
                </div>

                <div class='col-xs-1 text-left'>
                        <br>
                        
                        <left><strong>FIRMA</strong> </left>
                        <br>
                    </div>


            </div>
        </section>
    </div>
</body>

</html>

";
}

?>
