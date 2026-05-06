<?php
session_start();
require_once "../../config/database.php";
echo ($_GET['act']);

if (empty($_SESSION['username']) && empty($_SESSION['password'])){
	echo "<meta http-equiv='refresh' content='0; url=index.php?alert=1'>";
}

else {


    /* =========================
    INSERT
    ========================= */
    
    if ($_GET['act'] == 'insert') {

    $descripcion = mysqli_real_escape_string($mysqli, $_POST['descripcion']);
    $unidad      = mysqli_real_escape_string($mysqli, $_POST['unidad']);
    $valor       = (int) $_POST['valor'];
    $categoria_id = !empty($_POST['categoria_id']) 
                ? (int) $_POST['categoria_id'] 
                : "NULL";

    // Normalizar monto
    $monto_raw = str_replace('.', '', $_POST['monto']);
    $monto_raw = str_replace(',', '.', $monto_raw);
    $monto = (float) $monto_raw;

    $fraccionable = isset($_POST['es_tarifa_fraccionable']) ? 1 : 0;
    $activo       = isset($_POST['activo']) ? 1 : 0;
    $es_default   = isset($_POST['es_default']) ? (int) $_POST['es_default'] : 0;

    // 👉 si esta es default, desmarco las demás
    if ($es_default == 1) {
      mysqli_query($mysqli, "UPDATE tarifas SET es_default = 0 where categoria_id = $categoria_id");
    }

    $query = mysqli_query($mysqli, "
        INSERT INTO tarifas 
            (descripcion, unidad, valor, monto, es_tarifa_fraccionable, activo, es_default, categoria_id)
        VALUES 
            ('$descripcion', '$unidad', '$valor', '$monto', '$fraccionable', '$activo', '$es_default', '$categoria_id')
    ") or die(mysqli_error($mysqli));

    if ($query) {
        header("location: ../../main.php?module=tarifas&alert=1");
        exit;
    }
}

    /* =========================
    UPDATE
    ========================= */
    elseif ($_GET['act'] == 'update') {

    if (isset($_POST['id'])) {

        $id_tarifa   = (int) $_POST['id'];
        $descripcion = mysqli_real_escape_string($mysqli, $_POST['descripcion']);
        $unidad      = mysqli_real_escape_string($mysqli, $_POST['unidad']);
        $valor       = (int) $_POST['valor'];
        $monto       = (float) $_POST['monto'];
        $fraccionable = isset($_POST['es_tarifa_fraccionable']) ? 1 : 0;
        $activo      = ($_POST['activo'] == 1) ? 1 : 0;
        $es_default   = isset($_POST['es_default']) ? (int) $_POST['es_default'] : 0;
        $categoria_id = !empty($_POST['categoria_id']) ? (int) $_POST['categoria_id'] : NULL;

        // 👉 si marca default, desmarca las otras
        if ($es_default == 1) {
        mysqli_query($mysqli, "UPDATE tarifas SET es_default = 0 
        WHERE id != $id_tarifa
        AND categoria_id = $categoria_id
        ");
        }


        $query = mysqli_query($mysqli, "
            UPDATE tarifas SET
                descripcion = '$descripcion',
                categoria_id = $categoria_id,
                unidad = '$unidad',
                valor = '$valor',
                monto = '$monto',
                es_tarifa_fraccionable = '$fraccionable',
                activo = '$activo',
                es_default = '$es_default'
                WHERE 
                id = '$id_tarifa'
        ") or die(mysqli_error($mysqli));

        if ($query) {
            header("location: ../../main.php?module=tarifas&alert=2");
            exit;
        }

    } else {
        die('Falta el ID de la tarifa');
    }
}

    /* =========================
    DELETE
    ========================= */
    elseif ($_GET['act'] == 'delete') {
        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];

            $query = mysqli_query($mysqli, "DELETE FROM tarifas WHERE id='$id'")
                    or die(mysqli_error($mysqli));

            if ($query) {
                header("location: ../../main.php?module=tarifas&alert=3");
                exit;
            }
        }
    }
 }
?>