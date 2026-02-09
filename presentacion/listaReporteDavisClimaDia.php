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
$iddispositivo = $_GET['iddispositivo'];
$listar = $_GET['listar'];
$anio = $_GET['anio'];
$mes = $_GET['mes'];
$desde = $_GET['desde'];
$hasta = $_GET['hasta'];
$frecuencia = $_GET['frecuencia'];

$rowDispositivo = $objCase->getRowTableFiltroSimple('dispositivo','iddispositivo',$iddispositivo);

$ultimoregistro = $objRep->consultarDatosSensorUltimoEnvioDavis($iddispositivo);
$ultimoregistro = $ultimoregistro->fetch(PDO::FETCH_NAMED);

$data = $objRep->consultarDatosSensorPorDiaDavis($iddispositivo, $desde, $hasta, $frecuencia);
$data = $data->fetchAll(PDO::FETCH_NAMED);

$serieTemMax = array();
$serieTemMin = array();
$serieTemPro = array();
$serieHumMax = array();
$serieHumMin = array();
$serieHumPro = array();
$serieVieMax = array();
$serieVieMin = array();
$serieViePro = array();
$seriePrecipitacion = array();
$serieRadiacion = array();

$formatofecha = "%d/%m/%Y %H:%M";
if($frecuencia=='D'){
    $formatofecha = "%d/%m/%Y";
}

// Inicializar variables
$radiacionDiaria = 0;
$prevTimestamp = null;
foreach($data as $k=>$v){
    if($frecuencia=="M"){
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

    }else if($frecuencia=="H" || $frecuencia=="D"){
        if($frecuencia=="H"){
            $fecha = new DateTime($v['fechahora'], new DateTimeZone('UTC'));
        }else if($frecuencia=="D"){
            $fecha = new DateTime($v['fecha']);
        }
        $timestamp = $fecha->getTimestamp()*1000; // Convertir fecha a timestamp en milisegundos
        $serieTemMax[] = [$timestamp, floatval($v['tempmax'])]; // Formato [timestamp, valor] 
        $serieTemMin[] = [$timestamp, floatval($v['tempmin'])]; // Formato [timestamp, valor] 
        $serieTemPro[] = [$timestamp, floatval($v['temppro'])]; // Formato [timestamp, valor] 
        $serieHumMax[] = [$timestamp, floatval($v['hummax'])]; // Formato [timestamp, valor] 
        $serieHumMin[] = [$timestamp, floatval($v['hummin'])]; // Formato [timestamp, valor] 
        $serieHumPro[] = [$timestamp, floatval($v['humpro'])]; // Formato [timestamp, valor] 
        $serieVieMax[] = [$timestamp, floatval($v['vvemax'])]; // Formato [timestamp, valor] 
        $serieVieMin[] = [$timestamp, floatval($v['vvemin'])]; // Formato [timestamp, valor] 
        $serieViePro[] = [$timestamp, floatval($v['vvepro'])]; // Formato [timestamp, valor]
        $seriePrecipitacion[] = [$timestamp, floatval($v['precipitacion'])]; // Formato [timestamp, valor] 
        $serieRadiacion[] = [$timestamp, floatval($v['radiacion_solar'])]; // Formato [timestamp, valor] 
    }

}

?>
                                     
<!--begin::Separator-->
<div class="separator separator-solid mb-7"></div>
<!--end::Separator-->
<!--begin::Bottom-->
<div class="d-flex align-items-center flex-wrap">
    <!--begin: Item-->
    <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
        <div class="d-flex flex-column text-dark-75">
            <span class="font-weight-bolder font-size-sm text-success"><?= (isset($ultimoregistro['dia']))? $ultimoregistro['dia']:'' ?></span>
            <span class="font-weight-bolder font-size-h5 text-success"><?= (isset($ultimoregistro['hora']))? $ultimoregistro['hora']:'' ?></span>
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
            <span class="text-dark-50 font-weight-bold"></span><?= (isset($ultimoregistro['temperatura']))? $ultimoregistro['temperatura']:'0.00' ?>°C</span>
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
            <span class="text-dark-50 font-weight-bold"></span><?= (isset($ultimoregistro['humedad_relativa']))? $ultimoregistro['humedad_relativa']:'0.00' ?>%</span>
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
            <span class="text-dark-50 font-weight-bold"></span><?= (isset($ultimoregistro['velocidad_viento']))? $ultimoregistro['velocidad_viento']:'0.00' ?>m/s</span>
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
            <span class="text-dark-50 font-weight-bold"></span><?= (isset($ultimoregistro['direccion_viento']))? $ultimoregistro['direccion_viento']:'' ?></span>
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
            <span class="text-dark-50 font-weight-bold"></span><?= (isset($ultimoregistro['precipitacion']))? $ultimoregistro['precipitacion']:'0.00' ?>m<sup>3</sup></span>
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
            <span class="text-dark-50 font-weight-bold"></span><?= (isset($ultimoregistro['radiacion_solar']))? $ultimoregistro['radiacion_solar']:'0.00' ?>W/s</span>
        </div>
    </div>
    <!--end: Item-->
