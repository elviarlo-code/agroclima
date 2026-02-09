<?php
require_once('../logica/clsDashboard.php');
$objDash = new clsDashboard();

$listakanban = $objDash->consultarkanban($_SESSION['idpersona']);
$listakanban = $listakanban->fetchAll(PDO::FETCH_NAMED);

$kanbanData = [];
foreach ($listakanban as $row) {
    $boardId = $row['idkanban'];

    // Busca si ya existe un board con el mismo ID
    $existingBoardIndex = null;
    foreach ($kanbanData as $index => $board) {
        if ($board['id'] === $boardId) {
            $existingBoardIndex = $index;
            break;
        }
    }

    if ($existingBoardIndex === null) {
        // Si el board no existe, lo agrega como un nuevo elemento
        $kanbanData[] = [
            'id' => $boardId,
            'title' => $row['titulo'],
            'grid' => $row['grid'],
            'items' => []
        ];
        $existingBoardIndex = count($kanbanData) - 1; // Obtén el índice del último elemento agregado
    }

    // Agrega el item al board correspondiente
    $kanbanData[$existingBoardIndex]['items'][] = [
        'id'        => $row['idtarjeta'],
        'title'     => $row['titulo_dash'],
        'subtitle'  => $row['subtitulo_dash'],
        'body'      => $row['body_dash'],
        'grafico'   => $row['grafico_dash'],
        'url'       => $row['link_dash']
    ];
}

foreach($kanbanData as $kx=>$vx){
    foreach($vx['items'] as $ky=>$vy){
        require_once($vy['url']);
    }
}


$bodyContenido = array();
foreach ($listakanban as $row) {
    if($row['body_dash']!="" && $row['idtarjeta']>0){
        $bodyVariable = $row['body_dash']; // Ejemplo: $bodyVariable contiene "$bodyContent"
        // Evaluar dinámicamente el contenido de la variable
        eval('$variableResultado = ' . $bodyVariable . ';');
        //print_r($variableResultado);
        $bodyContenido[$row['idtarjeta']] = $variableResultado;
    }else{
        $bodyContenido[$row['idtarjeta']] = '';
    }
}


?>

<!--begin::Card-->
<div class="row">
    <div id="kt_kanban_1" class="col-md-12"></div>
