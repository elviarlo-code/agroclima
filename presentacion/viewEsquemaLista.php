<?php
require_once('../logica/clsTerreno.php');
require_once('../logica/clsCompartido.php');
$objTerr = new clsTerreno();

$sufijo = "esquema";
$nivel = 1;
if(isset($_GET['nivel'])){
	$nivel = $_GET['nivel'];
}

$idterreno = $_GET['idterreno'];
$terreno = htmlentities($_GET['terreno']);
$esquema = $_GET['nombre'];

$idopcion = (isset($_GET['idopcion']))? $_GET['idopcion']:0;
$puedeeditar = (isset($_GET['editar']))? $_GET['editar']:1;
$puedeanular = (isset($_GET['anular']))? $_GET['anular']:1;
$puedeeliminar = (isset($_GET['eliminar']))? $_GET['eliminar']:1;
$permiso_especial = (isset($_GET['permiso_especial']))? $_GET['permiso_especial']:1;
$permiso_especial1 = (isset($_GET['permiso_especial1']))? $_GET['permiso_especial1']:1;
$permiso_especial2 = (isset($_GET['permiso_especial2']))? $_GET['permiso_especial2']:1;

$data=$objTerr->consultarEsquema($idterreno, $esquema);
$data=$data->fetchAll(PDO::FETCH_NAMED); 

