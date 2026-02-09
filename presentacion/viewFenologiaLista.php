<?php
require_once('../logica/clsCultivo.php');
require_once('../logica/clsCompartido.php');
$objCul = new clsCultivo();

$sufijo = "fenologia";
$nivel = 1;
if(isset($_GET['nivel'])){
	$nivel = $_GET['nivel'];
}

$idcultivo = $_GET['idcultivo'];
$cultivo = htmlentities($_GET['cultivo']);
$fenologia = $_GET['nombre'];

$idopcion = (isset($_GET['idopcion']))? $_GET['idopcion']:0;
$puedeeditar = (isset($_GET['editar']))? $_GET['editar']:1;
$puedeanular = (isset($_GET['anular']))? $_GET['anular']:1;
$puedeeliminar = (isset($_GET['eliminar']))? $_GET['eliminar']:1;
$permiso_especial = (isset($_GET['permiso_especial']))? $_GET['permiso_especial']:1;
$permiso_especial1 = (isset($_GET['permiso_especial1']))? $_GET['permiso_especial1']:1;
$permiso_especial2 = (isset($_GET['permiso_especial2']))? $_GET['permiso_especial2']:1;

$data=$objCul->consultarFenologia($idcultivo, $fenologia);
$data=$data->fetchAll(PDO::FETCH_NAMED); 

$valoreskc = array();
$fases = array();
$colores = highchartsColor();
$inicio = 0;
$xAxis = array();
$contador = 0;
foreach($data as $k=>$v){
    for ($i=0; $i<$v['duracion'] ; $i++){ 
        $valoreskc[] = floatval($v['valorkc']);
        $xAxis[] = $contador;
        $contador++;
    }

    $fases[] = array('inicio'=>$inicio, 'fin'=>$v['duracion'] + $inicio, 'color'=>$colores[$k], 'nombre'=>$v['nombre']);
    $inicio += $v['duracion'];
}

?>
<div class="table-responsive">
	<p><strong>Arrastrar ( <i class="fa fa-ellipsis-v"></i> <i class="fa fa-ellipsis-v"></i> )y ubicar en el orden deseado</strong></p>
	<table class="table table-bordered table-vertical-center table-hover table-sm font-size-sm">
	    <thead class="thead-light">
	        <tr>
	            <th scope="col">Ordenar</th>
	            <th scope="col">Fenologia</th>
	            <th scope="col">Duracion (dias)</th>
	            <th scope="col" colspan="2">Opciones</th>
	        </tr>
	    </thead>
	    <tbody class="todo-list form-group" id="ordenable">
	    	<?php foreach($data as $kx=>$fila){ 
	    			$class="";
	    			if($fila['estado']=='A'){
						$class="text-danger";
					}

	    	?>
	        <tr class="<?= $class ?>" id="<?php echo $fila['idfenologia']; ?>">
	            <td>
					<span class="handle">
		    		 	<i class="fa fa-ellipsis-v"></i>
		    		 	<i class="fa fa-ellipsis-v"></i>
		    		</span>
				</td>
	            <td><?= $fila['nombre'] ?></td>
	            <td><?= $fila['duracion'] ?></td>
	            <td>
	            	<button type="button" class="btn btn-light-primary font-weight-bold btn-sm" data-toggle="tooltip" data-placement="top" title="Editar Fenologia" href="javascript:void(0)" onclick="editar<?= $sufijo ?>('<?php echo $fila['idfenologia'];?>')" ><i class="fa fa-edit"></i> Editar</button>
	            </td>
	            <td>
	            	<button type="button" class="btn btn-light-danger font-weight-bold btn-sm" data-toggle="tooltip" data-placement="top" title="Eliminar Fenologia" href="javascript:void(0)" onclick="cambiarEstado<?= $sufijo ?>('<?php echo $fila['idfenologia'];?>','E')" ><i class="fa fa-times"></i> Eliminar</button>
	            </td>
	        </tr>
	    	<?php } ?>
	    </tbody>
	</table>
</div>
<div>
	<figure class="highcharts-figure">
    	<div id="grafico"></div>
	</figure>
</div>
<script>
$(document).on('shown.bs.dropdown', '#divLista<?php echo $sufijo;?> .table-responsive', function (e) {
    // The .dropdown container
    var $container = $(e.target);

    // Find the actual .dropdown-menu
    var $dropdown = $container.find('.dropdown-menu');
    if ($dropdown.length) {
        //Guarde una referencia para que podamos encontrarlo después de adjuntarlo al cuerpo.
        $container.data('dropdown-menu', $dropdown);
    } else {
        $dropdown = $container.data('dropdown-menu');
    }

    $dropdown.css('top', ($container.offset().top + $container.outerHeight()) + 'px');
    $dropdown.css('left', $container.offset().left + 'px');
    $dropdown.css('position', 'absolute');
    $dropdown.css('display', 'block');
    $dropdown.appendTo('body');
});

$(document).on('hide.bs.dropdown', '#divLista<?php echo $sufijo;?> .table-responsive', function (e) {
    //Ocultar el menú desplegable vinculado a este botón
    $(e.target).data('dropdown-menu').css('display', 'none');
});

