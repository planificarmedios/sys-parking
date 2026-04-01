

<?php  

if ($_GET['form']=='add') { ?>

  <section class="content-header">
    <h1>
      <i class="fa fa-edit icon-title"></i> Agregar Usuario
    </h1>
    <ol class="breadcrumb">
      <li><a href="../clients/?module=start"><i class="fa fa-home"></i> Inicio </a></li>
      <li><a href="../clients/?module=clients"> Clientes </a></li>
      <li class="active"> agregar </li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <!-- form start -->
          <form role="form" class="form-horizontal" method="POST" action="modules/clients/proses.php?act=insert" enctype="multipart/form-data">
            <div class="box-body">

              <div class="form-group">
                <label class="col-sm-2 control-label">Nombre</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="denominacion" autocomplete="off" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Abonado</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="abonado" autocomplete="off" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Dirección</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="direccion" autocomplete="off" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Localidad</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="localidad" autocomplete="off" required>
                </div>
              </div>

              <!-- <div class="form-group">
                <label class="col-sm-2 control-label">Baja</label>
                <div class="col-sm-5">
                  <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked" checked>
                </div>
              </div> -->

              
            </div><!-- /.box body -->

            <div class="box-footer">
              <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <input type="submit" class="btn btn-primary btn-submit" name="Guardar" value="Guardar">
                  <a href="?module=clients" class="btn btn-default btn-reset">Cancelar</a>
                </div>
              </div>
            </div><!-- /.box footer -->
          </form>
        </div><!-- /.box -->
      </div><!--/.col -->
    </div>   <!-- /.row -->
  </section><!-- /.content -->
<?php
}

