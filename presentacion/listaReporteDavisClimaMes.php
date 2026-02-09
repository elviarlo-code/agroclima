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

$rowDispositivo = $objCase->getRowTableFiltroSimple('dispositivo','iddispositivo',$iddispositivo);

$data = $objRep->consultarDatosSensorPorMesDavis($iddispositivo, $anio, $mes);
$data = $data->fetchAll(PDO::FETCH_NAMED);

$rangoTem = [];
$tempProm = [];

$rangoHum = [];
$humeProm = [];

$rangoVie = [];
$vienProm = [];

$seriePrecipitacion = array();
$serieRadiacion = array();

$formatofecha = "%d/%m/%Y";

// Inicializar variables
$radiacionDiaria = 0;
$prevTimestamp = null;
foreach($data as $k=>$v){

    $fecha = new DateTime($v['fecha']);
    $timestamp = $fecha->getTimestamp()*1000; // Convertir fecha a timestamp en milisegundos

    // Rango de temperaturas (mínima y máxima)
    $rangoTem[] = [$timestamp, floatval($v['tempmin']), floatval($v['tempmax'])];
    // Promedio de temperatura
    $tempProm[] = [$timestamp, floatval($v['temppro'])];

    // Rango de temperaturas (mínima y máxima)
    $rangoHum[] = [$timestamp, floatval($v['hummin']), floatval($v['hummax'])];
    // Promedio de temperatura
    $humeProm[] = [$timestamp, floatval($v['humpro'])];

    // Rango de temperaturas (mínima y máxima)
    $rangoVie[] = [$timestamp, floatval($v['vvemin']), floatval($v['vvemax'])];
    // Promedio de temperatura
    $vienProm[] = [$timestamp, floatval($v['vvepro'])];
    
    $seriePrecipitacion[] = [$timestamp, floatval($v['precipitacion'])]; // Formato [timestamp, valor] 
    $serieRadiacion[] = [$timestamp, floatval($v['radiacion_solar'])]; // Formato [timestamp, valor] 

}

// Convertir las series a formato JSON para usarlas en JavaScript
$rangoTemJSON = json_encode($rangoTem);
$promeTemJSON = json_encode($tempProm);

$rangoHumJSON = json_encode($rangoHum);
$promeHumJSON = json_encode($humeProm);

$rangoVieJSON = json_encode($rangoVie);
$promeVieJSON = json_encode($vienProm);

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

?>
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

rangoTem = <?= $rangoTemJSON; ?>;
promeTem = <?= $promeTemJSON; ?>;

chartTemperatura = Highcharts.chart('graficoTemp', {
    exporting: {
        sourceWidth: 1150,
        sourceHeight: 400,
        enabled: false
    },
    title: {
        text: 'Temperaturas en <?= $meses[$mes].' '.$anio ?>',
        align: 'left'
    },
    subtitle: {
        text: 'Fuente: <?= $rowDispositivo['nombre'] ?>',
        align: 'left'
    },
    xAxis: {
        type: 'datetime'
    },
    yAxis: {
        title: {
            text: 'Temperatura (°C)'
        }
    },
    tooltip: {
        shared: true,
        useHTML: true,
        formatter: function () {
            // Formatear la fecha
            const fecha = Highcharts.dateFormat('%e %b %Y', this.x);

            // Construir el contenido del tooltip
            let contenido = `<b>Fecha:</b> ${fecha}<br>`;
            this.points.forEach(point => {
                const color = point.color; // Color del punto
                const serieNombre = point.series.name; // Nombre de la serie

                // Verificar si la serie es de tipo arearange
                if (point.series.type === 'arearange') {
                    const min = point.point.low; // Valor mínimo del rango
                    const max = point.point.high; // Valor máximo del rango
                    contenido += `
                        <span style="color:${color}">\u25CF</span> 
                        <b>${serieNombre}:</b> ${min}°C - ${max}°C<br>
                    `;
                } else {
                    // Series normales (sin rango)
                    const valor = point.y; // Valor del punto
                    contenido += `
                        <span style="color:${color}">\u25CF</span> 
                        <b>${serieNombre}:</b> ${valor}°C<br>
                    `;
                }
            });

            return contenido;
        },
        backgroundColor: 'rgba(255, 255, 255, 0.9)', // Fondo semitransparente
        borderColor: '#666666', // Color del borde
        borderRadius: 8, // Bordes redondeados
        borderWidth: 1
    },
    plotOptions: {
        series: {
            pointStart: Date.UTC(2024, 4, 1), // Inicio de la serie
            pointInterval: 24 * 3600 * 1000 // Un día en milisegundos
        }
    },
    series: [{
        name: 'Temperatura Promedio',
        data: promeTem,
        zIndex: 1,
        marker: {
            fillColor: 'white',
            lineWidth: 2,
            lineColor: '#2caffe'
        }
    }, {
        name: 'Rango de temperatura',
        data: rangoTem,
        type: 'arearange',
        lineWidth: 0,
        linkedTo: ':previous',
        color: '#6bc7fe',
        fillOpacity: 0.3,
        zIndex: 0,
        marker: {
            enabled: false
        }
    }]
});


rangoHum = <?= $rangoHumJSON; ?>;
promeHum = <?= $promeHumJSON; ?>;

