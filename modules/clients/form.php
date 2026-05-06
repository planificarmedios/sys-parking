<?php  

if ($_GET['form']=='add') { ?>

<section class="content-header">
  <h1>
    <i class="fa fa-edit icon-title"></i> Agregar Cliente
  </h1>
</section>

<section class="content">
  <div class="row">
    <div class="col-md-12">

      <div class="box box-primary">
        <form role="form" class="form-horizontal" method="POST" action="modules/clients/proces.php?act=insert">

          <div class="box-body">

            <!-- Nombre -->
            <div class="form-group">
              <label class="col-sm-2 control-label">Nombre</label>
              <div class="col-sm-5">
                <input type="text" class="form-control" name="denominacion" required>
              </div>
            </div>

            <!-- Patente -->
            <div class="form-group">
              <label class="col-sm-2 control-label">Patente</label>
              <div class="col-sm-5">
                <input 
                  type="text" 
                  class="form-control" 
                  name="patente" 
                  required
                  style="text-transform:uppercase"
                  oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g,'')">
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-2 control-label">Categoría</label>
              <div class="col-sm-5">
                <select name="categoria_id" id="categoria_id" class="form-control" required>
                  <option value="">Seleccionar</option>
                  <?php
                  $cats = mysqli_query($mysqli, "SELECT * FROM categorias WHERE activo=1");
                  while($c = mysqli_fetch_assoc($cats)){
                    echo "<option value='{$c['id']}'>{$c['nombre']}</option>";
                  }
                  ?>
                </select>
              </div>
            </div>

            <!-- Fecha inicio -->
            <div class="form-group">
              <label class="col-sm-2 control-label">Fecha inicio</label>
              <div class="col-sm-5">
                <input 
                  type="date"
                  class="form-control"
                  name="fecha_inicio"
                  value="<?= date('Y-m-d') ?>"
                  required>
              </div>
            </div>

            <!-- Tarifa -->
            <div class="form-group">
              <label class="col-sm-2 control-label">Tarifa</label>
              <div class="col-sm-5">
                <select name="tarifa_id" id="tarifa_id" class="form-control" required>
                  <option value="">Seleccione categoría primero</option>
                </select>
              </div>
            </div>

            <!-- Dirección -->
            <div class="form-group">
              <label class="col-sm-2 control-label">Dirección</label>
              <div class="col-sm-5">
                <input type="text" class="form-control" name="direccion" required>
              </div>
            </div>

            <!-- Celular -->
            <div class="form-group">
              <label class="col-sm-2 control-label">Celular</label>
              <div class="col-sm-5">
                <input type="text" class="form-control" name="telefonos" required>
              </div>
            </div>

            <!-- Localidad -->
            <div class="form-group">
              <label class="col-sm-2 control-label">Localidad</label>
              <div class="col-sm-5">
                <input type="text" class="form-control" name="localidad" required>
              </div>
            </div>

          </div>

          <div class="box-footer">
            <div class="form-group">
              <div class="col-sm-offset-2 col-sm-10">
                <input type="submit" class="btn btn-primary" value="Guardar">
                <a href="?module=clients" class="btn btn-default">Cancelar</a>
              </div>
            </div>
          </div>

        </form>
      </div>

    </div>
  </div>
</section>

<?php
}

elseif ($_GET['form']=='edit') { 

  if (isset($_GET['id'])) {
    $id_cliente = $_GET['id'];
    $query = mysqli_query($mysqli, "SELECT * FROM clientes WHERE id='$id_cliente'");
    $data  = mysqli_fetch_assoc($query);
  }

  $hoy = date('Y-m-d');
  $vigente = ($data['activo'] == 1 && $data['fecha_fin'] >= $hoy);
?>

<section class="content-header">
  <h1>
    <i class="fa fa-edit icon-title"></i> Modificar Cliente
  </h1>
</section>

<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box box-primary">

        <form role="form" class="form-horizontal" method="POST" action="modules/clients/proces.php?act=update">

          <input type="hidden" name="id_cliente" value="<?= $data['id']; ?>">
          <input type="hidden" name="tarifa_id" value="<?= $data['tarifa_id']; ?>">

          <div class="box-body">

            <!-- Patente -->
            <div class="form-group">
              <label class="col-sm-2 control-label">Patente</label>
              <div class="col-sm-5">
                <input readonly type="text" name="patente"  class="form-control" 
                       value="<?= strtoupper($data['patente']); ?>">
              </div>
            </div>

            <!-- Tarifa -->
            <div class="form-group">
              <label class="col-sm-2 control-label">Tarifa</label>
              <div class="col-sm-5">
                <input type="text" class="form-control" 
                  value="<?php
                    $t = mysqli_query($mysqli, "SELECT descripcion FROM tarifas WHERE id=".$data['tarifa_id']);
                    $tar = mysqli_fetch_assoc($t);
                    echo $tar['descripcion'] ?? '-';
                  ?>" readonly>
              </div>
            </div>

            <!-- Estado abono -->
            <div class="form-group">
              <label class="col-sm-2 control-label">Estado Abono</label>
              <div class="col-sm-5">
                <input type="text" class="form-control" 
                       value="<?= $vigente ? 'Vigente' : 'Vencido' ?>" readonly>
              </div>
            </div>

            <!-- Nombre -->
            <div class="form-group">
              <label class="col-sm-2 control-label">Cliente</label>
              <div class="col-sm-5">
                <input type="text" class="form-control" name="denominacion" 
                       value="<?= $data['denominacion']; ?>" required>
              </div>
            </div>

            <!-- Celular -->
            <div class="form-group">
              <label class="col-sm-2 control-label">Celular</label>
              <div class="col-sm-5">
                <input type="text" class="form-control" name="telefonos" 
                       value="<?= $data['telefonos']; ?>" required>
              </div>
            </div>

            <!-- Dirección -->
            <div class="form-group">
              <label class="col-sm-2 control-label">Dirección</label>
              <div class="col-sm-5">
                <input type="text" class="form-control" name="direccion" 
                       value="<?= $data['direccion']; ?>" required>
              </div>
            </div>

            <!-- Localidad -->
            <div class="form-group">
              <label class="col-sm-2 control-label">Localidad</label>
              <div class="col-sm-5">
                <input type="text" class="form-control" name="localidad" 
                       value="<?= $data['localidad']; ?>" required>
              </div>
            </div>

            <!-- Estado -->
            <div class="form-group">
              <label class="col-sm-2 control-label">Estado</label>
              <div class="col-sm-5">
                <select name="activo" class="form-control" required>
                  <option value="0" <?= ($data['activo'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
                  <option value="1" <?= ($data['activo'] == 1) ? 'selected' : ''; ?>>Activo</option>
                </select>
              </div>
            </div>

          </div>

          <div class="box-footer">
            <div class="form-group">
              <div class="col-sm-offset-2 col-sm-10">
                <input type="submit" class="btn btn-primary" value="Guardar">
                <a href="?module=clients" class="btn btn-default">Cancelar</a>
              </div>
            </div>
          </div>

        </form>

      </div>
    </div>
  </div>
</section>

<?php } ?>

<script>

  document.getElementById('categoria_id').addEventListener('change', function () {

  const categoriaId = this.value;

  fetch('modules/clients/get_tarifas.php?categoria_id=' + categoriaId)
    .then(res => res.json())
    .then(data => {

      const select = document.getElementById('tarifa_id');
      select.innerHTML = '<option value="">Seleccionar</option>';

      data.forEach(t => {
        select.innerHTML += `<option value="${t.id}">
          ${t.descripcion} - $${t.monto}
        </option>`;
      });

    });
});


</script>