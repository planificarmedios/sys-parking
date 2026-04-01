

<?php
session_start();


require_once "../../config/database.php";


if (empty($_SESSION['username']) && empty($_SESSION['password'])){
	echo "<meta http-equiv='refresh' content='0; url=index.php?alert=1'>";
} else {

if (isset($_GET['act']) && $_GET['act'] === 'insert') {

    // Datos del formulario
    $denominacion = mysqli_real_escape_string($mysqli, trim($_POST['denominacion']));
    $patente      = mysqli_real_escape_string($mysqli, trim($_POST['patente']));
    $telefonos    = mysqli_real_escape_string($mysqli, trim($_POST['telefonos']));
    $direccion    = mysqli_real_escape_string($mysqli, trim($_POST['direccion']));
    $localidad    = mysqli_real_escape_string($mysqli, trim($_POST['localidad']));

    $tarifa_id    = (int) $_POST['tarifa_id'];
    $fecha_inicio = $_POST['fecha_inicio'];

    // Traer unidad y valor de la tarifa seleccionada
    $qTarifa = mysqli_query($mysqli, "
        SELECT unidad, valor
        FROM tarifas
        WHERE id = $tarifa_id
        LIMIT 1
    ") or die(mysqli_error($mysqli));

    $tarifa = mysqli_fetch_assoc($qTarifa);

    // Por defecto, fecha fin = fecha inicio
    $fecha_fin = $fecha_inicio;

    // Calcular fecha fin solo si la tarifa es por días
    if ($tarifa && $tarifa['unidad'] === 'dias') {

        // Restamos 1 para no contar dos veces el día inicial
        $dias = (int) $tarifa['valor'] - 1;

        $fecha = new DateTime($fecha_inicio);
        $fecha->modify("+{$dias} days");
        $fecha_fin = $fecha->format('Y-m-d');
    }

    // Insertar cliente
    $query = mysqli_query($mysqli, "
        INSERT INTO clientes
        (
            denominacion,
            direccion,
            localidad,
            patente,
            telefonos,
            tarifa_id,
            fecha_inicio,
            fecha_fin,
            activo
        )
        VALUES
        (
            '$denominacion',
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

    // Redirección
    header("Location: ../../main.php?module=clients&alert=1");
    exit;
}
	
	elseif ($_GET['act']=='update') {
		if (isset($_POST['Guardar'])) {
			if (isset($_POST['id_cliente'])) {
				$id_cliente          = mysqli_real_escape_string($mysqli, trim($_POST['id_cliente']));
				$denominacion        = mysqli_real_escape_string($mysqli, trim($_POST['denominacion']));
				$telefonos           = mysqli_real_escape_string($mysqli, trim($_POST['telefonos']));
                $patente             =  mysqli_real_escape_string($mysqli, trim($_POST['patente']));
				$direccion           = mysqli_real_escape_string($mysqli, trim($_POST['direccion']));
				$localidad           = mysqli_real_escape_string($mysqli, trim($_POST['localidad']));
                $activo           = mysqli_real_escape_string($mysqli, trim($_POST['activo']));
				

                $query = mysqli_query($mysqli, "UPDATE clientes SET 
				denominacion = '$denominacion',                                             
				patente  =  '$patente',
                localidad = '$localidad', 
                telefonos = '$telefonos',
                activo =     '$activo',
				direccion = '$direccion'
                WHERE id  = '$id_cliente'")
                or die('error: '.mysqli_error($mysqli));

    
                if ($query) {
                    header("location: ../../main.php?module=clients&alert=2");
                }
				
				
			}
		}
	}

	elseif ($_GET['act']=='delete') {
        if (isset($_GET['id'])) {
            $id_cliente = $_GET['id'];
      
										
			$query = mysqli_query($mysqli,  "DELETE FROM clientes WHERE id = '$id_cliente'")
                                            or die('error '.mysqli_error($mysqli));

            if ($query) {
                     header("location: ../../main.php?module=clients&alert=4");
            }
        }
    } 


}		
?>