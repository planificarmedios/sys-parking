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
        <form class="form-horizontal" method="POST"
              action="modules/vehiculos/proces.php?act=insert">

          <div class="box-body">

            <!-- Patente -->
            <div class="form-group">
              <label class="col-sm-2 control-label">Patente</label>
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

            <!-- Fecha ingreso -->
            <div class="form-group">
              <label class="col-sm-2 control-label">Fecha ingreso</label>
              <div class="col-sm-5">
                <input type="text" class="form-control"
                       value="<?= date('d/m/Y') ?>" readonly>
              </div>
            </div>

            <!-- Hora ingreso -->
            <div class="form-group">
              <label class="col-sm-2 control-label">Hora ingreso</label>
              <div class="col-sm-5">
                <input type="text" class="form-control"
                       value="<?= date('H:i') ?>" readonly>
              </div>
            </div>

            <!-- Fecha fin abono -->
            <div class="form-group" id="grupo_fecha_fin" style="display:none;">
              <label class="col-sm-2 control-label">Vence</label>
              <div class="col-sm-5">
                <input type="text" id="fecha_fin_abono"
                       class="form-control" readonly>
              </div>
            </div>

            <!-- Tarifa -->
            <div class="form-group">
              <label class="col-sm-2 control-label">Tarifa</label>
              <div class="col-sm-5">

                <select id="tarifa_id" class="form-control" required>
                  <?php
                  $tarifas = mysqli_query($mysqli, "
                    SELECT id, descripcion, es_default
                    FROM tarifas
                    WHERE activo = 1
                    ORDER BY es_default DESC, id ASC
                  ");

                  while ($t = mysqli_fetch_assoc($tarifas)) {
                    $selected = ($t['es_default'] == 1) ? 'selected' : '';
                    echo "<option value='{$t['id']}' $selected>
                            {$t['descripcion']}
                          </option>";
                  }
                  ?>
                </select>

                <!-- valor REAL que se envía -->
                <input type="hidden" name="tarifa_id" id="tarifa_hidden">

              </div>
            </div>

          </div>

          <div class="box-footer">
            <div class="form-group">
              <label class="col-sm-2 control-label">Ticket</label>

              <div class="col-sm-5">
<!-- 
                <label class="radio-inline">
                  <input type="radio" name="modo_ticket" value="preview">
                  Vista previa
                </label> -->

                <label class="radio-inline">
                  <input type="radio" name="modo_ticket" value="directo" checked>
                  Imprimir directamente
                </label>

              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="?module=vehiculos" class="btn btn-default">Cancelar</a>
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
  const tarifaSelect = document.getElementById('tarifa_id');
  const tarifaHidden = document.getElementById('tarifa_hidden');

  // inicializar hidden con la tarifa por defecto
  tarifaHidden.value = tarifaSelect.value;

  patenteInput.addEventListener('blur', function () {
    const patente = this.value.trim();
    if (patente.length < 5) return;
    verificarAbono(patente);
  });

  tarifaSelect.addEventListener('change', function () {
    tarifaHidden.value = this.value;
  });

});

function verificarAbono(patente) {
  fetch('/sys_parking/modules/vehiculos/verificar_abono.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'patente=' + encodeURIComponent(patente)
  })
  .then(res => res.text())
  .then(text => {
    console.log('RESPUESTA CRUDA >>>', text);
    const data = JSON.parse(text);
    manejarRespuestaAbono(data);
  })
  .catch(err => {
    console.error('ERROR REAL >>>', err);
  });
}

function bloquearTarifa(tarifaId) {
  const select = document.getElementById('tarifa_id');
  select.value = tarifaId;
  select.disabled = true;
  document.getElementById('tarifa_hidden').value = tarifaId;
}

function habilitarSelectTarifa() {
  const select = document.getElementById('tarifa_id');
  select.disabled = false;
  document.getElementById('tarifa_hidden').value = select.value;
}

function mostrarFechaFin(fecha) {
  document.getElementById('fecha_fin_abono').value = fecha;
  document.getElementById('grupo_fecha_fin').style.display = 'block';
}

function ocultarFechaFin() {
  document.getElementById('grupo_fecha_fin').style.display = 'none';
}

function toastSuccess(msg) {
  Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'success',
    title: msg,
    showConfirmButton: false,
    timer: 4000,
    timerProgressBar: true
  });
}

function toastWarning(msg) {
  Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'warning',
    title: msg,
    showConfirmButton: false,
    timer: 5000,
    timerProgressBar: true
  });
}

let avisoMostrado = false;

