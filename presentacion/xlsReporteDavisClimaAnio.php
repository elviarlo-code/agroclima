<?php 
require_once('../phpExcel/PHPExcel.php');
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

$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()
		->setCreator("RITEC")
		->setLastModifiedBy("RITEC")
		->setTitle("Exportar Excel con PHP")
		->setSubject("Documentos RITEC")
		->setDescription("Documento generado con PHPExcel")
		->setKeywords("Datos Climaticos")
		->setCategory("reportes");


$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A1", "DISPOSITIVO: ".$rowDispositivo['nombre']."\nDATOS CLIMATICOS \n".$fechas);

$objPHPExcel->setActiveSheetIndex(0)->getRowDimension(1)->setRowHeight(55); 
$objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->setWrapText(true);


$objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A2', '#')
        ->setCellValue('B2', 'MES')
        ->setCellValue('C2', 'TEMP. MIN (°C)')
        ->setCellValue('D2', 'TEMP. MAX (°C)')
        ->setCellValue('E2', 'HUMD. MIN (%)')
        ->setCellValue('F2', 'HUMD. MAX (%)')
        ->setCellValue('G2', 'PRECIPITACION (m3)')
        ->setCellValue('H2', 'RAD.SOLAR (W/s)');

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);


$styleArray = array('font' => array( 'bold' => true, ),
    				'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,),
    				'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,),),
					);
$styleArray02 = array(
    'font' => array(
        'bold' => true,
        'size'  => 10,
        'color' => array(
            'rgb' => 'FFFFFF'
        )
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    ),
    'fill' => array(
        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
        'rotation'   => 90,
        'startcolor' => array(
            'rgb' => '458d94'
        )
      ),
      'borders' => array(
        'allborders' => array(
        'style' => PHPExcel_Style_Border::BORDER_THIN,
        ),
    ),
);
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:H1');
$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A2:H2')->applyFromArray($styleArray02);



$y=3;
$xi=1;
foreach($data as $kx=>$fila){

    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$y, $xi)
            ->setCellValue('B'.$y, $meses[$fila['mes']])
            ->setCellValue('C'.$y, $fila['tempmin'])
            ->setCellValue('D'.$y, $fila['tempmax'])
            ->setCellValue('E'.$y, $fila['hummin'])
            ->setCellValue('F'.$y, $fila['hummax'])
            ->setCellValue('G'.$y, $fila['precipitacion'])
            ->setCellValue('H'.$y, $fila['radiacion_solar']);
    $y++;
    $xi++;
}
$objPHPExcel->setActiveSheetIndex(0)->getStyle('C3:H'.$y)->getNumberFormat()->setFormatCode('0.00');


$styleArray = array(
    'font' => array(
        'size'  => 9
    ),
    'borders' => array(
        'allborders' => array(
        'style' => PHPExcel_Style_Border::BORDER_THIN,
        )
    )
);

$objPHPExcel->getActiveSheet()->getStyle('A3:H'.($y-1))->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->setTitle('ClimaDia');


// Guardar y exportar
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename=Reporte_Clima_Anual.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->setIncludeCharts(TRUE);
$objWriter->save('php://output');
exit;
?>