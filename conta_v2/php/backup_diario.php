<?php
if(!isset($conexion)){ include("conexion.php");}
 $sql2 = "SELECT * FROM registro";
$ejecutar_consulta = $conexion->query($sql2);

if(isset($_POST['create_pdf'])){
   include("funciones.php"); 
  include('../tcpdf/tcpdf.php');
    
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Contabilidad vicaria');
    $pdf->SetTitle($_POST['reporte_name']);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(20, 20, 20, false);
    $pdf->SetAutoPageBreak(true, 20);
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->addPage();
    $content = '';

    if($ejecutar_consulta->num_rows!=0){
    $sql = "SELECT DISTINCTROW(transaccion) AS transacciones FROM registro";
    $ejecutar_consulta2 = $conexion->query($sql);
                while($registro = $ejecutar_consulta2->fetch_assoc()){

                     $transaccion1=$registro["transacciones"];
                     $content .= '
                  <div class="row">
                        <div class="col-lg-12">
                                 <h2 align="center">LIBRO DIARIO</h2>
                                    <p align="center">';
                                     $fechaactual = getdate();
                                    print_r($fechaactual);
                                    $content .= '
                                    Fecha: '.$fechaactual[mday].' de '.$fechaactual[month].' de '.$fechaactual[year].' 
                                    </p>
                            <div>
                                <h3><strong> 
                                    Ficha N°: '.$transaccion1.'
                               </strong> </h3>
                            <br/>
                            </div>
                            <table class="table-bordered" width="100%" border="1">

                                <thead>
                                <tr height="10%">
                                    <th width = "5%" align="center"> <b> ID </b></th>
                                    <th width = "15%" align="center"><b> FECHA</b></th>
                                    <th width = "10%" align="center"><b> CUENTA</b></th>
                                    <th width = "40%" align="center"><b> DESCRIPCION</b></th>
                                    <th width = "10%" align="center"><b> DEBE</b> </th>
                                    <th width = "10%" align="center"><b> HABER </b></th>
                                    <th width = "15%" align="center"><b> DIFERENCIA </b></th>
                                </tr>
                                </thead>
                            <tbody>
                    ';
                $transaccion1=$registro["transacciones"];
                $sql_1 = "SELECT id, DATE_FORMAT(fecha,'%d-%m-%Y')as fecha, cuenta, concepto, debe, haber FROM registro WHERE transaccion = 1 ORDER BY fecha ASC";
                $ex_query = $conexion->query($sql_1);
                if($ex_query->num_rows>0){
                                /*  '.$regs['concepto'].' */
                         while ($regs = $ex_query->fetch_assoc()) {
                            $content .=' 
                                <tr height="20">
                                <td width = "5%" align="center">'.$regs['id'].'</td>
                                <td width = "15%" align="center">'.$regs['fecha'].'</td>
                                <td width = "10%" align="center">'.$regs['cuenta'].'</td>
                                <td width = "40%" height="35" align="left">   </td> 
                                <td width = "10%" align="center">'.$regs['debe'].'</td>
                                <td width = "10%" align="center">'.$regs['haber'].'</td>
                                <td width = "15%" align="center">-</td>
                            </tr>';
                            } 

                $sql_2 = "SELECT SUM(debe) as sumadebe, SUM(haber) AS sumahaber FROM registro WHERE transaccion=$transaccion1";
                $ex_query = $conexion->query($sql_2);

                while($regs = $ex_query->fetch_assoc()){
                    $dif = $regs['sumadebe']-$regs['sumahaber'];
                    $content .=' 
                    <tr>
                    <td colspan="4" align="center"> SUMA FICHA NRO:'.$transaccion1 .' </td>
                    <td align="center">Bs. '.number_format($regs['sumadebe'], 2).'</td>
                    <td align="center">Bs.'.number_format($regs['sumahaber'], 2).'</td>
                    </tr>
                    ';           
                   }

                   $content .=' 
                   </tbody>
                   </table>
                   ';
                }
}

    $sql3 = "SELECT sum(debe) as sumadebe, sum(haber) as sumahaber from registro";
                $ejecutar_consulta = $conexion->query($sql3);
                    while($registro = $ejecutar_consulta->fetch_assoc()){
                    $dif = $registro["sumadebe"]-$registro["sumahaber"];
                    $content.=' 
                   
                    <div class="row">
                        <div class="col-lg-12">
                    <table class="table-bordered" border="0.5" width="100%">
                    <tr>
                        <td colspan"4" class="center"width="70%">
                            <strong>SUMAS TOTALES</strong></td>

                        <td  align="center" width="10%>
                            <strong>Bs.'.number_format($registro['sumadebe'],2).'</strong></td>

                       <td align="center" width="10%>
                            <strong>Bs'.number_format($registro['sumahaber'], 2).' </strong>
                       </td>
                       
                        <td "align="center" width="10% ><strong>Bs. '.number_format($dif, 2).'</strong></td>


                       ';

                       if($dif!=0){
                        $content .='
                        <td "align="center" width="10% ><strong>Bs. '.number_format($dif, 2).'</strong></td>
                        '; }
                        else{
                        $content .='
                        <td width="10%></td>
                        ';
                    }
                    $content .='        
                    </tr>
                    </table>
                    </div>
                    </div>
                    ';
      
                    
    }  
       
  }     
    $pdf->writeHTMLCell(0, 0, '', '', $content, 0, 1, 0, true, '', true);
    ob_end_clean();
    $pdf->output('Reporte.pdf', 'I');
    
}
?>

<?php
    include("funciones.php");
    include("sesion.php");
    if(!$_COOKIE["sesion"]){
        header("Location: salir.php");
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css"/>
    <link rel="stylesheet" type="text/css" href="../css/estilos.css"/>
    <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" />
    <script>
        !window.jQuery && document.write("<script src='../js/jquery.min.js'><\/script>");
    </script>
    <title>Libro Diario</title>
</head>

<body>
    <!-- Barra de navegación -->
    <?php include("nav.php"); ?>

    <!-- Contenido de la página -->
    <div class="container" id="contenido">
        <div class="row row-offcanvas row-offcanvas-right">
            <div class="col-xs-12 col-sm-9">
                <div class="page-header">
                    <?php $h1 = "Libro Diario";
                        echo '<h3>'.$h1.'</h3>'
                    ?>
                    <!--
                    <h3>Libro Diario</h3>
                    -->
                </div>
                <div class="row">
                    <div class="col-lg-12 well">
                    <!--
                        <h2 class="text-primary"><span class="glyphicon glyphicon-info-sign"></span> Libro Diario General</h2>
                        -->
                        <p align="justify" class="text-info">
                            En esta sección usted podrá revisar el historial de transacciones que se han realizado en el sistema de manera periódica. Para ver los detalles de cada transacción haga click en el ID correspondiente a cada registro (etiquetas verdes).
                        </p>
                    </div>
                    <hr>
                    <div class="col-lg-12">
                        <?php
                        if(isset($_GET["mensaje"])){
                            echo "<div class='alert alert-info alert-dismissable'>";
                            echo "<button type='button' class='close' data-dismiss='alert'>&times;</button>";
                            echo $_GET["mensaje"];
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <?php
                        if(!isset($conexion)){include("conexion.php");}
                        $sql = "SELECT * FROM registro";
                        $ejecutar_consulta = $conexion->query($sql);
                        if($ejecutar_consulta->num_rows!=0){
                            $sql = "SELECT DISTINCTROW(transaccion) AS transacciones FROM registro";
                            $ejecutar_consulta = $conexion->query($sql);
                            while($registro = $ejecutar_consulta->fetch_assoc()){
                                //print_r($registro); echo "<br />";
                                asientos($conexion, $registro["transacciones"]);
                            }
                            echo "<br><hr>";
                            $sql = "SELECT sum(debe) as sumadebe, sum(haber) as sumahaber from registro";
                            $ejecutar_consulta = $conexion->query($sql);
                            while($registro = $ejecutar_consulta->fetch_assoc()){
                                $dif = $registro["sumadebe"]-$registro["sumahaber"];
                                echo "<div>";
                                echo "<table class='table table-bordered table-condensed table-hover'>";
                                echo "<tr>";
                                echo "<td width='730' class='text-right'><strong>SUMAS TOTALES</strong></td>" ;
                                echo "<td width='90' align='right'><strong>$ ".number_format($registro["sumadebe"],2)."</strong></td>";
                                echo "<td width='90' align='right'><strong>$ ".number_format($registro["sumahaber"], 2)."</strong></td>";
                                if($dif!=0){
                                    echo "<td width='90' class='danger' align='right'><strong>$ ".number_format($dif, 2)."</strong></td>";
                                } else{
                                    echo "<td width='90'></td>";
                                }
                                echo "</tr>";
                                echo "</table>";
                                echo "</div>";
                            }
                        } else {
                            $sql = "CALL reiniciar_saldos()";
                            $ejecutar_consulta = $conexion->query($sql);
                            echo "<div class='alert alert-info'>";
                            echo "No hay asientos.";
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>
                <div class="col-md-12">
                <form method="post">
                    <input type="hidden" name="reporte_name" value="<?php echo $h1; ?>">
                    <input type="submit" name="create_pdf" class="btn btn-danger pull-right" value="Generar PDF">
                </form>
              </div>
            </div><!--/span-->

            <!-- Barra lateral o sidebar -->
            <?php include("sidebar.php"); ?>

        </div>
    </div>

    <!-- Pie de página o Footer -->
    <?php include("footer.php"); ?>

    <!-- Ventanas flotantes -->
    <?php include("modal.php"); ?>

    <script src="../js/bootstrap.min.js"></script>
</body>
</html>