

<?php
session_start();


require_once "../../config/database.php";


if (empty($_SESSION['username']) && empty($_SESSION['password'])){
	echo "<meta http-equiv='refresh' content='0; url=index.php?alert=1'>";
}

else {

	if ($_GET['act']=='insert') {
		if (isset($_POST['Guardar'])) {
	
			$denominacion  = mysqli_real_escape_string($mysqli, trim($_POST['denominacion']));
			$nro_abonado  = md5(mysqli_real_escape_string($mysqli, trim($_POST['abonado'])));
			$direccion = mysqli_real_escape_string($mysqli, trim($_POST['direccion']));
			$localidad = mysqli_real_escape_string($mysqli, trim($_POST['localidad']));

            $query = mysqli_query($mysqli, "INSERT INTO clientes(denominacion,direccion,localidad,nro_abonado)
                                            VALUES('$denominacion','$direccion','$localidad','$nro_abonado')")
                                            or die('error: '.mysqli_error($mysqli));    

          
            if ($query) {
                header("location: ../../main.php?module=clients&alert=1");
            }
		}	
	}
	
	elseif ($_GET['act']=='update') {
		if (isset($_POST['Guardar'])) {
			if (isset($_POST['id_cliente'])) {
				$id_cliente          = mysqli_real_escape_string($mysqli, trim($_POST['id_cliente']));
				$denominacion        = mysqli_real_escape_string($mysqli, trim($_POST['denominacion']));
				$nro_abonado         = mysqli_real_escape_string($mysqli, trim($_POST['nro_abonado']));
				$direccion           = mysqli_real_escape_string($mysqli, trim($_POST['direccion']));
				$localidad           = mysqli_real_escape_string($mysqli, trim($_POST['localidad']));
				

                $query = mysqli_query($mysqli, "UPDATE clientes SET 
				denominacion = '$denominacion',                                             
				nro_abonado  = '$nro_abonado',
                localidad = '$localidad', 
				direccion = '$direccion'
                WHERE id  = '$id_cliente'")
                or die('error: '.mysqli_error($mysqli));

    
                if ($query) {
                    header("location: ../../main.php?module=clients&alert=2");
                }
				
				
			}
		}
	}

	elseif ($_GET['act']=='delete') {
        if (isset($_GET['id'])) {
            $id_cliente = $_GET['id'];
      
            $query = mysqli_query($mysqli, "DELETE FROM agendas WHERE cliente_id='$id_cliente'")
                                            or die('error '.mysqli_error($mysqli));
											
			$query2 = mysqli_query($mysqli, "DELETE FROM clientes WHERE id='$id_cliente'")
                                            or die('error '.mysqli_error($mysqli));

            if ($query and $query2 ) {
                     header("location: ../../main.php?module=clients&alert=4");
            }
        }
    } 

    elseif ($_GET['act']=='deleteAbono') {
        if (isset($_GET['id_cliente']) and isset($_GET['codigo'])) {
            $cliente_id = $_GET['id_cliente'];
			$producto_id = $_GET['codigo'];
      
            $query = mysqli_query($mysqli, "DELETE FROM agendas WHERE cliente_id='$cliente_id' and producto_id='$producto_id'")
                                            or die('error '.mysqli_error($mysqli));


            if ($query) {
     
                header("location: ../../main.php?module=form_clients&form=edit&id=$cliente_id");
            }
        }
    } 
	
	
	elseif ($_GET['act']=='addabono') {
        if (isset($_GET['id_cliente']) and isset($_GET['codigo']) and isset($_GET['precio_venta'])) {
            $cliente_id = $_GET['id_cliente'];
			$producto_id = $_GET['codigo'];
			$precio_venta = $_GET['precio_venta'];

            $query = mysqli_query($mysqli, "INSERT INTO agendas (cliente_id, producto_id, precio_asignado, caducidad, cod_abono) 
			VALUES 	('$cliente_id', '$producto_id','$precio_venta','0000-00-00','$codigo')")
            or die('error '.mysqli_error($mysqli));


            if ($query) {
     
                header("location: ../../main.php?module=form_clients&form=edit&id=$cliente_id");
            }
        }
    } 

    elseif ($_GET['act']=='addAbonoAnual') {

        if (isset($_POST['id_cliente'])) {
            $id_cliente          = mysqli_real_escape_string($mysqli, trim($_POST['id_cliente']));
            $precio_venta        = mysqli_real_escape_string($mysqli, trim($_POST['precio_venta']));
            $producto_id       = mysqli_real_escape_string($mysqli, trim($_POST['codigo']));
            $periodo          = mysqli_real_escape_string($mysqli, trim($_POST['mes_renovacion_anual']));
    
            

              $query = mysqli_query($mysqli, "INSERT INTO agendas (cliente_id, producto_id, precio_asignado, caducidad) 
                                              VALUES 	('$id_cliente ', '$producto_id','$precio_venta','0000-00-00')")
             or die('error '.mysqli_error($mysqli));

             $query2 = mysqli_query($mysqli, "UPDATE clientes SET periodo = '$periodo' WHERE id = '$id_cliente'")
             or die('error '.mysqli_error($mysqli));

            if ($query and $query2) {
                header("location: ../../main.php?module=form_clients&form=edit&id=$id_cliente");
            }
            
            
        }
      
    }

}		
?>