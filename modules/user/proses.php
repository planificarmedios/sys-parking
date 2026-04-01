<?php
session_start();
require_once "../../config/database.php";

// 🔐 Seguridad básica
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    header("Location: ../../index.php?alert=1");
    exit;
}

$act = $_GET['act'] ?? '';

/* ======================================================
   INSERT USUARIO
====================================================== */
if ($act == 'insert') {

    if (isset($_POST['Guardar'])) {

        $username         = mysqli_real_escape_string($mysqli, trim($_POST['username']));
        $password         = md5(mysqli_real_escape_string($mysqli, trim($_POST['password'])));
        $name_user        = mysqli_real_escape_string($mysqli, trim($_POST['name_user']));
        $permisos_acceso  = mysqli_real_escape_string($mysqli, trim($_POST['permisos_acceso']));

        mysqli_query($mysqli, "
            INSERT INTO usuarios (username,password,name_user,permisos_acceso)
            VALUES ('$username','$password','$name_user','$permisos_acceso')
        ") or die(mysqli_error($mysqli));

        header("Location: ../../main.php?module=user&alert=1");
        exit;
    }
}

/* ======================================================
   UPDATE USUARIO
====================================================== */
elseif ($act == 'update') {

    if (isset($_POST['Guardar'], $_POST['id_user'])) {

        $id_user          = mysqli_real_escape_string($mysqli, $_POST['id_user']);
        $username         = mysqli_real_escape_string($mysqli, $_POST['username']);
        $name_user        = mysqli_real_escape_string($mysqli, $_POST['name_user']);
        $email            = mysqli_real_escape_string($mysqli, $_POST['email']);
        $telefono         = mysqli_real_escape_string($mysqli, $_POST['telefono']);
        $permisos_acceso  = mysqli_real_escape_string($mysqli, $_POST['permisos_acceso']);

        $password = !empty($_POST['password'])
            ? md5(mysqli_real_escape_string($mysqli, $_POST['password']))
            : null;

        // FOTO
        $foto_sql = "";
        if (!empty($_FILES['foto']['name'])) {

            $name_file = $_FILES['foto']['name'];
            $tmp_file  = $_FILES['foto']['tmp_name'];
            $ext       = strtolower(pathinfo($name_file, PATHINFO_EXTENSION));

            if (in_array($ext, ['jpg','jpeg','png'])) {
                move_uploaded_file($tmp_file, "../../images/user/".$name_file);
                $foto_sql = ", foto = '$name_file'";
            }
        }

        $password_sql = $password ? ", password = '$password'" : "";

        mysqli_query($mysqli, "
            UPDATE usuarios SET
                username = '$username',
                name_user = '$name_user',
                email = '$email',
                telefono = '$telefono',
                permisos_acceso = '$permisos_acceso'
                $password_sql
                $foto_sql
            WHERE id_user = '$id_user'
        ") or die(mysqli_error($mysqli));

        header("Location: ../../main.php?module=user&alert=2");
        exit;
    }
}

/* ======================================================
   ACTIVAR / DESACTIVAR
====================================================== */
elseif ($act == 'on' || $act == 'off') {

    if (isset($_GET['id'])) {

        $status = ($act == 'on') ? 'activo' : 'bloqueado';
        $id_user = $_GET['id'];

        mysqli_query($mysqli, "
            UPDATE usuarios
            SET status = '$status'
            WHERE id_user = '$id_user'
        ");

        header("Location: ../../main.php?module=user&alert=" . ($act == 'on' ? 3 : 4));
        exit;
    }
}

/* ======================================================
   UPDATE PERMISOS (PERFIL)
====================================================== */
elseif ($act == 'update_permisos') {

    // 🔐 Solo Super Admin
    if ($_SESSION['permisos_acceso'] !== 'Super Admin') {
        header("Location: ../../main.php?alert=403");
        exit;
    }

    $perfil   = $_POST['perfil']   ?? null;
    $permisos = $_POST['permisos'] ?? [];

    if (!$perfil || $perfil === 'Super Admin') {
        header("Location: ../../main.php?module=user");
        exit;
    }

    // Resetear permisos
    mysqli_query($mysqli, "
        UPDATE profile_modules
        SET puede_ver = 0, puede_acceder = 0
        WHERE perfil = '$perfil'
    ");

    foreach ($permisos as $modulo => $on) {

        $check = mysqli_query($mysqli, "
            SELECT id
            FROM profile_modules
            WHERE perfil = '$perfil' AND modulo = '$modulo'
            LIMIT 1
        ");

        if (mysqli_num_rows($check)) {
            mysqli_query($mysqli, "
                UPDATE profile_modules
                SET puede_ver = 1, puede_acceder = 1
                WHERE perfil = '$perfil' AND modulo = '$modulo'
            ");
        } else {
            mysqli_query($mysqli, "
                INSERT INTO profile_modules (perfil, modulo, puede_ver, puede_acceder)
                VALUES ('$perfil','$modulo',1,1)
            ");
        }
    }

    header("Location: ../../main.php?module=user&alert=permisos_ok");
    exit;
}