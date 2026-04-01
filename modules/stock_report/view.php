<!-- Content Header (Page header)
<section class="content-header">
  <h1>
    <i class="fa fa-file-text-o icon-title"></i>Generar Recibos de Agenda
  </h1>
  <ol class="breadcrumb">
    <li><a href="?module=start"><i class="fa fa-home"></i> Inicio</a></li>
    <li class="active">informe</li>
    <li class="active"> imprimir agenda</li>
  </ol>
</section>


<section class="content">
  <div class="row">
    <div class="col-md-12">

      
      <div class="box box-primary">
      
        <form role="form" class="form-horizontal" method="GET" action="modules/sales_invoice/index.php" target="_blank">
          <div class="box-body">

            <div class="form-group">
              <label class="col-sm-3">Fecha de Impresión</label>
              <div class="col-sm-2">
                <input type="month" name="tgl_awal" autocomplete="off" required>
              </div>

              
              <div class="col-sm-2" hidden>
                <input style="margin-left:-35px" type="text" class="form-control date-picker" data-date-format="dd-mm-yyyy" name="tgl_akhir" autocomplete="off" value = '2022-01-01'>
              </div>
            </div>
          </div>
          
          <div class="box-footer">
            <div class="form-group">
              <div class="col-sm-offset-1 col-sm-11">
                <button type="submit" class="btn btn-success btn-social btn-submit" style="width: 120px;">
                  <i class="fa fa-print"></i> Imprimir
                </button>
              </div>
            </div>
          </div>
        </form>
      </div><
    </div>
  </div>   
</section>
 -->

 <!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <i class="fa fa-file-text-o icon-title"></i>Generar Recibo/s de Agenda
  </h1>
  <ol class="breadcrumb">
    <li><a href="?module=start"><i class="fa fa-home"></i> Inicio</a></li>
    <li class="active">informe</li>
    <li class="active"> imprimir agenda</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">

      
      <div class="box box-primary">
         <!-- form start 
		stock_report/view.php
		-->
        <form role="form" class="form-horizontal" method="GET" action="modules/sales_invoice/index.php" target="_blank">
          <div class="box-body">

				<div class="form-group">
				  <label class="col-sm-3">Fecha de Impresión</label>
					  <div class="col-sm-2">
						<input type="month" name="tgl_awal" autocomplete="off" required>
					  </div>

				  
					  <div class="col-sm-2" hidden>
						<input style="margin-left:-35px" type="text" class="form-control date-picker" data-date-format="dd-mm-yyyy" name="tgl_akhir" autocomplete="off" value = '2022-01-01'>
					  </div>
				  
				</div>
          </div>
		  
		  
		  <div class="box-body">

				<div class="form-group">
				  <label class="col-sm-3">Opción de Impresión</label>
				  <div class="col-sm-4">
						<select class="form-control" name="opcion_impresion" required="">
							<option value="1">Mensuales</option>
							<option value="2">Anuales</option>
							<option value="3">Mensuales y Anuales</option>
					
					  </select>				  
				  </div>
				</div>
          </div>
		  
		  
		   <div class="box-body">
		   
	

            <div class="form-group">
              <label class="col-sm-3">Anuales</label>
              <div class="col-sm-4">
                 <select class="form-control" name="mes_renovacion_anual" required="">
				    <option value="0" selected >Mensuales...</option>  
				    <option value="1">Anual Renovacion Enero</option>
                    <option value="2">Anual Renovacion Febrero</option>
                    <option value="3">Anual Renovacion Marzo</option>
                    <option value="4">Anual Renovacion Abril</option>
                    <option value="5">Anual Renovacion Mayo</option>
                    <option value="6">Anual Renovacion Junio</option>
                    <option value="7">Anual Renovacion Julio</option>
                    <option value="8">Anual Renovacion Agosto</option>
                    <option value="9">Anual Renovacion Septiembre</option>
                    <option value="10">Anual Renovacion Octubre</option>
                    <option value="11">Anual Renovacion Noviembre</option>
                    <option value="12">Anual Renovacion Diciembre</option>
                  </select>
              </div>
			  </div>
            </div>
		  
		  
		  
          
          <div class="box-footer">
            <div class="form-group">
              <div class="col-sm-offset-1 col-sm-11">
                <button type="submit" class="btn btn-success btn-social btn-submit" style="width: 120px;">
                  <i class="fa fa-print"></i> Imprimir
                </button>
              </div>
            </div>
          </div>
        </form>
      </div><!-- /.box -->
    </div><!--/.col -->
  </div>   <!-- /.row -->
</section><!-- /.content -->