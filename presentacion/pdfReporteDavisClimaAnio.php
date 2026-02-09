<?php
define('FPDF_FONTPATH','font/'); 
require_once('../fpdf/fpdf_personalizado.php');
include_once('../logica/clsCompartido.php');
require_once("../logica/clsCase.php");
require_once('../logica/clsReporte.php');

$objRep = new clsReporte();
$objCase = new clsCase();

$idopcion = (isset($_GET['idopcion']))? $_GET['idopcion']:0;
$iddispositivo = $_GET['iddispositivo'];
$listar = $_GET['listar'];
$anio = $_GET['anio'];
$mes = $_GET['mes'];
$desde = $_GET['desde'];
$hasta = $_GET['hasta'];
$sufijo = "reporteclima";

$rowDispositivo = $objCase->getRowTableFiltroSimple('dispositivo','iddispositivo',$iddispositivo);

$data = $objRep->consultarDatosSensorPorAnioDavis($iddispositivo, $anio);
$data = $data->fetchAll(PDO::FETCH_NAMED);

$meses = array(
    '1' => "ENERO",
    '2' => "FEBRERO",
    '3' => "MARZO",
    '4' => "ABRIL",
    '5' => "MAYO",
    '6' => "JUNIO",
    '7' => "JULIO",
    '8' => "AGOSTO",
    '9' => "SEPTIEMBRE",
    '10' => "OCTUBRE",
    '11' => "NOVIEMBRE",
    '12' => "DICIEMBRE"
);

$fechas = $anio;

// Función para agregar una imagen al PDF con control de espacio
function agregarImagenConControlEspacio($pdf, $rutaImagen, $x, &$y, $ancho, $alto, $margenInferior = 5) {
    $alturaPagina = 297; // Altura de una página A4 en mm
    $alturaDisponible = $alturaPagina - $y - $margenInferior; // Altura disponible restante

    // Verificar si hay espacio suficiente para la imagen
    if ($alturaDisponible < $alto) {
        $pdf->AddPage(); // Agregar nueva página si no hay espacio
        $y = 30; // Reiniciar posición Y en la nueva página
    }

    // Agregar la imagen
    $pdf->Image($rutaImagen, $x, $y, $ancho, $alto);
    $y += $alto + $margenInferior; // Ajustar posición Y para la siguiente imagen
}

// Configuración inicial
$altoImagen = 66;
$margenInferior = 15;

$pdf=new FPDF();
$pdf->AddPage('P','A4');
$pdf->SetFont('Arial','B',14);
$pdf->SetFillColor(220,220,220);
$alto=5;
$pdf->Ln(8);
$pdf->Cell(190,6,utf8_decode("DISPOSITIVO: ".$rowDispositivo['nombre']),0,1,'C');
$pdf->SetFont('Arial','B',10);
$pdf->Cell(190,6,"DATOS CLIMATICOS ".$fechas,0,1,'C');

// Espacio inicial para imágenes
$posicionY = $pdf->GetY()+5;

// Agregar imágenes dinámicamente
$imagenes = [
    "../files/imagenes/grafico_Temperatura_{$sufijo}{$iddispositivo}.png",
    "../files/imagenes/grafico_Humedad_{$sufijo}{$iddispositivo}.png",
    "../files/imagenes/grafico_Precipitacion_{$sufijo}{$iddispositivo}.png",
    "../files/imagenes/grafico_Radiacion_{$sufijo}{$iddispositivo}.png"
];

foreach($imagenes as $rutaImagen){
    if(file_exists($rutaImagen)){
        agregarImagenConControlEspacio($pdf, $rutaImagen, 10, $posicionY, 190, $altoImagen, $margenInferior);
    }else{
        // Registrar imagen faltante (opcional)
        error_log("Imagen no encontrada: $rutaImagen");
    }
}

$pdf->AddPage('P','A4');
$pdf->Ln();
$pdf->SetFont('Arial','B',8);
$pdf->Cell(30,$alto,"MES",1,0,'C',1);
$pdf->Cell(25,$alto,utf8_decode("TEMP. MIN"),1,0,'C',1);
$pdf->Cell(25,$alto,utf8_decode("TEMP. MAX"),1,0,'C',1);
$pdf->Cell(25,$alto,utf8_decode("HUMD. MIN"),1,0,'C',1);
$pdf->Cell(25,$alto,utf8_decode("HUMD. MAX"),1,0,'C',1);
$pdf->Cell(30,$alto,utf8_decode("PRECI.(m3)"),1,0,'C',1);
$pdf->Cell(30,$alto,utf8_decode("RAD. SOLAR(W/s)"),1,0,'C',1);
$i=1;
$pdf->SetFont('Arial','',8);
$pdf->Ln();
$pag_anterior=$pdf->PageNo();
$pdf->SetFillColor(255,255,255);
foreach($data as $k=>$fila){
	$pdf->Cell(8,$alto,'',0,0,'C');

	if($pag_anterior!=$pdf->PageNo()){
        $pdf->SetFont('Arial','B',8);
        $pag_anterior=$pdf->PageNo();
        $pdf->SetFillColor(220,220,220);
        $pdf->Ln(12);
        $pdf->Cell(30,$alto,"MES",1,0,'C',1);
        $pdf->Cell(25,$alto,utf8_decode("TEMP. MIN"),1,0,'C',1);
        $pdf->Cell(25,$alto,utf8_decode("TEMP. MAX"),1,0,'C',1);
        $pdf->Cell(25,$alto,utf8_decode("HUMD. MIN"),1,0,'C',1);
        $pdf->Cell(25,$alto,utf8_decode("HUMD. MAX"),1,0,'C',1);
        $pdf->Cell(30,$alto,utf8_decode("PRECI.(m3)"),1,0,'C',1);
        $pdf->Cell(30,$alto,utf8_decode("RAD. SOLAR(W/s)"),1,0,'C',1);
        $pdf->SetFont('Arial','',8);
        $pdf->Ln();
        $pdf->SetFillColor(255,255,255);
    }else{
		$pdf->Cell(-8,$alto,'',0,0,'C');
    }

    $pdf->Cell(30,$alto,$meses[$fila['mes']],1,0);
    $pdf->Cell(25,$alto,$fila['tempmin'],1,0,'R',1);
    $pdf->Cell(25,$alto,$fila['tempmax'],1,0,'R',1);
    $pdf->Cell(25,$alto,$fila['hummin'],1,0,'R',1);
    $pdf->Cell(25,$alto,$fila['hummax'],1,0,'R',1);
    $pdf->Cell(30,$alto,$fila['precipitacion'],1,0,'R',1);
    $pdf->Cell(30,$alto,floatval($fila['radiacion_solar']),1,0,'R',1);
    //$pdf->Cell(270,0,'',1,'L',0);
    $pdf->Ln();
$i++;
}

$pdf->SetAutoPageBreak('auto',2); 
$pdf->SetDisplayMode(75);
$pdf->Output('I','Reporte_Clima_Anual.pdf');
?>