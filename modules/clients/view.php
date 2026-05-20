<?php

$perfil = trim($_SESSION['permisos_acceso'] ?? '');

/* =========================================
FILTRO ESTADO
========================================= */

$filtro_estado = $_GET['estado'] ?? 'activos';

$where = '';

switch ($filtro_estado) {

    case 'inactivos':

        $where = "WHERE c.activo = 0";

        break;

    case 'todos':

        $where = "";

        break;

    default:

        $where = "WHERE c.activo = 1";

        $filtro_estado = 'activos';

        break;
}

echo "
<script>

console.log(
    'Perfil usuario:',
    '".($perfil ?? 'SIN PERFIL')."'
);

</script>
";
?>

<section class="content-header">

    <h1>
        <i class="fa fa-user icon-title"></i>
        Gestión de Clientes
    </h1>

    <div style="
        margin-top:15px;
        display:flex;
        gap:10px;
        justify-content:flex-end;
        align-items:center;
        flex-wrap:wrap;
    ">

        <!-- FILTRO -->

        <select id="filtro_estado"
                class="form-control"
                style="width:180px;">

            <option value="activos"
                <?= $filtro_estado == 'activos' ? 'selected' : '' ?>>

                Activos

            </option>

            <option value="inactivos"
                <?= $filtro_estado == 'inactivos' ? 'selected' : '' ?>>

                Inactivos

            </option>

            <option value="todos"
                <?= $filtro_estado == 'todos' ? 'selected' : '' ?>>

                Todos

            </option>

        </select>

        <!-- BOTON RENOVAR -->

        <button
            id="btnRenovarMasivo"
            type="button"
            class="btn btn-info"
            style="display:none;"
            data-toggle="modal"
            data-target="#modalRenovar">

            <i class="glyphicon glyphicon-calendar"></i>
            Renovar vigencias

        </button>

        <!-- BOTON AGREGAR -->

        <a class="btn btn-success btn-social"
           href="?module=form_clients&form=add"
           title="Agregar"
           data-toggle="tooltip">

            <i class="fa fa-plus"></i>
            Agregar

        </a>

    </div>

</section>

<!-- Main content -->