</div>
<!--end::Bottom-->
<!--begin::Separator-->
<div class="separator separator-solid my-7"></div>
<!--end::Separator-->
<div>
	<figure class="highcharts-figure">
    	<div id="graficoTemp"></div>
	</figure>
</div>
<div>
    <figure class="highcharts-figure">
        <div id="graficoHum"></div>
    </figure>
</div>
<div>
    <figure class="highcharts-figure">
        <div id="graficoVie"></div>
    </figure>
</div>
<div>
    <figure class="highcharts-figure">
        <div id="graficoPre"></div>
    </figure>
</div>
<div>
    <figure class="highcharts-figure">
        <div id="graficoRad"></div>
    </figure>
</div>
<script>

serieTemMax = <?= json_encode($serieTemMax); ?>;
serieTemMin = <?= json_encode($serieTemMin); ?>;
serieTemPro = <?= json_encode($serieTemPro); ?>;

// Configuración de series según frecuencia
seriesData = [];
    <?php if ($frecuencia == "M"){ ?>
        seriesData.push({
            name: 'Temperatura',
            data: serieTemPro,
            color: '#2caffe',
            marker: { symbol: 'circle' }
        });
    <?php }else{ ?>
        seriesData.push(
            {
                name: 'Temp. Máxima',
                data: serieTemMax,
                color: '#1e7ab1',
                marker: { symbol: 'circle' }
            },
            {
                name: 'Temp. Promedio',
                data: serieTemPro,
                color: '#2caffe',
                marker: { symbol: 'circle' }
            },
            {
                name: 'Temp. Mínima',
                data: serieTemMin,
                color: '#6bc7fe',
                marker: { symbol: 'circle' }
            }
        );
    <?php } ?>


