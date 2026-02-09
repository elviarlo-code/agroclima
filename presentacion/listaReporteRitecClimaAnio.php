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

$idopcion = (isset($_GET['idopcion']))? $_GET['idopcion']:0;
$iddispositivo = $_GET['iddispositivo'];
$listar = $_GET['listar'];
$anio = $_GET['anio'];
$mes = $_GET['mes'];
$desde = $_GET['desde'];
$hasta = $_GET['hasta'];

$rowDispositivo = $objCase->getRowTableFiltroSimple('dispositivo','iddispositivo',$iddispositivo);

$data = $objRep->consultarDatosSensorPorAnio($iddispositivo, $anio);
$data = $data->fetchAll(PDO::FETCH_NAMED);


// Inicializar arreglo base con todos los meses en cero
$valorRadiacion = array_fill(1, 12, 0);
$valorPrecipitacion = array_fill(1, 12, 0);
$valorTemperatura = array_fill(1, 12, ['tempmin' => 0, 'tempmax' => 0]);
$valorHumedad = array_fill(1, 12, ['humdmin' => 0, 'humdmax' => 0]);

// Recorrer los datos para asignar valores reales
foreach ($data as $k => $v) {
    $mes = intval($v['mes']); // Convertir el mes a entero
    $radiacionSolar = floatval($v['radiacion_solar']); // Asegurarse de que sea flotante
    $valorRadiacion[$mes] = $radiacionSolar; // Asignar el valor al mes correspondiente

    $precipitacion = floatval($v['precipitacion']); // Asegurarse de que sea flotante
    $valorPrecipitacion[$mes] = $precipitacion; // Asignar el valor al mes correspondiente

    // Procesar temperaturas máximas y mínimas
    $tempMax = isset($v['tempmax']) ? floatval($v['tempmax']) : 50;
    $tempMin = isset($v['tempmin']) ? floatval($v['tempmin']) : 0;
    $valorTemperatura[$mes] = ['tempmin' => $tempMin, 'tempmax' => $tempMax];

    // Procesar humedad máximas y mínimas
    $humdMax = isset($v['hummax']) ? floatval($v['hummax']) : 50;
    $humdMin = isset($v['hummin']) ? floatval($v['hummin']) : 0;
    $valorHumedad[$mes] = ['humdmin' => $humdMin, 'humdmax' => $humdMax];
}

// Convertir los meses numéricos a nombres
$radiacioncat = array_map(function ($num) use ($meses) {
    return $meses[$num];
}, array_keys($valorRadiacion));
$radiacionval = array_values($valorRadiacion);

// Convertir los meses numéricos a nombres
$precipitacioncat = array_map(function ($num) use ($meses) {
    return $meses[$num];
}, array_keys($valorPrecipitacion));
$precipitacionval = array_values($valorPrecipitacion);

// Convertir los meses numéricos a nombres (para categorías de los gráficos)
$temperaturacat = array_map(function ($num) use ($meses) {
    return $meses[$num];
}, array_keys($valorTemperatura));

// Preparar los datos de temperatura para el gráfico
$temperaturaval = [];
foreach ($valorTemperatura as $mes => $temps) {
    $temperaturaval[$mes] = [
        (is_null($temps['tempmin']) ? 0 : $temps['tempmin']),
        (is_null($temps['tempmax']) ? 0 : $temps['tempmax'])
    ];
}

// Convertir los meses numéricos a nombres (para categorías de los gráficos)
$humedadcat = array_map(function ($num) use ($meses) {
    return $meses[$num];
}, array_keys($valorHumedad));

// Preparar los datos de humedad para el gráfico
$humedadval = [];
foreach ($valorHumedad as $mes => $humds) {
    $humedadval[$mes] = [
        (is_null($humds['humdmin']) ? 0 : $humds['humdmin']),
        (is_null($humds['humdmax']) ? 0 : $humds['humdmax'])
    ];
}

