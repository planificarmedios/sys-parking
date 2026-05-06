<?php  

// =========================
// FORM ADD
// =========================
if ($_GET['form']=='add') { ?>

<section class="content-header">
  <h1>
    <i class="fa fa-money icon-title"></i> Agregar Tarifa
  </h1>
</section>

<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box box-primary">

        <form role="form" class="form-horizontal" 
              method="POST" 
              action="modules/tarifas/proces.php?act=insert">

          <div class="box-body">

          <div class="form-group">
            <label class="col-sm-2 control-label">Categoría</label>
            <div class="col-sm-5">
              <select name="categoria_id" class="form-control">
                <option value="">General (sin categoría)</option>

                <?php
                $categorias = mysqli_query($mysqli, "
                  SELECT id, nombre 
                  FROM categorias 
                  WHERE activo = 1
                  ORDER BY nombre ASC
                ");

                while ($c = mysqli_fetch_assoc($categorias)) {
                  echo "<option value='{$c['id']}'>
                          {$c['nombre']}
                        </option>";
                }
                ?>
              </select>
            </div>
          </div>

            <div class="form-group">
              <label class="col-sm-2 control-label">Descripción</label>
              <div class="col-sm-5">
                <input type="text" name="descripcion" class="form-control" required>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-2 control-label">Unidad</label>
              <div class="col-sm-5">
                <select name="unidad" class="form-control" required>
                  <option value="minutos">Minutos</option>
                  <option value="horas">Horas</option>
                  <option value="dias">Días</option>
                  <option value="fijo">Fijo</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-2 control-label">Valor</label>
              <div class="col-sm-5">
                <input type="number" name="valor" class="form-control" value="1" required>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-2 control-label">Monto</label>
              <div class="col-sm-5">
                <input type="number" name="monto" class="form-control" required>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-2 control-label">Fraccionable</label>
              <div class="col-sm-5">
                <input type="checkbox" name="es_tarifa_fraccionable" value="1">
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-2 control-label">Tarifa por defecto</label>
              <div class="col-sm-5">
                <div class="radio">
                  <label>
                    <input type="radio" name="es_default" value="1"> Sí
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input type="radio" name="es_default" value="0" checked> No
                  </label>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-2 control-label">Activa</label>
              <div class="col-sm-5">
                <input type="checkbox" name="activo" value="1" checked>
              </div>
            </div>

          </div>

          <div class="box-footer">
            <div class="form-group">
              <div class="col-sm-offset-2 col-sm-10">
                <input type="submit" class="btn btn-primary" value="Guardar">
                <a href="?module=tarifas" class="btn btn-default">Cancelar</a>
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

// =========================
// FORM EDIT
// =========================
elseif ($_GET['form']=='edit') { 

  if (isset($_GET['id'])) {
    $query = mysqli_query(
      $mysqli,
      "SELECT * FROM tarifas WHERE id='$_GET[id]'"
    ) or die('error: '.mysqli_error($mysqli));

    $data = mysqli_fetch_assoc($query);
  }
?>

<section class="content-header">
  <h1>
    <i class="fa fa-money icon-title"></i> Editar Tarifa
  </h1>
</section>

<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box box-primary">

        <form role="form" class="form-horizontal" 
              method="POST" 
              action="modules/tarifas/proces.php?act=update">

          <div class="box-body">

            <input type="hidden" name="id" value="<?php echo $data['id']; ?>">

            <div class="form-group">
              <label class="col-sm-2 control-label">Categoría</label>
              <div class="col-sm-5">
                <select name="categoria_id" class="form-control">
                  <option value="">General (sin categoría)</option>

                  <?php
                  $categorias = mysqli_query($mysqli, "
                    SELECT id, nombre 
                    FROM categorias 
                    WHERE activo = 1
                    ORDER BY nombre ASC
                  ");

                  while ($c = mysqli_fetch_assoc($categorias)) {

                    $selected = ($data['categoria_id'] == $c['id']) ? 'selected' : '';

                    echo "<option value='{$c['id']}' $selected>
                            {$c['nombre']}
                          </option>";
                  }
                  ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-2 control-label">Descripción</label>
              <div class="col-sm-5">
                <input type="text" name="descripcion" class="form-control"
                       value="<?php echo $data['descripcion']; ?>" required>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-2 control-label">Unidad</label>
              <div class="col-sm-5">
                <select name="unidad" class="form-control" required>
                  <option value="minutos" <?php if($data['unidad']=='minutos') echo 'selected'; ?>>Minutos</option>
                  <option value="horas" <?php if($data['unidad']=='horas') echo 'selected'; ?>>Horas</option>
                  <option value="dias" <?php if($data['unidad']=='dias') echo 'selected'; ?>>Días</option>
                  <option value="fijo" <?php if($data['unidad']=='fijo') echo 'selected'; ?>>Fijo</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-2 control-label">Valor</label>
              <div class="col-sm-5">
                <input type="number" name="valor" class="form-control"
                       value="<?php echo $data['valor']; ?>" required>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-2 control-label">Monto</label>
              <div class="col-sm-5">
                <input type="number" name="monto" class="form-control"
                       value="<?php echo $data['monto']; ?>" required>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-2 control-label">Fraccionable</label>
              <div class="col-sm-5">
                <input type="checkbox" name="es_tarifa_fraccionable" value="1"
                  <?php if($data['es_tarifa_fraccionable']==1) echo 'checked'; ?>>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-2 control-label">Tarifa por defecto</label>
              <div class="col-sm-5">
                <div class="radio">
                  <label>
                    <input type="radio" name="es_default" value="1"
                      <?php if ($data['es_default'] == 1) echo 'checked'; ?>> Sí
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input type="radio" name="es_default" value="0"
                      <?php if ($data['es_default'] == 0) echo 'checked'; ?>> No
                  </label>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-2 control-label">Activa</label>
              <div class="col-sm-5">
                <input type="checkbox" name="activo" value="1"
                  <?php if($data['activo']==1) echo 'checked'; ?>>
              </div>
            </div>

          </div>

          <div class="box-footer">
            <div class="form-group">
              <div class="col-sm-offset-2 col-sm-10">
                <input type="submit" class="btn btn-primary" value="Guardar">
                <a href="?module=tarifas" class="btn btn-default">Cancelar</a>
              </div>
            </div>
          </div>

        </form>

      </div>
    </div>
  </div>
</section>

<?php } ?>