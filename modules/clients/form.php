

<?php  

if ($_GET['form']=='add') { ?>

  <section class="content-header">
    <h1>
      <i class="fa fa-edit icon-title"></i> Agregar Usuario
    </h1>
    <ol class="breadcrumb">
      <li><a href="../clients/?module=start"><i class="fa fa-home"></i> Inicio </a></li>
      <li><a href="../clients/?module=clients"> Clientes </a></li>
      <li class="active"> agregar </li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <!-- form start -->
          <form role="form" class="form-horizontal" method="POST" action="modules/clients/proses.php?act=insert" enctype="multipart/form-data">
            <div class="box-body table-responsive">

              <div class="form-group">
                <label class="col-sm-2 control-label">Nombre</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="denominacion" autocomplete="on" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Patente</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="patente" autocomplete="on" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Fecha inicio</label>
                <div class="col-sm-5">
                  <input type="date"
                        class="form-control"
                        name="fecha_inicio"
                        id="fecha_inicio"
                        required
                        autocomplete="off">
                </div>
              </div>

              <script>
                $(function () {
                  $('#fecha_inicio').datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true,
                    todayHighlight: true
                  });
                });
              </script>

              <!-- Tarifa -->
              <div class="form-group">
                <label class="col-sm-2 control-label">Tarifa</label>
                <div class="col-sm-5">
                   <select name="tarifa_id" class="form-control" required>
                      <option value="" disabled selected>Seleccionar</option>

                      <?php
                      $tarifas = mysqli_query($mysqli, "
                          SELECT id, descripcion
                          FROM tarifas
                          WHERE activo = 1
                          ORDER BY id ASC
                      ");

                      while ($t = mysqli_fetch_assoc($tarifas)) {
                          echo "<option value='{$t['id']}'>
                                  {$t['descripcion']}
                                </option>";
                      }
                      ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Dirección</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="direccion" autocomplete="on" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Celular</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="telefonos" autocomplete="on"  required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Localidad</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="localidad" autocomplete="on" required>
                </div>
              </div>

              
            </div><!-- /.box body -->

            <div class="box-footer">
              <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <input type="submit" class="btn btn-primary btn-submit" name="Guardar" value="Guardar">
                  <a href="?module=clients" class="btn btn-default btn-reset">Cancelar</a>
                </div>
              </div>
            </div><!-- /.box footer -->
          </form>
        </div><!-- /.box -->
      </div><!--/.col -->
    </div>   <!-- /.row -->
  </section><!-- /.content -->
<?php
}

elseif ($_GET['form']=='edit') { 
  	if (isset($_GET['id'])) {
      $id_cliente = $_GET['id'];
      $query = mysqli_query($mysqli, "SELECT * FROM clientes WHERE id='$_GET[id]'") 
                                      or die('error: '.mysqli_error($mysqli));
      $data  = mysqli_fetch_assoc($query);
  	}	
?>

  <section class="content-header">
    <h1>
      <i class="fa fa-edit icon-title"></i> Modificar datos de Cliente
    </h1>
    <ol class="breadcrumb">
      <li><a href="../clients/?module=beranda"><i class="fa fa-home"></i> Inicio</a></li>
      <li><a href="../clients/?module=user"> Cliente </a></li>
      <li class="active"> Modificar </li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <!-- form start -->
          <form role="form" class="form-horizontal" method="POST" action="modules/clients/proses.php?act=update" enctype="multipart/form-data">
            <div class="box-body">

              <input type="hidden" name="id_cliente" value="<?php echo $data['id']; ?>">
       
              <div class="form-group">
                <label class="col-sm-2 control-label">Patente</label>
                <div class="col-sm-5">
                  <input readonly type="text" class="form-control" name="patente" autocomplete="off" value="<?php echo $data['patente']; ?>" required>
                </div>
              </div>

              <!-- Tarifa -->
            <div class="form-group">
              <label class="col-sm-2 control-label">Tarifa</label>
              <div class="col-sm-5">
                <select name="tarifa_id" class="form-control" required disabled>
                  <option value="" disabled <?= empty($data['tarifa_id']) ? 'selected' : '' ?>>
                    Seleccionar
                  </option>

                  <?php
                  $tarifas = mysqli_query($mysqli, "
                    SELECT id, descripcion, es_default
                    FROM tarifas
                    WHERE activo = 1
                    ORDER BY es_default DESC, id ASC
                  ");

                  while ($t = mysqli_fetch_assoc($tarifas)) {

                    if (!empty($data['tarifa_id'])) {
                        // MODO EDICIÓN
                        $selected = ($data['tarifa_id'] == $t['id']) ? 'selected' : '';
                    } else {
                        // MODO ALTA
                        $selected = ($t['es_default'] == 1) ? 'selected' : '';
                    }

                    echo "<option value='{$t['id']}' $selected>
                            {$t['descripcion']}
                          </option>";
                  }
                  ?>
                </select>
              </div>
            </div>
              
              <div class="form-group">
                <label class="col-sm-2 control-label">Cliente</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="denominacion" autocomplete="off" value="<?php echo $data['denominacion']; ?>" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Celular</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="telefonos" autocomplete="off" value="<?php echo $data['telefonos']; ?>" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Dirección</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="direccion" autocomplete="off" value="<?php echo $data['direccion']; ?>" required>
                </div>
              </div>
            
              <div class="form-group">
                <label class="col-sm-2 control-label">Localidad</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="localidad" autocomplete="off" value="<?php echo $data['localidad']; ?>" required>
                </div>
              </div>

              <div class="form-group">
              <label class="col-sm-2 control-label">Estado</label>
              <div class="col-sm-5">
                <select name="activo" class="form-control" required>
                  <option value="0" <?php echo ($data['activo'] == 0) ? 'selected' : ''; ?>>
                    Inactivo
                  </option>
                  <option value="1" <?php echo ($data['activo'] == 1) ? 'selected' : ''; ?>>
                    Activo
                  </option>
                </select>
              </div>
            </div>

            

            <div class="box-footer">
              <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <input type="submit" class="btn btn-primary btn-submit" name="Guardar" value="Guardar">
                  <a href="?module=clients" class="btn btn-default btn-reset">Cancelar</a>
                </div>
              </div>
            </div><!-- /.box footer -->
          </form>
         

        </div><!-- /.box -->
      </div><!--/.col -->
    </div>   <!-- /.row -->
  </section><!-- /.content -->
<?php
}

?>