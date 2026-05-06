<section class="content-header">
  <h1>
    <i class="fa fa-tags icon-title"></i> Gestión de Categorías

    <a class="btn btn-success btn-social pull-right" href="?module=form_categorias&form=add" title="Agregar" data-toggle="tooltip">
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
} elseif ($_GET['alert'] == 1) {
  echo "<div class='alert alert-success alert-dismissable'>
          <button type='button' class='close' data-dismiss='alert'>&times;</button>
          <h4><i class='icon fa fa-check-circle'></i> Éxito!</h4>
          Categoría registrada correctamente.
        </div>";
} elseif ($_GET['alert'] == 2) {
  echo "<div class='alert alert-success alert-dismissable'>
          <button type='button' class='close' data-dismiss='alert'>&times;</button>
          <h4><i class='icon fa fa-check-circle'></i> Éxito!</h4>
          Categoría modificada correctamente.
        </div>";
} elseif ($_GET['alert'] == 3) {
  echo "<div class='alert alert-danger alert-dismissable'>
          <button type='button' class='close' data-dismiss='alert'>&times;</button>
          <h4><i class='icon fa fa-check-circle'></i> Éxito!</h4>
          Categoría eliminada.
        </div>";
}
?>

      <div class="box box-primary">
        <div class="box-body table-responsive">

          <table id="dataTables1" class="table table-bordered table-striped table-hover">
            <thead>
              <tr>
                <th class="center">ID</th>
                <th class="center">Nombre</th>
                <th class="center">Estado</th>
                <th class="center">Acciones</th>
              </tr>
            </thead>

            <tbody>
<?php
$query = mysqli_query($mysqli, "SELECT * FROM categorias ORDER BY id DESC")
         or die('error: '.mysqli_error($mysqli));

while ($data = mysqli_fetch_assoc($query)) {

  $estado = $data['activo'] == 1 ? 'Activo' : 'Inactivo';

  echo "<tr>
    <td class='center'>{$data['id']}</td>
    <td class='center'>{$data['nombre']}</td>
    <td class='center'>{$estado}</td>
    <td class='center' width='120'>
      <div>

        <a data-toggle='tooltip' title='Modificar' class='btn btn-primary btn-sm'
           href='?module=form_categorias&form=edit&id={$data['id']}'>
          <i style='color:#fff' class='glyphicon glyphicon-edit'></i>
        </a>

        <a data-toggle='tooltip' title='Eliminar' class='btn btn-danger btn-sm'
           href='modules/categorias/proces.php?act=delete&id={$data['id']}'
           onclick=\"return confirm('¿Eliminar categoría {$data['nombre']}?');\">
          <i style='color:#fff' class='glyphicon glyphicon-trash'></i>
        </a>

      </div>
    </td>
  </tr>";
}
?>
            </tbody>
          </table>

        </div>
      </div>
    </div>
  </div>
</section>