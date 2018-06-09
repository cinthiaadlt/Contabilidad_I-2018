<?php 
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
	<title>Libro Mayor</title>
</head>

<body>
	<!-- Barra de navegación -->
	<?php include("nav.php"); ?>
	<?php include("funciones.php"); ?>
    <!--Actualiza los libros despeus de haber iingresado una nueva transaccion-->
    <?php 
    if(!isset($conexion)){ include("conexion.php");}
    $sql = "SELECT * FROM cuentas";
    $ejecutar_consulta = $conexion->query($sql);
    while($regs = $ejecutar_consulta->fetch_assoc()){
        actualizarCuentas($conexion, $regs["codigo_cuenta"]);
        saldosCuentas($conexion, $regs["codigo_cuenta"]);
    }
    ?>

	<!-- Contenido de la página -->
	<div class="container" id="contenido">
		<div class="row row-offcanvas row-offcanvas-right">
			<div class="col-xs-12 col-sm-9">
				<div class="page-header">
        			<h3>Libro Mayor</h3>
        		</div>
        		<div class="row">
                    <div class="col-lg-12 well">
                        <p align="justify" class="text-info">
                            Aquí se muestran los saldos de todas las cuentas registradas en el sistema. No aparecen las subcuentas puesto que sus montos totales se ven reflejados en las cuentas a las que pertenecen.
                        </p>
                    </div>
                    <hr>

        			<div class="col-lg-12">
        				<table class="table table-condensed table-bordered table-striped">
        					<thead>
        						<tr>
        							<th>Cuenta</th>
        							<th width="100">Debe</th>
        							<th width="100">Haber</th>
                                    
        						</tr>
        					</thead>
        					<tbody>
        					<?php
                                if(!isset($conexion)){
                                    include("conexion.php");
                                }
                                $sql = "SELECT DISTINCTROW(cuenta) cuentas FROM registro";
                                $ejecutar_consulta = $conexion->query($sql);
                                while($registro = $ejecutar_consulta->fetch_assoc()){
                                    actualizarCuentas($conexion, $registro["cuentas"]);
                                }
                                /*consulata para obtenr totales segun subgrupo*/
                                $consulta = "SELECT DISTINCT(c.codigo_cuenta),c.subgrupo,SUM((c.saldo_debe)) sumdebe,SUM((c.saldo_haber)) sumhaber FROM cuentas c,subgrupos s WHERE c.subgrupo=s.codigo_subgrupo GROUP by c.subgrupo";
                                $consulta = $conexion->query($consulta);
                                            /*Suma segun subgurupos de cuentas*/
                                            while ($subg = $consulta->fetch_assoc()) {
                                                $sql = "SELECT * FROM cuentas where subgrupo='".$subg["subgrupo"]."' ";
                                                $ejecutar_consulta = $conexion->query($sql);
                                                while($regs = $ejecutar_consulta->fetch_assoc()){
                                                    if ($regs["subgrupo"]=$subg["subgrupo"]) {
                                                    echo "<tr>";
                                                    echo "<td>".$regs["codigo_cuenta"]." - ".utf8_encode($regs["nombre_cuenta"])."</td>";
                                                    echo "<td align='right'>".number_format($regs["saldo_debe"],2)."</td>";
                                                    echo "<td align='right'>".number_format($regs["saldo_haber"],2)."</td>";
                                                    echo "</tr>";
                                                    }
                                                }                                               
                                                echo "<tr>";
                                                echo "<td class='text-right'><strong>Sumas Totales:</strong></td>";
                                                echo "<td align='right'>".number_format($subg["sumdebe"],2)."</td>";
                                                echo "<td align='right'>".number_format($subg["sumhaber"],2)."</td>";
                                                echo "</tr>";
                                            }   
                                    /*Total de todas las cuentas*/ 
                                    $sql = "SELECT SUM(saldo_debe) sumadebe, SUM(saldo_haber) sumahaber FROM cuentas";
                                    $ejecutar = $conexion->query($sql);
                                    echo "<tr>";
                                    while($reg = $ejecutar->fetch_assoc()){
                                        if($reg["sumadebe"]!=$reg["sumahaber"]){
                                            echo "<td class='danger'><strong>Totales:</strong> </td>";
                                            echo "<td class='text-right danger'><strong>".number_format($reg["sumadebe"],2)."</strong></td>";
                                            echo "<td class='text-right danger'><strong>".number_format($reg["sumahaber"],2)."</strong></td>";
                                        } else {
                                            echo "<td><strong>Totales:</strong> </td>";
                                            echo "<td class='text-right'><strong>".number_format($reg["sumadebe"],2)."</strong></td>";
                                            echo "<td class='text-right'><strong>".number_format($reg["sumahaber"],2)."</strong></td>";
                                        }
                                    }
                                    echo "</tr>";
                            ?>
        					</tbody>
        				</table>
        			</div>
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