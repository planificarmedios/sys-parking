<?php  

if ($_GET['form']=='add') { ?>

  <section class="content-header">
    <h1>
      <i class="fa fa-edit icon-title"></i> Agregar Categoría
    </h1>
    <ol class="breadcrumb">
      <li><a href="?module=start"><i class="fa fa-home"></i> Inicio </a></li>
      <li><a href="?module=categorias"> Categorías </a></li>
      <li class="active"> agregar </li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">

          <form role="form" class="form-horizontal" method="POST"
                action="modules/categorias/proces.php?act=insert">

            <div class="box-body">

              <div class="form-group">
                <label class="col-sm-2 control-label">Nombre</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="nombre" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Estado</label>
                <div class="col-sm-5">
                  <select name="activo" class="form-control">
                    <option value="1" selected>Activo</option>
                    <option value="0">Inactivo</option>
                  </select>
                </div>
              </div>

            </div>

            <div class="box-footer">
              <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <input type="submit" class="btn btn-primary btn-submit" value="Guardar">
                  <a href="?module=categorias" class="btn btn-default btn-reset">Cancelar</a>
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
    $query = mysqli_query($mysqli, "SELECT * FROM categorias WHERE id='$_GET[id]'")
             or die('error: '.mysqli_error($mysqli));
    $data = mysqli_fetch_assoc($query);
  }
?>

  <section class="content-header">
    <h1>
      <i class="fa fa-edit icon-title"></i> Modificar Categoría
    </h1>
    <ol class="breadcrumb">
      <li><a href="?module=start"><i class="fa fa-home"></i> Inicio</a></li>
      <li><a href="?module=categorias"> Categorías </a></li>
      <li class="active"> Modificar </li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">

          <form role="form" class="form-horizontal" method="POST"
                action="modules/categorias/proces.php?act=update">

            <div class="box-body">

              <input type="hidden" name="id" value="<?php echo $data['id']; ?>">

              <div class="form-group">
                <label class="col-sm-2 control-label">Nombre</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="nombre"
                         value="<?php echo $data['nombre']; ?>" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Estado</label>
                <div class="col-sm-5">
                  <select name="activo" class="form-control">
                    <option value="1" <?php echo ($data['activo'] == 1) ? 'selected' : ''; ?>>
                      Activo
                    </option>
                    <option value="0" <?php echo ($data['activo'] == 0) ? 'selected' : ''; ?>>
                      Inactivo
                    </option>
                  </select>
                </div>
              </div>

            </div>

            <div class="box-footer">
              <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <input type="submit" class="btn btn-primary btn-submit" value="Guardar">
                  <a href="?module=categorias" class="btn btn-default btn-reset">Cancelar</a>
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
?>