function manejarRespuestaAbono(data) {
  if (!data.tiene_abono) return;

  if (!avisoMostrado) {
    if (data.vigente) {
      toastSuccess(`Abono vigente hasta ${data.fecha_egreso}`);
    } else {
      toastWarning(`⚠️ Abono vencido el ${data.fecha_egreso}`);
    }
    avisoMostrado = true;
  }

  bloquearTarifa(data.tarifa_id);
  mostrarFechaFin(data.fecha_egreso);
}

</script>

<?php } 

/* ===========================
   EDITAR (solo patente)
=========================== */
elseif ($_GET['form'] == 'edit') {

  $id = (int)$_GET['id'];

  $q = mysqli_query($mysqli,"
    SELECT v.*, t.descripcion AS tarifa
    FROM vehiculos v
    JOIN tarifas t ON t.id = v.tarifa_id
    WHERE v.id = '$id'
  ");
  $veh = mysqli_fetch_assoc($q);

  $fecha = date('d/m/Y', strtotime($veh['fecha_ingreso']));
  $hora  = date('H:i', strtotime($veh['hora_ingreso']));
?>

<section class="content-header">
  <h1>
    <i class="fa fa-edit icon-title"></i> Editar Patente
  </h1>
</section>

<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box box-primary">

        <form role="form" class="form-horizontal"
              method="POST"
              action="modules/vehiculos/proces.php?act=update">

          <input type="hidden" name="id" value="<?= $veh['id'] ?>">

          <div class="box-body">

            <!-- Patente editable -->
            <div class="form-group">
              <label class="col-sm-2 control-label">Patente</label>
              <div class="col-sm-5">
                <input type="text" name="patente"
                       class="form-control"
                       value="<?= $veh['patente'] ?>"
                       required autofocus>
              </div>
            </div>

            <!-- Fecha -->
            <div class="form-group">
              <label class="col-sm-2 control-label">Fecha ingreso</label>
              <div class="col-sm-5">
                <input type="text" class="form-control"
                       value="<?= $fecha ?>" readonly>
              </div>
            </div>

            <!-- Hora -->
            <div class="form-group">
              <label class="col-sm-2 control-label">Hora ingreso</label>
              <div class="col-sm-5">
                <input type="text" class="form-control"
                       value="<?= $hora ?>" readonly>
              </div>
            </div>

            <!-- Tarifa -->
            <div class="form-group">
              <label class="col-sm-2 control-label">Tarifa</label>
              <div class="col-sm-5">
                <input type="text" class="form-control"
                       value="<?= $veh['tarifa'] ?>" readonly>
              </div>
            </div>

          </div>

             <div class="box-footer">
            <div class="form-group">
              <div class="col-sm-offset-2 col-sm-10">
                <input type="submit" class="btn btn-primary" value="Guardar">
                <a href="?module=vehiculos" class="btn btn-default">Cancelar</a>
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

    $id = (int)$_GET['id'];

    /* ===============================
       1. DATOS DEL VEHÍCULO
    =============================== */
    $q = mysqli_query($mysqli, "
        SELECT v.*, t.descripcion AS tarifa_base, t.unidad, t.valor, t.monto AS tarifa_monto
        FROM vehiculos v
        JOIN tarifas t ON t.id = v.tarifa_id
        WHERE v.id = $id
        LIMIT 1
    ");
    $veh = mysqli_fetch_assoc($q);

    if (!$veh) {
        echo "<div class='alert alert-danger'>Vehículo no encontrado</div>";
        return;
    }

    /* ===============================
       2. TIEMPO TRANSCURRIDO
    =============================== */
    $ingreso = strtotime($veh['fecha_ingreso'].' '.$veh['hora_ingreso']);
    $egreso  = time();

    $minutos_totales = ceil(($egreso - $ingreso) / 60);
    $horas_completas = floor($minutos_totales / 60);
    $minutos_resto   = $minutos_totales % 60;

    $detalle = [];         // Detalle de la opción seleccionada
    $total = 0;            // Total de la opción seleccionada
    $alternativas = [];    // Guardará todas las alternativas completas

    /* ===============================
       3. ABONOS → NO SE RECALCULAN
    =============================== */
    if ($veh['unidad'] === 'dias' && $veh['valor'] > 1) {

        $total = $veh['tarifa_monto'];
        $detalle[] = [
            'descripcion' => $veh['tarifa_base'],
            'cantidad'    => 1,
            'precio'      => $veh['tarifa_monto'],
            'subtotal'    => $veh['tarifa_monto']
        ];

        $alternativas[] = [
            'items'       => $detalle,
            'total'       => $veh['tarifa_monto'],
            'seleccionada'=> true
        ];

    } else {

        /* ===============================
           4. TARIFA POR HORA
        =============================== */
        $escenarios = []; // Para construir cada alternativa completa

        $q_hora = mysqli_query($mysqli, "
            SELECT *
            FROM tarifas
            WHERE activo = 1
              AND unidad = 'horas'
              AND es_tarifa_fraccionable = 1
        ");

        $tarifas_horas = [];
        while ($t = mysqli_fetch_assoc($q_hora)) {
            $tarifas_horas[] = $t;
        }

        // Escenario por hora completa
        if ($horas_completas > 0 && !empty($tarifas_horas)) {
            foreach ($tarifas_horas as $tarifa_hora) {
                $subtotal_horas = $horas_completas * $tarifa_hora['monto'];
                $items = [
                    [
                        'descripcion' => $tarifa_hora['descripcion'],
                        'cantidad'    => $horas_completas,
                        'precio'      => $tarifa_hora['monto'],
                        'subtotal'    => $subtotal_horas
                    ]
                ];
                $escenarios[] = [
                    'items' => $items,
                    'total' => $subtotal_horas
                ];
            }
        }

        /* ===============================
           5. REMANENTE (SOLO FRACCIONABLES)
        =============================== */
        if ($minutos_resto > 0) {
            $q_frac = mysqli_query($mysqli, "
                SELECT *
                FROM tarifas
                WHERE activo = 1
                  AND es_tarifa_fraccionable = 1
                  AND unidad IN ('minutos','horas')
            ");

            $tarifas_frac = [];
            while ($t = mysqli_fetch_assoc($q_frac)) {
                $tarifas_frac[] = $t;
            }

            $n = count($escenarios);
            if ($n === 0) {
                // Si no hay horas completas, creamos un escenario vacío
                $escenarios[] = ['items'=>[], 'total'=>0];
                $n = 1;
            }

            $nuevos_escenarios = [];
            foreach ($escenarios as $esc) {
                foreach ($tarifas_frac as $t) {
                    $bloque = (int)$t['valor'];
                    if ($bloque <= 0) continue;
                    if ($t['unidad'] == 'horas') $bloque *= 60;

                    $cantidad = ceil($minutos_resto / $bloque);
                    $costo = $cantidad * $t['monto'];

                    $items = $esc['items'];
                    $items[] = [
                        'descripcion' => $t['descripcion'],
                        'cantidad'    => $cantidad,
                        'precio'      => $t['monto'],
                        'subtotal'    => $costo
                    ];

                    $nuevos_escenarios[] = [
                        'items' => $items,
                        'total' => $esc['total'] + $costo
                    ];
                }
            }
            $escenarios = $nuevos_escenarios;
        }

        /* ===============================
           6. TOPE: ESTADÍA COMPLETA
        =============================== */
        $q_tope = mysqli_query($mysqli, "
            SELECT *
            FROM tarifas
            WHERE activo = 1
              AND unidad = 'fijo'
              AND valor = 1
            LIMIT 1
        ");
        $tope = mysqli_fetch_assoc($q_tope);
        if ($tope) {
            $escenarios[] = [
                'items' => [
                    [
                        'descripcion' => $tope['descripcion'],
                        'cantidad'    => 1,
                        'precio'      => $tope['monto'],
                        'subtotal'    => $tope['monto']
                    ]
                ],
                'total' => $tope['monto']
            ];
        }

        /* ===============================
           7. Elegimos la alternativa más económica
        =============================== */
        $min_total = PHP_INT_MAX;
        foreach ($escenarios as $esc) {
            $alternativas[] = [
                'items'       => $esc['items'],
                'total'       => $esc['total'],
                'seleccionada'=> false
            ];
            if ($esc['total'] < $min_total) {
                $min_total = $esc['total'];
                $detalle = $esc['items'];
                $total = $esc['total'];
            }
        }

        // Marcamos la seleccionada
        foreach ($alternativas as &$alt) {
            if ($alt['total'] == $total) $alt['seleccionada'] = true;
        }
        unset($alt);
    }

    $fecha = date('d/m/Y', strtotime($veh['fecha_ingreso']));
    $hora  = date('H:i', strtotime($veh['hora_ingreso']));
?>
<section class="content-header">
  <h1><i class="fa fa-arrow-right icon-title"></i> Registrar Salida</h1>
</section>

<section class="content">
  <div class="box box-primary">
    <form method="POST" action="modules/vehiculos/proces.php?act=cobrar" class="form-horizontal">
      <input type="hidden" name="id" value="<?= $veh['id'] ?>">
      <input type="hidden" name="monto_total" value="<?= $total ?>">

      <div class="box-body">

        <div class="form-group">
          <label class="col-sm-2 control-label">Patente</label>
          <div class="col-sm-4">
            <input type="text" id="patente" class="form-control" value="<?= strtoupper($veh['patente']) ?>" readonly>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-2 control-label">Medio de cobro</label>
          <div class="col-sm-4">
            <select name="medio_cobro" class="form-control" required>
              <option value="">-- Seleccionar --</option>
              <option value="efectivo">Efectivo</option>
              <option value="mercadopago">Mercado Pago</option>
              <option value="transferencia">Transferencia</option>
              <option value="CuentaDNI">Cuenta DNI</option>
              <option value="tarjeta">Tarjeta</option>
              <option value="otro">Abono PreCancelado</option>
            </select>
          </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Ingreso</label>
            <div class="col-sm-4">
              <input type="text" class="form-control" value="<?= $fecha.' '.$hora ?>" readonly>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label">Permanencia</label>
            <div class="col-sm-4">
              <input type="text" class="form-control" 
                    value="<?= $horas_completas ?> hora<?= $horas_completas != 1 ? 's' : '' ?> <?= $minutos_resto ?> minuto<?= $minutos_resto != 1 ? 's' : '' ?>" 
                    readonly>
            </div>
          </div>

        <div class="form-group">
          <label class="col-sm-2 control-label">Total a cobrar $</label>
          <div class="col-sm-4">
            <input type="text" name="total" class="form-control" value="<?= number_format($total,2) ?>" readonly>
          </div>
        </div>

        <br> 

         
      <div class="box-footer">
          <div class="form-group">
              <div class="col-sm-offset-2 col-sm-10">
                  <button type="submit" class="btn btn-success">
                    <i class="fa fa-check"></i> Confirmar Salida
                  </button>
                  <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modalAlternativas">
                    Cálculos alternativos
                  </button>
                  <a href="?module=vehiculos" class="btn btn-danger">Cancelar</a>
            </div>
            </div>
        </div>

        <h4>Detalle del cálculo</h4>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Concepto</th>
              <th>Cantidad</th>
              <th>Precio</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($detalle as $d): ?>
            <tr>
              <td><?= htmlspecialchars($d['descripcion']) ?></td>
              <td><?= $d['cantidad'] ?></td>
              <td>$<?= number_format($d['precio'],2) ?></td>
              <td>$<?= number_format($d['subtotal'],2) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
     

      <!-- Modal -->
      <div class="modal fade" id="modalAlternativas" tabindex="-1" aria-labelledby="modalAlternativasLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalAlternativasLabel">Cálculos Alternativos Analizados</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <?php foreach ($alternativas as $alt): ?>
                <table class="table table-sm table-bordered mb-3">
                  <thead>
                    <tr class="<?= $alt['seleccionada'] ? 'table-success fw-bold' : '' ?>">
                      <th colspan="4">Total: $<?= number_format($alt['total'],2) ?></th>
                    </tr>
                    <tr>
                      <th>Concepto</th>
                      <th>Cantidad</th>
                      <th>Precio</th>
                      <th>Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($alt['items'] as $item): ?>
                      <tr>
                        <td><?= htmlspecialchars($item['descripcion']) ?></td>
                        <td><?= $item['cantidad'] ?></td>
                        <td>$<?= number_format($item['precio'],2) ?></td>
                        <td>$<?= number_format($item['subtotal'],2) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              <?php endforeach; ?>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
      </div>

        

    </form>
  </div>
</section>

<script>

document.addEventListener('DOMContentLoaded', function () {

   const patenteInput = document.getElementById('patente');

  if (patenteInput) {
      verificarAbono(patenteInput.value);
  }

});

function verificarAbono(patente) {

  fetch('/sys_parking/modules/vehiculos/verificar_abono.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'patente=' + encodeURIComponent(patente)
  })

  .then(res => res.json())

  .then(data => {
      console.log('RESPUESTA ABONO >>>', data);
      manejarRespuestaAbono(data);
  })

  .catch(err => {
      console.error('ERROR REAL >>>', err);
  });

}

function toastSuccess(msg) {
  Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'success',
    title: msg,
    showConfirmButton: false,
    timer: 4000,
    timerProgressBar: true
  });
}

function toastWarning(msg) {
  Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'warning',
    title: msg,
    showConfirmButton: false,
    timer: 5000,
    timerProgressBar: true
  });
}

let avisoMostrado = false;

function manejarRespuestaAbono(data) {
  if (!data.tiene_abono) return;

  if (!avisoMostrado) {
    if (data.vigente) {
      toastSuccess(`Abono vigente hasta ${data.fecha_egreso}`);
    } else {
      toastWarning(`⚠️ Abono vencido el ${data.fecha_egreso}`);
    }
    avisoMostrado = true;
  }
}

</script>




<?php } ?>