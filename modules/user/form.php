<?php

// ==================================================
// FORM AGREGAR USUARIO
// ==================================================
if ($_GET['form'] == 'add') {
?>

<section class="content-header">
  <h1>
    <i class="fa fa-edit icon-title"></i> Agregar Usuario
  </h1>
</section>

<section class="content">
<div class="row">
<div class="col-md-12">
<div class="box box-primary">

<form class="form-horizontal"
      method="POST"
      action="modules/user/proses.php?act=insert"
      enctype="multipart/form-data">

<div class="box-body">

<div class="form-group">
  <label class="col-sm-2 control-label">Nombre de usuario</label>
  <div class="col-sm-5">
    <input type="text" class="form-control" name="username" required>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-2 control-label">Contraseña</label>
  <div class="col-sm-5">
    <input type="password" class="form-control" name="password" required>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-2 control-label">Nombre</label>
  <div class="col-sm-5">
    <input type="text" class="form-control" name="name_user" required>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-2 control-label">Permisos de acceso</label>
  <div class="col-sm-5">
    <select class="form-control" name="permisos_acceso" required>
      <option value=""></option>
      <option value="Super Admin">Super Admin</option>
      <option value="Gerente">Gerente</option>
      <option value="Operador">Operador</option>
    </select>
  </div>
</div>

</div>

<div class="box-footer">
  <button type="submit" class="btn btn-primary">Guardar</button>
  <a href="?module=user" class="btn btn-default">Cancelar</a>
</div>

</form>
</div>
</div>
</div>
</section>

<?php
// ==================================================
// FORM EDITAR USUARIO
// ==================================================
} elseif ($_GET['form'] == 'edit') {

    if (!isset($_GET['id'])) {
        header("Location: ?module=user");
        exit;
    }

    $query = mysqli_query($mysqli, "
        SELECT * FROM usuarios
        WHERE id_user = '$_GET[id]'
    ") or die(mysqli_error($mysqli));

    $data = mysqli_fetch_assoc($query);

    $perfilUsuarioEditado = $data['permisos_acceso'];
    $esSuperAdmin = ($_SESSION['permisos_acceso'] === 'Super Admin');

    $permisosPerfil = [];

    if ($esSuperAdmin) {
        $result = mysqli_query($mysqli, "
            SELECT modulo, puede_acceder, puede_ver
            FROM profile_modules
            WHERE perfil = '$perfilUsuarioEditado'
        ");

        while ($row = mysqli_fetch_assoc($result)) {
            $permisosPerfil[$row['modulo']] = $row;
        }
    }

    $modulosSistema = [
        'vehiculos' => 'Vehículos',
        'clients'   => 'Clientes',
        'cobranzas' => 'Cobranzas',
        'user'      => 'Usuarios',
        'simulador' => 'Simulador',
        'caja'      => 'Caja',
        'tarifas'   => 'Tarifas',
        'categorias' => 'Categorías de Vehículos',
    ];

?>

  => 'Tarifas',
    ];
?>

<section class="content-header">
  <h1><i class="fa fa-edit"></i> Modificar Usuario</h1>
</section>

<section class="content">
<div class="row">
<div class="col-md-12">
<div class="box box-primary">

<form class="form-horizontal"
      method="POST"
      action="modules/user/proses.php?act=update">

<div class="box-body">

              <input type="hidden" name="id_user" value="<?= $data['id_user']; ?>">

              <div class="form-group">
                <label class="col-sm-2 control-label">Nombre de Usuario</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="username" autocomplete="off" 
                  value="<?php echo $data['username']; ?>" required="">
                </div>
              </div>

              

              <div class="form-group">
                <label class="col-sm-2 control-label">Nombre</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="name_user" autocomplete="off" 
                  value="<?php echo $data['name_user']; ?>" required="">
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Email</label>
                <div class="col-sm-5">
                  <input type="email" class="form-control" name="email" autocomplete="off" 
                  value="<?php echo $data['email']; ?>">
                </div>
              </div>
            
              <div class="form-group">
                <label class="col-sm-2 control-label">Telefono</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="telefono" autocomplete="off" maxlength="13" 
                  onkeypress="return goodchars(event,'0123456789',this)" 
                  value="<?php echo $data['telefono']; ?>">
                </div>
              </div>

              <div class="form-group"> <label class="col-sm-2 control-label">Foto</label> 
                  <div class="col-sm-5"> <input type="file" name="foto"> <br/> <?php if ($data['foto']=="") { ?> 
                    <img style="border:1px solid #eaeaea;border-radius:5px;" 
                    src="images/user/user-default.png" width="90"> 
                    <?php } 
                    else 
                    { ?> <img style="border:1px solid #eaeaea;border-radius:5px;" 
                    src="images/user/<?php echo $data['foto']; ?>" width="90"> 
                    <?php 
                    } ?> 
                </div>
              </div>

            <?php
              $perfiles = ['Super Admin', 'Gerente', 'Operador'];
              $perfilActual = $data['permisos_acceso'];
            ?>


              <div class="form-group">
                <label class="col-sm-2 control-label">Permisos de acceso</label>
                <div class="col-sm-5">
                  <select class="form-control" name="permisos_acceso" required>
                      <?php foreach ($perfiles as $perfil): ?>
                          <option value="<?= $perfil ?>"
                              <?= ($perfil === $perfilActual) ? 'selected' : '' ?>>
                              <?= $perfil ?>
                          </option>
                      <?php endforeach; ?>
                  </select>
                </div>
              </div>

			
			<div class="box-footer">
              <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <input type="submit" class="btn btn-primary btn-submit" name="Guardar" value="Guardar">
                  <a href="?module=user" class="btn btn-default btn-reset">Cancelar</a>
                </div>
              </div>
            </div>

</form>
</div>
</div>
</div>
</section>

<?php if ($esSuperAdmin): ?>
<section class="content">
<div class="box box-warning">

<form method="POST" action="modules/user/proses.php?act=update_permisos">

<h4>Permisos del perfil: <?= $perfilUsuarioEditado ?></h4>

<input type="hidden" name="perfil" value="<?= $perfilUsuarioEditado ?>">

<table class="table table-bordered">
<?php foreach ($modulosSistema as $modulo => $label):
$perm = $permisosPerfil[$modulo] ?? ['puede_acceder'=>0,'puede_ver'=>0];
?>
<tr>
  <td><?= $label ?></td>
  <td><input type="checkbox" name="permisos[<?= $modulo ?>][acceder]"
      <?= $perm['puede_acceder'] ? 'checked' : '' ?>></td>  
  <td><input type="checkbox" name="permisos[<?= $modulo ?>][escribir]"
      <?= $perm['puede_ver'] ? 'checked' : '' ?>></td>
</tr>
<?php endforeach; ?>
</table>

<button class="btn btn-warning">Guardar permisos</button>
</form>

</div>
</section>
<?php endif; ?>

<?php
}
?>