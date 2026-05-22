<?php

if ($_GET['form'] == 'add') {

?>

<section class="content-header">
  <h1>
    <i class="fa fa-car icon-title"></i> Vehículos
  </h1>
</section>

<section class="content">
  <div class="row">
    <div class="col-md-12">

      <div class="box box-primary">

        <form class="form-horizontal" method="POST" action="modules/vehiculos/proces.php?act=insert">

            <div class="box-body">

                <!-- PATENTE -->

                <div class="form-group">

                    <label class="col-sm-2 control-label">
                        Patente
                    </label>

                    <div class="col-sm-5">

                        <input
                            id="patente"
                            type="text"
                            name="patente"
                            class="form-control"
                            autocomplete="on"
                            required
                            autofocus>

                    </div>

                </div>

                <!-- FECHA -->

                <div class="form-group">

                    <label class="col-sm-2 control-label">
                        Fecha ingreso
                    </label>

                    <div class="col-sm-5">

                        <input type="text"
                            class="form-control"
                            value="<?= date('d/m/Y') ?>"
                            readonly>

                    </div>

                </div>

                <!-- HORA -->

                <div class="form-group">

                    <label class="col-sm-2 control-label">
                        Hora ingreso
                    </label>

                    <div class="col-sm-5">

                        <input type="text"
                            class="form-control"
                            value="<?= date('H:i') ?>"
                            readonly>

                    </div>

                </div>

                <!-- VENCE -->

                <div class="form-group"
                    id="grupo_fecha_fin"
                    style="display:none;">

                    <label class="col-sm-2 control-label">
                        Vence
                    </label>

                    <div class="col-sm-5">

                        <input type="text"
                            id="fecha_fin_abono"
                            class="form-control"
                            readonly>

                    </div>

                </div>

                <!-- CATEGORIA -->

                <div class="form-group">

                    <label class="col-sm-2 control-label">
                        Categoría Vehículo
                    </label>

                    <div class="col-sm-5">

                        <select id="categoria_id"
                                name="categoria_id"
                                class="form-control"
                                required>

                            <option value="">
                                Seleccionar categoría
                            </option>

                            <?php

                            $cats = mysqli_query(
                                $mysqli,
                                "SELECT *
                                FROM categorias
                                WHERE activo = 1
                                ORDER BY nombre ASC"
                            );

                            while ($c = mysqli_fetch_assoc($cats)) {

                                echo "
                                <option value='{$c['id']}'>
                                    {$c['nombre']}
                                </option>";
                            }

                            ?>

                        </select>

                    </div>

                </div>

                <!-- TARIFA -->

                <div class="form-group">

                    <label class="col-sm-2 control-label">
                        Tarifa
                    </label>

                    <div class="col-sm-5">

                        <select id="tarifa_id"
                                class="form-control"
                                required>

                            <option value="">
                                Seleccionar tarifa
                            </option>

                        </select>

                        <input type="hidden"
                            name="tarifa_id"
                            id="tarifa_hidden">

                    </div>

                </div>

                <!-- MEDIO COBRO -->

                <div class="form-group"
                    id="grupo_pago_anticipado"
                    style="display:none;">

                    <label class="col-sm-2 control-label">
                        Medio de cobro
                    </label>

                    <div class="col-sm-5">

                        <select name="medio_cobro"
                                id="medio_cobro"
                                class="form-control">

                            <option value="">
                                Seleccionar medio
                            </option>

                            <option value="efectivo">
                                Efectivo
                            </option>

                            <option value="mercadopago">
                                Mercado Pago
                            </option>

                            <option value="transferencia">
                                Transferencia
                            </option>

                            <option value="CuentaDNI">
                                Cuenta DNI
                            </option>

                            <option value="tarjeta">
                                Tarjeta
                            </option>

                        </select>

                    </div>

                </div>

                <!-- TOTAL ESTADIA -->

                <div class="form-group"
                    id="grupo_monto_estadia"
                    style="display:none;">

                    <label class="col-sm-2 control-label">
                        Total a cobrar
                    </label>

                    <div class="col-sm-5">

                        <input type="text"
                            id="monto_estadia"
                            class="form-control"
                            readonly>

                    </div>

                </div>

                <!-- MENSAJE -->

                <div class="form-group"
                    id="grupo_mensaje_estadia"
                    style="display:none;">

                    <div class="col-sm-offset-2 col-sm-5">

                        <div class="alert alert-info"
                            style="margin-bottom:0;">

                            <i class="fa fa-info-circle"></i>

                            Esta tarifa se cobra al momento
                            del ingreso.

                        </div>

                    </div>

                </div>

            </div>

                <!-- FOOTER -->

                <div class="box-footer">

                    <!-- TICKET -->

                    <div class="form-group">

                        <label class="col-sm-2 control-label">
                            Ticket
                        </label>

                        <div class="col-sm-5">

                            <label class="radio-inline">

                                <input type="radio"
                                    name="modo_ticket"
                                    value="directo"
                                    checked>

                                Imprimir directamente

                            </label>

                        </div>

                    </div>

                    <!-- BOTONES -->

                    <div class="form-group">

                        <div class="col-sm-offset-2 col-sm-10">

                            <button type="submit"
                                    class="btn btn-primary">

                                Guardar

                            </button>

                            <a href="?module=vehiculos"
                            class="btn btn-default">

                                Cancelar

                            </a>

                        </div>

                    </div>

                </div>

        </form>

      </div>

    </div>
  </div>
</section>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const patenteInput = document.getElementById('patente');
    const categoria    = document.getElementById('categoria_id');
    const tarifa       = document.getElementById('tarifa_id');
    const tarifaHidden = document.getElementById('tarifa_hidden');

    patenteInput.addEventListener('blur', function () {

        const patente = this.value.trim().toUpperCase();
        document.getElementById('categoria_id').value = '';

        if (patente.length < 5) return;

        verificarCliente(patente);

    });

    categoria.addEventListener('change', function () {       
        cargarTarifas(this.value);
    });

    const grupoPago =
    document.getElementById(
        'grupo_pago_anticipado'
    );

    const grupoMonto =
        document.getElementById(
            'grupo_monto_estadia'
        );

    const grupoMensaje =
        document.getElementById(
            'grupo_mensaje_estadia'
        );

    const montoEstadia =
        document.getElementById(
            'monto_estadia'
        );

        tarifa.addEventListener('change', function () {

            document.getElementById(
                'tarifa_hidden'
            ).value = this.value;

            actualizarVistaEstadia();

        });

    });