<section class="content">

    <div class="row">

        <div class="col-md-12">

        <?php

        if (empty($_GET['alert'])) {

            echo "";

        }

        elseif ($_GET['alert'] == 1) {

            echo "
            <div class='alert alert-success alert-dismissable'>

                <button type='button'
                        class='close'
                        data-dismiss='alert'
                        aria-hidden='true'>

                    &times;

                </button>

                <h4>
                    <i class='icon fa fa-check-circle'></i>
                    Exito!
                </h4>

                Los nuevos datos de usuario se ha registrado correcamente.

            </div>";
        }

        elseif ($_GET['alert'] == 2) {

            echo "
            <div class='alert alert-success alert-dismissable'>

                <button type='button'
                        class='close'
                        data-dismiss='alert'
                        aria-hidden='true'>

                    &times;

                </button>

                <h4>
                    <i class='icon fa fa-check-circle'></i>
                    Exito!
                </h4>

                Los datos del cliente ha sido cambiado satisfactoriamente.

            </div>";
        }

        elseif ($_GET['alert'] == 3) {

            echo "
            <div class='alert alert-success alert-dismissable'>

                <button type='button'
                        class='close'
                        data-dismiss='alert'
                        aria-hidden='true'>

                    &times;

                </button>

                <h4>
                    <i class='icon fa fa-check-circle'></i>
                    Exito!
                </h4>

                El usuario ha sido activado correctamente.

            </div>";
        }

        elseif ($_GET['alert'] == 4) {

            echo "
            <div class='alert alert-danger alert-dismissable'>

                <button type='button'
                        class='close'
                        data-dismiss='alert'
                        aria-hidden='true'>

                    &times;

                </button>

                <h4>
                    <i class='icon fa fa-check-circle'></i>
                    Exito!
                </h4>

                Registro Eliminado.

            </div>";
        }

        ?>

            <div class="box box-primary">

                <div class="box-body table-responsive">

                    <table id="dataTables1"
                           class="table table-bordered table-striped table-hover">

                        <thead>

                            <tr>

                                <th width="30">

                                    <input type="checkbox"
                                           id="check_todos">

                                </th>

                                <th class="center">
                                    Patente
                                </th>

                                <th class="center">
                                    Categoría
                                </th>

                                <th class="center">
                                    Cliente
                                </th>

                                <th class="center">
                                    Fecha Inicio
                                </th>

                                <th class="center">
                                    Fecha Fin
                                </th>

                                <th class="center">
                                    Estado
                                </th>

                                <th class="center">
                                    Tarifa
                                </th>

                                <th class="center">
                                    Acciones
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php

                        $hoy = date('Y-m-d');

                        $query = mysqli_query($mysqli, "

                            SELECT
                                c.*,
                                c.telefonos AS celular,
                                t.descripcion AS tarifa,
                                cat.nombre AS categoria

                            FROM clientes c

                            LEFT JOIN tarifas t
                                ON t.id = c.tarifa_id

                            LEFT JOIN categorias cat
                                ON cat.id = c.categoria_id

                            $where

                            ORDER BY c.id ASC

                        ") or die('error: '.mysqli_error($mysqli));

                        while ($data = mysqli_fetch_assoc($query)) {

                            $fecha_inicio_db = $data['fecha_inicio'];

                            $fecha_fin_db = $data['fecha_fin'];

                            $fecha_inicio =
                                date(
                                    'd/m/Y',
                                    strtotime($fecha_inicio_db)
                                );

                            $fecha_fin =
                                date(
                                    'd/m/Y',
                                    strtotime($fecha_fin_db)
                                );

                            $estado = '';

                            if ($data['activo'] == 0) {

                                $estado = 'Inactivo';

                            }

                            elseif ($fecha_fin_db >= $hoy) {

                                $estado = 'Vigente';

                            }

                            else {

                                $estado = 'Vencido';

                            }

                            $label = '';

                            switch ($estado) {

                                case 'Vigente':

                                    $label =
                                        "<span class='badge-custom badge-vigente'>
                                            Vigente
                                        </span>";

                                    break;

                                case 'Vencido':

                                    $label =
                                        "<span class='badge-custom badge-vencido'>
                                            Vencido
                                        </span>";

                                    break;

                                default:

                                    $label =
                                        "<span class='badge-custom badge-inactivo'>
                                            Inactivo
                                        </span>";
                            }

                            $vencido =
                                strtotime($data['fecha_fin'])
                                <
                                strtotime(date('Y-m-d'));

                            $activo =
                                (int)$data['activo'] === 1;

                            $filaStyle = '';

                            if ((int)$data['activo'] === 0) {

                                $filaStyle = "
                                    style='
                                        color:#999;
                                        background:#f5f5f5;
                                    '
                                ";
                            }

                            echo "<tr $filaStyle>";

                            echo "<td>";

                            if ($vencido && $activo) {

                                echo "
                                <input type='checkbox'
                                       class='check-cliente'
                                       value='{$data['id']}'>";
                            }

                            echo "</td>";

                            echo "

                            <td>
                                <center>
                                    ".strtoupper($data['patente'])."
                                </center>
                            </td>

                            <td>
                                <center>
                                    {$data['categoria']}
                                </center>
                            </td>

                            <td>
                                <center>
                                    {$data['denominacion']}
                                </center>
                            </td>

                            <td>
                                <center>
                                    {$fecha_inicio}
                                </center>
                            </td>

                            <td>
                                <center>
                                    {$fecha_fin}
                                </center>
                            </td>

                            <td class='center'>
                                $label
                            </td>

                            <td>
                                <center>
                                    ".($data['tarifa'] ?? '-')."
                                </center>
                            </td>

                            <td class='center'
                                width='120'>

                                <div style='
                                    display:flex;
                                    gap:6px;
                                    justify-content:center;
                                '>

                                    <a data-toggle='tooltip'
                                       title='Modificar'
                                       class='btn btn-primary btn-sm'

                                       href='?module=form_clients&form=edit&id={$data['id']}&categoria_id={$data['categoria_id']}'>

                                        <i style='color:#fff'
                                           class='glyphicon glyphicon-edit'></i>

                                    </a>";

                                    if ($perfil == 'Super Admin') {

                                        echo "

                                        <a data-toggle='tooltip'
                                           title='Eliminar'
                                           class='btn btn-danger btn-sm'

                                           href='modules/clients/proces.php?act=delete&id={$data['id']}'

                                           onclick=\"return confirm('¿Eliminar registro?');\">

                                            <i style='color:#fff'
                                               class='glyphicon glyphicon-trash'></i>

                                        </a>";
                                    }

                            echo "

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

<!-- =========================================
MODAL RENOVAR VIGENCIAS
========================================= -->

<div class="modal fade"
     id="modalRenovar"
     tabindex="-1"
     role="dialog">

    <div class="modal-dialog" role="document">

        <form method="POST"
              action="modules/clients/proces.php?act=renovar_masivo">

            <div class="modal-content">

                <div class="modal-header">

                    <button type="button"
                            class="close"
                            data-dismiss="modal">

                        &times;

                    </button>

                    <h4 class="modal-title">

                        <i class="glyphicon glyphicon-calendar"></i>
                        Renovar vigencias masivamente

                    </h4>

                </div>

                <div class="modal-body">

                    <div id="contenedor_clientes"></div>

                    <div class="alert alert-info">

                        <b>Importante:</b>

                        <br><br>

                        • Solo se renovarán clientes vencidos.
                        <br>

                        • Cada cliente conservará su tarifa actual.
                        <br>

                        • También se mantendrá su categoría actual.
                        <br>

                        • La nueva vigencia se calculará desde el vencimiento anterior.

                    </div>

                    <p>

                        ¿Deseás continuar con la renovación masiva?

                    </p>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-default"
                            data-dismiss="modal">

                        Cancelar

                    </button>

                    <button type="submit"
                            class="btn btn-success">

                        <i class="glyphicon glyphicon-ok"></i>
                        Confirmar renovación

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const checks =
        document.querySelectorAll('.check-cliente');

    const btn =
        document.getElementById('btnRenovarMasivo');

    const checkTodos =
        document.getElementById('check_todos');

    function actualizarBoton() {

        const alguno =
            document.querySelectorAll(
                '.check-cliente:checked'
            ).length > 0;

        btn.style.display =
            alguno
            ? 'inline-block'
            : 'none';
    }

    checks.forEach(ch => {

        ch.addEventListener(
            'change',
            actualizarBoton
        );

    });

    checkTodos.addEventListener('change', function () {

        checks.forEach(ch => {

            ch.checked = this.checked;

        });

        actualizarBoton();

    });

});

document.addEventListener('DOMContentLoaded', function () {

    const modalBtn =
        document.getElementById('btnRenovarMasivo');

    modalBtn.addEventListener('click', function () {

        const checks =
            document.querySelectorAll(
                '.check-cliente:checked'
            );

        const contenedor =
            document.getElementById(
                'contenedor_clientes'
            );

        contenedor.innerHTML = '';

        checks.forEach(ch => {

            const input =
                document.createElement('input');

            input.type = 'hidden';

            input.name = 'clientes[]';

            input.value = ch.value;

            contenedor.appendChild(input);

        });

    });

});

document.getElementById('filtro_estado')
.addEventListener('change', function () {

    const estado = this.value;

    window.location.href =
        '?module=clients&estado=' + estado;

});

</script>