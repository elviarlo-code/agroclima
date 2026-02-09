<?php
require_once('../logica/clsCase.php');
require_once('../logica/clsCampania.php');
require_once('../logica/clsCompartido.php');
$objCase = new clsCase;
$objCam = new clsCampania();

$sufijo = "vistaCampania";
$idopcion = $_GET['idopcion'];
$idcampania = $_GET['idcampania'];
$nivel = 2;
if(isset($_GET['nivel'])){
	$nivel = $_GET['nivel'];
}

$registro = $objCam->seleccionarCampaniaPorID($idcampania);
$registro = $registro->fetch(PDO::FETCH_NAMED);

$estado = 'EN PROCESO';
$estado_label = "label-light-success";
if($registro['finalizado']==1){
	$estado = "FINALIZADO";
	$estado_label = "label-light-danger";
}

$foto = "assets/media/users/hoja1.jpg";
if($registro['imagen']!=""){
	if(file_exists("../files/imagenes/cultivos/".$registro['imagen'])){
		$foto = "files/imagenes/cultivos/".$registro['imagen'];
	}
}

$datasensor = array();
$serieTemMax = array();
$serieTemMin = array();
$serieTemPro = array();
$serieHumMax = array();
$serieHumMin = array();
$serieHumPro = array();
if($registro['iddispositivo']>0){
	if($registro['tipodis']=='RITEC01C'){
		$datasensor = $objCam->consultarDatoSensorCampania($registro['iddispositivo'], $registro['fechasiembra'], $registro['fechafin']);
		$datasensor = $datasensor->fetchAll(PDO::FETCH_NAMED);


		foreach($datasensor as $k=>$v){
	        $fecha = new DateTime($v['fecha']);
	        $timestamp = $fecha->getTimestamp()*1000; // Convertir fecha a timestamp en milisegundos
	    	$serieTemMax[] = [$timestamp, floatval($v['tempmax'])]; // Formato [timestamp, valor] 
	    	$serieTemMin[] = [$timestamp, floatval($v['tempmin'])]; // Formato [timestamp, valor] 
	    	$serieTemPro[] = [$timestamp, floatval($v['temppro'])]; // Formato [timestamp, valor] 
	    	$serieHumMax[] = [$timestamp, floatval($v['hummax'])]; // Formato [timestamp, valor] 
	    	$serieHumMin[] = [$timestamp, floatval($v['hummin'])]; // Formato [timestamp, valor] 
	    	$serieHumPro[] = [$timestamp, floatval($v['humpro'])]; // Formato [timestamp, valor] 
		}

	}
}