</div>
<!--end::Card-->
<script>
(function () {
"use strict";
let bodyContenido = <?= json_encode($bodyContenido) ?>;
// console.log(bodyContenido);

// Utilidad para generar IDs únicos
let generateUniqueId = () => `item_${Math.random().toString(36).substr(2, 9)}`;

// Crear card con contenido dinámico
let createCardTemplate = (title, subtitle, body, chartId=null) => `
    <div class="card card-custom">
        ${title!='' ? `<div class="card-header">
            <div class="card-title">
                <h3 class="card-label">${title}<small>${subtitle}</small></h3>
            </div>
        </div>` : ''}
        <div class="card-body">
            ${body}
            ${chartId ? `<div><figure class="highcharts-figure"><div id="${chartId}"></div></figure></div>` : ''}
        </div>
    </div>
`;

let datakanban = <?= json_encode($kanbanData); ?>;
console.log('Datos del Kanban:', datakanban);

// Convertir el objeto en un array
let boardsArray = Object.values(datakanban);

// Crear los boards para el Kanban
let boards = boardsArray.map(board => ({
    id: `board_${board.id}`, // ID único del board
    title: board.title || '', // Título del board
    class: board.class || '', // Clases adicionales (opcional)
    item: board.items.map(item => ({
        id: `item_${item.id}`, // ID único del ítem
        title: createCardTemplate(
            item.title || '', // Título predeterminado
            item.subtitle || '', // Subtítulo
            bodyContenido[item.id] || '',
            item.grafico || null
        )
    }))
}));


// Configuración Kanban
let initKanban = () => {
    let kanban = new jKanban({
        element: '#kt_kanban_1',
        gutter: '0',
        widthBoard: '100%',
        boards: boards,
        dropBoard: function(el, source, sibling) {
            setTimeout(() => {
                saveBoardOrder();
            }, 100);
        },
        dropEl: function(el, target, source, sibling) {
            console.log("Elemento movido:", el);
            setTimeout(() => {
                saveItemOrder(); // Guardar el orden después de un pequeño retraso
            }, 100);
            <?php 
                global $serieTemPro;
                if(isset($serieTemPro)){
            ?>
            if (el.querySelector('#graficoTemp')) {
                renderHighchartTemp('graficoTemp', <?= json_encode($serieTemPro); ?>, '<?= $rowDispositivo['nombre']; ?>');
            }
            <?php
                }
            ?>
            <?php 
                global $serieHumPro;
                if(isset($serieHumPro)){
            ?>
            if (el.querySelector('#graficoHum')) {
                renderHighchartHum('graficoHum', <?= json_encode($serieHumPro); ?>, '<?= $rowDispositivo['nombre']; ?>');
            }
            <?php
                }
            ?>
        }
    });

    applyColumnStyles();
};

// Aplicar estilos a las columnas
let applyColumnStyles = () => {

    Object.entries(datakanban).forEach(([key, value]) => {
        console.log(value);
        $("[data-id=board_"+value.id+"]").addClass(value.grid);
    });
    
    /*$("[data-id=_columna12]").addClass('col-md-12');
    $("[data-id=_columna61]").addClass('col-md-6');
    $("[data-id=_columna62]").addClass('col-md-6');
    $("[data-id=_columna41]").addClass('col-md-4');
    $("[data-id=_columna42]").addClass('col-md-4');
    $("[data-id=_columna43]").addClass('col-md-4');*/

    // Agregar la clase col-md-12 a los contenedores de columna (kanban-board)
    var kanbanColumns = document.querySelectorAll('.kanban-board');
    kanbanColumns.forEach(function (column) {
        column.classList.add('p-0');
        column.style.setProperty('margin-right', '0px', 'important');
    });

    var kanbanMain = document.querySelectorAll('.kanban-drag');
    kanbanMain.forEach(function (column) {
        column.classList.add('p-1');
    });

    var kanbanItem = document.querySelectorAll('.kanban-item');
    kanbanItem.forEach(function (column) {
        column.classList.add('p-0');
    });
};


// Guardar el orden de los boards (columnas)
function saveBoardOrder() {
    var boardOrder = [];
    document.querySelectorAll('.kanban-board').forEach((board, index) => {
        boardOrder.push({
            id: board.dataset.id, // Obtiene el ID de la columna desde el atributo data-id
            order: index + 1
        });
    });

    let datax = [];
    datax.push({ name: "accion", value: "GUARDAR_ORDEN_BOARD" });
    datax.push({ name: "boardOrder", value: JSON.stringify(boardOrder) });

    // Guardar el orden en la base de datos
    $.ajax({
        url: 'controlador/contDashboard.php',
        method: 'POST',
        data: datax,
        success: function (response) {
            console.log('Board order saved:', response);
        },
        error: function (xhr) {
            console.error('Error saving board order:', xhr);
        }
    });
}

// Guardar el orden de los items
function saveItemOrder() {
    var itemOrder = [];
    console.log("Guardando el orden de los elementos...");

    document.querySelectorAll('.kanban-board').forEach(board => {
        const boardId = board.dataset.id; // Obtiene el ID de la columna desde el atributo data-id
        board.querySelectorAll('.kanban-item').forEach((item, index) => {
            console.log("Elemento en la columna", boardId, "con ID", item.dataset.eid, "en posición", index + 1);
            itemOrder.push({
                id: item.dataset.eid, // Obtiene el ID del elemento desde el atributo data-eid
                boardId: boardId,
                order: index + 1
            });
        });
    });

    let datax = [];
    datax.push({ name: "accion", value: "GUARDAR_ORDEN_ITEM" });
    datax.push({ name: "itemOrder", value: JSON.stringify(itemOrder) });

    // Guardar el orden en la base de datos
    $.ajax({
        url: 'controlador/contDashboard.php',
        method: 'POST',
        data: datax,
        success: function (response) {
            console.log('Item order saved:', response);
        },
        error: function (xhr) {
            console.error('Error saving item order:', xhr);
        }
    });
}

// Inicializar el Kanban al cargar la página
initKanban();

<?php 
    global $tablaubigeo;
    if(isset($tablaubigeo)){
?>
$('#tabla_ubigeo').DataTable({
    'paging'      : true,
    'lengthChange': true,
    'searching'   : true,
    'ordering'    : true,
    'info'        : true,
    'autoWidth'   : true,
    'responsive'  : true,
    "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
    "order": [[ 1, "asc" ], [ 2, "asc" ], [ 3, "asc" ]],
    "language": {
        "decimal":        "",
        "emptyTable":     "Sin datos",
        "info":           "Del _START_ al _END_ de _TOTAL_ filas",
        "infoEmpty":      "Del 0 a 0 de 0 filas",
        "infoFiltered":   "(filtro de _MAX_ filas totales)",
        "infoPostFix":    "",
        "thousands":      ",",
        "lengthMenu":     "Ver _MENU_ filas",
        "loadingRecords": "Cargando...",
        "processing":     "Procesando...",
        "search":         "Buscar:",
        "zeroRecords":    "No se encontraron resultados",
        "paginate": {
            "first":      "Primero",
            "last":       "Ultimo",
            "next":       "Siguiente",
            "previous":   "Anterior"
        },
        "aria": {
            "sortAscending":  ": orden ascendente",
            "sortDescending": ": orden descendente"
        }
    }     
});
<?php
    }
?>

<?php 
    global $serieTemPro;
    if(isset($serieTemPro)){
?>
function renderHighchartTemp(containerId, serie, dispositivoNombre) {
    if (!document.querySelector('#'+containerId)) {
        console.error(`El contenedor "${containerId}" no existe en el DOM.`);
        return;
    }
    Highcharts.chart(containerId, {
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
            text: `<strong>Dispositivo:</strong> ${dispositivoNombre}`
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
            xDateFormat: '%Y-%m-%d',
            formatter: function () {
                let tooltipContent = `<strong>Fecha:</strong> ${Highcharts.dateFormat('%d/%m/%Y %H:%M', this.x)}<br>`;
                this.points.forEach(point => {
                    const symbolHTML = `<span style="color:${point.color}">●</span>`;
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
        series: [{
            name: 'Temperatura',
            data: serie,
            color: '#2caffe',
            marker: { symbol: 'circle' }
        }],
        credits: {
            enabled: false
        }
    });
}

    setTimeout(() => {
        renderHighchartTemp('graficoTemp', <?= json_encode($serieTemPro); ?>, '<?= $rowDispositivo['nombre']; ?>');
    }, 50);
<?php
    }
?>

<?php 
    global $serieHumPro;
    if(isset($serieHumPro)){
?>
function renderHighchartHum(containerId, serie, dispositivoNombre) {
    if (!document.querySelector('#'+containerId)) {
        console.error(`El contenedor "${containerId}" no existe en el DOM.`);
        return;
    }
    Highcharts.chart(containerId, {
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
            text: `<strong>Dispositivo:</strong> ${dispositivoNombre}`
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
            xDateFormat: '%Y-%m-%d',
            formatter: function () {
                let tooltipContent = `<strong>Fecha:</strong> ${Highcharts.dateFormat('%d/%m/%Y %H:%M', this.x)}<br>`;
                this.points.forEach(point => {
                    const symbolHTML = `<span style="color:${point.color}">●</span>`;
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
        series: [{
            name: 'Humedad',
            data: serie,
            color: '#fe6a35',
            marker: { symbol: 'circle' }
        }],
        credits: {
            enabled: false
        }
    });
}

    setTimeout(() => {
        renderHighchartHum('graficoHum', <?= json_encode($serieHumPro); ?>, '<?= $rowDispositivo['nombre']; ?>');
    }, 50);
<?php
    }
?>

})();
</script>