function verificarCliente(patente) {

    fetch('/sys_parking/modules/vehiculos/verificar_abono.php', {

        method: 'POST',

        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },

        body: 'patente=' + encodeURIComponent(patente)

    })

    .then(res => res.json())

    .then(data => {
        manejarCliente(data);

    })

    .catch(err => {

        console.error(err);

    });

}

/* =====================================================
   MANEJAR RESPUESTA
===================================================== */

function manejarCliente(data) {
    
    const patenteInput =
        document.getElementById('patente');

    const categoria =
        document.getElementById('categoria_id');

    const tarifa =
        document.getElementById('tarifa_id');

    const btnGuardar =
        document.querySelector(
            'button[type="submit"]'
        );

    /* =========================================
       VEHÍCULO YA INGRESADO
    ========================================= */

    if (data.vehiculo_abierto) {

        Swal.fire({
            icon: 'warning',
            title: 'Vehículo ya ingresado',
            text:
                'La patente ya posee un ingreso sin registrar salida.'
        });

        btnGuardar.disabled = true;

        patenteInput.value = '';

        setTimeout(() => {

            patenteInput.focus();

        }, 300);

        return;
    }

    btnGuardar.disabled = false;

    /* =========================================
       CLIENTE CON ABONO
    ========================================= */

    if (data.tiene_abono) {

        categoria.value =
            String(data.categoria_id);

        cargarTarifas(
            data.categoria_id,
            data.tarifa_id,
            data.vigente,
            true
        );

        mostrarFechaFin(
            data.fecha_egreso
        );

        categoria.disabled = true;

        if (data.vigente) {

            toastSuccess(
                'Abono vigente hasta '
                + data.fecha_egreso
            );

        } else {

            toastWarning(
                'Abono vencido el '
                + data.fecha_egreso
            );

            tarifa.disabled = false;
        }

    }

    /* =========================================
       CLIENTE SIN ABONO
    ========================================= */

    else {

        ocultarFechaFin();

        categoria.disabled = false;

        tarifa.disabled = false;

        tarifa.innerHTML = `
            <option value="">
                Seleccionar tarifa
            </option>
        `;
    }
}

