<?php
/*~ Archivo diario.php
.---------------------------------------------------------------------------.
|    Software: CAS - Computerized Accountancy System                        |
|     Versión: 1.0                                                          |
|   Lenguajes: PHP, HTML, CSS3 y Javascript                                 |
| ------------------------------------------------------------------------- |
|   Autores: Ricardo Vigil (alexcontreras@outlook.com)                      |
|          : Vanessa Campos                                                 |
|          : Ingrid Aguilar                                                 |
|          : Jhosseline Rodriguez                                           |
| Copyright (C) 2013, FIA-UES. Todos los derechos reservados.               |
| ------------------------------------------------------------------------- |
|                                                                           |
| Este archivo es parte del sistema de contabilidad C.A.S para la cátedra   |
| de Sistemas Contables de la Facultad de Ingeniería y Arquitectura de la   |
| Universidad de El Salvador.                                               |
|                                                                           |
'---------------------------------------------------------------------------'
*/
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
	<?php ?>

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
        				if(!isset($conexion)){ include("conexion.php");}
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
                    <input type="submit" name="create_pdf" class="btn btn-danger pull-right" value="Generar PDF" >
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


<!--GENERAR PDF-->
<?php
if(!isset($conexion)){ include("conexion.php");}
if(isset($_POST['create_pdf'])){
    include('../tcpdf/tcpdf.php');
    
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Miguel Caro');
    $pdf->SetTitle($_POST['reporte_name']);
    
    $pdf->setPrintHeader(false); 
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(20, 20, 20, false); 
    $pdf->SetAutoPageBreak(true, 20); 
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->addPage();
    $content = '';



    $content .= '
        <div class="row padding">
            <div class="col-md-12" style="text-align:center;">
                <span>Pdf Creator </span>By Miguel Angel
            </div>
        </div>
        
    ';

    
    $pdf->writeHTML($content, true, 0, true, 0);
    $pdf->lastPage();
    $pdf->output('Reporte.pdf', 'I');
    
}
?>