<?php
if(!isset($conexion)){ include("conexion.php");}
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
    $content .= '';
$content .= '

	<div class="container">
	<div class="row">
		<div class="col-lg-12">
		<table border="0.2">
				<tr>
					<td colspan="4">
					<h2 class="text-center" align="center">Balance General</h2>
						<p align="center">';
						 $fechaactual = getdate();
			            print_r($fechaactual);
			            $content .= '
			            Hasta la fecha: '.$fechaactual[mday].' de '.$fechaactual[month].' de '.$fechaactual[year].' 
						</p>
					</td>
				</tr>
		</table>		
				<div class="row">
					<div class="col-lg-3">
						<table class="table" border="0.5">
							<thead>
							<tr>
							
								<th colspan="2" align="center">
								<b><FONT SIZE="12" face="verdana" color="blue">ACTIVOS
								</font>	</b>
								</th>
							
							</tr>
							</thead>
						';	
							
							$sql = "SELECT * FROM cuentas WHERE codigo_cuenta LIKE '1%'";
							$ejecutar = $conexion->query($sql);
							while($acts = $ejecutar->fetch_assoc()){
								$content .= '
								<tr colspan="2">
								<td >'.$acts["codigo_cuenta"].'.  '.utf8_encode($acts['nombre_cuenta']).'</td>
								<td class="text-center" align="center">'.number_format($acts['saldo_debe']-$acts['saldo_haber'],2).'</td>
								</tr>';
							}
							$consulta = "SELECT SUM((saldo_debe-saldo_haber)) total FROM cuentas WHERE codigo_cuenta LIKE '1%'";
							$ejecutar_consulta = $conexion->query($consulta);
							if($ejecutar_consulta->num_rows > 0){
								while ($regs = $ejecutar_consulta->fetch_assoc()) {
									$content .= '
									<tr>
									<td class="text-center"><font color = "blue"><strong>TOTAL ACTIVOS:</strong></font></td>
									<td align="center"><font color = "blue"> '.number_format($regs["total"],2).'</font></td>
									</tr>';
								}
							}
						$content .= '	
						</table>
					</div>
					<div class="col-lg-3">
						<table class="table " border="0.5">
						<thead>
							<tr>
								<th colspan="2" align="center">
								<b><FONT SIZE="12" face="verdana" color="green">PASIVOS
								</font>	</b>
								</th>
							</tr>	
						</thead>	
							';
							
							$sql = "SELECT * FROM cuentas WHERE codigo_cuenta LIKE '2%'";
							$ejecutar = $conexion->query($sql);
							while($acts = $ejecutar->fetch_assoc()){
								$content .= '	
								<tr>
								<td>'.$acts['codigo_cuenta'].'.  '.utf8_encode($acts['nombre_cuenta']).'</td>
								<td class="text-center" align="center">'.number_format($acts['saldo_debe']-$acts['saldo_haber'],2).'</td>
								</tr>
								';
							}
							$consulta = "SELECT SUM((saldo_debe-saldo_haber)) total FROM cuentas WHERE codigo_cuenta LIKE '2%'";
							$ejecutar_consulta = $conexion->query($consulta);
							if($ejecutar_consulta->num_rows > 0){
								while ($regs = $ejecutar_consulta->fetch_assoc()) {
									$total_pasivos = $regs["total"];
									$content .= '	
									<tr>
									<td class="text-center"><font color = "green"><strong>TOTAL PASIVOS:</strong></font></td>
									<td align="center"><font color = " green">'.number_format($regs['total'],2).'</font></td>
									</tr>
									';
								}
							}
							$content .= '
						</table>
					</div>
					<div class="col-lg-3">
						<table class="table" border="0.5">
							<thead>
							<tr>
								<th colspan="2" align="center">
								<b><FONT SIZE="12" face="verdana" color="orange">CAPITAL;
								</font>	</b>
								</th>
							</tr>	
							</thead>	
							';
							
							$sql = "SELECT * FROM cuentas WHERE codigo_cuenta LIKE '3%'";
							$ejecutar = $conexion->query($sql);
							while($acts = $ejecutar->fetch_assoc()){
								$content .= '
								<tr>";
								<td>'.$acts['codigo_cuenta'].'.  '.utf8_encode($acts['nombre_cuenta']).'</td>
								<td> </td>
								</tr>';
							}
							$consulta = "SELECT SUM((saldo_debe-saldo_haber)) total FROM cuentas WHERE codigo_cuenta LIKE '3%'";
							$ejecutar_consulta = $conexion->query($consulta);
							if($ejecutar_consulta->num_rows > 0){
								while ($regs = $ejecutar_consulta->fetch_assoc()) {
									$total_capital = $regs["total"];
									$content .= '
									<tr>
									<td class="text-center"><font color = "orange"><strong>TOTAL CAPITAL:</strong></font></td>
									<td align="center"><font color = "orange">'.number_format($regs['total'],2).'</font></td>
									</tr>';
								}
							}
							
						$content .= '	
						</table>
					</div>
					<div class="col-lg-3">
						<table class="table" border ="0.5">
							
									<tr>
									<td class="text-right"><strong>Total Pasivos + Capital:</strong></td>
									<td align="center">'.number_format($total_pasivos+$total_capital,2).'</td>
									</tr>
																				
						</table>
					</div>
				</div>
			</div>
		</div>
				
	</div>
	</div>
	
    ';
				
							
						
    $pdf->writeHTMLCell(0, 0, '', '', $content, 0, 1, 0, true, '', true);
    ob_end_clean();
    $pdf->output('Reporte.pdf', 'I');
}
?>
   
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
	<title>Balance General</title>
