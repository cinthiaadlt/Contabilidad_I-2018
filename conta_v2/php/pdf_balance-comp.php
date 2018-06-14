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
<table class="table table-bordered table-hover" align="center">
<thead>
    <tr>
        <th colspan="6">
            <h2 class="text-center" align="center">Balance de Comprobación</h2>
            <p align="center">';
            $fechaactual = getdate();
            print_r($fechaactual);
            $content .= '
            Hasta la fecha: '.$fechaactual[mday].' de '.$fechaactual[month].' de '.$fechaactual[year].'
            </p>
            <br>
            <br>
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
    </tr>';
/*consulta para obtenr totales segun subgrupo*/
        $consulta = "SELECT DISTINCT(c.codigo_cuenta),c.subgrupo,SUM((c.saldo_debe)) sumdebe,SUM((c.saldo_haber)) sumhaber FROM cuentas c,subgrupos s WHERE c.subgrupo=s.codigo_subgrupo GROUP by c.subgrupo";
        $consulta = $conexion->query($consulta);
        
        /*Suma segun subgurupos de cuentas*/
    while ($subg = $consulta->fetch_assoc()) {
        $sql = "SELECT * FROM cuentas where subgrupo='".$subg["subgrupo"]."' ";
        $ejecutar = $conexion->query($sql);
        $deudor=0;
        $acreedor=0;
        while($regs = $ejecutar->fetch_assoc()){
           $content .='
            <tr>
            <td>'.$regs['codigo_cuenta'].' - '.utf8_encode($regs['nombre_cuenta']).'</td>';
            if($regs["saldo_debe"]==0){
                $deudor =$deudor+0; 
                $acreedor =$acreedor+ ($regs["saldo_haber"]-$regs["saldo_haber"]);
                $content .='
                 <td class="text-right">'.number_format($regs['saldo_debe'],2).'</td>
                 <td class="text-right">'.number_format($regs['saldo_haber'],2).'</td>
                 <td align="right">$ '.number_format($deudor, 2).'</td>
                 <td align="right">$ '.number_format($acreedor, 2).'</td>
                ';
            } elseif ($regs["saldo_haber"]==0){
                        $deudor = $deudor+$regs["saldo_debe"]-$regs["saldo_haber"];
                        $acreedor = $acreedor+0;
                        $content .='
                         <td class="text-right">'.number_format($regs['saldo_debe'],2).'</td>
                         <td class="text-right">'.number_format($regs['saldo_haber'],2).'</td>
                         <td align="right">$ '.number_format($deudor, 2).'</td>
                         <td align="right">$ '.number_format($acreedor, 2).'</td>
                        ';
                    }elseif ($regs["saldo_debe"]<$regs["saldo_haber"]) {
                        if ($acreedor>$regs["saldo_debe"]) {
                            $acreedor=$acreedor-$regs["saldo_haber"];
                        }
                        else{
                            $acreedor=$acreedor-$regs["saldo_debe"];
                        }
                        $deudor =$deudor+ 0;
                        $acreedor =$acreedor+ ($regs["saldo-haber"]-$regs["saldo_debe"]);
                        $content .='
                         <td class="text-right">'.number_format($regs['saldo_debe'],2).'</td>
                         <td class="text-right">'.number_format($regs['saldo_haber'],2).'</td>
                         <td align="right">$ '.number_format($deudor, 2).'</td>
                         <td align="right">$ '.number_format($acreedor, 2).'</td>
                        ';
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
                        $content .='
                         <td class="text-right">'.number_format($regs['saldo_debe'],2).'</td>
                         <td class="text-right">'.number_format($regs['saldo_haber'],2).'</td>
                         <td align="right">$ '.number_format($deudor, 2).'</td>
                         <td align="right">$ '.number_format($acreedor, 2).'</td>
                        ';
                    }
                $content .='</tr>';
        }
        $content .='</tr>
                     <td class="text-right" colspan="3"><strong>Sumas Totales:</strong></td>
                     <td align="right">'.number_format($deudor,2).'</td>
                     <td align="right">'.number_format($acreedor,2).'</td>
                     </tr>";
                    ';
    }
 $content.='  
 </tbody> 
 </table>
 </div>
'; 
  
    $pdf->writeHTMLCell(0, 0, '', '', $content, 0, 1, 0, true, '', true);
    ob_end_clean();
    $pdf->output('Reporte.pdf', 'I');
}
?>