chartTemperatura = Highcharts.chart('graficoTemp', {
    exporting: {
        sourceWidth: 1150,
        sourceHeight: 400,
        enabled: false
    },
    chart: {
        type: 'spline'
    },
    title: {
        text: 'Temperatura'
    },
    subtitle: {
        text: '<strong>Dispositivo:</strong> <?= $rowDispositivo['nombre'] ?>'
    },
    xAxis: {
        type: 'datetime',
        title: { text: '' },
        labels: { format: '{value:%d %b %Y, %H:%M}' },
        accessibility: { description: 'Fechas' }
    },
    yAxis: {
        title: { text: 'Temperatura (°C)' },
        labels: { format: '{value}°' }
    },
    tooltip: {
        crosshairs: true,
        shared: true,
        xDateFormat: '%Y-%m-%d', // Formato de fecha en el tooltip
        formatter: function () {
            // Construir el contenido del tooltip
            let tooltipContent = `<strong>Fecha:</strong> ${Highcharts.dateFormat('<?= $formatofecha ?>', this.x)}<br>`;
            this.points.forEach(point => {
                const symbolHTML = `<span style="color:${point.color}">●</span>`; // Punto de color
                tooltipContent += `${symbolHTML} <strong>${point.series.name}:</strong> ${point.y}°<br>`;
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
    series: seriesData,
    credits: {
        enabled: false
    }
});

serieHumMax = <?= json_encode($serieHumMax); ?>;
serieHumMin = <?= json_encode($serieHumMin); ?>;
serieHumPro = <?= json_encode($serieHumPro); ?>;

// Configuración de series según frecuencia
seriesData = [];
    <?php if ($frecuencia == "M"){ ?>
        seriesData.push({
            name: 'Humedad',
            data: serieHumPro,
            color: '#fe6a35',
            marker: { symbol: 'circle' }
        });
    <?php }else{ ?>
        seriesData.push(
            {
                name: 'Hum. Máxima',
                data: serieHumMax,
                color: '#cb542a',
                marker: { symbol: 'circle' }
            },
            {
                name: 'Hum. Promedio',
                data: serieHumPro,
                color: '#fe6a35',
                marker: { symbol: 'circle' }
            },
            {
                name: 'Hum. Mínima',
                data: serieHumMin,
                color: '#fe875d',
                marker: { symbol: 'circle' }
            }
        );
    <?php } ?>


chartHumedad = Highcharts.chart('graficoHum', {
    exporting: {
        sourceWidth: 1150,
        sourceHeight: 400,
        enabled: false
    },
    chart: {
        type: 'spline'
    },
    title: {
        text: 'Humedad Relativa'
    },
    subtitle: {
        text: '<strong>Dispositivo:</strong> <?= $rowDispositivo['nombre'] ?>'
    },
    xAxis: {
        type: 'datetime',
        title: { text: '' },
        labels: { format: '{value:%d %b %Y, %H:%M}' },
        accessibility: { description: 'Fechas' }
    },
    yAxis: {
        title: { text: 'Humedad (%)' },
        labels: { format: '{value}%' }
    },
    tooltip: {
        crosshairs: true,
        shared: true,
        xDateFormat: '%Y-%m-%d', // Formato de fecha en el tooltip
        formatter: function () {
            // Construir el contenido del tooltip
            let tooltipContent = `<strong>Fecha:</strong> ${Highcharts.dateFormat('<?= $formatofecha ?>', this.x)}<br>`;
            this.points.forEach(point => {
                const symbolHTML = `<span style="color:${point.color}">●</span>`; // Punto de color
                tooltipContent += `${symbolHTML} <strong>${point.series.name}:</strong> ${point.y}%<br>`;
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
    series: seriesData,
    credits: {
        enabled: false
    }
});

serieVieMax = <?= json_encode($serieVieMax); ?>;
serieVieMin = <?= json_encode($serieVieMin); ?>;
serieViePro = <?= json_encode($serieViePro); ?>;

// Configuración de series según frecuencia
seriesData = [];
    <?php if ($frecuencia == "M"){ ?>
        seriesData.push({
            name: 'Velocidad del viento',
            data: serieViePro,
            color: '#544fc5',
            marker: { symbol: 'circle' }
        });
    <?php }else{ ?>
        seriesData.push(
            {
                name: 'V. Viento Máxima',
                data: serieVieMax,
                color: '#3a3789',
                marker: { symbol: 'circle' }
            },
            {
                name: 'V. Viento Promedio',
                data: serieViePro,
                color: '#544fc5',
                marker: { symbol: 'circle' }
            },
            {
                name: 'V. Viento Mínima',
                data: serieVieMin,
                color: '#8783d6',
                marker: { symbol: 'circle' }
            }
        );
    <?php } ?>


chartViento = Highcharts.chart('graficoVie', {
    exporting: {
        sourceWidth: 1150,
        sourceHeight: 400,
        enabled: false
    },
    chart: {
        type: 'spline'
    },
    title: {
        text: 'Velocidad del viento'
    },
    subtitle: {
        text: '<strong>Dispositivo:</strong> <?= $rowDispositivo['nombre'] ?>'
    },
    xAxis: {
        type: 'datetime',
        title: { text: '' },
        labels: { format: '{value:%d %b %Y, %H:%M}' },
        accessibility: { description: 'Fechas' }
    },
    yAxis: {
        title: { text: 'Velocidad del Viento (m/s)' },
        labels: { format: '{value}m/s' }
    },
    tooltip: {
        crosshairs: true,
        shared: true,
        xDateFormat: '%Y-%m-%d', // Formato de fecha en el tooltip
        formatter: function () {
            // Construir el contenido del tooltip
            let tooltipContent = `<strong>Fecha:</strong> ${Highcharts.dateFormat('<?= $formatofecha ?>', this.x)}<br>`;
            this.points.forEach(point => {
                const symbolHTML = `<span style="color:${point.color}">●</span>`; // Punto de color
                tooltipContent += `${symbolHTML} <strong>${point.series.name}:</strong> ${point.y}m/s<br>`;
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
    series: seriesData,
    credits: {
        enabled: false
    }
});

seriePrecipitacion = <?= json_encode($seriePrecipitacion); ?>;

// Configuración de series según frecuencia
seriesData = [];
seriesData.push({
    name: 'Precipitacion',
    data: seriePrecipitacion,
    color: '#ee81ff'
});
    

chartPrecipitacion = Highcharts.chart('graficoPre', {
    exporting: {
        sourceWidth: 1150,
        sourceHeight: 400,
        enabled: false
    },
    chart: {
        type: 'column'
    },
    title: {
        text: 'Precipitación'
    },
    subtitle: {
        text: '<strong>Dispositivo:</strong> <?= $rowDispositivo['nombre'] ?>'
    },
    xAxis: {
        type: 'datetime',
        title: { text: '' },
        labels: { format: '{value:%d %b %Y, %H:%M}' },
        accessibility: { description: 'Fechas' }
    },
    yAxis: {
        title: { 
            text: 'Precipitación (m<sup>3</sup>)' 
        },
        labels: { 
            format: '{value}m<sup>3</sup>' // Agregar la unidad m³ a las etiquetas del eje Y
        }
    },
    tooltip: {
        crosshairs: true,
        shared: true,
        xDateFormat: '%Y-%m-%d', // Formato de fecha en el tooltip
        formatter: function () {
            // Construir el contenido del tooltip
            let tooltipContent = `<strong>Fecha:</strong> ${Highcharts.dateFormat('<?= $formatofecha ?>', this.x)}<br>`;
            this.points.forEach(point => {
                const symbolHTML = `<span style="color:${point.color}">●</span>`; // Punto de color
                tooltipContent += `${symbolHTML} <strong>${point.series.name}:</strong> ${point.y}m<sup>3</sup><br>`;
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
    series: seriesData,
    credits: {
        enabled: false
    }
});


serieRadiacion = <?= json_encode($serieRadiacion); ?>;

// Configuración de series según frecuencia
seriesData = [];
seriesData.push({
    name: 'Radiación Solar',
    data: serieRadiacion,
    color: '#ffd700'
});
    

chartRadiacion = Highcharts.chart('graficoRad', {
    exporting: {
        sourceWidth: 1150,
        sourceHeight: 400,
        enabled: false
    },
    chart: {
        type: 'column'
    },
    title: {
        text: 'Radiación Solar'
    },
    subtitle: {
        text: '<strong>Dispositivo:</strong> <?= $rowDispositivo['nombre'] ?>'
    },
    xAxis: {
        type: 'datetime',
        title: { text: '' },
        labels: { format: '{value:%d %b %Y, %H:%M}' },
        accessibility: { description: 'Fechas' }
    },
    yAxis: {
        title: { 
            text: 'Radiación (W/s)' 
        },
        labels: { 
            format: '{value}W/s' // Agregar la unidad m³ a las etiquetas del eje Y
        }
    },
    tooltip: {
        crosshairs: true,
        shared: true,
        xDateFormat: '%Y-%m-%d', // Formato de fecha en el tooltip
        formatter: function () {
            // Construir el contenido del tooltip
            let tooltipContent = `<strong>Fecha:</strong> ${Highcharts.dateFormat('<?= $formatofecha ?>', this.x)}<br>`;
            this.points.forEach(point => {
                const symbolHTML = `<span style="color:${point.color}">●</span>`; // Punto de color
                tooltipContent += `${symbolHTML} <strong>${point.series.name}:</strong> ${point.y}W/s<br>`;
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
    series: seriesData,
    credits: {
        enabled: false
    }
});


function downloadPNG<?= $sufijo ?>(charimg) {
    let charts = {
        "Temperatura": chartTemperatura,
        "Humedad": chartHumedad,
        "Viento": chartViento,
        "Precipitacion": chartPrecipitacion,
        "Radiacion": chartRadiacion
    };

    let chart = charts[charimg];
    if (!chart) {
        console.error(`No se encontró el gráfico para "${charimg}"`);
        return;
    }

    let svgData = chart.getSVG();
    if (!svgData) {
        console.error(`No se pudo generar el SVG para "${charimg}"`);
        return;
    }

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
        link.download = 'Grafico_'+charimg+'.png';
        link.click();
    };
    img.src = url;
}

function exportAllCharts() {
    let charts = [
        { name: "Temperatura", chart: chartTemperatura },
        { name: "Humedad", chart: chartHumedad },
        { name: "Viento", chart: chartViento },
        { name: "Precipitacion", chart: chartPrecipitacion },
        { name: "Radiacion", chart: chartRadiacion }
    ];

    let chartWidth = 1150; // Ancho fijo para cada gráfico
    let chartHeight = 400; // Alto fijo para cada gráfico
    let scale = window.devicePixelRatio || 2; // Escala dinámica

    // Crear canvas para combinar los gráficos
    let canvas = document.createElement('canvas');
    canvas.width = chartWidth * scale;
    canvas.height = chartHeight * charts.length * scale; // Altura total = gráfico individual * cantidad de gráficos
    let ctx = canvas.getContext('2d');
    ctx.scale(scale, scale);

    // Promesa para manejar la carga de gráficos en serie
    let loadChartPromises = charts.map((chartObj, index) => {
        return new Promise((resolve, reject) => {
            let svgData = chartObj.chart.getSVG();
            let svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
            let url = URL.createObjectURL(svgBlob);
            let img = new Image();

            img.onload = () => {
                let yPosition = index * chartHeight; // Posición vertical
                ctx.drawImage(img, 0, yPosition, chartWidth, chartHeight);
                URL.revokeObjectURL(url);
                resolve();
            };

            img.onerror = () => {
                console.error(`Error cargando el gráfico "${chartObj.name}"`);
                URL.revokeObjectURL(url);
                reject(`Error cargando el gráfico "${chartObj.name}"`);
            };

            img.src = url;
        });
    });

    // Combinar gráficos y descargar la imagen final
    Promise.all(loadChartPromises)
        .then(() => {
            let link = document.createElement('a');
            link.href = canvas.toDataURL('image/png');
            link.download = 'Todos_los_graficos.png';
            link.click();
        })
        .catch((error) => {
            console.error("Error exportando gráficos: ", error);
        });
}

function saveAllChartsToServer() {
    let charts = {
        "Temperatura": chartTemperatura,
        "Humedad": chartHumedad,
        "Viento": chartViento,
        "Precipitacion": chartPrecipitacion,
        "Radiacion": chartRadiacion
    };

    let chartWidth = 1150; // Ancho del gráfico
    let chartHeight = 400; // Alto del gráfico
    let scale = 2; // Escala del canvas

    let imagesData = {};

    // Función para procesar cada gráfico y convertirlo en base64
    function processChart(chartName, chart) {
        return new Promise((resolve, reject) => {
            let svgData = chart.getSVG();
            let canvas = document.createElement('canvas');

            canvas.width = chartWidth * scale;
            canvas.height = chartHeight * scale;

            let ctx = canvas.getContext('2d');
            ctx.scale(scale, scale);

            let img = new Image();
            let svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
            let url = URL.createObjectURL(svgBlob);

            img.onload = function () {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0, chartWidth, chartHeight);
                URL.revokeObjectURL(url);

                let pngBase64 = canvas.toDataURL('image/png');
                imagesData[chartName] = pngBase64; // Guardar la imagen como base64 en el objeto
                resolve();
            };

            img.onerror = function () {
                URL.revokeObjectURL(url);
                reject(`Error procesando el gráfico "${chartName}"`);
            };

            img.src = url;
        });
    }

    // Procesar todos los gráficos en paralelo
    let chartPromises = Object.entries(charts).map(([chartName, chart]) =>
        processChart(chartName, chart)
    );

    Promise.all(chartPromises)
        .then(() => {
            // Enviar todas las imágenes al servidor
            $.ajax({
                method: "POST",
                url: 'controlador/contDispositivo.php',
                data: {
                    accion: "GUARDAR_IMAGENES_SERVIDOR",
                    imagenes: imagesData,
                    sufijo: '<?= $sufijo ?>',
                    id: <?= $iddispositivo ?>
                }
            })
            .done(function (response) {
                // console.log("Todas las imágenes fueron guardadas con éxito.", response);
                ExportarDataPDF<?= $sufijo ?>();
            })
            .fail(function (error) {
                console.error("Error guardando las imágenes.", error);
            });
        })
        .catch((error) => {
            console.error("Error procesando los gráficos:", error);
        });
}


</script>