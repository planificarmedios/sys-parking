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

    case 'categorias':
        include "modules/categorias/view.php";
        break;

    case 'simulador':
        include "modules/simulador/view.php";
        break;

    case 'caja':
        include "modules/caja/view.php";
        break;

    case 'form_medicines':
        include "modules/medicines/form.php";
        break;

    case 'form_clients':
        include "modules/clients/form.php";
        break;

    case 'form_categorias':
        include "modules/categorias/form.php";
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