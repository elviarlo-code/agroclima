<?php 
require_once("../logica/clsReporte.php");
require_once("../logica/clsCompartido.php");
require_once("../logica/clsCase.php");
$objCase = new clsCase();
$objReporte = new clsReporte();

$sufijo = 'reporteclima';
$iddispositivo = 0;
if(isset($_GET['iddispositivo'])){
    $iddispositivo = $_GET['iddispositivo'];
}

$idopcion=0;
if(isset($_GET['idoptx'])){
    $idopcion=$_GET['idoptx'];
}

$nivel = 1;
if(isset($_GET['nivel'])){
    $nivel = $_GET['nivel'];
}

$ver = 0;
if(isset($_GET['ver'])){
    $ver = $_GET['ver'];
}

//consultar años para el filtro ordenarlos de manera descentente
$arrayAnios = generarArrayAnios('2023');
$mesActual = date('m');
//array meses
$arrayMeses = array(
    '01' => "ENERO",
    '02' => "FEBRERO",
    '03' => "MARZO",
    '04' => "ABRIL",
    '05' => "MAYO",
    '06' => "JUNIO",
    '07' => "JULIO",
    '08' => "AGOSTO",
    '09' => "SEPTIEMBRE",
    '10' => "OCTUBRE",
    '11' => "NOVIEMBRE",
    '12' => "DICIEMBRE"
);

$listaDispositivo = $objCase->getListTableFiltroSimple('dispositivo', 'estado', 'N', 'tipo', 'DAVIS01');