</head>

<body>
	<!-- Barra de navegación -->
	<?php include("nav.php"); ?>

	<!-- Contenido de la página -->
	<div class="container" id="contenido">
		<div class="row row-offcanvas row-offcanvas-right">
			<div class="col-xs-12 col-sm-9">
				<div class="page-header">
					<h3>Balance General</h3>
				</div>
				<div class="container">
					<div class="row">
						<div class="col-lg-12">

							<table class="table ">
								<thead>
									<tr>
										<th colspan="4">
										<h2 class="text-center">Balance General</h2>
										<!--
											<h2 class="text-center">Vinos Nonualcos y Cia. S.A</h2>
											<p align="center">
												<strong>Balance General</strong>
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
							</table>

						</div>

						<div class="container">
							<div class="row">
								<div class="col-lg-6">
									<div>
										<div class="row">
											<div class="col-lg-12">

												<table class="table table-condensed table-hover ">
													<tr>
														<th colspan="2">Activos</th>
													</tr>
													<?php
													include_once("conexion.php");
													$sql = "SELECT * FROM cuentas WHERE codigo_cuenta LIKE '1%'";
													$ejecutar = $conexion->query($sql);
													while($acts = $ejecutar->fetch_assoc()){
														echo "<tr colspan='2'>";
														echo "<td >".$acts["codigo_cuenta"].". ".utf8_encode($acts["nombre_cuenta"])."</td>";
														echo "<td class='text-right'>".number_format($acts["saldo_debe"]-$acts["saldo_haber"],2)."</td>";
														echo "</tr>";
													}
													$consulta = "SELECT SUM((saldo_debe-saldo_haber)) total FROM cuentas WHERE codigo_cuenta LIKE '1%'";
													$ejecutar_consulta = $conexion->query($consulta);
													if($ejecutar_consulta->num_rows > 0){
														while ($regs = $ejecutar_consulta->fetch_assoc()) {
															echo "<tr>";
															echo "<td class='text-right'><strong>Total Activos:</strong></td>";
															echo "<td align='right'>".number_format($regs["total"],2)."</td>";
															echo "</tr>";
														}
													}
													?>
												</table>
											</div>
										</div>
									</div>
								</div>
								<div class="col-lg-6">
									<div>
										<div class="row">
											<div class="col-lg-12">

												<table class="table ">
													<tr>
														<th>Pasivos</th>
													</tr>	

													<?php
													if(!isset($conexion)){include("conexion.php");}
													$sql = "SELECT * FROM cuentas WHERE codigo_cuenta LIKE '2%'";
													$ejecutar = $conexion->query($sql);
													while($acts = $ejecutar->fetch_assoc()){
														echo "<tr>";
														echo "<td>".$acts["codigo_cuenta"].". ".utf8_encode($acts["nombre_cuenta"])."</td>";
														echo "</tr>";
													}
													$consulta = "SELECT SUM((saldo_debe-saldo_haber)) total FROM cuentas WHERE codigo_cuenta LIKE '2%'";
													$ejecutar_consulta = $conexion->query($consulta);
													if($ejecutar_consulta->num_rows > 0){
														while ($regs = $ejecutar_consulta->fetch_assoc()) {
															$total_pasivos = $regs["total"];
															echo "<tr>";
															echo "<td class='text-right'><strong>Total Pasivos:</strong></td>";
															echo "<td align='right'>".number_format($regs["total"],2)."</td>";
															echo "</tr>";
														}
													}
													?>
												</table>

											</div>
										</div>
										<div class="row">
											<div class="col-lg-12">
												<table class="table">
													<tr>
														<th>Capital</th>
													</tr>

													<?php
													if(!isset($conexion)){include("conexion.php");}
													$sql = "SELECT * FROM cuentas WHERE codigo_cuenta LIKE '3%'";
													$ejecutar = $conexion->query($sql);
													while($acts = $ejecutar->fetch_assoc()){
														echo "<tr>";
														echo "<td>".$acts["codigo_cuenta"].". ".utf8_encode($acts["nombre_cuenta"])."</td>";
														echo "</tr>";
													}
													$consulta = "SELECT SUM((saldo_debe-saldo_haber)) total FROM cuentas WHERE codigo_cuenta LIKE '3%'";
													$ejecutar_consulta = $conexion->query($consulta);
													if($ejecutar_consulta->num_rows > 0){
														while ($regs = $ejecutar_consulta->fetch_assoc()) {
															$total_capital = $regs["total"];
															echo "<tr>";
															echo "<td class='text-right'><strong>Total Capital:</strong></td>";
															echo "<td align='right'>".number_format($regs["total"],2)."</td>";
															echo "</tr>";
														}
													}
													?>
												</table>

											</div>
										</div>
										<div class="row">
											<div class="col-lg-12">
												<table class="table">
													<?php
															echo "<tr>";
															echo "<td class='text-right'><strong>Total Pasivos + Capital:</strong></td>";
															echo "<td align='right'>".number_format($total_pasivos+$total_capital,2)."</td>";
															echo "</tr>";
																										
													?>
												</table>

											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

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