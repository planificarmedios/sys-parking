<?php
session_start();

require_once "../../config/database.php";

if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    echo "<meta http-equiv='refresh' content='0; url=index.php?alert=1'>";
    exit;
}

/* =====================================================
   INSERT
===================================================== */
if (isset($_GET['act']) && $_GET['act'] === 'insert') {

    $denominacion = mysqli_real_escape_string($mysqli, trim($_POST['denominacion']));
    $patente      = mysqli_real_escape_string($mysqli, trim($_POST['patente']));
    $telefonos    = mysqli_real_escape_string($mysqli, trim($_POST['telefonos']));
    $direccion    = mysqli_real_escape_string($mysqli, trim($_POST['direccion']));
    $localidad    = mysqli_real_escape_string($mysqli, trim($_POST['localidad']));

    $categoria_id = (int) $_POST['categoria_id'];
    $tarifa_id    = (int) $_POST['tarifa_id'];
    $fecha_inicio = $_POST['fecha_inicio'];

    /* ===============================
       CALCULAR FECHA FIN
    =============================== */
    $qTarifa = mysqli_query($mysqli, "
        SELECT unidad, valor
        FROM tarifas
        WHERE id = $tarifa_id
        LIMIT 1
    ") or die(mysqli_error($mysqli));

    $tarifa = mysqli_fetch_assoc($qTarifa);

    $fecha_fin = $fecha_inicio;

    if ($tarifa && $tarifa['unidad'] === 'dias') {
        $dias = (int) $tarifa['valor'] - 1;

        $fecha = new DateTime($fecha_inicio);
        $fecha->modify("+{$dias} days");
        $fecha_fin = $fecha->format('Y-m-d');
    }

    /* ===============================
       INSERT
    =============================== */
    $query = mysqli_query($mysqli, "
        INSERT INTO clientes (
            denominacion,
            categoria_id,
            direccion,
            localidad,
            patente,
            telefonos,
            tarifa_id,
            fecha_inicio,
            fecha_fin,
            activo
        ) VALUES (
            '$denominacion',
            $categoria_id,
            '$direccion',
            '$localidad',
            '$patente',
            '$telefonos',
            $tarifa_id,
            '$fecha_inicio',
            '$fecha_fin',
            1
        )
    ") or die(mysqli_error($mysqli));

    header("Location: ../../main.php?module=clients&alert=1");
    exit;
}

/* =====================================================
   UPDATE
===================================================== */
elseif (isset($_GET['act']) && $_GET['act'] === 'update') {

    if (isset($_POST['id_cliente'])) {

        $id_cliente   = (int) $_POST['id_cliente'];
        $denominacion = mysqli_real_escape_string($mysqli, trim($_POST['denominacion']));
        $telefonos    = mysqli_real_escape_string($mysqli, trim($_POST['telefonos']));
        $patente      = mysqli_real_escape_string($mysqli, trim($_POST['patente']));
        $direccion    = mysqli_real_escape_string($mysqli, trim($_POST['direccion']));
        $localidad    = mysqli_real_escape_string($mysqli, trim($_POST['localidad']));
        $activo       = (int) $_POST['activo'];

        // Puede no venir si no lo pusiste en el form edit
        $categoria_id = isset($_POST['categoria_id']) && $_POST['categoria_id'] !== ''
                        ? (int) $_POST['categoria_id']
                        : "NULL";

        $query = mysqli_query($mysqli, "
            UPDATE clientes SET 
                denominacion = '$denominacion',
                categoria_id = $categoria_id,
                patente      = '$patente',
                localidad    = '$localidad',
                telefonos    = '$telefonos',
                activo       = $activo,
                direccion    = '$direccion'
            WHERE id = $id_cliente
        ") or die('Error UPDATE: '.mysqli_error($mysqli));

        header("location: ../../main.php?module=clients&alert=2");
        exit;

    } else {
        die('Falta ID del cliente');
    }
}

/* =====================================================
   DELETE
===================================================== */
elseif (isset($_GET['act']) && $_GET['act'] === 'delete') {

    if (isset($_GET['id'])) {

        $id_cliente = (int) $_GET['id'];

        $query = mysqli_query($mysqli, "
            DELETE FROM clientes 
            WHERE id = $id_cliente
        ") or die('Error DELETE: '.mysqli_error($mysqli));

        header("location: ../../main.php?module=clients&alert=4");
        exit;
    }
}
?>