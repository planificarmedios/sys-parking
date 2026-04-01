<section class="content-header">
  <h1>
    Simulador de Tarifas
    <small>Herramienta visual de cálculo en tiempo real</small>
    <button class="btn btn-info pull-right" onclick="recalcular()">
      <i class="fa fa-refresh"></i> Recalcular
    </button>
  </h1>
</section>

<section class="content">

  <!-- =========================
       FECHAS / HORARIOS
  ========================== -->
  <div class="box box-primary">
    <div class="box-header with-border">
      <h3 class="box-title"><i class="fa fa-clock-o"></i> Fechas y horarios</h3>
    </div>

    <div class="box-body form-horizontal">

      <div class="form-group">
        <label class="col-sm-2 control-label">Ingreso</label>
        <div class="col-sm-3">
          <input type="date" id="fecha_ingreso" class="form-control">
        </div>
        <div class="col-sm-2">
          <input type="time" id="hora_ingreso" class="form-control">
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-2 control-label">Egreso</label>
        <div class="col-sm-3">
          <input type="date" id="fecha_egreso" class="form-control">
        </div>
        <div class="col-sm-2">
          <input type="time" id="hora_egreso" class="form-control">
        </div>
      </div>

      <hr>

      <div class="form-group">
        <label class="col-sm-2 control-label">Permanencia</label>
        <div class="col-sm-4">
          <input id="permanencia" class="form-control" readonly>
        </div>

        <label class="col-sm-2 control-label">Total a cobrar</label>
        <div class="col-sm-4">
          <input id="total" class="form-control input-lg text-bold" readonly>
        </div>
      </div>

    </div>
  </div>

  <!-- =========================
       TARIFARIO
  ========================== -->
  <div class="box box-warning">
    <div class="box-header with-border">
      <h3 class="box-title"><i class="fa fa-table"></i> Tarifario (editable)</h3>
    </div>

    <div class="box-body table-responsive">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>Descripción</th>
            <th>Unidad</th>
            <th>Valor</th>
            <th>Monto</th>
            <th>Fraccionable</th>
            <th>Activo</th>
            <th>Tope diario</th>
          </tr>
        </thead>
        <tbody id="tablaTarifas"></tbody>
      </table>
    </div>
  </div>

  <!-- =========================
       DETALLE SELECCIONADO
  ========================== -->
  <div class="box box-success">
    <div class="box-header with-border">
      <h3 class="box-title"><i class="fa fa-check-circle"></i> Cálculo seleccionado</h3>
    </div>

    <div class="box-body table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Concepto</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody id="detalle"></tbody>
      </table>
    </div>
  </div>

  <!-- =========================
       ALTERNATIVAS
  ========================== -->
  <div class="box box-info">
    <div class="box-header with-border">
      <h3 class="box-title"><i class="fa fa-eye"></i> Alternativas analizadas</h3>
    </div>

    <div class="box-body table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Detalle</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody id="alternativas"></tbody>
      </table>
    </div>
  </div>

</section>

<script>
/* =========================
   TARIFARIO BASE
========================= */
let tarifas = [
  {desc:'Abono mensual', unidad:'dias', valor:30, monto:90000, fraccionable:0, activo:1, tope:0},
  {desc:'Abono semanal', unidad:'dias', valor:7, monto:55000, fraccionable:0, activo:1, tope:0},
  {desc:'3 días completos', unidad:'dias', valor:3, monto:25000, fraccionable:0, activo:1, tope:0},
  {desc:'Estadía completa', unidad:'fijo', valor:1, monto:10000, fraccionable:0, activo:1, tope:1},
  {desc:'Por hora', unidad:'horas', valor:1, monto:2000, fraccionable:1, activo:1, tope:0},
  {desc:'Fracción 30 minutos', unidad:'minutos', valor:30, monto:1100, fraccionable:1, activo:1, tope:0},
  {desc:'Fracción 15 minutos', unidad:'minutos', valor:15, monto:600, fraccionable:1, activo:1, tope:0}
];

