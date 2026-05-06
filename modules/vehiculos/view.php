<section class="content-header">
  <h1>
    Vehículos en Playa
    <a class="btn btn-success pull-right" href="?module=form_vehiculos&form=add">
      <i class="fa fa-plus"></i> Ingresar Vehículo
    </a>
  </h1>
</section>



<section class="content">
  <?php
  if (empty($_GET['alert'])) {
      echo "";
    } 

    elseif ($_GET['alert'] == 'success_cobro') {
      echo "<div class='alert alert-success alert-dismissable'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4>  <i class='icon fa fa-check-circle'></i> Exito!</h4>
              Salida registrada correcamente.
            </div>";
    }
    ?>

  <div class="box box-primary">
    <div class="box-body table-responsive">
      <table id="dataTables1" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th class="center">Patente</th>
            <th class="center">Ingreso</th>
            <th class="center">Tarifa</th>
            <th class="center">Acciones</th>
          </tr>
        </thead>
        <tbody>

        <?php
        $query = mysqli_query($mysqli, "
            SELECT v.*, t.descripcion 
            FROM vehiculos v
            INNER JOIN tarifas t ON t.id = v.tarifa_id
            WHERE v.en_playa = 1
            AND
            v.fecha_egreso IS NULL
            ORDER BY v.id DESC
        ");

        $no = 1;
        
        while ($data = mysqli_fetch_assoc($query)) {
          $fecha = date('d/m/Y', strtotime($data['fecha_ingreso']));
          $hora  = date('H:i', strtotime($data['hora_ingreso']));
          
          echo "<script>console.log(" . json_encode($data) . ");</script>";
        ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><center><?= strtoupper($data['patente']) ?></td>
            <td><center><?= $fecha . ' ' . $hora ?></td>
            <td><center><?= $data['descripcion'] ?></td>
            <td class="text-center">

              <?php if ($_SESSION['permisos_acceso'] == 'Super Admin') { ?>

                <a data-toggle="tooltip" data-placement="top" title="Eliminar"
                  href="modules/vehiculos/proces.php?act=delete&id=<?= $data['id'] ?>"
                  onclick="return confirm('¿Eliminar el registro de este <?= $data['patente'] ?> ?')"
                  class="btn btn-danger btn-sm">
                  <i class="fa fa-trash"></i>
                </a>

              <?php } ?>

              <a data-toggle="tooltip" data-placement="top" title="Imprimir ticket" class="btn btn-info btn-sm" 
              href="modules/vehiculos/ticket_ingreso.php?id=<?= $data['id'] ?>">
                 <i class="fa fa-print"></i>
              </a>

              <a data-toggle="tooltip" data-placement="top" title="Ticket prueba" class="btn btn-danger btn-sm" 
              href="modules/vehiculos/ticket_prueba.php?id=<?= $data['id'] ?>&preview=1" rel="noopener noreferrer" target="_blank">
                 <i class="fa fa-print"></i>
              </a>

              <a data-toggle="tooltip"  class="btn btn-success btn-sm" data-placement="top" title="Registrar salida" 
                href="?module=form_vehiculos&form=cobrar&id=<?= $data['id'] ?>&tarifa_id=<?= $data['tarifa_id'] ?>">
                <i class="fa fa-arrow-right"></i>
              </a>

            </td>
          </tr>
        <?php } ?>

        </tbody>
      </table>
    </div>
  </div>
</section>