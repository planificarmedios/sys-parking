<?php
$perfil = $_SESSION['permisos_acceso'];
$modulosPermitidos = [];

$query = mysqli_query($mysqli, "
    SELECT modulo
    FROM profile_modules
    WHERE perfil = '$perfil'
      AND puede_ver = 1
");

while ($row = mysqli_fetch_assoc($query)) {
    $modulosPermitidos[] = $row['modulo'];
}

function puedeVer($modulo, $lista) {
    return in_array($modulo, $lista);
}
?>

<ul class="sidebar-menu">
  <li class="header">MENU</li>

  <?php if (puedeVer('tarifas', $modulosPermitidos)) { ?>
  <li class="<?= ($_GET['module']=='tarifas' || $_GET['module']=='form_tarifas') ? 'active' : '' ?>">
    <a href="?module=tarifas">
      <i class="fa fa-dollar"></i> <span>Tarifas</span>
    </a>
  </li>
  <?php } ?>

  <?php if (puedeVer('vehiculos', $modulosPermitidos)) { ?>
  <li class="<?= ($_GET['module']=='vehiculos' || $_GET['module']=='form_vehiculos') ? 'active' : '' ?>">
    <a href="?module=vehiculos">
      <i class="fa fa-car"></i> <span>E/S de Vehículos</span>
    </a>
  </li>
  <?php } ?>

  <?php if (puedeVer('cobranzas', $modulosPermitidos)) { ?>
  <li class="<?= ($_GET['module']=='cobranzas' || $_GET['module']=='form_cobranzas') ? 'active' : '' ?>">
    <a href="?module=cobranzas">
      <i class="fa fa-money"></i> <span>Cobranzas</span>
    </a>
  </li>
  <?php } ?>

  <?php if (puedeVer('simulador', $modulosPermitidos)) { ?>
  <li class="<?= ($_GET['module']=='simulador' || $_GET['module']=='form_simulador') ? 'active' : '' ?>">
    <a href="?module=simulador">
      <i class="fa fa-flask"></i> <span>Simulador</span>
    </a>
  </li>
  <?php } ?>

  <?php if (puedeVer('caja', $modulosPermitidos)) { ?>
  <li class="<?= ($_GET['module']=='caja' || $_GET['module']=='form_caja') ? 'active' : '' ?>">
    <a href="?module=caja">
      <i class="fa fa-money"></i> <span>Caja</span>
    </a>
  </li>
  <?php } ?>

  <?php if (puedeVer('categorias', $modulosPermitidos)) { ?>
  <li class="<?= ($_GET['module']=='categorias' || $_GET['module']=='form_categorias') ? 'active' : '' ?>">
    <a href="?module=categorias">
      <i class="fa fa-flask"></i> <span>Categoría de Vehículos</span>
    </a>
  </li>
  <?php } ?>

  <?php if (puedeVer('clients', $modulosPermitidos)) { ?>
  <li class="<?= ($_GET['module']=='clients') ? 'active' : '' ?>">
    <a href="?module=clients">
      <i class="fa fa-users"></i> <span>Clientes</span>
    </a>
  </li>
  <?php } ?>

  <?php if (puedeVer('user', $modulosPermitidos)) { ?>
  <li class="<?= ($_GET['module']=='user' || $_GET['module']=='form_user') ? 'active' : '' ?>">
    <a href="?module=user">
      <i class="fa fa-user"></i> <span>Usuarios</span>
    </a>
  </li>
  <?php } ?>

  <?php if ($perfil === 'Super Admin') { ?>
  <li>
    <a href="BackUp/proces.php">
      <i class="fa fa-database"></i> <span>BackUp</span>
    </a>
  </li>
  <?php } ?>
</ul>