/* =========================
   RENDER TARIFAS
========================= */
function renderTarifas() {
  let html = '';
  tarifas.forEach((t,i)=>{
    html += `
      <tr>
        <td>${t.desc}</td>
        <td>${t.unidad}</td>
        <td>${t.valor}</td>
        <td>
          <input type="number" class="form-control"
                 value="${t.monto}"
                 onchange="tarifas[${i}].monto=Number(this.value);recalcular()">
        </td>
        <td>
          <input type="checkbox" ${t.fraccionable?'checked':''}
                 onchange="tarifas[${i}].fraccionable=this.checked?1:0;recalcular()">
        </td>
        <td>
          <input type="checkbox" ${t.activo?'checked':''}
                 onchange="tarifas[${i}].activo=this.checked?1:0;recalcular()">
        </td>
        <td>
          <input type="checkbox" ${t.tope?'checked':''}
                 onchange="tarifas[${i}].tope=this.checked?1:0;recalcular()">
        </td>
      </tr>`;
  });
  document.getElementById('tablaTarifas').innerHTML = html;
}

/* =========================
   CÁLCULO PRINCIPAL
========================= */
function recalcular() {

  const fi = document.getElementById('fecha_ingreso').value;
  const hi = document.getElementById('hora_ingreso').value;
  const fe = document.getElementById('fecha_egreso').value;
  const he = document.getElementById('hora_egreso').value;
  if (!fi || !hi || !fe || !he) return;

  const ingreso = new Date(fi + 'T' + hi);
  const egreso  = new Date(fe + 'T' + he);

  let minutos_totales = Math.ceil((egreso - ingreso) / 60000);
  if (minutos_totales < 0) minutos_totales = 0;

  const horas = Math.floor(minutos_totales / 60);
  const resto = minutos_totales % 60;

  document.getElementById('permanencia').value =
    horas + ' hora(s) ' + resto + ' minuto(s)';

  let escenarios = [];

  /* =========================
     HORAS COMPLETAS
  ========================= */
  tarifas.filter(t => t.activo && t.unidad === 'horas' && t.fraccionable)
    .forEach(t => {
      if (horas > 0) {
        escenarios.push({
          items: [{ desc: t.desc, cant: horas, sub: horas * t.monto }],
          total: horas * t.monto
        });
      }
    });

  if (escenarios.length === 0) {
    escenarios.push({ items: [], total: 0 });
  }

  /* =========================
     FRACCIONES (CORREGIDO)
  ========================= */
  if (resto > 0) {

    let fraccionables = tarifas.filter(t =>
      t.activo &&
      t.fraccionable &&
      (t.unidad === 'minutos' || t.unidad === 'horas')
    );

    let nuevos = [];

    escenarios.forEach(esc => {
      fraccionables.forEach(t => {

        let bloque = t.unidad === 'horas' ? 60 : t.valor;
        let cantidad = Math.ceil(resto / bloque);
        if (cantidad <= 0) return;

        let items = JSON.parse(JSON.stringify(esc.items));
        items.push({
          desc: t.desc,
          cant: cantidad,
          sub: cantidad * t.monto
        });

        nuevos.push({
          items: items,
          total: esc.total + (cantidad * t.monto)
        });
      });
    });

    escenarios = nuevos;
  }

  /* =========================
     TOPE (ESTADÍA COMPLETA)
  ========================= */
  tarifas.filter(t => t.activo && t.tope)
    .forEach(t => {
      escenarios.push({
        items: [{ desc: t.desc, cant: 1, sub: t.monto }],
        total: t.monto
      });
    });

  /* =========================
     ELEGIR MEJOR OPCIÓN
  ========================= */
  let mejor = escenarios.reduce((a, b) => a.total < b.total ? a : b);

  document.getElementById('total').value =
    '$ ' + mejor.total.toLocaleString('es-AR');

  document.getElementById('detalle').innerHTML =
    mejor.items.map(i => `
      <tr>
        <td>${i.desc}</td>
        <td>${i.cant}</td>
        <td>$ ${i.sub.toLocaleString('es-AR')}</td>
      </tr>
    `).join('');

  document.getElementById('alternativas').innerHTML =
    escenarios.map(e => `
      <tr class="${e.total === mejor.total ? 'success' : ''}">
        <td>${e.items.map(i => i.desc).join(' + ')}</td>
        <td>$ ${e.total.toLocaleString('es-AR')}</td>
      </tr>
    `).join('');
}

/* =========================
   INIT
========================= */
renderTarifas();
</script>