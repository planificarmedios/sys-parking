  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <i class="fa fa-home icon-title"></i> Inicio
    </h1>
    <ol class="breadcrumb">
      <li><a href="?module=beranda"><i class="fa fa-home"></i> Inicio</a></li>
    </ol>
  </section>
  
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-lg-12 col-xs-12">
        <div class="alert alert-info alert-dismissable">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <p style="font-size:15px">
            <i class="icon fa fa-user"></i> Bienvenido <strong><?php echo $_SESSION['name_user']; ?></strong> al Sistema de Gestión.
          </p>        
        </div>
      </div>  
    </div>

    <!-- <form role="form" class="form-horizontal" method="GET" action="backup_db.php" target="_blank">

		   
		  
          
          <div class="box-footer">
            <div class="form-group">
              <div class="col-sm-offset-1 col-sm-11">
                <button type="submit" class="btn btn-success btn-social btn-submit" style="width: 120px;">
                  <i class="fa fa-print"></i> Exportar DB
                </button>
              </div>
            </div>
          </div>
        </form> -->

    
   
    <!-- Small boxes (Stat box) -->
    <div class="row">
     

    <div class="col-lg-6 col-xs-6">
        <!-- small box -->
        <div style="background-color:#00a65a;color:#fff" class="small-box">
          <div class="inner">
            <?php   
   
            $query = mysqli_query($mysqli, "SELECT COUNT(id) as numero FROM clientes")
                                            or die('Error '.mysqli_error($mysqli));


            $data = mysqli_fetch_assoc($query);
            ?>
            <h3><?php echo $data['numero']; ?></h3>
            <p>Clientes Activos</p>
          </div>
          <div class="icon">
            <i class="fa fa-file-o"></i>
          </div>
          <a href="" class="small-box-footer" title="Clientes" data-toggle="tooltip"><i class="fa fa-print"></i></a>
        </div>
      </div><!-- ./col -->

      <div class="col-lg-6 col-xs-6">
        <!-- small box -->
        <div style="background-color:#f39c12;color:#fff" class="small-box">
          <div class="inner">
            <?php  
  
            $query = mysqli_query($mysqli, 
            "SELECT COUNT(m.id) as 'contador' FROM clientes m 
            LEFT JOIN agendas e ON e.cliente_id = m.id
            WHERE e.cliente_id IS NULL")
            or die('Error'.mysqli_error($mysqli));

            $data = mysqli_fetch_assoc($query);
            ?>
            <h3><?php echo $data['contador']; ?></h3>
            <p>Clientes sin Agenda</p>
          </div>
          <div class="icon">
            <i class="fa fa-file-text-o"></i>
          </div>
          <a href="?module=form_clients&form=sinAgenda" class="small-box-footer" title="Imprimir" data-toggle="tooltip"><i class="fa fa-print"></i></a>
        </div>
      </div><!-- ./col -->

      <div class="col-lg-3 col-xs-6">
        
      </div><!-- ./col -->
    </div><!-- /.row -->
  </section><!-- /.content -->