<?php
define('FPDF_FONTPATH','font/'); 
include('../fpdf/fpdf_personalizado.php');
require_once('../logica/clsCase.php');
require_once('../logica/clsCompartido.php');
require_once('../logica/clsCultivo.php');

$objCul = new clsCultivo();
$objCase = new clsCase();

if(isset($_GET["idcultivo"])){
	$idcultivo = $_GET["idcultivo"];
	$para_enviar_email = false;
}

if(isset($_POST["idcultivo"])){
	$idcultivo = $_POST["idcultivo"];
	$para_enviar_email = true;
}

$sizeText=7;
$dataCultivo = $objCase->getRowTableById('cultivo',$idcultivo,'idcultivo');
$data=$objCul->consultarFenologia($idcultivo, '');

$pdf=new FPDF();
$pdf->AddPage('P','A4');
$pdf->SetFont('Arial','BI',16);
$pdf->SetFillColor(220,220,220);

if($dataCultivo['imagen']!=''){
	if(file_exists('../files/imagenes/cultivos/'.$dataCultivo['imagen'])){
		$pdf->Image('../files/imagenes/cultivos/'.$dataCultivo['imagen'],10,32,40,40);
	}
}else{
	if(file_exists('../assets/media/users/hoja1.jpg')){
		$pdf->Image('../assets/media/users/hoja1.jpg',10,32,40,40);
	}
}

$alto=5;
$pdf->Ln(10);
$pdf->Cell(190,6,utf8_decode("INFORMACIÓN GENERAL DEL CULTIVO"),0,1,'C');
$pdf->Ln(10);
$pdf->SetFont('Arial','B',8);

$pdf->Cell(50);
$pdf->Cell(140,5,"DATOS DEL CULTIVO",1,1,'L');

$pdf->SetFont('Arial','B',8);

$pdf->Cell(50);
$pdf->Cell(25,5,"NOMBRE:",'L',0,'L');
$pdf->SetFont('Arial','',8);
$pdf->Cell(115,5,$dataCultivo['nombre'],'R',1,'L');

$pdf->Cell(50);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(25,5,"VARIEDAD:",'L',0,'L');
$pdf->SetFont('Arial','',8);
$pdf->Cell(115,5,$dataCultivo['variedad'],'R',1,'L');

$pdf->Cell(50);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(25,5,"ALTURA:",'L',0,'L');
$pdf->SetFont('Arial','',8);
$pdf->Cell(115,5,$dataCultivo['altura'],'R',1,'L');

$pdf->Cell(50);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(25,5,utf8_decode("RAÍZ MÁXIMA:"),'L',0,'L');
$pdf->SetFont('Arial','',8);
$pdf->Cell(115,5,$dataCultivo['raiz_maxima'],'R',1,'L');

$pdf->Cell(50);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(25,5,utf8_decode("RAÍZ MÍNIMA:"),'LB',0,'L');
$pdf->SetFont('Arial','',8);
$pdf->Cell(115,5,$dataCultivo['raiz_minima'],'RB',1,'L');
$pdf->Ln(7);

$pdf->SetFont('Arial','B',8);
$pdf->Cell(10,$alto,"#",1,0,'C',1);
$pdf->Cell(125,$alto,utf8_decode("FENOLOGÍA"),1,0,'C',1);
$pdf->Cell(30,$alto,utf8_decode("DURACIÓN (DÍAS)"),1,0,'C',1);
$pdf->Cell(25,$alto,utf8_decode("KC"),1,0,'C',1);
$i=1;
$pdf->SetFont('Arial','',$sizeText);
$pdf->Ln();
$pag_anterior=$pdf->PageNo();

while($fila=$data->fetch(PDO::FETCH_NAMED)){
	
	$pdf->Cell(8,$alto,'',0,0,'C');

	if($pag_anterior!=$pdf->PageNo()){
		$pdf->SetFont('Arial','B',8);
		$pag_anterior=$pdf->PageNo();
		$pdf->Ln(12);
		$pdf->Cell(10,$alto,"#",1,0,'C',1);
		$pdf->Cell(125,$alto,utf8_decode("FENOLOGÍA"),1,0,'C',1);
		$pdf->Cell(30,$alto,utf8_decode("DURACIÓN (DÍAS)"),1,0,'C',1);
		$pdf->Cell(25,$alto,utf8_decode("KC"),1,0,'C',1);
		$pdf->SetFont('Arial','',$sizeText);
		$pdf->Ln();
	}else{
		$pdf->Cell(-8,$alto,'',0,0,'C');
	}	

		$pdf->Cell(10,$alto,$i,1,0,'C',0);
		$pdf->Cell(125,$alto,utf8_decode($fila['nombre']),1,0,'L',0);
		$pdf->Cell(30,$alto, $fila["duracion"],1,0,'C',0);
		$pdf->Cell(25,$alto,floatval($fila['valorkc']),1,0,'C',0);
		$pdf->Ln();
$i++;
}

$posicionY = $pdf->GetY() + 5;

if(file_exists('../files/imagenes/grafico_fenologia'.$idcultivo.'.png')){
	$pdf->Image('../files/imagenes/grafico_fenologia'.$idcultivo.'.png',10,$posicionY,190,66);
}


if(!$para_enviar_email){
	$pdf->SetAutoPageBreak('auto',2); 
	$pdf->SetDisplayMode(75);
	$pdf->Output();
}
?>