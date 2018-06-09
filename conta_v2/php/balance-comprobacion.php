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
	<title>Balance de Comprobación</title>
</head>

<body>
	<!-- Barra de navegación -->
	<?php include("nav.php"); ?>

	<!-- Contenido de la página -->
	<div class="container" id="contenido">
		<div class="row row-offcanvas row-offcanvas-right">
			<div class="col-xs-12 col-sm-9">
				<div class="page-header">
        			<h3>Balance de Comprobación</h3>
        		</div>
        		<div class="row">
        			<div class="container">
        				<table class="table table-bordered table-hover">
        					<thead>
								<tr>
									<th colspan="6">
									<!--Nombre de la entidad -->
										<h2 class="text-center">Balance de Comprobación</h2>
										<!--
										<p align="center">
											<strong>Balance de Comprobación</strong>
										</p>
										-->
										<p align="center">
											<script>
												var month=new Array();
												month[0]="Enero";
												month[1]="Febrero";
												month[2]="Marzo";
												month[3]="Abril";
												month[4]="Mayo";
												month[5]="Junio";
												month[6]="Julio";
												month[7]="Agosto";
												month[8]="Septiembre";
												month[9]="Octubre";
												month[10]="Noviembre";
												month[11]="Diciembre";
												var fecha = new Date();
												document.write("Al " + fecha.getDate() + " de " + month[fecha.getMonth()] + " de " + fecha.getFullYear());
											</script>
										</p>
									</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th>Cuenta</th>
									<th>Debe</th>
									<th>Haber</th>
									<th >Saldo debe</th>
                                    <th >Saldo haber</th>
								</tr>
								<?php 
								error_reporting(E_ALL ^ E_NOTICE);
								if(!isset($conexion)){
									include("conexion.php");
									/*consulata para obtenr totales segun subgrupo*/
                                	$consulta = "SELECT DISTINCT(c.codigo_cuenta),c.subgrupo,SUM((c.saldo_debe)) sumdebe,SUM((c.saldo_haber)) sumhaber FROM cuentas c,subgrupos s WHERE c.subgrupo=s.codigo_subgrupo GROUP by c.subgrupo";
                               		$consulta = $conexion->query($consulta);
                               		
                                    /*Suma segun subgurupos de cuentas*/
                                while ($subg = $consulta->fetch_assoc()) {
									$sql = "SELECT * FROM cuentas where subgrupo='".$subg["subgrupo"]."' ";
									$ejecutar = $conexion->query($sql);
									$deudor=0;
									$acreedor=0;
									while($regs = $ejecutar->fetch_assoc()){
										echo "<tr>";
										echo "<td>".utf8_encode($regs["codigo_cuenta"])." ".utf8_encode($regs["nombre_cuenta"])."</td>";
										if($regs["saldo_debe"]==0){
											$deudor =$deudor+0; 
                                            $acreedor =$acreedor+ ($regs["saldo_haber"]-$regs["saldo_haber"]);
											echo "<td class='text-right'>".number_format($regs["saldo_debe"],2)."</td>";
                                            echo "<td class='text-right'>".number_format($regs["saldo_haber"],2)."</td>";
                                            echo "<td align='right'>$ ".number_format($deudor, 2)."</td>";
                                            echo "<td align='right'>$ ".number_format($acreedor, 2)."</td>";
										} elseif ($regs["saldo_haber"]==0){
                                            		$deudor = $deudor+$regs["saldo_debe"]-$regs["saldo_haber"];
                                            		$acreedor = $acreedor+0;
													echo "<td class='text-right'>".number_format($regs["saldo_debe"],2)."</td>";
                                                    echo "<td class='text-right'>".number_format($regs["saldo_haber"],2)."</td>";
                                                    echo "<td align='right'>$ ".number_format($deudor, 2)."</td>";
                                            		echo "<td align='right'>$ ".number_format($acreedor, 2)."</td>";
												}elseif ($regs["saldo_debe"]<$regs["saldo_haber"]) {
                                                    if ($acreedor>$regs["saldo_debe"]) {
                                                		$acreedor=$acreedor-$regs["saldo_haber"];
                                                	}
                                                	else{
                                                		$acreedor=$acreedor-$regs["saldo_debe"];
                                                	}
                                                    $deudor =$deudor+ 0;
                                                    $acreedor =$acreedor+ ($regs["saldo-haber"]-$regs["saldo_debe"]);
                                                    echo "<td class='text-right'>".number_format($regs["saldo_debe"],2)."</td>";
                                                    echo "<td class='text-right'>".number_format($regs["saldo_haber"],2)."</td>";
                                                    echo "<td align='right'>$ ".number_format($deudor, 2)."</td>";
                                            		echo "<td align='right'>$ ".number_format($acreedor, 2)."</td>";
                                                }
                                                elseif ($regs["saldo_debe"]>$regs["saldo_haber"]) {
                                                	if ($deudor>$regs["saldo_haber"]) {
                                                		$deudor=$deudor-$regs["saldo_debe"];
                                                	}
                                                	else{
                                                		$deudor=$deudor-$regs["saldo_haber"];
                                                	}

                                                    $deudor = $deudor+($regs["saldo_debe"]-$regs["saldo_haberbe"]);
                                                    $acreedor = $acreedor+ 0;
                                                    echo "<td class='text-right'>".number_format($regs["saldo_debe"],2)."</td>";
                                                    echo "<td class='text-right'>".number_format($regs["saldo_haber"],2)."</td>";
                                                    echo "<td align='right'>$ ".number_format($deudor, 2)."</td>";
                                            		echo "<td align='right'>$ ".number_format($acreedor, 2)."</td>";
                                                }

											echo "</tr>";
									}
									echo "<tr>";
                                                echo "<td class='text-right' colspan='3'><strong>Sumas Totales:</strong></td>";
                                              
                                                echo "<td align='right'>".number_format($deudor,2)."</td>";
                                                echo "<td align='right'>".number_format($acreedor,2)."</td>";
                                                echo "</tr>";
								}
									/*Total de todas las cuentas*/ 
									$sql = "SELECT SUM(saldo_debe) sumadebe, SUM(saldo_haber) sumahaber FROM cuentas";
									$ejecutar = $conexion->query($sql);
									echo "<tr>";
									while($reg = $ejecutar->fetch_assoc()){
											if ($reg["sumadebe"]<$reg["sumahaber"]) {
                                                    $deudor = 0;
                                                    $acreedor = $reg["sumahaber"]-$reg["sumadebe"];
                                                }
                                                elseif ($reg["sumdebe"]>$reg["sumhaber"]) {
                                                    $deudor = $reg["sumdebe"]-$reg["sumhaber"];
                                                    $acreedor = 0;
                                                }

										if($reg["sumadebe"]!=$reg["sumahaber"]){
											echo "<td class='danger'><strong>Totales:</strong> </td>";
											echo "<td class='text-right danger'><strong>".number_format($reg["sumadebe"],2)."</strong></td>";
											echo "<td class='text-right danger'><strong>".number_format($reg["sumahaber"],2)."</strong></td>";
											if ($reg["sumadebe"]>=$reg["sumahaber"]) {
												echo "<td class='text-right danger'>$ ".number_format($reg["sumadebe"]-$reg["sumahaber"], 2)."</td>";
												echo "<td class='text-right danger'>$ ".number_format(0, 2)."</td>";
											}
											else{
												echo "<td class='text-right danger'>$ ".number_format(0, 2)."</td>";
												echo "<td class='text-right danger'>$ ".number_format($reg["sumahaber"]-$reg["sumadebe"], 2)."</td>";
											}
											
                                            
										} else {
											echo "<td><strong>Totales:</strong> </td>";
											echo "<td class='text-right'><strong>".number_format($reg["sumadebe"],2)."</strong></td>";
											echo "<td class='text-right'><strong>".number_format($reg["sumahaber"],2)."</strong></td>";
											echo "<td class='text-right danger'>$ ".number_format('0', 2)."</td>";
                                            echo "<td class='text-right danger'>$ ".number_format('0', 2)."</td>";
										}
										
											
									}
									echo "</tr>";
								}
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