function cargarTarifas(
    categoriaId,
    tarifaSeleccionada = null,
    bloquear = false,
    incluirTodas = false,
    autoSeleccionarDefault = true
) {

    fetch(
    '/sys_parking/modules/vehiculos/tarifas_categoria.php?categoria_id='
    + categoriaId
    + '&todas=' + (incluirTodas ? 1 : 0)
)

    .then(res => res.json())

    .then(data => {

        const tarifa = document.getElementById('tarifa_id');

        tarifa.innerHTML = '';

        if (data.length === 0) {

            tarifa.innerHTML =
                '<option value="">Sin tarifas</option>';

            return;
        }

        data.forEach(t => {

            const option = document.createElement('option');

            option.value = t.id;

            option.textContent = t.descripcion;

            option.dataset.default = t.es_default;
            option.dataset.tope = t.es_tope_diario;

            option.dataset.monto = t.monto;

            if (
                String(t.id) ===
                String(tarifaSeleccionada)
            ) {
                option.selected = true;
            }

            tarifa.appendChild(option);

        });

        if ( !tarifaSeleccionada && autoSeleccionarDefault ) {

            for (let i = 0; i < tarifa.options.length; i++) {

                const option = tarifa.options[i];

                if (parseInt(option.dataset.default) === 1) {

                    tarifa.selectedIndex = i;

                    break;
                }
            }
        }

        document.getElementById('tarifa_hidden').value =
        tarifa.value;

        tarifa.disabled = bloquear;
        actualizarVistaEstadia();
        tarifa.dispatchEvent(
            new Event('change')
        );

    })

    .catch(err => {

        console.error(err);

    });

}

function actualizarVistaEstadia() {

    const tarifa =
        document.getElementById('tarifa_id');

    const option =
        tarifa.options[tarifa.selectedIndex];

    if (!option) {
        return;
    }

    /*
        NORMALIZAR TEXTO
    */

    const texto =
        option.text
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "");

    const monto =
        option.dataset.monto || 0;

    const grupoPago =
        document.getElementById(
            'grupo_pago_anticipado'
        );

    const grupoMonto =
        document.getElementById(
            'grupo_monto_estadia'
        );

    const grupoMensaje =
        document.getElementById(
            'grupo_mensaje_estadia'
        );

    const montoEstadia =
        document.getElementById(
            'monto_estadia'
        );

    /*
        DETECTAR ESTADIA
    */

    const esEstadia = parseInt(option.dataset.tope) === 1;

    if (esEstadia) {

        grupoPago.style.display = 'block';

        grupoMonto.style.display = 'block';

        grupoMensaje.style.display = 'block';

        montoEstadia.value =
            '$ ' +
            parseFloat(monto)
            .toLocaleString(
                'es-AR',
                {
                    minimumFractionDigits: 2
                }
            );

    } else {

        grupoPago.style.display = 'none';

        grupoMonto.style.display = 'none';

        grupoMensaje.style.display = 'none';

        montoEstadia.value = '';

    }

}

/* =====================================================
   FECHA FIN
===================================================== */

function mostrarFechaFin(fecha) {

    document.getElementById('fecha_fin_abono').value =
        fecha;

    document.getElementById('grupo_fecha_fin').style.display =
        'block';

}

function ocultarFechaFin() {

    document.getElementById('grupo_fecha_fin').style.display =
        'none';

}

/* =====================================================
   TOASTS
===================================================== */

function toastSuccess(msg) {

    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: msg,
        showConfirmButton: false,
        timer: 4000
    });

}

function toastWarning(msg) {

    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'warning',
        title: msg,
        showConfirmButton: false,
        timer: 5000
    });

}

