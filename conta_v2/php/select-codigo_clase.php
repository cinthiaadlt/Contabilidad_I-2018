<?php 
include("sesion.php");
if(!isset($conexion)){
	include("conexion.php");
}
error_reporting(E_ALL ^ E_NOTICE);
$consulta = "SELECT * FROM clasificaciones ORDER BY codigo_clasificacion DESC LIMIT 1;";
$ejecutar_consulta = $conexion->query($consulta);
$num_regs = $ejecutar_consulta->num_rows;
while($registro = $ejecutar_consulta->fetch_assoc()){
	if ($num_regs>0) {
		$id=$registro["codigo_clasificacion"];
		while($id<9){
			$id=$id+1;
			echo "<option value='";
			echo $id;
			echo "'";
			if($_GET["clase_slc"]==$id){
				echo " selected";
			}
			echo ">" . $id.". </option>";
		}
	}
	else{
		$id=1;
		echo "<option value='";
			echo $id;
			echo "'";
			if($_GET["clase_slc"]==$id){
				echo " selected";
			}
			echo ">" . $id.". </option>";
	}

	
}
?>