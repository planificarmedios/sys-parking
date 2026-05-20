<?php

if ($_GET['form'] == 'manual') {

?>

<section class="content-header">

    <h1>
        <i class="fa fa-money icon-title"></i>
        Ingreso manual a caja
    </h1>

</section>

<section class="content">

    <div class="box box-primary">
        <form method="POST"
              action="modules/caja/proces.php?act=insert_manual"
              class="form-horizontal">

            <div class="box-body">


                <div class="form-group">

                    <label class="col-sm-2 control-label">
                        Tipo movimiento
                    </label>

                    <div class="col-sm-4">

                        <select
							name="tipo_movimiento"
							id="tipo_movimiento"
							class="form-control"
							required>

							<option value="ingreso">
								Ingreso
							</option>

							<option value="egreso">
								Egreso
							</option>

						</select>

                    </div>

                </div>

                <!-- TIPO MOVIMIENTO -->

                <div class="form-group">

                    <label class="col-sm-2 control-label">
                        Detalle
                    </label>

                    <div class="col-sm-4">

                        <select id="tipo_ingreso"
                                name="tipo_ingreso"
                                class="form-control"
                                required>

                            <option value="">
                                Seleccionar
                            </option>

                            <option value="abono">
                                Abono / Renovación
                            </option>

                            <option value="manual">
                                Otro concepto manual
                            </option>

                        </select>

                    </div>

                </div>

                <!-- PATENTE -->

                <div class="form-group"
                     id="grupo_patente"
                     style="display:none;">

                    <label class="col-sm-2 control-label">
                        Patente
                    </label>

                    <div class="col-sm-4">

                        <input type="text"
                               name="patente"
                               id="patente"
                               class="form-control"
                               autocomplete="off">

                    </div>

                </div>

                <!-- CATEGORIA -->

                <div class="form-group"
                     id="grupo_categoria"
                     style="display:none;">

                    <label class="col-sm-2 control-label">
                        Categoría
                    </label>

                    <div class="col-sm-4">

                        <select id="categoria_id"
                                name="categoria_id"
                                class="form-control">

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

                <div class="form-group"
                     id="grupo_tarifa"
                     style="display:none;">

                    <label class="col-sm-2 control-label">
                        Tarifa / Abono
                    </label>

                    <div class="col-sm-4">

                        <select id="tarifa_id"
                                name="tarifa_id"
                                class="form-control">

                            <option value="">
                                Seleccionar tarifa
                            </option>

                        </select>

                    </div>

                </div>

                <!-- CONCEPTO -->

                <div class="form-group"
                     id="grupo_concepto"
                     style="display:none;">

                    <label class="col-sm-2 control-label">
                        Concepto
                    </label>

                    <div class="col-sm-4">

                        <input type="text"
                               name="concepto"
                               id="concepto"
                               class="form-control">

                    </div>

                </div>

                <!-- IMPORTE -->

                <div class="form-group">

                    <label class="col-sm-2 control-label">
                        Importe
                    </label>

                    <div class="col-sm-4">

                        <input type="text"
                               step="0.01"
                               min="0"
                               name="monto"
                               id="monto"
                               class="form-control"
                               required>

                    </div>

                </div>

                <!-- MEDIO COBRO -->

                <div class="form-group">

                    <label class="col-sm-2 control-label">
                        Medio
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
                                Otro
                            </option>

                        </select>

                    </div>

                </div>

                <!-- DETALLE -->

                <div class="form-group">

                    <label class="col-sm-2 control-label">
                        Observaciones
                    </label>

                    <div class="col-sm-6">

                        <textarea name="detalle"
                                  class="form-control"
                                  rows="3"></textarea>

                    </div>

                </div>

            </div>

            <div class="box-footer">

                <button type="submit"
                        class="btn btn-success">

                    <i class="fa fa-save"></i>
                    Confirmar

                </button>

                <a href="?module=cajas"
                   class="btn btn-danger">

                    Cancelar

                </a>

            </div>

        </form>

    </div>

</section>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const tipoIngreso = document.getElementById('tipo_ingreso');

    const grupoPatente  = document.getElementById('grupo_patente');
    const grupoCategoria = document.getElementById('grupo_categoria');
    const grupoTarifa = document.getElementById('grupo_tarifa');
    const grupoConcepto = document.getElementById('grupo_concepto');
    const categoria = document.getElementById('categoria_id');

    const patente =
    document.getElementById('patente');

patente.addEventListener('blur', function () {

    const valor =
        this.value.trim().toUpperCase();

    if (valor.length < 5) {
        return;
    }

    fetch(
        'modules/caja/buscar_patente.php',
        {
            method: 'POST',

            headers: {
                'Content-Type':
                    'application/x-www-form-urlencoded'
            },

            body:
                'patente='
                +
                encodeURIComponent(valor)
        }
    )

    .then(res => res.json())

    .then(data => {

        console.log(
            'Busqueda patente:',
            data
        );

        if (!data.ok) {
            return;
        }

        categoria.value =
            data.categoria_id;

        cargarTarifas(
            data.categoria_id
        );

        setTimeout(() => {

    tarifa.value = data.tarifa_id;

    const option =
        tarifa.options[tarifa.selectedIndex];

    if (option) {

        monto.value =
            option.dataset.monto || '';

    }

}, 300);

    })

    .catch(err => {

        console.error(err);

    });

});


    const tarifa = document.getElementById('tarifa_id');
    const monto = document.getElementById('monto');

    tipoIngreso.addEventListener('change', function () {

        resetFormulario();

        if (this.value === 'abono') {

            grupoPatente.style.display = 'block';
            grupoCategoria.style.display = 'block';
            grupoTarifa.style.display = 'block';

        }

        if (this.value === 'manual') {

            grupoConcepto.style.display = 'block';

        }

    });

    categoria.addEventListener('change', function () {

        cargarTarifas(this.value);

    });

    tarifa.addEventListener('change', function () {

        const option =
            tarifa.options[tarifa.selectedIndex];

        const montoTarifa =
            option.dataset.monto || 0;

        monto.value = montoTarifa;

    });

});

function resetFormulario() {

    document.getElementById('grupo_patente').style.display = 'none';
    document.getElementById('grupo_categoria').style.display = 'none';
    document.getElementById('grupo_tarifa').style.display = 'none';
    document.getElementById('grupo_concepto').style.display = 'none';

}

function cargarTarifas(categoriaId) {

    fetch(
    '/sys_parking/modules/caja/tarifas_categoria.php?categoria_id='
    + categoriaId
    )

    .then(res => res.json())

    .then(data => {

        const tarifa =
            document.getElementById('tarifa_id');

        tarifa.innerHTML =
            '<option value="">Seleccionar tarifa</option>';

        data.forEach(t => {

            console.log (data)
            if (parseInt(t.es_tarifa_fraccionable) === 1) {
                return;
            }

            const option =
                document.createElement('option');

            option.value = t.id;

            option.textContent =
                t.descripcion +
                ' - $ ' +
                parseFloat(t.monto).toLocaleString();

            option.dataset.monto = t.monto;

            tarifa.appendChild(option);

        });

    })

    .catch(err => {

        console.error(err);

    });

}

document.getElementById('monto')
.addEventListener('input', function () {

    this.value = this.value.replace('-', '');

});


</script>

<?php

}

?>