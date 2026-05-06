<?php
session_start();

require_once "../../config/database.php";

if (empty($_SESSION['username']) && empty($_SESSION['password'])){
	echo "<meta http-equiv='refresh' content='0; url=../../index.php?alert=1'>";
} else {

	// ================= INSERT =================
	if (isset($_GET['act']) && $_GET['act'] === 'insert') {

		$nombre = mysqli_real_escape_string($mysqli, trim($_POST['nombre']));
		$activo = (int) $_POST['activo'];

		$query = mysqli_query($mysqli, "
			INSERT INTO categorias (nombre, activo)
			VALUES ('$nombre', $activo)
		") or die(mysqli_error($mysqli));

		if ($query) {
			header("Location: ../../main.php?module=categorias&alert=1");
			exit;
		}
	}

	// ================= UPDATE =================
	elseif ($_GET['act'] == 'update') {

		if (isset($_POST['id'])) {

			$id     = (int) $_POST['id'];
			$nombre = mysqli_real_escape_string($mysqli, trim($_POST['nombre']));
			$activo = (int) $_POST['activo'];

			$query = mysqli_query($mysqli, "
				UPDATE categorias SET
					nombre = '$nombre',
					activo = $activo
				WHERE id = $id
			") or die(mysqli_error($mysqli));

			if ($query) {
				header("Location: ../../main.php?module=categorias&alert=2");
				exit;
			}
		}
	}

	// ================= DELETE =================
	elseif ($_GET['act'] == 'delete') {

		if (isset($_GET['id'])) {

			$id = (int) $_GET['id'];

			$query = mysqli_query($mysqli, "
				DELETE FROM categorias WHERE id = $id
			") or die(mysqli_error($mysqli));

			if ($query) {
				header("Location: ../../main.php?module=categorias&alert=3");
				exit;
			}
		}
	}

}
?>