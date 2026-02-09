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
$frecuencia = $_GET['frecuencia'];

$rowDispositivo = $objCase->getRowTableFiltroSimple('dispositivo','iddispositivo',$iddispositivo);

$data = $objRep->consultarDatosSensorPorDiaDavis($iddispositivo, $desde, $hasta, $frecuencia);
$data = $data->fetchAll(PDO::FETCH_NAMED);

$fechas = "";
if($_GET['desde']!=''){
    $fechas .= "DESDE ".formatoCortoFecha($_GET['desde']);
}

if($_GET['hasta']!=''){
    $fechas .= " HASTA ".formatoCortoFecha($_GET['hasta']);
}else{
    $fechas .= " HASTA ".date('d/m/Y');
}


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
        ->setCellValue('B2', 'FECHA')
        ->setCellValue('C2', 'TEMP. MIN (°C)')
        ->setCellValue('D2', 'TEMP. PRO (°C)')
        ->setCellValue('E2', 'TEMP. MAX (°C)')
        ->setCellValue('F2', 'HUMD. MIN (%)')
        ->setCellValue('G2', 'HUMD. PRO (%)')
        ->setCellValue('H2', 'HUMD. MAX (%)')
        ->setCellValue('I2', 'V.V. MIN (m/s)')
        ->setCellValue('J2', 'V.V. PRO (m/s)')
        ->setCellValue('K2', 'V.V. MAX (m/s)')
        ->setCellValue('L2', 'PRECIPITACION (m3)')
        ->setCellValue('M2', 'RAD.SOLAR (W/s)');

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(18);


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
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:M1');
$objPHPExcel->getActiveSheet()->getStyle('A1:M1')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A2:M2')->applyFromArray($styleArray02);



$y=3;
$xi=1;
foreach($data as $kx=>$fila){

    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$y, $xi)
            ->setCellValue('B'.$y, $fila['fecharepor'])
            ->setCellValue('C'.$y, $fila['tempmin'])
            ->setCellValue('D'.$y, $fila['temppro'])
            ->setCellValue('E'.$y, $fila['tempmax'])
            ->setCellValue('F'.$y, $fila['hummin'])
            ->setCellValue('G'.$y, $fila['humpro'])
            ->setCellValue('H'.$y, $fila['hummax'])
            ->setCellValue('I'.$y, $fila['vvemin'])
            ->setCellValue('J'.$y, $fila['vvepro'])
            ->setCellValue('K'.$y, $fila['vvemax'])
            ->setCellValue('L'.$y, $fila['precipitacion'])
            ->setCellValue('M'.$y, $fila['radiacion_solar']);
    $y++;
    $xi++;
}
$objPHPExcel->setActiveSheetIndex(0)->getStyle('C3:M'.$y)->getNumberFormat()->setFormatCode('0.00');


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

$objPHPExcel->getActiveSheet()->getStyle('A3:M'.($y-1))->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->setTitle('ClimaDia');


// Guardar y exportar
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename=Reporte_Clima_Diario.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->setIncludeCharts(TRUE);
$objWriter->save('php://output');
exit;
?>