</script>

<?php

} elseif ($_GET['form'] == 'edit') {

    $id = (int) $_GET['id'];

    $q = mysqli_query($mysqli, "

    SELECT
    v.*,
    c.categoria_id,
    v.categoria_id AS categoria_posta,
    t.descripcion AS tarifa
FROM vehiculos v

JOIN tarifas t
    ON t.id = v.tarifa_id

LEFT JOIN clientes c
    ON c.patente COLLATE utf8mb4_unicode_ci =
       v.patente COLLATE utf8mb4_unicode_ci
   AND c.activo = 1

WHERE v.id = '$id'

    LIMIT 1

");

    $veh = mysqli_fetch_assoc($q);

    $fecha = date(
        'd/m/Y',
        strtotime($veh['fecha_ingreso'])
    );

    $hora = date(
        'H:i',
        strtotime($veh['hora_ingreso'])
    );

?>

<section class="content-header">
  <h1>
    <i class="fa fa-edit icon-title"></i>
    Editar Patente
  </h1>
</section>

<section class="content">

  <div class="row">

    <div class="col-md-12">

      <div class="box box-primary">

        <form role="form"
              class="form-horizontal"
              method="POST"
              action="modules/vehiculos/proces.php?act=update">

          <input type="hidden"
                 name="id"
                 value="<?= $veh['id'] ?>">

          <div class="box-body">

            <div class="form-group">

              <label class="col-sm-2 control-label">
                Patente
              </label>

              <div class="col-sm-5">

                <input type="text"
                       name="patente"
                       class="form-control"
                       value="<?= $veh['patente'] ?>"
                       required>

              </div>

            </div>

            <div class="form-group">

              <label class="col-sm-2 control-label">
                Fecha ingreso
              </label>

              <div class="col-sm-5">

                <input type="text"
                       class="form-control"
                       value="<?= $fecha ?>"
                       readonly>

              </div>

            </div>

            <div class="form-group">

              <label class="col-sm-2 control-label">
                Hora ingreso
              </label>

              <div class="col-sm-5">

                <input type="text"
                       class="form-control"
                       value="<?= $hora ?>"
                       readonly>

              </div>

            </div>

            <div class="form-group">

              <label class="col-sm-2 control-label">
                Tarifa
              </label>

              <div class="col-sm-5">

                <input type="text"
                       class="form-control"
                       value="<?= $veh['tarifa'] ?>"
                       readonly>

              </div>

            </div>

          </div>

          <div class="box-footer">

            <div class="form-group">

              <div class="col-sm-offset-2 col-sm-10">

                <input type="submit"
                       class="btn btn-primary"
                       value="Guardar">

                <a href="?module=vehiculos"
                   class="btn btn-default">

                  Cancelar

                </a>

              </div>

            </div>

          </div>

        </form>

      </div>

    </div>

  </div>

</section>

<?php

}

elseif ($_GET['form'] == 'cobrar') {

    require_once "modules/vehiculos/calcular_tarifa.php";

    $id = (int) $_GET['id'];

    $tarifa_id = (int) $_GET['tarifa_id'];

    $categoria_id = (int) $_GET['categoria_id'];

    /* =========================================
       OBTENER VEHICULO
    ========================================= */

    $queryVehiculo = mysqli_query($mysqli, "

        SELECT *
        FROM vehiculos
        WHERE id = '$id'
        LIMIT 1

    ");

    $vehiculoBase =
        mysqli_fetch_assoc($queryVehiculo);

    if (!$vehiculoBase) {

        echo "
        <div class='alert alert-danger'>
            Vehículo no encontrado
        </div>";

        return;
    }

    /* =========================================
       VEHICULO PREPAGADO
    ========================================= */

    if ((int)$vehiculoBase['pagado'] === 1) {

        $veh = $vehiculoBase;

        $total = 0;

        $detalle = [];

        $alternativas = [];

        $minutos_totales = 0;

    }

    /* =========================================
       CALCULO NORMAL
    ========================================= */

    else {

        $resultado = calcularTarifaVehiculo(
            $mysqli,
            $id
        );

        if (!$resultado) {

            echo "
            <div class='alert alert-danger'>
                Vehículo no encontrado
            </div>";

            return;
        }

        $veh = $resultado['vehiculo'];

        $total = $resultado['total'];

        $detalle = $resultado['detalle'];

        $alternativas = $resultado['alternativas'];

        $minutos_totales =
            $resultado['minutos_totales'];
    }

    /* =========================================
       TIEMPO
    ========================================= */

    if ((int)$veh['pagado'] === 1) {

        $horas_completas = 0;

        $minutos_resto = 0;

    } else {

        $horas_completas =
            floor($minutos_totales / 60);

        $minutos_resto =
            $minutos_totales % 60;
    }

    /* =========================================
       FECHA / HORA
    ========================================= */

    $fecha = date(
        'd/m/Y',
        strtotime($veh['fecha_ingreso'])
    );

    $hora = date(
        'H:i',
        strtotime($veh['hora_ingreso'])
    );

    /* =========================================
       TARIFA
    ========================================= */

    $descripcionTarifa = '';

    $queryTarifa = mysqli_query($mysqli, "

        SELECT descripcion

        FROM tarifas

        WHERE id = '$tarifa_id'

        LIMIT 1

    ");

    if ($rowTarifa = mysqli_fetch_assoc($queryTarifa)) {

        $descripcionTarifa =
            $rowTarifa['descripcion'];
    }

?>

<section class="content">

    <div class="box box-primary">

        <form method="POST"
              action="modules/vehiculos/proces.php?act=cobrar"
              class="form-horizontal">

            <input type="hidden"
                   name="id"
                   value="<?= $veh['id'] ?>">

            <div class="box-body">

                <div class="form-group">

                    <label class="col-sm-2 control-label">
                        Patente
                    </label>

                    <div class="col-sm-4">

                        <input type="text"
                               name="patente"
                               class="form-control"
                               value="<?= strtoupper($veh['patente']) ?>"
                               readonly>

                    </div>

                </div>

                <!-- CATEGORÍA -->

                <div class="form-group">

                    <label class="col-sm-2 control-label">
                        Categoría
                    </label>

                    <div class="col-sm-4">

                        <select
                            id="categoria_id"
                            name="categoria_id"
                            class="form-control"
                            disabled
                            required>

                            <option value="">
                                Seleccionar categoría
                            </option>

                            <?php

                            $categoriaSeleccionada =
                                (int)$veh['categoria_final_id'];

                            $cats = mysqli_query(
                                $mysqli,
                                "SELECT *
                                 FROM categorias
                                 WHERE activo = 1
                                 ORDER BY nombre ASC"
                            );

                            while ($c = mysqli_fetch_assoc($cats)) {

                                $selected =
                                    (
                                        $categoriaSeleccionada
                                        ==
                                        $c['id']
                                    )
                                    ? 'selected'
                                    : '';

                                echo "
                                <option value='{$c['id']}' $selected>
                                    {$c['nombre']}
                                </option>";
                            }

                            ?>

                        </select>

                        <input type="hidden"
                               name="categoria_hidden"
                               value="<?= $veh['categoria_final_id'] ?>">

                    </div>

                </div>

                <!-- TARIFA -->

                <!-- <div class="form-group">

                    <label class="col-sm-2 control-label">
                        Tarifa / Abono
                    </label>

                    <div class="col-sm-4">

                        <input type="text"
                               class="form-control"
                               value="<?= $descripcionTarifa ?>"
                               readonly>

                    </div>

                    <input type="hidden"
                           name="tarifa_id_hidden"
                           value="<?= $tarifa_id ?>">

                </div> -->

                <!-- INGRESO -->

                <div class="form-group">

                    <label class="col-sm-2 control-label">
                        Ingreso
                    </label>

                    <div class="col-sm-4">

                        <input type="text"
                               class="form-control"
                               value="<?= $fecha.' '.$hora ?>"
                               readonly>

                    </div>

                </div>

                <!-- PERMANENCIA -->

                <div class="form-group">

                    <label class="col-sm-2 control-label">
                        Permanencia
                    </label>

                    <div class="col-sm-4">

                        <input type="text"
                               class="form-control"
                               value="<?= $horas_completas ?> hora(s) <?= $minutos_resto ?> minuto(s)"
                               readonly>

                    </div>

                </div>

                <!-- MEDIO COBRO -->

                <div class="form-group">

                    <label class="col-sm-2 control-label">
                        Medio de cobro
                    </label>

                    <div class="col-sm-4">

                        <select name="medio_cobro"
                                class="form-control"
                                required>

                            <option value="">
                                -- Seleccionar --
                            </option>

                            <option value="efectivo">
                                Efectivo
                            </option>

                            <option value="mercadopago">
                                Mercado Pago
                            </option>

                            <option value="transferencia">
                                Transferencia
                            </option>

                            <option value="CuentaDNI">
                                Cuenta DNI
                            </option>

                            <option value="tarjeta">
                                Tarjeta
                            </option>

                            <option value="otro">
                                Abono PreCancelado
                            </option>

                        </select>

                    </div>

                </div>

                <!-- TOTAL -->

                <div class="form-group">

                    <label class="col-sm-2 control-label">
                        Total
                    </label>

                    <div class="col-sm-4">

                        <input type="text"
                               class="form-control"
                               value="$<?= number_format($total,2) ?>"
                               readonly>

                    </div>

                    <input type="hidden"
                           id="total_hidden"
                           name="total_hidden"
                           value="<?= $total ?>">

                </div>

            </div>

            <!-- BOTONES -->

            <div class="box-footer">

                <button type="submit"
                        class="btn btn-success">

                    <i class="fa fa-check"></i>
                    Confirmar salida

                </button>

                <a href="?module=vehiculos"
                   class="btn btn-danger">

                    Cancelar

                </a>

            </div>

            <div class="box-body">

                <hr>

                <!-- MEJOR ALTERNATIVA -->

                <h4>
                    Mejor alternativa encontrada
                </h4>

                <table border="1"
                       cellpadding="8"
                       cellspacing="0"
                       width="100%">

                    <tr style="background:#eee;">

                        <th>
                            Concepto
                        </th>

                        <th>
                            Cantidad
                        </th>

                        <th>
                            Precio
                        </th>

                        <th>
                            Subtotal
                        </th>

                    </tr>

                    <?php foreach ($detalle as $d): ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars($d['descripcion']) ?>
                        </td>

                        <td>
                            <?= $d['cantidad'] ?>
                        </td>

                        <td>
                            $<?= number_format($d['precio'],2) ?>
                        </td>

                        <td>
                            $<?= number_format($d['subtotal'],2) ?>
                        </td>

                    </tr>

                    <?php endforeach; ?>

                </table>

                <br>

                <!-- ALTERNATIVAS ANALIZADAS -->

                <h4>
                    Alternativas analizadas
                </h4>

                <table border="1"
                       cellpadding="8"
                       cellspacing="0"
                       width="100%">

                    <tr style="background:#eee;">

                        <th width="60%">
                            Alternativa
                        </th>

                        <th width="20%">
                            Tarifa / Abono Base
                        </th>

                        <th width="20%">
                            Total
                        </th>

                    </tr>

                    <?php foreach ($alternativas as $a): ?>

                        <?php

                        $bg =
                            ($a['total'] == $total)
                            ? '#dff0d8'
                            : '#fff';

                        ?>

                        <tr style="background:<?= $bg ?>;">

                            <td>

                                <?php foreach ($a['items'] as $i): ?>

                                    <?= $i['cantidad'] ?>

                                    x

                                    <?= htmlspecialchars($i['descripcion']) ?>

                                    ($<?= number_format($i['subtotal'],2) ?>)

                                    <br>

                                <?php endforeach; ?>

                            </td>

                            <td>

                                <?= htmlspecialchars(
                                    $a['abono_base'] ?? '-'
                                ) ?>

                            </td>

                            <td>

                                <b>
                                    $<?= number_format($a['total'],2) ?>
                                </b>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </table>

            </div>

        </form>

    </div>

</section>

<?php

}

?>
