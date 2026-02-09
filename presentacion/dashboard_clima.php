<?php
require_once('../logica/clsCase.php');
require_once('../logica/clsReporte.php');
$objRep = new clsReporte();
$objCase = new clsCase();

$sufijo = "dashTemp";
$nivel = 1;
if(isset($_GET['nivel'])){
	$nivel = $_GET['nivel'];
}

$idopcion = (isset($_GET['idopcion']))? $_GET['idopcion']:0;
$iddispositivo = 33;
$desde = '2024-12-01';
$hasta = '2024-12-01';
$frecuencia = "M";

$rowDispositivo = $objCase->getRowTableFiltroSimple('dispositivo','iddispositivo',$iddispositivo);

$data = $objRep->consultarDatosSensorPorDia($iddispositivo, $desde, $hasta, $frecuencia);
$data = $data->fetchAll(PDO::FETCH_NAMED);

$serieTemPro = array();
$serieHumPro = array();
$serieViePro = array();
$seriePrecipitacion = array();
$serieRadiacion = array();

$formatofecha = "%d/%m/%Y %H:%M";

// Inicializar variables
$radiacionDiaria = 0;
$prevTimestamp = null;
foreach($data as $k=>$v){
    $fecha = new DateTime($v['fecha'], new DateTimeZone('UTC'));
    $timestamp = $fecha->getTimestamp()*1000; // Convertir fecha a timestamp en milisegundos

    $serieTemPro[] = [$timestamp, floatval($v['temperatura'])]; // Formato [timestamp, valor]
    $serieHumPro[] = [$timestamp, floatval($v['humedad_relativa'])]; // Formato [timestamp, valor] 
    $serieViePro[] = [$timestamp, floatval($v['velocidad_viento'])]; // Formato [timestamp, valor] 
    $seriePrecipitacion[] = [$timestamp, floatval($v['precipitacion'])]; // Formato [timestamp, valor] 
    $serieRadiacion[] = [$timestamp, floatval($v['radiacion_solar'])]; // Formato [timestamp, valor] 

    //Calculo de radiacion Diaria
    $currentTimestamp = strtotime($v["fecha"]); // Convertir a timestamp
    $radiacion = $v["radiacion_solar"];

    if ($prevTimestamp !== null) {
        // Calcular diferencia de tiempo en horas
        $timeDiff = ($currentTimestamp - $prevTimestamp) / 3600;
        // Calcular contribución de radiación
        $radiacionDiaria += $radiacion * $timeDiff;
    }

    // Actualizar el timestamp anterior
    $prevTimestamp = $currentTimestamp;
}

?>