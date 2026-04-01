<section class="content-header">
  <h1>
    <i class="fa fa-money icon-title"></i> Tarifas

    <a class="btn btn-success btn-social pull-right" 
       href="?module=form_tarifas&form=add" 
       title="agregar" data-toggle="tooltip">
      <i class="fa fa-plus"></i> Agregar
    </a>
  </h1>
</section>

<section class="content">
  <div class="row">
    <div class="col-md-12">

    <?php  
    if (empty($_GET['alert'])) {
      echo "";
    } 
    elseif ($_GET['alert'] == 1) {
      echo "<div class='alert alert-success alert-dismissable'>
              <button type='button' class='close' data-dismiss='alert'>&times;</button>
              <h4><i class='icon fa fa-check-circle'></i> Éxito!</h4>
              Tarifa registrada correctamente.
            </div>";
    }
    elseif ($_GET['alert'] == 2) {
      echo "<div class='alert alert-success alert-dismissable'>
              <button type='button' class='close' data-dismiss='alert'>&times;</button>
              <h4><i class='icon fa fa-check-circle'></i> Éxito!</h4>
              Tarifa modificada correctamente.
            </div>";
    }
    elseif ($_GET['alert'] == 3) {
      echo "<div class='alert alert-success alert-dismissable'>
              <button type='button' class='close' data-dismiss='alert'>&times;</button>
              <h4><i class='icon fa fa-check-circle'></i> Éxito!</h4>
              Tarifa eliminada correctamente.
            </div>";
    }
    ?>

      <div class="box box-primary">
        <div class="box-body">

          <table id="dataTables1" class="table table-bordered table-striped table-hover">
            <thead>
              <tr>
                <th class="center">No.</th>
                <th class="center">Descripción</th>
                <th class="center">Unidad</th>
                <th class="center">Valor</th>
                <th class="center">Monto</th>
                <th class="center">Fraccionable</th>
                <th class="center">Tarifa por defecto</th>
                <th class="center">Estado</th>
                <th class="center">Acciones</th>
              </tr>
            </thead>
            <tbody>

            <?php  
            $no = 1;
            $query = mysqli_query(
              $mysqli,
              "SELECT * FROM tarifas ORDER BY id DESC"
            ) or die('error: '.mysqli_error($mysqli));

            while ($data = mysqli_fetch_assoc($query)) {

              $fraccionable = $data['es_tarifa_fraccionable'] == 1 ? 'Si' : 'No';
              $estado = $data['activo'] == 1 ? 'Activo' : 'Inactivo';
              $es_default  = $data['es_default'] == 1 ? 'Si' : 'No';


              echo "<tr>
                      <td width='30' class='center'>$no</td>
                      <td>$data[descripcion]</td>
                      <td class='center'>$data[unidad]</td>
                      <td class='center'>$data[valor]</td>
                      <td align='right'>$ ".number_format($data['monto'], 0, ',', '.')."</td>
                      <td class='center'>$fraccionable</td>
                      <td class='center'>$es_default</td>
                      <td class='center'>$estado</td>
                      <td class='center' width='90'>
                        <a data-toggle='tooltip' title='Modificar'
                           class='btn btn-primary btn-sm'
                           href='?module=form_tarifas&form=edit&id=$data[id]'>
                          <i style='color:#fff' class='glyphicon glyphicon-edit'></i>
                        </a>

                        <a data-toggle='tooltip' title='Eliminar'
                           class='btn btn-danger btn-sm'
                           href='modules/tarifas/proces.php?act=delete&id=$data[id]'
                           onclick=\"return confirm('¿Eliminar la tarifa $data[descripcion]?');\">
                          <i style='color:#fff' class='glyphicon glyphicon-trash'></i>
                        </a>
                      </td>
                    </tr>";
              $no++;
            }
            ?>

            </tbody>
          </table>

        </div>
      </div>
    </div>
  </div>
</section>