function editar<?= $sufijo ?>(idfenologia){
	ViewModal('presentacion/viewFenologiaMant','idfenologia='+idfenologia+'&idcultivo=<?= $idcultivo ?>&accion=MODIFICAR&idopcion=<?= $idopcion ?>&nivel=<?= $nivel + 1 ?>&puedeeditar=<?= $puedeeditar ?>','divmodal<?= $nivel ?>','EDITAR FENOLOGIA - <?= $cultivo ?>');
}

function cambiarEstado<?= $sufijo ?>(idfenologia, estado){
	let msj="";
	if(estado=="A"){msj="¿Esta seguro de anular la fenologia?";}
	if(estado=="N"){msj="¿Esta seguro de activar la fenologia?";}
	if(estado=="E"){msj="¿Esta seguro de eliminar la fenologia?";}
	confirm(msj,'ProcesoCambiarEstado<?= $sufijo ?>("'+idfenologia+'","'+estado+'")');
}

function ProcesoCambiarEstado<?= $sufijo ?>(idfenologia,estado){
	$('#divLista<?php echo $sufijo; ?>').LoadingOverlay('show',{
		size: 20,
		maxSize: 40
	});
	$.ajax({
		method: "POST",
		url: 'controlador/contCultivo.php',
		data: {accion: "CAMBIAR_ESTADO_FENOLOGIA",
				'idfenologia': idfenologia,
				'estado': estado,
				'idopcion': <?= $idopcion ?>
			}
	})
	.done(function( text ) {
		evaluarActividadSistema(text);
		$('#divLista<?php echo $sufijo; ?>').LoadingOverlay('hide');
		$.toast({'text': text,'icon': 'success', 'position':'top-right'});	
		verPagina<?php echo $sufijo; ?>(1);
	})
}

$("#ordenable").sortable({ 
    placeholder: "ui-state-highlight", 
    update: function(){ 
        var ordenElementos = $(this).sortable("toArray").toString(); 
        datax = [];

        datax.push({name: "accion",value: "ORDENAR_FENOLOGIA" });		 		
        datax.push({name: "ordenElementos",value: ordenElementos });		 		
        $.ajax({
            method: "POST",
            url: 'controlador/contCultivo.php',
            data: datax 
        })
        .done(function( text ) {
            evaluarActividadSistema(text);
            if(text.substring(0,3)!="***"){
                verPagina<?php echo $sufijo;?>(1);
            	$.toast({'text': text,'icon': 'success', 'position':'top-right'});
            }else{
               $.toast({'text': text,'icon': 'error', 'position':'top-right'}); 
            }
        });
    } 
});
$(function () {
    // jQuery UI sortable for the todo list
    $('.todo-list').sortable({
    placeholder         : 'sort-highlight',
    handle              : '.handle',
    forcePlaceholderSize: true,
    zIndex              : 999999
    });

});

xAxisData = <?php echo json_encode($xAxis); ?>;
seriesData = <?php echo json_encode($valoreskc); ?>;
// Data retrieved https://en.wikipedia.org/wiki/List_of_cities_by_average_temperature
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
        text: '<strong>Cultivo:</strong> '+'<?= $cultivo ?>'
    },
    xAxis: {
        categories: xAxisData,
        accessibility: {
            description: 'Valor Kc'
        }
    },
    yAxis: {
        title: {
            text: null
        },
        labels: {
            format: '{value}'
        },
        gridLineColor: 'rgba(0, 0, 0, 0.07)'
    },
    tooltip: {
        crosshairs: true,
        shared: true
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
        name: 'Kc',
        marker: {
            symbol: 'square'
        },
        data: seriesData

    }],
    credits: {
        enabled: false
    },
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
        link.download = '<?= 'FENOLOGIA_'.str_replace(" ","_",$cultivo).'.png' ?>';
        link.click();
    };
    img.src = url;
}

function saveImage<?= $sufijo ?>() {
    $('#divmodal<?= $nivel-1 ?>Contenido').LoadingOverlay('show',{
        size: 20,
        maxSize: 40
    });
    // Obtener el SVG del gráfico
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
        let pngBase64 = canvas.toDataURL('image/png');

        // Enviar la imagen en formato base64 al servidor PHP
        let datax = [];
        datax.push({name: "accion",value:"GUARDAR_IMAGEN_SERVIDOR"});
        datax.push({name: "sufijo",value:"<?= $sufijo ?>"});
        datax.push({name: "id",value:"<?= $idcultivo ?>"});
        datax.push({name: "pngBase64",value:pngBase64});
        $.ajax({
            method: "POST",
            url: 'controlador/contCultivo.php',
            data: datax
        })
        .done(function( text ) {
            $('#divmodal<?= $nivel-1 ?>Contenido').LoadingOverlay('hide');
            evaluarActividadSistema(text);
            if(text.substring(0,3)!="***"){
                window.open('presentacion/pdfFormatoCultivoFenologia.php?idcultivo=<?= $idcultivo ?>','_blank', 'width = 700, height = 500');        
            }else{
                $.toast({'text': text,'icon': 'error', 'position':'top-right'});
            }
        });
        URL.revokeObjectURL(url);
    };
    img.src = url;
}


</script>