// Convertir los arrays a formato JSON
$humedadcatJson = json_encode($humedadcat);
$humedadvalJson = json_encode(array_values($humedadval));

$temperaturacatJson = json_encode($temperaturacat);
$temperaturavalJson = json_encode(array_values($temperaturaval));

$precipitacioncatJson = json_encode($precipitacioncat);
$precipitacionvalJson = json_encode($precipitacionval);

$radiacioncatJson = json_encode($radiacioncat);
$radiacionvalJson = json_encode($radiacionval);


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
        <div id="graficoPre"></div>
    </figure>
</div>
<div>
    <figure class="highcharts-figure">
        <div id="graficoRad"></div>
    </figure>
</div>
<script>


chartTemperatura = Highcharts.chart('graficoTemp', {
    exporting: {
        sourceWidth: 1150,
        sourceHeight: 400,
        enabled: false
    },
    chart: {
        type: 'columnrange',
        inverted: true
    },
    title: {
        text: 'Temperaturas en <?= $anio ?>',
        align: 'left'
    },
    subtitle: {
        text: 'Fuente: <?= $rowDispositivo['nombre'] ?>',
        align: 'left'
    },
    xAxis: {
        categories: <?= $temperaturacatJson ?> // Meses
    },

    yAxis: {
        title: {
            text: 'Temperatura ( °C )'
        }
    },

    tooltip: {
        valueSuffix: '°C'
    },

    plotOptions: {
        columnrange: {
            borderRadius: '50%',
            dataLabels: {
                enabled: true,
                format: '{y}°C'
            }
        }
    },

    legend: {
        enabled: false
    },

    series: [{
        name: 'Rango de Temperatura',
        data: <?= $temperaturavalJson ?> // Datos de temperatura
    }]

});


chartHumedad = Highcharts.chart('graficoHum', {
    exporting: {
        sourceWidth: 1150,
        sourceHeight: 400,
        enabled: false
    },
    chart: {
        type: 'columnrange',
        inverted: true
    },
    title: {
        text: 'Humedades en <?= $anio ?>',
        align: 'left'
    },
    subtitle: {
        text: 'Fuente: <?= $rowDispositivo['nombre'] ?>',
        align: 'left'
    },
    xAxis: {
        categories: <?= $humedadcatJson ?> // Meses
    },

    yAxis: {
        title: {
            text: 'Humedad (%)'
        }
    },

    tooltip: {
        valueSuffix: '%'
    },

    plotOptions: {
        columnrange: {
            borderRadius: '50%',
            dataLabels: {
                enabled: true,
                format: '{y}%'
            }
        }
    },

    legend: {
        enabled: false
    },

    series: [{
        name: 'Rango de Humedad',
        color: '#fe6a35',
        data: <?= $humedadvalJson ?>
    }]

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
        text: 'Precipitación en <?= $anio ?>',
        align: 'left'
    },
    subtitle: {
        text: 'Fuente: <?= $rowDispositivo['nombre'] ?>',
        align: 'left'
    },
    xAxis: {
        categories: <?= $precipitacioncatJson ?>, // Meses
        crosshair: true,
        accessibility: {
            description: 'Countries'
        }
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
        valueSuffix: ' m<sup>3</sup>'
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: [
        {
            name: 'Precipitación',
            color: '#ee81ff',
            data: <?= $precipitacionvalJson ?> // Valores (incluyendo ceros)
        }
    ]
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
        text: 'Radiación Solar en <?= $anio ?>',
        align: 'left'
    },
    subtitle: {
        text: 'Fuente: <?= $rowDispositivo['nombre'] ?>',
        align: 'left'
    },
    xAxis: {
        categories: <?= $radiacioncatJson ?>, // Meses
        crosshair: true,
        accessibility: {
            description: 'Countries'
        }
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
        valueSuffix: ' W/s'
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: [
        {
            name: 'Radiación Solar',
            color: '#ffd700',
            data: <?= $radiacionvalJson ?> // Valores (incluyendo ceros)
        }
    ]
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