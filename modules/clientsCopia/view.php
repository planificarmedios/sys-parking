

<section class="content-header">
  <h1>
    <i class="fa fa-user icon-title"></i> Gestión de Clientes

    <a class="btn btn-success btn-social pull-right" href="?module=form_clients&form=add" title="Agregar" data-toggle="tooltip">
      <i class="fa fa-plus"></i> Agregar
    </a>
  </h1>

</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">

    <?php  

    if (empty($_GET['alert'])) {
      echo "";
    } 

    elseif ($_GET['alert'] == 1) {
      echo "<div class='alert alert-success alert-dismissable'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4>  <i class='icon fa fa-check-circle'></i> Exito!</h4>
              Los nuevos datos de usuario se ha registrado correcamente.
            </div>";
    }

    elseif ($_GET['alert'] == 2) {
      echo "<div class='alert alert-success alert-dismissable'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4>  <i class='icon fa fa-check-circle'></i> Exito!</h4>
           Los datos del cliente ha sido cambiado satisfactoriamente.
            </div>";
    }

    elseif ($_GET['alert'] == 3) {
      echo "<div class='alert alert-success alert-dismissable'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4>  <i class='icon fa fa-check-circle'></i> Exito!</h4>
            El usuario ha sido activado correctamente.
            </div>";
    }
 
    elseif ($_GET['alert'] == 4) {
      echo "<div class='alert alert-danger alert-dismissable'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4>  <i class='icon fa fa-check-circle'></i> Exito!</h4>
             Registro Eliminado.
            </div>";
    }
   
    elseif ($_GET['alert'] == 5) {
      echo "<div class='alert alert-danger alert-dismissable'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4>  <i class='icon fa fa-times-circle'></i> Error!</h4>
             Asegúrese de que el archivo que se sube es correcto.
            </div>";
    }

    elseif ($_GET['alert'] == 6) {
      echo "<div class='alert alert-danger alert-dismissable'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4>  <i class='icon fa fa-times-circle'></i> Error!</h4>
            Asegúrese de que la imagen no es más de 1 MB.
            </div>";
    }
 
    elseif ($_GET['alert'] == 7) {
      echo "<div class='alert alert-danger alert-dismissable'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4>  <i class='icon fa fa-times-circle'></i> Error!</h4>
             Asegúrese de que el tipo de archivo subido sea  *.JPG, *.JPEG, *.PNG.
            </div>";
    }
    ?>

      <div class="box box-primary">
        <div class="box-body">
     
          <table id="dataTables1" class="table table-bordered table-striped table-hover">
       
            <thead>
              <tr>
                <th class="center">ID</th>
                <th class="center">Abonado</th>
                <th class="center">Cliente</th>
                <th class="center">Dirección</th>
                <th class="center">Localidad</th>
                <th class="center">Facturación</th>
                <th class="center">Acciones</th>
              </tr>
            </thead>
                        <tbody>
            <?php  
            $no = 1;
      
            $query = mysqli_query($mysqli, "SELECT * FROM clientes ORDER BY id ASC")
                                            or die('error: '.mysqli_error($mysqli));


            while ($data = mysqli_fetch_assoc($query)) { 

              $periodo = $data['periodo'];

              switch ($periodo) {
                case '1': $mes = 'Enero';break;
                case '2': $mes = 'Febrero';break;
                case '3': $mes = 'Marzo';break;
                case '4': $mes = 'Abril';break;
                case '5': $mes = 'Mayo';break;
                case '6': $mes = 'Junio';break;
                case '7': $mes = 'Julio';break;
                case '8': $mes = 'Agosto';break;
                case '9': $mes = 'Septiembre';break;
                case '10': $mes = 'Octubre';break;
                case '11': $mes = 'Noviembre';break;
                case '12': $mes = 'Diciembre';break;
                default: $mes = 'Mensual'; break;
              }

              
  
              echo "<tr>
                      <td width='50' class='center'>$data[id]</td>
                      <td>$data[nro_abonado]</td>
                      <td><center>$data[denominacion]</center></td>
                      <td><center>$data[direccion]</center></td>
                      <td><center>$data[localidad]</center></td>
                      <td><center>$mes</center></td>
                      <td class='center' width='100'>
                          <div>";


              echo "      <a data-toggle='tooltip' data-placement='top' title='Modificar' class='btn btn-primary btn-sm' href='?module=form_clients&form=edit&id=$data[id]'>
                                <i style='color:#fff' class='glyphicon glyphicon-edit'></i>
                                </a>";
                                ?>
                                              <a data-toggle="tooltip" data-placement="top" title="Eliminar" class="btn btn-danger btn-sm" href="modules/clients/proses.php?act=delete&id=<?php echo $data['id'];?>" onclick="return confirm('estas seguro de eliminar a <?php echo $data['denominacion']; ?> ?');">
                                                  <i style="color:#fff" class="glyphicon glyphicon-trash"></i>
                                              </a>
                                <?php
                                  echo "    </div>
                      </td>
                    </tr>";
              
            }
            ?>
            </tbody>
          </table>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div><!--/.col -->
  </div>   <!-- /.row -->
</section><!-- /.content