?>
<div class="table-responsive">
	<table class="table table-bordered table-vertical-center table-hover table-sm font-size-sm">
	    <thead class="thead-light">
	        <tr>
	            <th scope="col">Esquema</th>
	            <th scope="col">Estado</th>
	            <th scope="col" colspan="5">Opciones</th>
	        </tr>
	    </thead>
	    <tbody>
	    	<?php foreach($data as $kx=>$fila){ 
	    			$label="label-success";
	    			$estado = "ACTIVADO";
	    			if($fila['activo']==0){
						$label="label-danger";
						$estado = "DESACTIVADO";
					}

	    	?>
	        <tr id="<?php echo $fila['idesquema']; ?>">
	            <td><?= $fila['descripcion'] ?></td>
	            <td>
	            	<span class="label label-lg <?= $label ?> label-inline mr-2"><?= $estado ?></span>
	            </td>
	            <td>
	            	<?php if($fila['activo']==0){ ?>
	            	<button type="button" class="btn btn-light-info font-weight-bold btn-sm" data-toggle="tooltip" data-placement="top" title="" href="javascript:void(0)" onclick="habilitar<?= $sufijo ?>('<?php echo $fila['idesquema'];?>',1)" ><i class="far fa-check-circle"></i> Activar</button>
	            	<?php }else{ ?>
	            	<button type="button" class="btn btn-light-warning font-weight-bold btn-sm" data-toggle="tooltip" data-placement="top" title="" href="javascript:void(0)" onclick="habilitar<?= $sufijo ?>('<?php echo $fila['idesquema'];?>',0)" ><i class="fas fa-times-circle"></i> Desactivar</button>
	            	<?php } ?>
	            </td>
	            <td>
	            	<button type="button" class="btn btn-light-primary font-weight-bold btn-sm" data-toggle="tooltip" data-placement="top" title="Editar Esquema" href="javascript:void(0)" onclick="editar<?= $sufijo ?>('<?php echo $fila['idesquema'];?>')" ><i class="fa fa-edit"></i> Edit</button>
	            </td>
	            <td>
	            	<button type="button" class="btn btn-light-dark font-weight-bold btn-sm" data-toggle="tooltip" data-placement="top" title="Ver Turnos" href="javascript:void(0)" onclick="viewTurnos<?= $sufijo ?>('<?php echo $fila['idesquema'];?>')" ><i class="far fa-clone"></i> Tur</button>
	            </td>
	            <td>
	            	<button type="button" class="btn btn-light-dark font-weight-bold btn-sm" data-toggle="tooltip" data-placement="top" title="Ver Mapa" href="javascript:void(0)" onclick="viewMapa<?= $sufijo ?>('<?php echo $fila['idesquema'];?>', <?php echo $fila['latitud'];?>, <?php echo $fila['longitud'];?>)" ><i class="far fa-clone"></i> Map</button>
	            </td>
	            <td>
	            	<button type="button" class="btn btn-light-danger font-weight-bold btn-sm" data-toggle="tooltip" data-placement="top" title="Eliminar Esquema" href="javascript:void(0)" onclick="cambiarEstado<?= $sufijo ?>('<?php echo $fila['idesquema'];?>','E')" ><i class="fa fa-times"></i> Elim</button>
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

function editar<?= $sufijo ?>(idesquema){
	ViewModal('presentacion/viewEsquemaMant','idesquema='+idesquema+'&idterreno=<?= $idterreno ?>&accion=MODIFICAR&idopcion=<?= $idopcion ?>&nivel=<?= $nivel + 1 ?>&puedeeditar=<?= $puedeeditar ?>','divmodalmediano','EDITAR ESQUEMA');
}

function viewTurnos<?= $sufijo ?>(idesquema){
	ViewModal('presentacion/adminTurno','idesquema='+idesquema+'&idterreno=<?= $idterreno ?>&accion=MODIFICAR&buscar=1&idopcion=<?= $idopcion ?>&nivel=<?= $nivel + 1 ?>&puedeeditar=<?= $puedeeditar ?>','divmodal<?= $nivel ?>','LISTADO DE TURNO',0);
}

function viewMapa<?= $sufijo ?>(idesquema, lat, lng){
	ViewModal('presentacion/viewEsquemaMapa','idesquema='+idesquema+'&idterreno=<?= $idterreno ?>&latitud='+lat+'&longitud='+lng+'&accion=MODIFICAR&buscar=1&idopcion=<?= $idopcion ?>&nivel=<?= $nivel + 1 ?>&puedeeditar=<?= $puedeeditar ?>','divmodal<?= $nivel ?>','ESQUEMA HIDRÁULICO',0);
}

function cambiarEstado<?= $sufijo ?>(idesquema, estado){
	let msj="";
	if(estado=="A"){msj="¿Esta seguro de anular el esquema?";}
	if(estado=="N"){msj="¿Esta seguro de activar el esquema?";}
	if(estado=="E"){msj="¿Esta seguro de eliminar el esquema?";}
	confirm(msj,'ProcesoCambiarEstado<?= $sufijo ?>("'+idesquema+'","'+estado+'")');
}

function ProcesoCambiarEstado<?= $sufijo ?>(idesquema,estado){
	$('#divLista<?php echo $sufijo; ?>').LoadingOverlay('show',{
		size: 20,
		maxSize: 40
	});
	$.ajax({
		method: "POST",
		url: 'controlador/contTerreno.php',
		data: {accion: "CAMBIAR_ESTADO_ESQUEMA",
				'idesquema': idesquema,
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

function habilitar<?= $sufijo ?>(idesquema,activo){
	let msj="";
	if(activo==0){msj="¿Esta seguro de desactivar el esquema?";}
	if(activo==1){msj="¿Esta seguro de activar el esquema?";}
	confirm(msj,'ProcesoHabilitar<?= $sufijo ?>("'+idesquema+'","'+activo+'")');
}

function ProcesoHabilitar<?= $sufijo ?>(idesquema,activo){
	$('#divLista<?php echo $sufijo; ?>').LoadingOverlay('show',{
		size: 20,
		maxSize: 40
	});
	$.ajax({
		method: "POST",
		url: 'controlador/contTerreno.php',
		data: {accion: "ACTIVAR_DESACTIVAR_ESQUEMA",
				'idesquema': idesquema,
				'idterreno': <?= $idterreno ?>,
				'activo': activo,
				'idopcion': <?= $idopcion ?>
			}
	})
	.done(function( text ) {
		evaluarActividadSistema(text);
		$('#divLista<?php echo $sufijo; ?>').LoadingOverlay('hide');
		if(text.substring(0,3)!="***"){
			$.toast({'text': text,'icon': 'success', 'position':'top-right'});	
			verPagina<?php echo $sufijo; ?>(1);		
		}else{
			$.toast({'text': text,'icon': 'error', 'position':'top-right'});
		}
	})
}

</script>