elseif ($_GET['form']=='edit') { 
  	if (isset($_GET['id'])) {
      $id_cliente = $_GET['id'];
      $query = mysqli_query($mysqli, "SELECT * FROM clientes WHERE id='$_GET[id]'") 
                                      or die('error: '.mysqli_error($mysqli));
      $data  = mysqli_fetch_assoc($query);
  	}	
?>

  <section class="content-header">
    <h1>
      <i class="fa fa-edit icon-title"></i> Modificar datos de Cliente
    </h1>
    <ol class="breadcrumb">
      <li><a href="../clients/?module=beranda"><i class="fa fa-home"></i> Inicio</a></li>
      <li><a href="../clients/?module=user"> Cliente </a></li>
      <li class="active"> Modificar </li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <!-- form start -->
          <form role="form" class="form-horizontal" method="POST" action="modules/clients/proses.php?act=update" enctype="multipart/form-data">
            <div class="box-body">

              <input type="hidden" name="id_cliente" value="<?php echo $data['id']; ?>">
       
              <div class="form-group">
                <label class="col-sm-2 control-label">Abonado</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="nro_abonado" autocomplete="off" value="<?php echo $data['nro_abonado']; ?>" required>
                </div>
              </div>
              
              <div class="form-group">
                <label class="col-sm-2 control-label">Cliente</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="denominacion" autocomplete="off" value="<?php echo $data['denominacion']; ?>" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Dirección</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="direccion" autocomplete="off" value="<?php echo $data['direccion']; ?>" required>
                </div>
              </div>
            
              <div class="form-group">
                <label class="col-sm-2 control-label">Localidad</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="localidad" autocomplete="off" value="<?php echo $data['localidad']; ?>" required>
                </div>
              </div>

            

            <div class="box-footer">
              <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <input type="submit" class="btn btn-primary btn-submit" name="Guardar" value="Guardar">
                  <a href="?module=clients" class="btn btn-default btn-reset">Cancelar</a>
                </div>
              </div>
            </div><!-- /.box footer -->
          </form>

          <h1>     
          <i class="fa fa-calendar icon-title" style="color:#FFF"></i> 
          <a class="btn btn-success btn-social pull-right" href='?module=form_clients&form=asoc&id_cliente=<?php echo $id_cliente;?>' title="Agendar Abono" data-toggle="tooltip">
              <i class="fa fa-calendar"></i> Agendar Abono
            </a>
          </h1>

          <div class="box box-primary">
        <div class="box-body">

    
          <table id="dataTables2" class="table table-bordered table-striped table-hover">
      
            <thead>
              <tr>
                <th class="center">Código</th>
                <th class="center">Concepto</th>
                <th class="center">Importe de Abono</th>
              
                <th></th>
              </tr>
            </thead>
            <tbody>
            <?php  
            $no = 1;
            $query = mysqli_query($mysqli, "SELECT prod.id, prod.nombre, prod.precio 
            FROM agendas ag
            JOIN clientes cl ON ag.cliente_id = cl.id 
            JOIN productos prod on prod.id = ag.producto_id
            WHERE cl.id = '$_GET[id]'") 
            or die('error: '.mysqli_error($mysqli));
            

            while ($data2 = mysqli_fetch_assoc($query)) { 
              
              $precio_venta = format_rupiah($data2['precio']);
              $id2 = format_rupiah($data2['id']);
           
              echo "<tr>
                      <td width='80' class='center'>$data2[id]</td>
                      <td width='180'><center>$data2[nombre]</center></td>
                      <td width='100' align='right'><center>$ $precio_venta</center></td>
                      <td class='center' width='80'>
                        <div>";
            ?>
                          <a data-toggle="tooltip" data-placement="top" title="Eliminar Abono" class="btn btn-danger btn-sm" href="modules/clients/proses.php?act=deleteAbono&id_cliente=<?php echo $id_cliente;?>&codigo=<?php echo $id2;;?>" onclick="return confirm('estas seguro deseas elimar el registro permanentemente?');">
                              <i style="color:#fff" class="glyphicon glyphicon-trash"></i>
                          </a>
            <?php
              echo "    </div>
                      </td>
                    </tr>";
              $no++;
            }
            ?>
            </tbody>
          </table>
        
        
          </div>
        </div><!-- /.box-body -->
      </div><!-- /.box -->



        </div><!-- /.box -->
      </div><!--/.col -->
    </div>   <!-- /.row -->
  </section><!-- /.content -->
<?php
}

/////////////////////////////////////////////////////////

elseif ($_GET['form']=='sinAgenda') { ?>

<section class="content-header">
  <h1>
    <i class="fa fa-user icon-title"></i> Clientes sin Agenda

    
  </h1>

</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">

  
      <div class="box box-primary">
        <div class="box-body">
     
          <table id="dataTables1" class="table table-bordered table-striped table-hover">
       
            <thead>
              <tr>
                <th class="center">ID</th>
                <th class="center">Abonado</th>
                <th class="center">Cliente</th>
                <th class="center">Dirección</th>
                <th class="center">Localidad</th>
                <th class="center"></th>
              </tr>
            </thead>
                        <tbody>
            <?php  
            $no = 1;
      
            $query = mysqli_query($mysqli, "SELECT m.id, m.nro_abonado, m.denominacion, m.direccion, m.localidad FROM clientes m LEFT JOIN agendas e ON e.cliente_id = m.id WHERE e.cliente_id IS NULL")
                                            or die('error: '.mysqli_error($mysqli));


            while ($data = mysqli_fetch_assoc($query)) { 
  
              echo "<tr>
                      <td width='50' class='center'>$data[id]</td>
                      <td>$data[nro_abonado]</td>
                      <td><center>$data[denominacion]</center></td>
                      <td><center>$data[direccion]</center></td>
                      <td><center>$data[localidad]</center></td>

                      <td class='center' width='100'>
                          <div>";


              echo "      <a data-toggle='tooltip' data-placement='top' title='Modificar' class='btn btn-primary btn-sm' href='?module=form_clients&form=edit&id=$data[id]'>
                                <i style='color:#fff' class='glyphicon glyphicon-edit'></i>
                                </a>";
                                ?>
                                              <a data-toggle="tooltip" data-placement="top" title="Eliminar" class="btn btn-danger btn-sm" href="modules/clients/proses.php?act=delete&id=<?php echo $data['id'];?>" onclick="return confirm('estas seguro de eliminar a <?php echo $data['denominacion']; ?> ?');">
                                                  <i style="color:#fff" class="glyphicon glyphicon-trash"></i>
                                              </a>
                                <?php
                                  echo "    </div>
                      </td>
                    </tr>";
              
            }
            ?>
            </tbody>
          </table>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div><!--/.col -->
  </div>   <!-- /.row -->
</section><!-- /.content
<?php
}

///////////////////////////////////////////////////////////

elseif ($_GET['form']=='edit') { 
  	if (isset($_GET['id'])) {
      $id_cliente = $_GET['id'];
      $query = mysqli_query($mysqli, "SELECT * FROM clientes WHERE id='$_GET[id]'") 
                                      or die('error: '.mysqli_error($mysqli));
      $data  = mysqli_fetch_assoc($query);
  	}	
?>

  <section class="content-header">
    <h1>
      <i class="fa fa-edit icon-title"></i> Modificar datos de Cliente
    </h1>
    <ol class="breadcrumb">
      <li><a href="../clients/?module=beranda"><i class="fa fa-home"></i> Inicio</a></li>
      <li><a href="../clients/?module=user"> Cliente </a></li>
      <li class="active"> Modificar </li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <!-- form start -->
          <form role="form" class="form-horizontal" method="POST" action="modules/clients/proses.php?act=update" enctype="multipart/form-data">
            <div class="box-body">

              <input type="hidden" name="id_cliente" value="<?php echo $data['id']; ?>">
       
              <div class="form-group">
                <label class="col-sm-2 control-label">Abonado</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="nro_abonado" autocomplete="off" value="<?php echo $data['nro_abonado']; ?>" required>
                </div>
              </div>
              
              <div class="form-group">
                <label class="col-sm-2 control-label">Cliente</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="denominacion" autocomplete="off" value="<?php echo $data['denominacion']; ?>" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Dirección</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="direccion" autocomplete="off" value="<?php echo $data['direccion']; ?>" required>
                </div>
              </div>
            
              <div class="form-group">
                <label class="col-sm-2 control-label">Localidad</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="localidad" autocomplete="off" value="<?php echo $data['localidad']; ?>" required>
                </div>
              </div>

            

            <div class="box-footer">
              <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <input type="submit" class="btn btn-primary btn-submit" name="Guardar" value="Guardar">
                  <a href="?module=clients" class="btn btn-default btn-reset">Cancelar</a>
                </div>
              </div>
            </div><!-- /.box footer -->
          </form>

          <h1>     
          <i class="fa fa-calendar icon-title" style="color:#FFF"></i> 
          <a class="btn btn-success btn-social pull-right" href='?module=form_clients&form=asoc&id_cliente=<?php echo $id_cliente;?>' title="Agendar Abono" data-toggle="tooltip">
              <i class="fa fa-calendar"></i> Agendar Abono
            </a>
          </h1>

          <div class="box box-primary">
        <div class="box-body">

    
          <table id="dataTables2" class="table table-bordered table-striped table-hover">
      
            <thead>
              <tr>
                <th class="center">Código</th>
                <th class="center">Concepto</th>
                <th class="center">Importe de Abono</th>
              
                <th></th>
              </tr>
            </thead>
            <tbody>
            <?php  
            $no = 1;
            $query = mysqli_query($mysqli, "SELECT prod.id, prod.nombre, prod.precio 
            FROM agendas ag
            JOIN clientes cl ON ag.cliente_id = cl.id 
            JOIN productos prod on prod.id = ag.producto_id
            WHERE cl.id = '$_GET[id]'") 
            or die('error: '.mysqli_error($mysqli));
            

            while ($data2 = mysqli_fetch_assoc($query)) { 
              
              $precio_venta = format_rupiah($data2['precio']);
           
              echo "<tr>
                      <td width='80' class='center'>$data2[id]</td>
                      <td width='180'><center>$data2[nombre]</center></td>
                      <td width='100' align='right'><center>$ $precio_venta</center></td>
                      <td class='center' width='80'>
                        <div>";
            ?>
                          <a data-toggle="tooltip" data-placement="top" title="Eliminar Abono" class="btn btn-danger btn-sm" href="modules/clients/proses.php?act=deleteAbono&id_cliente=<?php echo $id_cliente?>&codigo=<?php echo $data2['id'];?>" onclick="return confirm('estas seguro de eliminar a <?php echo $data['nombre']; ?> ?');">
                              <i style="color:#fff" class="glyphicon glyphicon-trash"></i>
                          </a>
            <?php
              echo "    </div>
                      </td>
                    </tr>";
              $no++;
            }
            ?>
            </tbody>
          </table>
        
        
          </div>
        </div><!-- /.box-body -->
      </div><!-- /.box -->



        </div><!-- /.box -->
      </div><!--/.col -->
    </div>   <!-- /.row -->
  </section><!-- /.content -->
<?php
}


//////////////////////////////////////////////////

elseif ($_GET['form']=='asoc') { 
  if (isset($_GET['id_cliente'])) {
    $id_cliente = $_GET['id_cliente'];
    
  }	
if (isset($_GET['id_cliente'])) {
      $id_cliente = $_GET['id_cliente'];
     
  	}	
?>

<div class="box box-primary">
        <div class="box-body">
    
          <table id="dataTables2" class="table table-bordered table-striped table-hover">
      
            <thead>
              <tr>
                <th class="center">Código</th>
                <th class="center">Nombre</th>
                <th class="center">Importe de Abono</th>
              
                <th></th>
              </tr>
            </thead>
            <tbody>
            <?php  
            $no = 1;
            $query = mysqli_query($mysqli, "SELECT id,nombre,precio FROM productos ORDER BY precio DESC")
                                            or die('error: '.mysqli_error($mysqli));

            while ($data = mysqli_fetch_assoc($query)) { 
              $precio_venta = format_rupiah($data['precio']);
           
              echo "<tr>
                      <td width='80' class='center'>$data[id]</td>
                      <td width='180'><center>$data[nombre]</center></td>
                      <td width='100' align='right'><center>$ $precio_venta</center></td>
                      <td class='center' width='80'>
                        <div>";
            ?>
                          <a data-toggle="tooltip" data-placement="top" title="Agregar Abono a la Agenda del Cliente" class="btn btn-success btn-sm" href="modules/clients/proses.php?act=addabono&precio_venta=<?php echo $precio_venta;?>&id_cliente=<?php echo $id_cliente;?>&codigo=<?php echo $data['id'];?>" onclick="return confirm('estas seguro de agregar <?php echo $data['nombre']; ?> ?');">
                              <i style="color:#fff" class="glyphicon glyphicon-plus"></i>
                          </a>
            <?php
              echo "    </div>
                      </td>
                    </tr>";
              $no++;
            }
            ?>
            </tbody>
          </table>
        </div><!-- /.box-body -->
</div><!-- /.box --




<?php
}


?>