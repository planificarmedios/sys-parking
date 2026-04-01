<?php
// ===============================
// FILTROS (fecha por defecto = hoy)
// ===============================
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
$medio = $_GET['medio_cobro'] ?? '';

// ===============================
// WHERE DINÁMICO
// ===============================
$where = "WHERE en_playa = 0";

if ($fecha !== '') {
  $where .= " AND fecha_egreso = '$fecha'";
}

if ($medio !== '') {
  $where .= " AND medio_cobro = '$medio'";
}

// ===============================
// LISTADO DE COBRANZAS
// ===============================
$cobranzas = mysqli_query($mysqli, "
  SELECT
    patente,
    fecha_ingreso,
    hora_ingreso,
    fecha_egreso,
    hora_egreso,
    monto_total,
    medio_cobro
  FROM vehiculos
  $where
  ORDER BY hora_egreso DESC
");

// ===============================
// TOTAL GENERAL
// ===============================
$total = mysqli_query($mysqli, "
  SELECT SUM(monto_total) AS total
  FROM vehiculos
  $where
");
$row_total = mysqli_fetch_assoc($total);
$total_general = $row_total['total'] ?? 0;
?>

<section class="content-header">
  <h1>
    <i class="fa fa-money"></i> Cobranzas
  </h1>
</section>

<section class="content">
  <div class="row">
    <div class="col-md-12">

      <!-- FILTROS -->
      <div class="box box-primary">
        <div class="box-body">

          <form method="GET" class="form-inline">
            <input type="hidden" name="module" value="cobranzas">

            <div class="form-group">
              <label>Fecha</label>
              <input type="date"
                     name="fecha"
                     class="form-control"
                     value="<?= $fecha ?>">
            </div>

            <div class="form-group">
              <label>Medio de cobro</label>
              <select name="medio_cobro" class="form-control">
                <option value="">Todos</option>
                <option value="efectivo" <?= $medio=='efectivo'?'selected':'' ?>>Efectivo</option>
                <option value="mercadopago" <?= $medio=='mercadopago'?'selected':'' ?>>Mercado Pago</option>
                <option value="transferencia" <?= $medio=='transferencia'?'selected':'' ?>>Transferencia</option>
                <option value="CuentaDNI" <?= $medio=='CuentaDNI'?'selected':'' ?>>Cuenta DNI</option>
                <option value="tarjeta" <?= $medio=='tarjeta'?'selected':'' ?>>Tarjeta</option>
                <option value="otro" <?= $medio=='otro'?'selected':'' ?>>Otro</option>
              </select>
            </div>

            <button type="submit" class="btn btn-primary">
              Filtrar
            </button>

            <!-- VUELVE A HOY -->
            <a href="?module=cobranzas" class="btn btn-default">
              Hoy
            </a>

            <!-- HISTÓRICO -->
            <a href="?module=cobranzas&fecha=" class="btn btn-warning">
              Ver todo
            </a>

          </form>

        </div>
      </div>

      <!-- CONTEXTO -->
      <p class="text-muted">
        Mostrando cobranzas de:
        <strong>
          <?= $fecha ? date('d/m/Y', strtotime($fecha)) : 'todas las fechas' ?>
        </strong>
        <?= $medio ? ' - Medio: '.ucfirst($medio) : '' ?>
      </p>

      <!-- TABLA -->
      <div class="box box-success">
        <div class="box-body table-responsive">

          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Patente</th>
                <th>Ingreso</th>
                <th>Egreso</th>
                <th>Monto</th>
                <th>Medio de cobro</th>
              </tr>
            </thead>
            <tbody>
              <?php if (mysqli_num_rows($cobranzas) > 0) { ?>
                <?php while ($row = mysqli_fetch_assoc($cobranzas)) { ?>
                  <tr>
                    <td><?= $row['patente'] ?></td>
                    <td>
                      <?= date('d/m/Y', strtotime($row['fecha_ingreso'])) ?>
                      <?= substr($row['hora_ingreso'],0,5) ?>
                    </td>
                    <td>
                      <?= date('d/m/Y', strtotime($row['fecha_egreso'])) ?>
                      <?= substr($row['hora_egreso'],0,5) ?>
                    </td>
                    <td>$ <?= number_format($row['monto_total'], 2, ',', '.') ?></td>
                    <td><?= ucfirst($row['medio_cobro']) ?></td>
                  </tr>
                <?php } ?>
              <?php } else { ?>
                <tr>
                  <td colspan="5" class="text-center text-muted">
                    No hay cobranzas para los filtros seleccionados
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>

        </div>

        <!-- TOTAL -->
        <div class="box-footer">
          <h4>
            Total
            <?= $medio ? '('.ucfirst($medio).')' : 'general' ?>:
            <strong>$ <?= number_format($total_general, 2, ',', '.') ?></strong>
          </h4>
        </div>

      </div>

    </div>
  </div>
</section>s