// mapea el número del mes a su nombre en español
$meses = array(
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

$fini = explode('-', $registro['fechaini']);
$fechaini = $fini[2].' '.substr($meses[$fini[1]],0,3).', '.$fini[0];

$fsie = explode('-', $registro['fechasiembra']);
$fechasiembra = $fsie[2].' '.substr($meses[$fsie[1]],0,3).', '.$fsie[0];

$fechaCosecha = "-- ---, ----";

// Fechas de inicio y fin
$fechaInicio = new DateTime($registro['fechaini']);
$final = date('Y-m-d');
if($registro['finalizado']==1){
	$final = $registro['fechafin'];
}
$fechaFinal = new DateTime($final);

// Calcular la diferencia entre las fechas
$diferencia = $fechaInicio->diff($fechaFinal);

$fenologia=$objCam->consultarFenologiaCampania($idcampania);
$fenologia=$fenologia->fetchAll(PDO::FETCH_NAMED); 

$valoreskc = array();
$fases = array();
$colores = highchartsColor();
$inicio = 0;
$xAxis = array();
$contador = 0;
$seriesData = [];
$fechaInicio = new DateTime($registro['fechasiembra']);
$fechaInicio->modify('-1 day');
foreach($fenologia as $k=>$v){
    for ($i=0; $i<$v['duracion'] ; $i++){ 
        $valoreskc[] = floatval($v['valorkc']);
        $xAxis[] = $contador;
        $fechaInicio->modify('+1 day');
        $timestamp = $fechaInicio->getTimestamp()*1000; // Convertir fecha a timestamp en milisegundos
    	$seriesData[] = [$timestamp, floatval($v['valorkc'])]; // Formato [timestamp, valor]
        $contador++;
    }

    $finicio = new DateTime($registro['fechasiembra']);
    $iniciox = $finicio->modify('+'.$inicio.' day');
    $iniciox = $finicio->getTimestamp()*1000;

    $ffin = new DateTime($registro['fechasiembra']);
    $finx = $ffin->modify('+'.($v['duracion'] + $inicio).' day');
    $finx = $ffin->getTimestamp()*1000;

    $fases[] = array('inicio'=>$iniciox, 'fin'=>$finx, 'color'=>$colores[$k], 'nombre'=>$v['nombre']);
    $inicio += $v['duracion'];
}

?>
<!--begin::Card-->
<div class="card card-custom gutter-b">
	<div class="card-body">
		<div class="d-flex">
			<!--begin: Pic-->
			<div class="flex-shrink-0 mr-7 mt-lg-0 mt-3">
				<div class="symbol symbol-50 symbol-lg-120">
					<img alt="<?= $registro['cultivo'] ?>" src="<?= $foto ?>" />
				</div>
				<div class="symbol symbol-50 symbol-lg-120 symbol-primary d-none">
					<span class="font-size-h3 symbol-label font-weight-boldest"><?= $registro['cultivo'] ?></span>
				</div>
			</div>
			<!--end: Pic-->
			<!--begin: Info-->
			<div class="flex-grow-1">
				<div class="d-flex align-items-center justify-content-between flex-wrap">
				<div class="mr-3">
					<div class="d-flex align-items-center mr-3">
						<!--begin::Name-->
						<a href="#" class="d-flex align-items-center text-dark text-hover-primary font-size-h5 font-weight-bold mr-3">
							<?= $registro['descripcion'] ?>
						</a>
						<!--end::Name-->
						<span class="label <?= $estado_label ?> label-inline font-weight-bolder mr-1"><?= $estado ?></span>
						<div class="dropdown dropdown-inline" data-toggle="tooltip" title="" data-placement="left" data-original-title="Quick actions">
							<a href="#" class="btn btn-clean btn-hover-light-primary btn-xs btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i class="fa fa-caret-down"></i>
							</a>
							<div class="dropdown-menu dropdown-menu-md dropdown-menu-right" style="">
								<!--begin::Navigation-->
								<ul class="navi navi-hover">
									<li class="navi-header font-weight-bold">
										<span class="font-size-lg">Opciones:</span>
										<i class="flaticon2-information icon-md text-muted" data-toggle="tooltip" data-placement="right" title="" data-original-title="Click to learn more..."></i>
									</li>
									<li class="navi-separator mb-3 opacity-70"></li>
									<li class="navi-item">
										<a href="#" class="navi-link">
											<span class="navi-text">
												<span class="label label-xl label-inline label-light-success">Cultivo</span>
											</span>
										</a>
									</li>
									<li class="navi-item">
										<a href="#" class="navi-link" onclick="verDatosDispositivo<?= $sufijo ?>()">
											<span class="navi-text">
												<span class="label label-xl label-inline label-light-danger">Datos Sensores</span>
											</span>
										</a>
									</li>
									<li class="navi-item">
										<a href="#" class="navi-link">
											<span class="navi-text">
												<span class="label label-xl label-inline label-light-warning">Terreno</span>
											</span>
										</a>
									</li>
									<li class="navi-item">
										<a href="#" class="navi-link">
											<span class="navi-text">
												<span class="label label-xl label-inline label-light-primary">Esquema Hidráulico</span>
											</span>
										</a>
									</li>
								</ul>
								<!--end::Navigation-->
							</div>
						</div>
					</div>
					<!--begin::Contacts-->
					<div class="d-flex flex-wrap mb-2 font-size-xs">
						<a href="#" class="text-muted text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0">
							<?= $registro['fecha_registro'] ?>
						</a>
						<a href="#" class="text-muted text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0">
							<?= $registro['registrador'] ?>
						</a>
					</div>
					<!--end::Contacts-->
				</div>
				<!--begin::User-->
				<!--begin::Actions-->
				<div class="mb-10">
					<a href="#" class="btn btn-sm btn-light-primary font-weight-bolder mr-2" onclick="configuracion<?= $sufijo ?>()">
						Campaña
					</a>
					<a href="#" class="btn btn-sm btn-light-warning font-weight-bolder mr-2" onclick="verFenologia<?= $sufijo ?>()">
						Fenologia
					</a>
					<div class="dropdown dropdown-inline">
						<a href="#" class="btn btn-light-info btn-sm font-weight-bolder dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Exportar</a>
						<div class="dropdown-menu dropdown-menu-md dropdown-menu-right" style="">
							<!--begin::Navigation-->
							<ul class="navi navi-hover">
								<li class="navi-item">
									<a href="#" class="navi-link" onclick="downloadPNG<?= $sufijo ?>()">
										<span class="navi-icon">
											<i class="flaticon2-writing"></i>
										</span>
										<span class="navi-text">Descargar Imagen Fenologia</span>
									</a>
								</li>
								<li class="navi-item">
									<a href="#" class="navi-link">
										<span class="navi-icon">
											<i class="flaticon2-writing"></i>
										</span>
										<span class="navi-text">Save &amp; continue</span>
									</a>
								</li>
								<li class="navi-item">
									<a href="#" class="navi-link">
										<span class="navi-icon">
											<i class="flaticon2-writing"></i>
										</span>
										<span class="navi-text">Save &amp; continue</span>
									</a>
								</li>
								<li class="navi-item">
									<a href="#" class="navi-link">
										<span class="navi-icon">
											<i class="flaticon2-writing"></i>
										</span>
										<span class="navi-text">Save &amp; continue</span>
									</a>
								</li>
								<li class="navi-item">
									<a href="#" class="navi-link">
										<span class="navi-icon">
											<i class="flaticon2-writing"></i>
										</span>
										<span class="navi-text">Save &amp; continue</span>
									</a>
								</li>
							</ul>
							<!--end::Navigation-->
						</div>
					</div>
				</div>
				<!--end::Actions-->
			</div>
				<!--begin: Title-->
				<div class="row mt-1">
					<!--begin::Contacts-->
					<div class="col-md-5">
						<a href="#" class="text-dark-75 font-weight-bold mb-lg-0 mb-2">
							<div class="d-flex align-items-center justify-content-between mb-2">
								<span class="font-weight-bold mr-2">Cultivo:</span>
								<a href="#" class="text-muted text-hover-primary">
									<?= $registro['cultivo'] ?>
								</a>
							</div>
						</a>
					</div>
					<div class="col-md-5">
						<a href="#" class="text-dark-75 font-weight-bold mb-lg-0 mb-2">
							<div class="d-flex align-items-center justify-content-between mb-2">
								<span class="font-weight-bold mr-2">Turno:</span>
								<a href="#" class="text-muted text-hover-primary">
									<?= $registro['turno'] ?>
								</a>
							</div>
						</a>
					</div>
					<!--end::Contacts-->
				</div>
				<div class="row">
					<!--begin::Contacts-->
					<div class="col-md-5">
						<a href="#" class="text-dark-75 font-weight-bold mb-lg-0 mb-2">
							<div class="d-flex align-items-center justify-content-between mb-2">
								<span class="font-weight-bold mr-2">Terreno:</span>
								<a href="#" class="text-muted text-hover-primary">
									<?= $registro['terreno'] ?>
								</a>
							</div>
						</a>
					</div>
					<div class="col-md-5">
						<a href="#" class="text-dark-75 font-weight-bold mb-lg-0 mb-2">
							<div class="d-flex align-items-center justify-content-between mb-2">
								<span class="font-weight-bold mr-2">Área:</span>
								<a href="#" class="text-muted text-hover-primary">
									<?= $registro['area'] ?> m<sup>2</sup>
								</a>
							</div>
						</a>
					</div>
					<!--end::Contacts-->
				</div>
				<!--end: Title-->
				<!--begin: Content-->
				<div class="d-flex align-items-center flex-wrap justify-content-between">
					<div class="d-flex flex-wrap align-items-center pt-2">
						<div class="d-flex align-items-center mr-10">
							<div class="mr-6">
								<div class="font-weight-bold mb-2">Inicio</div>
								<span class="btn btn-sm btn-text btn-light-primary text-uppercase font-weight-bold"><?= $fechaini ?></span>
							</div>
							<div class="mr-6">
								<div class="font-weight-bold mb-2">Siembra</div>
								<span class="btn btn-sm btn-text btn-light-success text-uppercase font-weight-bold"><?= $fechasiembra ?></span>
							</div>
							<div class="">
								<div class="font-weight-bold mb-2">Cosecha</div>
								<span class="btn btn-sm btn-text btn-light-danger text-uppercase font-weight-bold"><?= $fechaCosecha ?></span>
							</div>
						</div>
						<div hidden class="flex-grow-1 flex-shrink-0 w-150px w-xl-300px mt-4 mt-sm-0">
							<span class="font-weight-bold">Progreso</span>
							<div class="progress progress-xs mt-2 mb-2">
								<div class="progress-bar bg-success" role="progressbar" style="width: 63%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
							<span class="font-weight-bolder text-dark">78%</span>
						</div>
					</div>
				</div>
				<!--end: Content-->
			</div>
			<!--end: Info-->
		</div>
		<div class="separator separator-solid my-4"></div>
		<!--begin: Items-->
		<div class="d-flex align-items-center flex-wrap">
			<!--begin: Item-->
			<!--begin: Item-->
			<div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
				<span class="mr-4">
					<i class="flaticon-file-2 icon-2x text-muted font-weight-bold"></i>
				</span>
				<div class="d-flex flex-column text-dark-75">
					<span class="font-weight-bolder font-size-sm">Días Transcurridos</span>
					<span class="font-weight-bolder font-size-h5">
						<?= $diferencia->days ?>
					</span>
				</div>
			</div>
			<!--end: Item-->
			<!--begin: Item-->
			<div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
				<span class="mr-4">
					<i class="flaticon-file-2 icon-2x text-muted font-weight-bold"></i>
				</span>
				<div class="d-flex flex-column text-dark-75">
					<span class="font-weight-bolder font-size-sm">Actividades</span>
					<span class="font-weight-bolder font-size-h5">
						100
					</span>
				</div>
			</div>
			<!--end: Item-->
			<div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
				<span class="mr-4">
					<i class="flaticon-piggy-bank icon-2x text-muted font-weight-bold"></i>
				</span>
				<div class="d-flex flex-column text-dark-75">
					<span class="font-weight-bolder font-size-sm">Gastos</span>
					<span class="font-weight-bolder font-size-h5">
						<span class="text-dark-50 font-weight-bold">S/.</span>249,500
					</span>
				</div>
			</div>
			<!--end: Item-->
		</div>
		<!--begin: Items-->
	</div>
</div>
<!--end::Card-->
<div>
	<figure class="highcharts-figure">
    	<div id="grafico"></div>
	</figure>
</div>
<script>

seriesData = <?= json_encode($seriesData); ?>;
serieTemMax = <?= json_encode($serieTemMax); ?>;
serieTemMin = <?= json_encode($serieTemMin); ?>;
serieTemPro = <?= json_encode($serieTemPro); ?>;
serieHumMax = <?= json_encode($serieHumMax); ?>;
serieHumMin = <?= json_encode($serieHumMin); ?>;
serieHumPro = <?= json_encode($serieHumPro); ?>;

chartImagen = Highcharts.chart('grafico', {
    exporting: {
        sourceWidth: 1150,
        sourceHeight: 400,  
        enabled: false
    },
    chart: {
        type: 'line'
    },
    title: {
        text: 'Fases Fenológicas'
    },
    subtitle: {
        text: '<strong>Cultivo:</strong> '+'<?= $registro['cultivo'] ?>'
    },
    xAxis: {
        type: 'datetime',
        title: {
            text: 'Fechas'
        },
        labels: {
            format: '{value:%d/%m/%Y}' // Formato de fecha para las etiquetas
        },
        accessibility: {
            description: 'Valor Kc'
        }
    },
    yAxis: [
        {
            title: {
                text: ''
            },
            labels: {
                format: '{value}' // Formato para el eje Kc
            },
            gridLineColor: 'rgba(0, 0, 0, 0.07)', // Color de las líneas de la cuadrícula
            opposite: false // Eje principal (izquierda)
        },
        {
            title: {
                text: 'Temperatura (°C)'
            },
            labels: {
                format: '{value}°C' // Formato para el eje Temperatura
            },
            opposite: true // Eje en el lado derecho
        },
        {
            title: {
                text: 'Humedad (%)'
            },
            labels: {
                format: '{value}%' // Formato para el eje Humedad
            },
            opposite: false, // Segundo eje en la izquierda
            offset: 25 // Desplazamiento para separar de Kc
        }
    ],
    tooltip: {
        crosshairs: true,
        shared: true,
        xDateFormat: '%Y-%m-%d', // Formato de fecha en el tooltip
        formatter: function () {
            // Construir el contenido del tooltip
            let tooltipContent = `<strong>Fecha:</strong> ${Highcharts.dateFormat('%d/%m/%Y', this.x)}<br>`;
            this.points.forEach(point => {
                const symbolHTML = `<span style="color:${point.color}">●</span>`; // Punto de color
                tooltipContent += `${symbolHTML} <strong>${point.series.name}:</strong> ${point.y}<br>`;
            });
            return tooltipContent;
        }
    },
    plotOptions: {
        spline: {
            marker: {
                radius: 4,
                lineColor: '#666666',
                lineWidth: 1
            }
        }
    },
    series: [
        {
            name: 'Kc',
            marker: {
                symbol: 'square'
            },
            data: seriesData,
            yAxis: 0 // Asociar al primer eje (Kc)
        },
        {
            name: 'Temperatura Max',
            marker: {
                symbol: 'circle'
            },
            color: '#1e7ab1',
            data: serieTemMax,
            visible: false, // Serie desactivada por defecto
            yAxis: 1 // Asociar al segundo eje (Temperatura)
        },
        {
            name: 'Temperatura Pro',
            marker: {
                symbol: 'circle'
            },
            color: '#2caffe',
            data: serieTemPro,
            yAxis: 1 // Asociar al segundo eje (Temperatura)
        },
        {
            name: 'Temperatura Min',
            marker: {
                symbol: 'circle'
            },
            color: '#6bc7fe',
            data: serieTemMin,
            visible: false, // Serie desactivada por defecto
            yAxis: 1 // Asociar al segundo eje (Temperatura)
        },
        {
            name: 'Humedad Max',
            marker: {
                symbol: 'diamond'
            },
            color: '#cb542a',
            data: serieHumMax,
            visible: false, // Serie desactivada por defecto
            yAxis: 2 // Asociar al tercer eje (Humedad)
        },
        {
            name: 'Humedad Pro',
            marker: {
                symbol: 'diamond'
            },
            color: '#fe6a35',
            data: serieHumPro,
            yAxis: 2 // Asociar al tercer eje (Humedad)
        },
        {
            name: 'Humedad Min',
            marker: {
                symbol: 'diamond'
            },
            color: '#fe875d',
            data: serieHumMin,
            visible: false, // Serie desactivada por defecto
            yAxis: 2 // Asociar al tercer eje (Humedad)
        }
    ],
    credits: {
        enabled: false
    }
});


<?php foreach ($fases as $key => $value) { ?>
                
    chartImagen.xAxis[0].addPlotLine({        
        dashStyle: 'dash',
        color: '#6A329F',
        width: 2,
        value: <?= $value['fin'] ?>,
        zIndex: 3,
        label: {
            text: '',
            verticalAlign: 'middle',
            textAlign: 'center',
            x: -50
        }
    });

    chartImagen.xAxis[0].addPlotBand({
        color: '<?= $value['color'] ?>', // Color value
        from: <?= $value['inicio'] ?>, // Start of the plot band
        to: <?= $value['fin'] ?>,
        id: '<?= $value['nombre'] ?>',// End of the plot band
        label: {
            text: '<?= $value['nombre'] ?>', // Content of the label.
            textAlign: 'left',
            align: 'right',
            verticalAlign: 'top',
            x: -15,
            y: 10,
            rotation: 90,
            style: {
                color: 'blue',
                //fontWeight: 'bold',
                fontSize: '10px'
            }
        }
    });
<?php } ?>

function downloadPNG<?= $sufijo ?>() {
    let svgData = chartImagen.getSVG();

    // Crear un canvas temporal sin añadirlo al DOM
    let canvas = document.createElement('canvas');
    let scale = 2;
    let width = 1150;
    let height = 400;

    canvas.width = width * scale;
    canvas.height = height * scale;

    let ctx = canvas.getContext('2d');
    ctx.scale(scale, scale);

    // Convertir el SVG a una imagen y dibujar en el canvas
    let img = new Image();
    let svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
    let url = URL.createObjectURL(svgBlob);

    img.onload = function () {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(img, 0, 0, width, height);
        URL.revokeObjectURL(url);

        // Descargar la imagen
        const link = document.createElement('a');
        link.href = canvas.toDataURL('image/png');
        link.download = '<?= 'FENOLOGIA_'.str_replace(" ","_",$registro['cultivo']).'.png' ?>';
        link.click();
    };
    img.src = url;
}

function configuracion<?= $sufijo ?>(){
	ViewModal('presentacion/viewCampania_configuracion','accion=MODIFICAR&idopcion=<?= $idopcion ?>&nivel=<?= $nivel + 1 ?>&idcampania=<?= $idcampania ?>&fhini=<?= $registro['fechaini'] ?>&fsiembra=<?= $registro['fechasiembra'] ?>&fhfin=<?= $registro['fechafin'] ?>&iddispositivo=<?= $registro['iddispositivo'] ?>','divmodalmediano','Configurar Campaña',0);
}

function verFenologia<?= $sufijo ?>(){
	ViewModal('presentacion/viewFenologiaCampaniaAdmin','nivel=<?= $nivel + 1 ?>&idopcion=<?= $idopcion ?>&idcultivo=<?= $registro['idcultivo'] ?>&idcampania=<?= $idcampania ?>','divmodal<?= $nivel ?>','FENOLOGIA');
}

function verDatosDispositivo<?= $sufijo ?>(){
	ViewModal('presentacion/adminReporteRitecClima','nivel=<?= $nivel + 1 ?>&idopcion=<?= $idopcion ?>&idcultivo=<?= $registro['idcultivo'] ?>&idcampania=<?= $idcampania ?>&iddispositivo=<?= $registro['iddispositivo'] ?>&ver=1','divmodal<?= $nivel ?>','Datos Climáticos',0);
}

$(document).ready(function(){
    $("html, body").animate({ scrollTop: 0 }, "slow");
})
</script>