chartHumedad = Highcharts.chart('graficoHum', {
    exporting: {
        sourceWidth: 1150,
        sourceHeight: 400,
        enabled: false
    },
    title: {
        text: 'Humedad Relativa en <?= $meses[$mes].' '.$anio ?>',
        align: 'left'
    },
    subtitle: {
        text: 'Fuente: <?= $rowDispositivo['nombre'] ?>',
        align: 'left'
    },
    xAxis: {
        type: 'datetime'
    },
    yAxis: {
        title: {
            text: 'Humedad (%)'
        }
    },
    tooltip: {
        shared: true,
        useHTML: true,
        formatter: function () {
            // Formatear la fecha
            const fecha = Highcharts.dateFormat('%e %b %Y', this.x);

            // Construir el contenido del tooltip
            let contenido = `<b>Fecha:</b> ${fecha}<br>`;
            this.points.forEach(point => {
                const color = point.color; // Color del punto
                const serieNombre = point.series.name; // Nombre de la serie

                // Verificar si la serie es de tipo arearange
                if (point.series.type === 'arearange') {
                    const min = point.point.low; // Valor mínimo del rango
                    const max = point.point.high; // Valor máximo del rango
                    contenido += `
                        <span style="color:${color}">\u25CF</span> 
                        <b>${serieNombre}:</b> ${min}% - ${max}%<br>
                    `;
                } else {
                    // Series normales (sin rango)
                    const valor = point.y; // Valor del punto
                    contenido += `
                        <span style="color:${color}">\u25CF</span> 
                        <b>${serieNombre}:</b> ${valor}%<br>
                    `;
                }
            });

            return contenido;
        },
        backgroundColor: 'rgba(255, 255, 255, 0.9)', // Fondo semitransparente
        borderColor: '#666666', // Color del borde
        borderRadius: 8, // Bordes redondeados
        borderWidth: 1
    },
    plotOptions: {
        series: {
            pointStart: Date.UTC(2024, 4, 1), // Inicio de la serie
            pointInterval: 24 * 3600 * 1000 // Un día en milisegundos
        }
    },
    series: [{
        name: 'Humedad Promedio',
        data: promeHum,
        zIndex: 1,
        color: '#fe6a35',
        marker: {
            fillColor: 'white',
            lineWidth: 2,
            lineColor: '#fe6a35'
        }
    }, {
        name: 'Rango de Humedad',
        data: rangoHum,
        type: 'arearange',
        lineWidth: 0,
        linkedTo: ':previous',
        color: '#fe9671',
        fillOpacity: 0.3,
        zIndex: 0,
        marker: {
            enabled: false
        }
    }]
});


rangoVie = <?= $rangoVieJSON; ?>;
promeVie = <?= $promeVieJSON; ?>;

chartViento = Highcharts.chart('graficoVie', {
    exporting: {
        sourceWidth: 1150,
        sourceHeight: 400,
        enabled: false
    },
    title: {
        text: 'Velocidad de Viento en <?= $meses[$mes].' '.$anio ?>',
        align: 'left'
    },
    subtitle: {
        text: 'Fuente: <?= $rowDispositivo['nombre'] ?>',
        align: 'left'
    },
    xAxis: {
        type: 'datetime'
    },
    yAxis: {
        title: {
            text: 'V. de Viento (m/s)'
        }
    },
    tooltip: {
        shared: true,
        useHTML: true,
        formatter: function () {
            // Formatear la fecha
            const fecha = Highcharts.dateFormat('%e %b %Y', this.x);

            // Construir el contenido del tooltip
            let contenido = `<b>Fecha:</b> ${fecha}<br>`;
            this.points.forEach(point => {
                const color = point.color; // Color del punto
                const serieNombre = point.series.name; // Nombre de la serie

                // Verificar si la serie es de tipo arearange
                if (point.series.type === 'arearange') {
                    const min = point.point.low; // Valor mínimo del rango
                    const max = point.point.high; // Valor máximo del rango
                    contenido += `
                        <span style="color:${color}">\u25CF</span> 
                        <b>${serieNombre}:</b> ${min}m/s - ${max}m/s<br>
                    `;
                } else {
                    // Series normales (sin rango)
                    const valor = point.y; // Valor del punto
                    contenido += `
                        <span style="color:${color}">\u25CF</span> 
                        <b>${serieNombre}:</b> ${valor}m/s<br>
                    `;
                }
            });

            return contenido;
        },
        backgroundColor: 'rgba(255, 255, 255, 0.9)', // Fondo semitransparente
        borderColor: '#666666', // Color del borde
        borderRadius: 8, // Bordes redondeados
        borderWidth: 1
    },
    plotOptions: {
        series: {
            pointStart: Date.UTC(2024, 4, 1), // Inicio de la serie
            pointInterval: 24 * 3600 * 1000 // Un día en milisegundos
        }
    },
    series: [{
        name: 'V. de Viento Promedio',
        data: promeVie,
        zIndex: 1,
        color: '#544fc5',
        marker: {
            fillColor: 'white',
            lineWidth: 2,
            lineColor: '#544fc5'
        }
    }, {
        name: 'Rango de V. Viento',
        data: rangoVie,
        type: 'arearange',
        lineWidth: 0,
        linkedTo: ':previous',
        color: '#8783d6',
        fillOpacity: 0.3,
        zIndex: 0,
        marker: {
            enabled: false
        }
    }]
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
        text: 'Precipitación en <?= $meses[$mes].' '.$anio ?>',
        align: 'left'
    },
    subtitle: {
        text: 'Fuente: <?= $rowDispositivo['nombre'] ?>',
        align: 'left'
    },
    xAxis: {
        type: 'datetime',
        title: { text: '' },
        labels: { format: '{value:%d %b %Y}' },
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
        text: 'Radiación Solar en <?= $meses[$mes].' '.$anio ?>',
        align: 'left'
    },
    subtitle: {
        text: 'Fuente: <?= $rowDispositivo['nombre'] ?>',
        align: 'left'
    },
    xAxis: {
        type: 'datetime',
        title: { text: '' },
        labels: { format: '{value:%d %b %Y}' },
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