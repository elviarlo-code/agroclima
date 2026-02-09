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
        ->setCellValue('C2', 'TEMPERATURA (°C)')
        ->setCellValue('D2', 'HUMEDAD (%)')
        ->setCellValue('E2', 'V. DEL VIENTO (m/s)')
        ->setCellValue('F2', 'PRECIPITACION (m3)')
        ->setCellValue('G2', 'RAD.SOLAR (W/s)');

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(18);


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
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:G1');
$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A2:G2')->applyFromArray($styleArray02);



$y=3;
$xi=1;
foreach($data as $kx=>$fila){

    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$y, $xi)
            ->setCellValue('B'.$y, formatoCortoFechaHora($fila['fecha']))
            ->setCellValue('C'.$y, $fila['temperatura'])
            ->setCellValue('D'.$y, $fila['humedad_relativa'])
            ->setCellValue('E'.$y, $fila['velocidad_viento'])
            ->setCellValue('F'.$y, $fila['precipitacion'])
            ->setCellValue('G'.$y, $fila['radiacion_solar']);
    $y++;
    $xi++;
}
$objPHPExcel->setActiveSheetIndex(0)->getStyle('C3:G'.$y)->getNumberFormat()->setFormatCode('0.00');


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

$objPHPExcel->getActiveSheet()->getStyle('A3:G'.($y-1))->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->setTitle('ClimaDia');

// Crear el dataset para el gráfico
$labelTemp = [
    new PHPExcel_Chart_DataSeriesValues('String', "'ClimaDia'!\$C\$2", null, 1), // Etiqueta: Temperatura
];
$xAxisTemp = [
    new PHPExcel_Chart_DataSeriesValues('String', "'ClimaDia'!\$B\$3:\$B\$" . ($y - 1), null, $y - 3), // Valores del eje X (fechas)
];
$valorTemp = [
    new PHPExcel_Chart_DataSeriesValues('Number', "'ClimaDia'!\$C\$3:\$C\$" . ($y - 1), null, $y - 3), // Valores del eje Y (temperaturas)
];

// Configuración del gráfico
$series = new PHPExcel_Chart_DataSeries(
    PHPExcel_Chart_DataSeries::TYPE_LINECHART, // Tipo de gráfico
    PHPExcel_Chart_DataSeries::GROUPING_STANDARD, // Agrupación estándar
    range(0, count($valorTemp) - 1), // Rangos de series
    $labelTemp, // Etiquetas del dataset
    $xAxisTemp,  // Valores del eje X
    $valorTemp  // Valores del eje Y
);

$series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);

$layout = new PHPExcel_Chart_Layout();
$plotArea = new PHPExcel_Chart_PlotArea($layout, [$series]);
$legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_TOP, null, false); // Leyenda inferior
$title = new PHPExcel_Chart_Title('Temperatura',$layout);
$xAxisLabel = new PHPExcel_Chart_Title('Fecha'); // Etiqueta para el eje X
$yAxisLabel = new PHPExcel_Chart_Title('Temperatura (°C)'); // Etiqueta para el eje Y

// Crear el objeto del gráfico
$chart = new PHPExcel_Chart(
    'chart1',       // Nombre del gráfico
    $title,         // Título del gráfico
    $legend,        // Leyenda en la parte inferior
    $plotArea,      // Área del gráfico
    true,           // Proporcional
    0,              // Rotación
    $xAxisLabel,    // Etiqueta del eje X
    $yAxisLabel     // Etiqueta del eje Y
);

// Asignar el gráfico a una posición en la hoja
$chart->setTopLeftPosition('I3');  // Posición superior izquierda
$chart->setBottomRightPosition('R20'); // Posición inferior derecha
$chart->setTitle($title);

// Añadir el gráfico a la hoja activa
$objPHPExcel->getActiveSheet()->addChart($chart);


// Crear gráficos adicionales para Humedad, Velocidad del Viento, Precipitación y Radiación Solar
// HUMEDAD
$labelHumedad = [
    new PHPExcel_Chart_DataSeriesValues('String', "'ClimaDia'!\$D\$2", null, 1), // Etiqueta: Humedad
];
$xAxisHumedad = [
    new PHPExcel_Chart_DataSeriesValues('String', "'ClimaDia'!\$B\$3:\$B\$" . ($y - 1), null, $y - 3), // Valores del eje X (fechas)
];
$valorHumedad = [
    new PHPExcel_Chart_DataSeriesValues('Number', "'ClimaDia'!\$D\$3:\$D\$" . ($y - 1), null, $y - 3), // Valores del eje Y (humedad)
];

$seriesHumedad = new PHPExcel_Chart_DataSeries(
    PHPExcel_Chart_DataSeries::TYPE_LINECHART,
    PHPExcel_Chart_DataSeries::GROUPING_STANDARD,
    range(0, count($valorHumedad) - 1),
    $labelHumedad,
    $xAxisHumedad,
    $valorHumedad
);

$seriesHumedad->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);

$layout = new PHPExcel_Chart_Layout();
$plotAreaHumedad = new PHPExcel_Chart_PlotArea($layout, [$seriesHumedad]);
$titleHumedad = new PHPExcel_Chart_Title('Humedad Relativa',$layout);
$xAxisLabelHumedad = new PHPExcel_Chart_Title('Fecha');
$yAxisLabelHumedad = new PHPExcel_Chart_Title('Humedad (%)');

$chartHumedad = new PHPExcel_Chart(
    'chart2',
    $titleHumedad,
    null,
    $plotAreaHumedad,
    true,
    0,
    $xAxisLabelHumedad,
    $yAxisLabelHumedad
);

