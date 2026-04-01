<?php

/*
require_once "config/database.php";
require_once "config/fungsi_tanggal.php";
require_once "config/fungsi_rupiah.php";


if (empty($_SESSION['username']) && empty($_SESSION['password'])){
	echo "<meta http-equiv='refresh' content='0; url=index.php?alert=1'>";
}

else {
	
	if ($_GET['module'] == 'medicines') {
		include "modules/medicines/view.php";
	}

	elseif ($_GET['module'] == 'clients') {
		include "modules/clients/view.php";
	}

	elseif ($_GET['module'] == 'simulador') {
		include "modules/simulador/view.php";
	}
	

	elseif ($_GET['module'] == 'form_medicines') {
		include "modules/medicines/form.php";
	}

	elseif ($_GET['module'] == 'form_clients') {
		include "modules/clients/form.php";
	}

	elseif ($_GET['module'] == 'medicines_transaction') {
		include "modules/medicines_transaction/view.php";
	}

	elseif ($_GET['module'] == 'form_medicines_transaction') {
		include "modules/medicines_transaction/form.php";
	}
	

	elseif ($_GET['module'] == 'stock_inventory') {
		include "modules/stock_inventory/view.php";
	}

	elseif ($_GET['module'] == 'stock_report') {
		include "modules/stock_report/view.php";
	}

	elseif ($_GET['module'] == 'user') {
		include "modules/user/view.php";
	}


	elseif ($_GET['module'] == 'form_user') {
		include "modules/user/form.php";
	}

	elseif ($_GET['module'] == 'profile') {
		include "modules/profile/view.php";
		}


	elseif ($_GET['module'] == 'form_profile') {
		include "modules/profile/form.php";
	}


	elseif ($_GET['module'] == 'tarifas') {
		include "modules/tarifas/view.php";
	}

	elseif ($_GET['module'] == 'form_tarifas') {
		include "modules/tarifas/form.php";
	}

	elseif ($_GET['module'] == 'vehiculos') {
		include "modules/vehiculos/view.php";
	}

	elseif ($_GET['module'] == 'cobranzas') {
		include "modules/cobranzas/view.php";
	}

	elseif ($_GET['module'] == 'form_vehiculos') {
		include "modules/vehiculos/form.php";
	}

	elseif ($_GET['module'] == 'password') {
		include "modules/password/view.php";
	}

	elseif ($_GET['module'] == 'password') {
		include "modules/password/view.php";
	}
}
 */
?>

<?php
// Seguridad de sesión (ya debería venir validada desde main.php,
// pero esto no molesta como doble check)
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    header('Location: index.php?alert=1');
    exit;
}

$module = $_GET['module'] ?? 'vehiculos';

// 🔧 NORMALIZACIÓN DEL MÓDULO
$moduloBase = $module;

// todos los forms dependen del módulo base
if (str_starts_with($module, 'form_')) {
    $moduloBase = str_replace('form_', '', $module);
}

if (!puedeAccederModulo($_SESSION['permisos_acceso'], $moduloBase)) {
    include "modules/error/403.php";
    exit;
}

// Router
switch ($module) {

    case 'medicines':
        include "modules/medicines/view.php";
        break;

    case 'clients':
        include "modules/clients/view.php";
        break;

    case 'simulador':
        include "modules/simulador/view.php";
        break;

    case 'form_medicines':
        include "modules/medicines/form.php";
        break;

    case 'form_clients':
        include "modules/clients/form.php";
        break;

    case 'medicines_transaction':
        include "modules/medicines_transaction/view.php";
        break;

    case 'form_medicines_transaction':
        include "modules/medicines_transaction/form.php";
        break;

    case 'stock_inventory':
        include "modules/stock_inventory/view.php";
        break;

    case 'stock_report':
        include "modules/stock_report/view.php";
        break;

    case 'user':
        include "modules/user/view.php";
        break;

    case 'form_user':
        include "modules/user/form.php";
        break;

    case 'profile':
        include "modules/profile/view.php";
        break;

    case 'form_profile':
        include "modules/profile/form.php";
        break;

    case 'tarifas':
        include "modules/tarifas/view.php";
        break;

    case 'form_tarifas':
        include "modules/tarifas/form.php";
        break;

    case 'vehiculos':
        include "modules/vehiculos/view.php";
        break;

    case 'form_vehiculos':
        include "modules/vehiculos/form.php";
        break;

    case 'cobranzas':
        include "modules/cobranzas/view.php";
        break;

    case 'password':
        include "modules/password/view.php";
        break;

    default:
        include "modules/error/404.php";
        break;
}