<?php 
include("sesion.php");
if(!isset($conexion)){
	include("conexion.php");
}
error_reporting(E_ALL ^ E_NOTICE);
$consulta = "SELECT * FROM clasificaciones;";
$ejecutar_consulta = $conexion->query($consulta);
while($registro = $ejecutar_consulta->fetch_assoc()){
	echo "<option value='";
	echo $registro["codigo_clasificacion"];
	echo "'";
	if($_GET["clase_slc"]==$registro["codigo_clasificacion"]){
		echo " selected";
	}
	echo ">" . $registro["codigo_clasificacion"].". ".utf8_encode($registro["nombre_clasificacion"])."</option>";
}
?>