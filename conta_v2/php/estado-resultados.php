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
    $content .= '
<div class="container">
	<div class="row">
		<div class="col-lg-12">
		<table class="table " border="1" cellpadding="5">
			<thead>
				<tr>
					<th colspan="4">
					<h2 class="text-center" align="center">Estado de Resultados</h2>
						<p align="center">';
							$fecha_ac = actual_date();	
							 $content .= '
							 '.$fecha_ac.'
						</p>
					</th>
				</tr>
			</thead>
			<tbody>
			
			';
 				$sql = "SELECT * FROM cuentas WHERE codigo_cuenta LIKE '4%'";
                $ejecutar = $conexion->query($sql);
                while($acts = $ejecutar->fetch_assoc()){
                    $content .= '
                    <tr>
                    <td colspan="3" >'.$acts['codigo_cuenta'].'.'.utf8_encode($acts['nombre_cuenta']).'</td>
                    <td align="center">'.number_format($acts['saldo_debe']-$acts['saldo_haber'],2).'</td>
                    </tr> ';
                }
                
                $consulta = "SELECT SUM((saldo_debe-saldo_haber)) total FROM cuentas WHERE codigo_cuenta LIKE '4%'";
                $ejecutar_consulta = $conexion->query($consulta);
                if($ejecutar_consulta->num_rows > 0){
                    while ($regs = $ejecutar_consulta->fetch_assoc()) {
                        $content .= '
                        <tr>
                        <td class="text-left"  colspan="3" ><strong>Total</strong></td>
                        <td align="center">'.number_format($regs['total'],2).'</td>
                        </tr>';
                    }
                }
$content .= '
		</tbody>	
		</table>
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
	<title>Estado de Resultados</title>
</head>

<body>
	<!-- Barra de navegación -->
	<?php include("nav.php"); ?>

	<!-- Contenido de la página -->
	<div class="container" id="contenido">
		<div class="row row-offcanvas row-offcanvas-right">
		
			<div class="col-xs-12 col-sm-9">
				<div class="page-header">
					<h3>Estado de Resultados</h3>
				</div>
				<div class="container">
					<div class="row">
						<div class="col-lg-12">

							<table class="table ">
								<thead>
									<tr>
										<th colspan="4">
										<h2 class="text-center">Estado de Resultados</h2>
											<p align="center">
												<?php 
												include("funciones.php");
												 echo actual_date (); ?>
											</p>
										</th>
									</tr>
								</thead>
							</table>

						</div>

						<div class="container">
							<div class="row">
								<div class="col-lg-12">
									<div>
										<div class="row">
											<div class="col-lg-12">

												<table class="table table-condensed table-bordered table-hover ">
													<tr>
														<th colspan="2">Resultados</th>
													</tr>
													<?php
													include_once("conexion.php");
													$sql = "SELECT * FROM cuentas WHERE codigo_cuenta LIKE '4%'";
													$ejecutar = $conexion->query($sql);
													while($acts = $ejecutar->fetch_assoc()){
														echo "<tr colspan='2'>";
														echo "<td >".$acts["codigo_cuenta"].". ".utf8_encode($acts["nombre_cuenta"])."</td>";
														echo "<td class='text-right'>".number_format($acts["saldo_debe"]-$acts["saldo_haber"],2)."</td>";
														echo "</tr>";
													}
													$consulta = "SELECT SUM((saldo_debe-saldo_haber)) total FROM cuentas WHERE codigo_cuenta LIKE '4%'";
													$ejecutar_consulta = $conexion->query($consulta);
													if($ejecutar_consulta->num_rows > 0){
														while ($regs = $ejecutar_consulta->fetch_assoc()) {
															echo "<tr>";
															echo "<td class='text-right'><strong>Total</strong></td>";
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
								
							</div>
						</div>

					</div>
				</div>
				<div class="col-md-12">
                <form method="post" ><!-- action="pdf_balance-comp.php"-->
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