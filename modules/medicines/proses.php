
<?php
session_start();


require_once "../../config/database.php";

if (empty($_SESSION['username']) && empty($_SESSION['password'])){
    echo "<meta http-equiv='refresh' content='0; url=index.php?alert=1'>";
}

else {
    if ($_GET['act']=='insert') {
        if (isset($_POST['Guardar'])) {
     
            $nombre  = mysqli_real_escape_string($mysqli, trim($_POST['nombre']));
            $pcompra = $_POST['pcompra'];
            $pventa = str_replace('.', '', mysqli_real_escape_string($mysqli, trim($_POST['pventa'])));
            $unidad     = mysqli_real_escape_string($mysqli, trim($_POST['unidad']));
  
            $query = mysqli_query($mysqli, "INSERT INTO productos(nombre,precio) 
                                            VALUES('$nombre','$pventa')")
                                            or die('error '.mysqli_error($mysqli));    

        
            if ($query) {
         
                header("location: ../../main.php?module=medicines&alert=1");
            }   
        }   
    }
    
    elseif ($_GET['act']=='update') {
        if (isset($_POST['Guardar'])) {
            if (isset($_POST['id'])) {
                $id = $_POST['id'];
        
                $nombre  = mysqli_real_escape_string($mysqli, trim($_POST['nombre']));
                $pventa = str_replace('.', '', mysqli_real_escape_string($mysqli, trim($_POST['pventa'])));
               

                $query = mysqli_query($mysqli, "UPDATE productos SET  nombre       = '$nombre',
                                                                      precio      = '$pventa'
                                                              WHERE id       = '$id'")
                                                or die('error: '.mysqli_error($mysqli));

    
                if ($query) {
                  
                    header("location: ../../main.php?module=medicines&alert=2");
                }         
            }
        }
    }

    elseif ($_GET['act']=='delete') {
        if (isset($_GET['id'])) {
            $codigo = $_GET['id'];
      
            $query = mysqli_query($mysqli, "DELETE FROM productos WHERE id='$codigo'")
                                            or die('error '.mysqli_error($mysqli));

            $query2 = mysqli_query($mysqli, "DELETE FROM agendas WHERE producto_id='$codigo'")
                                            or die('error '.mysqli_error($mysqli));
       


            if ($query) {
     
                header("location: ../../main.php?module=medicines&alert=3");
            }
        }
    }       
}       
?>