<section class="content-header">
  <h1>
    <i class="fa fa-money icon-title"></i> Caja
  </h1>
</section>

<?php

/* ===============================
   FILTROS
=============================== */

$fecha_desde = isset($_GET['fecha_desde'])
    ? $_GET['fecha_desde']
    : date('Y-m-d');

$fecha_hasta = isset($_GET['fecha_hasta'])
    ? $_GET['fecha_hasta']
    : date('Y-m-d');

$medio_cobro = isset($_GET['medio_cobro'])
    ? $_GET['medio_cobro']
    : '';

$where = "DATE(c.fecha_movimiento)
          BETWEEN '$fecha_desde'
          AND '$fecha_hasta'";

if ($medio_cobro != '') {
    $where .= " AND c.medio_cobro = '$medio_cobro'";
}

/* ===============================
   CONSULTA
=============================== */

$query = mysqli_query($mysqli, "

SELECT

    c.*,

    c.concepto AS concepto,

    t.descripcion AS tarifa,

    cat.nombre AS categoria

FROM caja c

LEFT JOIN tarifas t
    ON t.id = c.tarifa_id

LEFT JOIN categorias cat
    ON cat.id = c.categoria_id

WHERE $where

ORDER BY c.fecha_movimiento DESC

") or die(mysqli_error($mysqli));

/* ===============================
   TOTAL
=============================== */

$qTotal = mysqli_query($mysqli, "

SELECT
    SUM(monto) total
FROM caja c
WHERE $where

");

$totalData = mysqli_fetch_assoc($qTotal);

$totalCaja = $totalData['total'] ?? 0;

?>

<section class="content">

  <div class="row">

    <div class="col-md-12">

      <div class="box box-primary">

        <!-- =========================
             FILTROS
        ========================== -->

        <div class="box-header with-border">

          <form method="GET" class="form-inline">

            <input type="hidden" name="module" value="caja">

            <div class="form-group">

              <label>Desde</label>

              <input
                type="date"
                name="fecha_desde"
                class="form-control"
                value="<?= $fecha_desde ?>"
              >

            </div>

            <div class="form-group" style="margin-left:10px;">

              <label>Hasta</label>

              <input
                type="date"
                name="fecha_hasta"
                class="form-control"
                value="<?= $fecha_hasta ?>"
              >

            </div>

            <div class="form-group" style="margin-left:10px;">

              <label>Medio</label>

              <select
                name="medio_cobro"
                class="form-control"
              >

                <option value="">Todos</option>

                <option value="efectivo"
                  <?= ($medio_cobro == 'efectivo') ? 'selected' : '' ?>>
                  Efectivo
                </option>

                <option value="mercadopago"
                  <?= ($medio_cobro == 'mercadopago') ? 'selected' : '' ?>>
                  Mercado Pago
                </option>

                <option value="transferencia"
                  <?= ($medio_cobro == 'transferencia') ? 'selected' : '' ?>>
                  Transferencia
                </option>

                <option value="CuentaDNI"
                  <?= ($medio_cobro == 'CuentaDNI') ? 'selected' : '' ?>>
                  Cuenta DNI
                </option>

                <option value="tarjeta"
                  <?= ($medio_cobro == 'tarjeta') ? 'selected' : '' ?>>
                  Tarjeta
                </option>

                <option value="otro"
                  <?= ($medio_cobro == 'otro') ? 'selected' : '' ?>>
                  Otro
                </option>

              </select>

            </div>

            <button
              type="submit"
              class="btn btn-primary"
              style="margin-left:10px;"
            >
              <i class="fa fa-search"></i> Filtrar
            </button>

            <!-- =========================
                 EXPORTAR
            ========================== -->

            <a
              href="modules/caja/export_excel.php?fecha_desde=<?= $fecha_desde ?>&fecha_hasta=<?= $fecha_hasta ?>&medio_cobro=<?= $medio_cobro ?>"
              class="btn btn-success"
              target="_blank"
              style="margin-left:10px;"
            >
              <i class="fa fa-file-excel-o"></i> Excel
            </a>

            <a target="_blank"
              href="modules/caja/export_pdf.php?fecha_desde=<?= $fecha_desde ?>&fecha_hasta=<?= $fecha_hasta ?>&medio_cobro=<?= $medio_cobro ?>"
              class="btn btn-danger">

              <i class="fa fa-file-pdf-o"></i>
              Exportar PDF

            </a>

          </form>

        </div>

        <!-- =========================
             TOTAL
        ========================== -->

        <div class="box-body">

          <div class="alert alert-info">

            <h4 style="margin:0;">

              Total caja:
              <strong>
                $<?= number_format($totalCaja, 2) ?>
              </strong>

            </h4>

          </div>

        </div>

        <!-- =========================
             TABLA
        ========================== -->

        <div class="box-body table-responsive">

          <table
            id="dataTables1"
            class="table table-bordered table-striped table-hover"
          >

            <thead>

              <tr>

                <th>Fecha</th>

                <th>Patente</th>

                <th>Categoría</th>

                <th>Tarifa</th>

                <th>Medio</th>

                <th>Monto</th>

              </tr>

            </thead>

            <tbody>

              <?php while ($data = mysqli_fetch_assoc($query)) { ?>

                <tr>

                  <td>
                    <?= date(
                        'd/m/Y H:i',
                        strtotime($data['fecha_movimiento'])
                    ) ?>
                  </td>

                  <td>
                    <?= strtoupper($data['patente']) ?>
                  </td>

                  <td>
                    <?= $data['categoria'] ?>
                  </td>

                  <td>
                    <?= $data['tarifa'] ?>
                  </td>

                  <td>
                    <?= $data['medio_cobro'] ?>
                  </td>

                  <td>

                    <strong>

                      $<?= number_format($data['monto'], 2) ?>

                    </strong>

                  </td>

                </tr>

              <?php } ?>

            </tbody>

          </table>

        </div>

      </div>

    </div>

  </div>

</section>