?>
<style>
    .select2-selection__rendered{
        padding-top: 1px !important;
        padding-bottom: 1px !important;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card card-custom gutter-b">
            <?php if($ver==0){ ?>
            <div class="card-header" style="min-height: 50px">
                <div class="card-title">
                    <span class="card-icon">
                        <i class="flaticon-map-location text-primary"></i>
                    </span>
                    <h3 class="card-label">Reporte Clima
                    <small>Datos climáticos</small></h3>
                </div>
            </div>
            <?php } ?>
            <div class="card-body <?php if($ver==1){ ?> p-0 <?php } ?>">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text font-weight-bolder">Dispositivo</span>
                            </div>
                            <select class="form-control" name="busquedaDispositivo<?= $sufijo ?>" id="busquedaDispositivo<?= $sufijo ?>">
                                <?php while($fila = $listaDispositivo->fetch(PDO::FETCH_NAMED)){ ?>
                                    <option value="<?= $fila['iddispositivo'] ?>" tipo="<?= $fila['tipo'] ?>"><?= $fila['nombre'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text font-weight-bolder">Listar</span>
                            </div>
                            <select class="form-control" name="busquedaListar<?= $sufijo ?>" id="busquedaListar<?= $sufijo ?>" onchange="activarFiltros<?= $sufijo ?>()">
                                <option value="D" selected>Por Día</option>
                                <option value="M">Por Mes</option>
                                <option value="A">Por Año</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4" id="contenedorAnio">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text font-weight-bolder">Año</span>
                            </div>
                            <select class="form-control" name="busquedaAnio<?= $sufijo ?>" id="busquedaAnio<?= $sufijo ?>" >
                                <option value="T">- Todos -</option>
                                <?php foreach ($arrayAnios as $anio) { ?>
                                    <option value="<?=$anio?>"><?=$anio?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4" id="contenedorMes">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text font-weight-bolder">Mes</span>
                            </div>
                            <select class="form-control" name="busquedaMes<?= $sufijo ?>" id="busquedaMes<?= $sufijo ?>">
                                <?php foreach ($arrayMeses as $nm => $mes) { ?>
                                    <option value="<?= $nm ?>"><?=$mes?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4" id="contenedorFecha">
                        <div class="input-group input-group-sm">
                            <input type="date" name="busquedaDesde<?= $sufijo; ?>" id="busquedaDesde<?= $sufijo; ?>" class="form-control" onKeyUp="if(event.keyCode=='13'){ verPagina<?php echo $sufijo;?>(1); }" value="<?= date('Y-m-d') ?>" autocomplete="off">
                            <div class="input-group-prepend">
                                <span class="input-group-text font-weight-bolder">a</span>
                            </div>
                            <input type="date" name="busquedaHasta<?= $sufijo; ?>" id="busquedaHasta<?= $sufijo; ?>" class="form-control" onKeyUp="if(event.keyCode=='13'){ verPagina<?php echo $sufijo;?>(1); }" value="<?= date('Y-m-d') ?>" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-4" id="contenedorFrecuencia">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text font-weight-bolder">Frecuencia de Datos</span>
                            </div>
                            <select class="form-control" name="busquedaFrecuencia<?= $sufijo ?>" id="busquedaFrecuencia<?= $sufijo ?>">
                                <option value="D">Día</option>
                                <option value="H">Hora</option>
                                <option value="M" selected>Minuto</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <button type="button" class="btn btn-light-primary btn-sm" id="btnBuscar" onclick="verPagina<?php echo $sufijo;?>(1)">
                            <i class="flaticon2-magnifier-tool"></i> Buscar
                        </button>
                        <button type="button" class="btn btn-light-warning btn-sm" id="btnBuscar" onclick="verPagina<?php echo $sufijo;?>(1,1,1)">
                            <i class="flaticon-calendar-3"></i> Hoy
                        </button>
                        <div class="dropdown dropdown-inline">
                            <a href="#" class="btn btn-light-info btn-sm font-weight-bolder dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="flaticon-multimedia-4"></i> Imagen</a>
                            <div class="dropdown-menu dropdown-menu-md dropdown-menu-right" style="">
                                <!--begin::Navigation-->
                                <ul class="navi navi-hover">
                                    <li class="navi-item">
                                        <a href="#" class="navi-link" onclick="downloadPNG<?= $sufijo ?>('Temperatura')">
                                            <span class="navi-icon">
                                                <i class="flaticon-graph"></i>
                                            </span>
                                            <span class="navi-text">Temperatura</span>
                                        </a>
                                    </li>
                                    <li class="navi-item">
                                        <a href="#" class="navi-link" onclick="downloadPNG<?= $sufijo ?>('Humedad')">
                                            <span class="navi-icon">
                                                <i class="flaticon-graph"></i>
                                            </span>
                                            <span class="navi-text">Humedad Relativo</span>
                                        </a>
                                    </li>
                                    <li class="navi-item" listar="D">
                                        <a href="#" class="navi-link" onclick="downloadPNG<?= $sufijo ?>('Viento')">
                                            <span class="navi-icon">
                                                <i class="flaticon-graph"></i>
                                            </span>
                                            <span class="navi-text">Velocidad del Viento</span>
                                        </a>
                                    </li>
                                    <li class="navi-item" listar="M">
                                        <a href="#" class="navi-link" onclick="downloadPNG<?= $sufijo ?>('Viento')">
                                            <span class="navi-icon">
                                                <i class="flaticon-graph"></i>
                                            </span>
                                            <span class="navi-text">Velocidad del Viento</span>
                                        </a>
                                    </li>
                                    <li class="navi-item">
                                        <a href="#" class="navi-link" onclick="downloadPNG<?= $sufijo ?>('Precipitacion')">
                                            <span class="navi-icon">
                                                <i class="flaticon-graph"></i>
                                            </span>
                                            <span class="navi-text">Precipitación</span>
                                        </a>
                                    </li>
                                    <li class="navi-item">
                                        <a href="#" class="navi-link" onclick="downloadPNG<?= $sufijo ?>('Radiacion')">
                                            <span class="navi-icon">
                                                <i class="flaticon-graph"></i>
                                            </span>
                                            <span class="navi-text">Radiación Solar</span>
                                        </a>
                                    </li>
                                    <li class="navi-item">
                                        <a href="#" class="navi-link" onclick="exportAllCharts()">
                                            <span class="navi-icon">
                                                <i class="flaticon-graph"></i>
                                            </span>
                                            <span class="navi-text">Todos los graficos</span>
                                        </a>
                                    </li>
                                </ul>
                                <!--end::Navigation-->
                            </div>
                        </div>
                        <div class="dropdown dropdown-inline">
                            <a href="#" class="btn btn-light-info btn-sm font-weight-bolder dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="flaticon-logout"></i> Exportar</a>
                            <div class="dropdown-menu dropdown-menu-md dropdown-menu-right" style="">
                                <!--begin::Navigation-->
                                <ul class="navi navi-hover">
                                    <li class="navi-item">
                                        <a href="#" class="navi-link" onclick="ExportarDataExcel<?= $sufijo ?>()">
                                            <span class="navi-icon">
                                                <i class="far fa-file-excel"></i>
                                            </span>
                                            <span class="navi-text">Exportar Excel</span>
                                        </a>
                                    </li>
                                    <li class="navi-item">
                                        <a href="#" class="navi-link" onclick="saveAllChartsToServer()">
                                            <span class="navi-icon">
                                                <i class="far fa-file-pdf"></i>
                                            </span>
                                            <span class="navi-text">Exportar PDF</span>
                                        </a>
                                    </li>
                                </ul>
                                <!--end::Navigation-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-custom <?php if($ver==0){ ?> gutter-b <?php } ?>">
            <div class="card-body <?php if($ver==1){ ?> p-0 <?php } ?> min-h-xl-400px" id="divLista<?php echo $sufijo;?>">
            </div>
        </div>
    </div>
</div>
<script>
<?php if($iddispositivo>0){ ?>
    $('#busquedaDispositivo<?= $sufijo ?>').val('<?= $iddispositivo ?>');
<?php } ?>
    
// $('#busquedaAnio<?= $sufijo ?>').select2({
//     placeholder: "Seleccionar Año",
// });
$('#busquedaAnio<?= $sufijo ?>').val('<?= date('Y') ?>').trigger('change');
$('#busquedaMes<?= $sufijo ?>').val('<?= $mesActual ?>');


function verPagina<?php echo $sufijo;?>(pagina, loading=1, actual=0){

    $('#nroPagina<?php echo $sufijo; ?>').val(pagina);

    // Crear una instancia del objeto Date
    fechaActual = new Date();

    // Obtener los componentes de la fecha
    anioActual = fechaActual.getFullYear();
    mesActual = (fechaActual.getMonth() + 1).toString().padStart(2, '0'); // Mes (0-11, por eso se suma 1)
    diaActual = fechaActual.getDate().toString().padStart(2, '0'); // Día del mes

    // Formatear la fecha en el formato YYYY-MM-DD
    fechaFormateada = `${anioActual}-${mesActual}-${diaActual}`;

    if(actual==1){
        $('#busquedaListar<?= $sufijo; ?>').val('D');
        $('#busquedaDesde<?= $sufijo; ?>').val(fechaFormateada);
        $('#busquedaHasta<?= $sufijo; ?>').val(fechaFormateada);
        $('#busquedaFrecuencia<?= $sufijo ?>').val('M');
        activarFiltros<?= $sufijo ?>();
    }
    
    let iddispositivo = $('#busquedaDispositivo<?= $sufijo; ?>').val();
    let listar = $('#busquedaListar<?= $sufijo; ?>').val();
    let anio = $('#busquedaAnio<?= $sufijo ?>').val();
    let mes = $('#busquedaMes<?= $sufijo ?>').val();
    let desde = $('#busquedaDesde<?= $sufijo; ?>').val();
    let hasta = $('#busquedaHasta<?= $sufijo; ?>').val();
    let frecuencia = $('#busquedaFrecuencia<?= $sufijo ?>').val();
    let nivel = <?= $nivel ?>;
    let tipo = $('#busquedaDispositivo<?= $sufijo; ?> option:selected').attr("tipo");
    console.log(tipo);

    let filtro='iddispositivo='+iddispositivo+'&listar='+listar+'&anio='+anio+'&mes='+mes+'&desde='+desde+'&hasta='+hasta+'&frecuencia='+frecuencia+'&nivel='+nivel;

    let extra='';
    if(document.getElementById('cboCantidadBusqueda<?php echo $sufijo;?>')){
        extra='&pagina='+pagina+'&cantidad='+$('#cboCantidadBusqueda<?php echo $sufijo;?>').val();
    }

    let extrapermiso = '&idopcion=<?= $idopcion ?>';

    if(listar=='D'){
        setRun('presentacion/listaReporteDavisClimaDia',filtro+extra+extrapermiso,'divLista<?php echo $sufijo;?>','divLista<?php echo $sufijo;?>',loading); 
    }else if(listar=='M'){
        setRun('presentacion/listaReporteDavisClimaMes',filtro+extra+extrapermiso,'divLista<?php echo $sufijo;?>','divLista<?php echo $sufijo;?>',loading); 
    }else if(listar=='A'){
        setRun('presentacion/listaReporteDavisClimaAnio',filtro+extra+extrapermiso,'divLista<?php echo $sufijo;?>','divLista<?php echo $sufijo;?>',loading); 
    }
}

verPagina<?php echo $sufijo;?>(1);

function activarFiltros<?= $sufijo ?>() {
    let listar = $('#busquedaListar<?= $sufijo ?>').val(); // Obtener el filtro seleccionado

    // Mostrar/ocultar los contenedores según la selección
    if (listar === 'D') {
        $("#contenedorAnio").attr("hidden", true);
        $("#contenedorMes").attr("hidden", true);
        $("#contenedorFecha").removeAttr("hidden");
        $("#contenedorFrecuencia").removeAttr("hidden");
    } else if (listar === 'M') {
        $("#contenedorAnio").removeAttr("hidden");
        $("#contenedorMes").removeAttr("hidden");
        $("#contenedorFecha").attr("hidden", true);
        $("#contenedorFrecuencia").attr("hidden", true);
    } else if (listar === 'A') {
        $("#contenedorAnio").removeAttr("hidden");
        $("#contenedorMes").attr("hidden", true);
        $("#contenedorFecha").attr("hidden", true);
        $("#contenedorFrecuencia").attr("hidden", true);
    }

    // Ajustar las opciones del menú desplegable
    let filtroSeleccionado = listar;
    $(".navi-item").each(function () {
        let listarAttr = $(this).attr("listar");
        if (listarAttr === filtroSeleccionado || !listarAttr) {
            $(this).show(); // Mostrar opción
        } else {
            $(this).hide(); // Ocultar opción
        }
    });
}


function ExportarDataExcel<?= $sufijo ?>(){
    let iddispositivo = $('#busquedaDispositivo<?= $sufijo; ?>').val();
    let listar = $('#busquedaListar<?= $sufijo; ?>').val();
    let anio = $('#busquedaAnio<?= $sufijo ?>').val();
    let mes = $('#busquedaMes<?= $sufijo ?>').val();
    let desde = $('#busquedaDesde<?= $sufijo; ?>').val();
    let hasta = $('#busquedaHasta<?= $sufijo; ?>').val();
    let frecuencia = $('#busquedaFrecuencia<?= $sufijo ?>').val();
    let nivel = <?= $nivel ?>;

    let filtro='iddispositivo='+iddispositivo+'&listar='+listar+'&anio='+anio+'&mes='+mes+'&desde='+desde+'&hasta='+hasta+'&frecuencia='+frecuencia+'&nivel='+nivel;

    let extra='';
    if(document.getElementById('cboCantidadBusqueda<?php echo $sufijo;?>')){
        extra='&pagina='+pagina+'&cantidad='+$('#cboCantidadBusqueda<?php echo $sufijo;?>').val();
    }

    let extrapermiso = '&idopcion=<?= $idopcion ?>';

    if(listar=='D'){
        if(frecuencia=='M'){
            window.open('presentacion/xlsReporteDavisClimaDiaMinuto.php?'+filtro+extra+extrapermiso,'_blank');
        }else{
            window.open('presentacion/xlsReporteDavisClimaDia.php?'+filtro+extra+extrapermiso,'_blank');
        }
    }else if(listar=='M'){
        window.open('presentacion/xlsReporteDavisClimaMes.php?'+filtro+extra+extrapermiso,'_blank');
    }else if(listar=='A'){
        window.open('presentacion/xlsReporteDavisClimaAnio.php?'+filtro+extra+extrapermiso,'_blank');
    }
    
}

function ExportarDataPDF<?= $sufijo ?>(){
    let iddispositivo = $('#busquedaDispositivo<?= $sufijo; ?>').val();
    let listar = $('#busquedaListar<?= $sufijo; ?>').val();
    let anio = $('#busquedaAnio<?= $sufijo ?>').val();
    let mes = $('#busquedaMes<?= $sufijo ?>').val();
    let desde = $('#busquedaDesde<?= $sufijo; ?>').val();
    let hasta = $('#busquedaHasta<?= $sufijo; ?>').val();
    let frecuencia = $('#busquedaFrecuencia<?= $sufijo ?>').val();
    let nivel = <?= $nivel ?>;

    let filtro='iddispositivo='+iddispositivo+'&listar='+listar+'&anio='+anio+'&mes='+mes+'&desde='+desde+'&hasta='+hasta+'&frecuencia='+frecuencia+'&nivel='+nivel;

    let extra='';
    if(document.getElementById('cboCantidadBusqueda<?php echo $sufijo;?>')){
        extra='&pagina='+pagina+'&cantidad='+$('#cboCantidadBusqueda<?php echo $sufijo;?>').val();
    }

    let extrapermiso = '&idopcion=<?= $idopcion ?>';

    if(listar=='D'){
        if(frecuencia=='M'){
            window.open('presentacion/pdfReporteDavisClimaDiaMinuto.php?'+filtro+extra+extrapermiso,"_blank", 'width = 600, height = 500');
        }else{
            window.open('presentacion/pdfReporteDavisClimaDia.php?'+filtro+extra+extrapermiso,"_blank", 'width = 600, height = 500');
        }
    }else if(listar=='M'){
        window.open('presentacion/pdfReporteDavisClimaMes.php?'+filtro+extra+extrapermiso,"_blank", 'width = 600, height = 500');
    }else if(listar=='A'){
        window.open('presentacion/pdfReporteDavisClimaAnio.php?'+filtro+extra+extrapermiso,"_blank", 'width = 600, height = 500');
    }
}



activarFiltros<?= $sufijo ?>();
document.addEventListener("DOMContentLoaded", () => {
    activarFiltros<?= $sufijo ?>();
});

$(document).ready(function(){
    $("html, body").animate({ scrollTop: 0 }, "slow");
})
</script>