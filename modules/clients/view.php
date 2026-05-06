

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
        <div class="box-body table-responsive">
     
          <table id="dataTables1" class="table table-bordered table-striped table-hover">
       
            <thead>
              <tr>
                <th class="center">Patente</th>
                <th class="center">Categoría</th>
                <th class="center">Cliente</th>
                <th class="center">Fecha Inicio</th>
                <th class="center">Fecha Fin</th>
                <th class="center">Estado</th>
                <th class="center">Tarifa</th>
                <th class="center">Acciones</th>
              </tr>
            </thead>
                        <tbody>
            <?php  
            $no = 1;
            $hoy = date('Y-m-d');
      
            $query = mysqli_query($mysqli, "SELECT 
                                                c.*, 
                                                c.telefonos AS celular,
                                                t.descripcion AS tarifa,
                                                cat.nombre AS categoria
                                            FROM clientes c
                                            LEFT JOIN tarifas t 
                                                ON t.id = c.tarifa_id
                                            LEFT JOIN categorias cat 
                                                ON cat.id = c.categoria_id
                                            ORDER BY c.id ASC"
                                            )or die('error: '.mysqli_error($mysqli));


            while ($data = mysqli_fetch_assoc($query)) {

          $fecha_inicio_db = $data['fecha_inicio']; // Y-m-d
          $fecha_fin_db    = $data['fecha_fin'];    // Y-m-d
          $vigente = ($data['activo'] == 1 && $fecha_fin_db >= $hoy);
          $tipo = ($vigente) ? 'Abono' : 'Ocasional';

          // Solo para mostrar
          $fecha_inicio = date('d/m/Y', strtotime($fecha_inicio_db));
          $fecha_fin    = date('d/m/Y', strtotime($fecha_fin_db));

          $estado = '';
          if ($data['activo'] == 0) {
              $estado = 'Inactivo';
          } elseif ($fecha_fin_db >= $hoy) {
              $estado = 'Vigente';
          } else {
              $estado = 'Vencido';
          }

          $label = '';
          switch ($estado) {
              case 'Vigente':
                  $label = "<span class='badge-custom badge-vigente'>Vigente</span>";
                  break;

              case 'Vencido':
                  $label = "<span class='badge-custom badge-vencido'>Vencido</span>";
                  break;

              default:
                  $label = "<span class='badge-custom badge-inactivo'>Inactivo</span>";
          }

          echo "<tr>
          <td><center>".strtoupper($data['patente'])."</center></td>
          <td><center>{$data['categoria']}</center></td>
          <td><center>{$data['denominacion']}</center></td>
          <td><center>{$fecha_inicio}</center></td>
          <td><center>{$fecha_fin}</center></td>
          <td class='center'>$label</td>
          <td><center>".($data['tarifa'] ?? '-')."</center></td>
          <td class='center' width='120'>
              <div>

                <a data-toggle='tooltip' title='Modificar' class='btn btn-primary btn-sm'
                   href='?module=form_clients&form=edit&id={$data['id']}'>
                  <i style='color:#fff' class='glyphicon glyphicon-edit'></i>
                </a>";

                  if ($data['activo'] == 1) {
                      echo "
                              <a data-toggle='tooltip' title='Eliminar' class='btn btn-danger btn-sm'
                                href='modules/clients/proces.php?act=delete&id={$data['id']}'
                                onclick=\"return confirm('¿Estás seguro de eliminar a {$data['denominacion']}?');\">
                                <i style='color:#fff' class='glyphicon glyphicon-trash'></i>
                              </a>";
                  } else {
                      echo "
                              <a data-toggle='tooltip' class='btn btn-default btn-sm' href='#'>
                                <i style='color:white' class='glyphicon glyphicon-edit'></i>
                              </a>";
                  }

                  echo "   </div>
                          </td>
                        </tr>";
              }?>
            </tbody>
          </table>
        </div><!-- /.box-body -->
      </div>
    </div><!--/.col -->
  </div>   <!-- /.row -->
</section><!-- /.content