$chartHumedad->setTopLeftPosition('I22');  // Ajustar posición
$chartHumedad->setBottomRightPosition('R39');
$objPHPExcel->getActiveSheet()->addChart($chartHumedad);

// VELOCIDAD DEL VIENTO
$labelViento = [
    new PHPExcel_Chart_DataSeriesValues('String', "'ClimaDia'!\$E\$2", null, 1), // Etiqueta: Velocidad del Viento
];
$xAxisViento = [
    new PHPExcel_Chart_DataSeriesValues('String', "'ClimaDia'!\$B\$3:\$B\$" . ($y - 1), null, $y - 3), // Valores del eje X
];
$valorViento = [
    new PHPExcel_Chart_DataSeriesValues('Number', "'ClimaDia'!\$E\$3:\$E\$" . ($y - 1), null, $y - 3), // Valores del eje Y
];

$seriesViento = new PHPExcel_Chart_DataSeries(
    PHPExcel_Chart_DataSeries::TYPE_LINECHART,
    PHPExcel_Chart_DataSeries::GROUPING_STANDARD,
    range(0, count($valorViento) - 1),
    $labelViento,
    $xAxisViento,
    $valorViento
);

$plotAreaViento = new PHPExcel_Chart_PlotArea(null, [$seriesViento]);
$titleViento = new PHPExcel_Chart_Title('Velocidad del Viento');
$xAxisLabelViento = new PHPExcel_Chart_Title('Fecha');
$yAxisLabelViento = new PHPExcel_Chart_Title('Velocidad (m/s)');

$chartViento = new PHPExcel_Chart(
    'chart3',
    $titleViento,
    null,
    $plotAreaViento,
    true,
    0,
    $xAxisLabelViento,
    $yAxisLabelViento
);

$chartViento->setTopLeftPosition('I41');
$chartViento->setBottomRightPosition('R58');
$objPHPExcel->getActiveSheet()->addChart($chartViento);

// PRECIPITACIÓN
$labelPrecipitacion = [
    new PHPExcel_Chart_DataSeriesValues('String', "'ClimaDia'!\$F\$2", null, 1), // Etiqueta: Precipitación
];
$xAxisPrecipitacion = [
    new PHPExcel_Chart_DataSeriesValues('String', "'ClimaDia'!\$B\$3:\$B\$" . ($y - 1), null, $y - 3), // Valores del eje X
];
$valorPrecipitacion = [
    new PHPExcel_Chart_DataSeriesValues('Number', "'ClimaDia'!\$F\$3:\$F\$" . ($y - 1), null, $y - 3), // Valores del eje Y
];

$seriesPrecipitacion = new PHPExcel_Chart_DataSeries(
    PHPExcel_Chart_DataSeries::TYPE_BARCHART, // tipo de gráfico
    PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,
    range(0, count($valorPrecipitacion) - 1),
    $labelPrecipitacion,
    $xAxisPrecipitacion,
    $valorPrecipitacion
);

$plotAreaPrecipitacion = new PHPExcel_Chart_PlotArea(null, [$seriesPrecipitacion]);
$titlePrecipitacion = new PHPExcel_Chart_Title('Precipitación');
$xAxisLabelPrecipitacion = new PHPExcel_Chart_Title('Fecha');
$yAxisLabelPrecipitacion = new PHPExcel_Chart_Title('Precipitación (m3)');

$chartPrecipitacion = new PHPExcel_Chart(
    'chart4',
    $titlePrecipitacion,
    null,
    $plotAreaPrecipitacion,
    true,
    0,
    $xAxisLabelPrecipitacion,
    $yAxisLabelPrecipitacion
);

$chartPrecipitacion->setTopLeftPosition('I60');
$chartPrecipitacion->setBottomRightPosition('R77');
$objPHPExcel->getActiveSheet()->addChart($chartPrecipitacion);

// RADIACIÓN SOLAR
$labelSolar = [
    new PHPExcel_Chart_DataSeriesValues('String', "'ClimaDia'!\$G\$2", null, 1), // Etiqueta: Radiación Solar
];
$xAxisSolar = [
    new PHPExcel_Chart_DataSeriesValues('String', "'ClimaDia'!\$B\$3:\$B\$" . ($y - 1), null, $y - 3), // Valores del eje X
];
$valorSolar = [
    new PHPExcel_Chart_DataSeriesValues('Number', "'ClimaDia'!\$G\$3:\$G\$" . ($y - 1), null, $y - 3), // Valores del eje Y
];

$seriesSolar = new PHPExcel_Chart_DataSeries(
    PHPExcel_Chart_DataSeries::TYPE_BARCHART, // tipo de gráfico
    PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,
    range(0, count($valorSolar) - 1),
    $labelSolar,
    $xAxisSolar,
    $valorSolar
);

$plotAreaSolar = new PHPExcel_Chart_PlotArea(null, [$seriesSolar]);
$titleSolar = new PHPExcel_Chart_Title('Radiación Solar');
$xAxisLabelSolar = new PHPExcel_Chart_Title('Fecha');
$yAxisLabelSolar = new PHPExcel_Chart_Title('Radiación Solar (W/s)');

$chartSolar = new PHPExcel_Chart(
    'chart5',
    $titleSolar,
    null,
    $plotAreaSolar,
    true,
    0,
    $xAxisLabelSolar,
    $yAxisLabelSolar
);

$chartSolar->setTopLeftPosition('I79');
$chartSolar->setBottomRightPosition('R96');
$objPHPExcel->getActiveSheet()->addChart($chartSolar);

// Guardar y exportar
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename=Reporte_Clima_Minuto.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->setIncludeCharts(TRUE);
$objWriter->save('php://output');
exit;
?>