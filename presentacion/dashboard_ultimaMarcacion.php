<?php
require_once('../logica/clsCase.php');
require_once('../logica/clsReporte.php');
$objRep = new clsReporte();
$objCase = new clsCase();

$sufijo = "reporteclima";
$nivel = 1;
if(isset($_GET['nivel'])){
	$nivel = $_GET['nivel'];
}

$idopcion = (isset($_GET['idopcion']))? $_GET['idopcion']:0;
$iddispositivo = 33;

$rowDispositivo = $objCase->getRowTableFiltroSimple('dispositivo','iddispositivo',$iddispositivo);
$ultimoregistro = $objRep->consultarDatosSensorUltimoEnvio($iddispositivo);
$ultimoregistro = $ultimoregistro->fetch(PDO::FETCH_NAMED);

$bodyContenidoUltimo = '<div class="d-flex align-items-center flex-wrap">
    <!--begin: Item-->
    <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
        <div class="d-flex flex-column text-dark-75">
            <span class="font-weight-bolder font-size-sm text-success">'.$ultimoregistro['dia'].'</span>
            <span class="font-weight-bolder font-size-h5 text-success">'.$ultimoregistro['hora'].'</span>
        </div>
    </div>
    <!--end: Item-->
    <!--begin: Item-->
    <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
        <span class="mr-4">
            <i class="icon-xl fas fa-temperature-low" style="color: #2caffe;"></i>
        </span>
        <div class="d-flex flex-column text-dark-75">
            <span class="font-weight-bolder font-size-sm">Temperatura</span>
            <span class="font-weight-bolder font-size-h5">
            <span class="text-dark-50 font-weight-bold"></span>'.$ultimoregistro['temperatura'].'°C</span>
        </div>
    </div>
    <!--end: Item-->
    <!--begin: Item-->
    <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
        <span class="mr-4">
            <i class="icon-xl fas fa-percent" style="color: #fe6a35"></i>
        </span>
        <div class="d-flex flex-column text-dark-75">
            <span class="font-weight-bolder font-size-sm">Humedad</span>
            <span class="font-weight-bolder font-size-h5">
            <span class="text-dark-50 font-weight-bold"></span>'.$ultimoregistro['humedad_relativa'].'%</span>
        </div>
    </div>
    <!--end: Item-->
    <!--begin: Item-->
    <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
        <span class="mr-4">
            <i class="icon-xl fas fa-wind" style="color: #544fc5"></i>
        </span>
        <div class="d-flex flex-column text-dark-75">
            <span class="font-weight-bolder font-size-sm">V. del Viento</span>
            <span class="font-weight-bolder font-size-h5">
            <span class="text-dark-50 font-weight-bold"></span>'.$ultimoregistro['velocidad_viento'].'m/s</span>
        </div>
    </div>
    <!--end: Item-->
    <!--begin: Item-->
    <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
        <span class="mr-4">
            <i class="icon-xl fas fa-bullseye" style="color: #544fc5"></i>
        </span>
        <div class="d-flex flex-column text-dark-75">
            <span class="font-weight-bolder font-size-sm">Dirección V.</span>
            <span class="font-weight-bolder font-size-h5">
            <span class="text-dark-50 font-weight-bold"></span>'.$ultimoregistro['direccion_viento'].' '.$ultimoregistro['puntos_cardinales'].'</span>
        </div>
    </div>
    <!--end: Item-->
    <!--begin: Item-->
    <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
        <span class="mr-4">
            <i class="icon-xl fas fa-cloud-rain" style="color: #ee81ff"></i>
        </span>
        <div class="d-flex flex-column text-dark-75">
            <span class="font-weight-bolder font-size-sm">Precipitación</span>
            <span class="font-weight-bolder font-size-h5">
            <span class="text-dark-50 font-weight-bold"></span>'.$ultimoregistro['precipitacion'].'m<sup>3</sup></span>
        </div>
    </div>
    <!--end: Item-->
    <!--begin: Item-->
    <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
        <span class="mr-4">
            <i class="icon-xl fas fa-sun" style="color: #ffd700;"></i>
        </span>
        <div class="d-flex flex-column text-dark-75">
            <span class="font-weight-bolder font-size-sm">Rad. Solar</span>
            <span class="font-weight-bolder font-size-h5">
            <span class="text-dark-50 font-weight-bold"></span>'.$ultimoregistro['radiacion_solar'].'W/s</span>
        </div>
    </div>
    <!--end: Item-->
</div>';

// Pasar el contenido a JavaScript
// $bodyContenidoUltimo = addslashes($bodyContenidoUltimo); // Escapar comillas para JavaScript
?>