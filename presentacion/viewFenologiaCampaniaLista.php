<?php
require_once('../logica/clsCampania.php');
require_once('../logica/clsCompartido.php');
$objCamp = new clsCampania();

$sufijo = "campfeno";
$nivel = 1;
if(isset($_GET['nivel'])){
	$nivel = $_GET['nivel'];
}

$idcultivo = $_GET['idcultivo'];
$idcampania = $_GET['idcampania'];
$cultivo = htmlentities($_GET['cultivo']);
$fenologia = $_GET['nombre'];

$idopcion = (isset($_GET['idopcion']))? $_GET['idopcion']:0;
$puedeeditar = (isset($_GET['editar']))? $_GET['editar']:1;
$puedeanular = (isset($_GET['anular']))? $_GET['anular']:1;
$puedeeliminar = (isset($_GET['eliminar']))? $_GET['eliminar']:1;
$permiso_especial = (isset($_GET['permiso_especial']))? $_GET['permiso_especial']:1;
$permiso_especial1 = (isset($_GET['permiso_especial1']))? $_GET['permiso_especial1']:1;
$permiso_especial2 = (isset($_GET['permiso_especial2']))? $_GET['permiso_especial2']:1;

$data=$objCamp->consultarFenologiaCampania($idcampania, $fenologia);
$data=$data->fetchAll(PDO::FETCH_NAMED); 

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
	ViewModal('presentacion/viewFenologiaCampaniaMant','idfenologia='+idfenologia+'&idcultivo=<?= $idcultivo ?>&idcampania=<?= $idcampania ?>&accion=MODIFICAR&idopcion=<?= $idopcion ?>&nivel=<?= $nivel + 1 ?>&puedeeditar=<?= $puedeeditar ?>','divmodal<?= $nivel ?>','EDITAR FENOLOGIA - <?= $cultivo ?>');
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
		url: 'controlador/contCampania.php',
		data: {accion: "CAMBIAR_ESTADO_FENOLOGIA_CAMPANIA",
				'idfenologia': idfenologia,
				'estado': estado,
				'idopcion': <?= $idopcion ?>,
                'idcampania': <?= $idcampania ?>
			}
	})
	.done(function( text ) {
		evaluarActividadSistema(text);
		$('#divLista<?php echo $sufijo; ?>').LoadingOverlay('hide');

        let estado = 'N';
        let nivel = <?= $nivel - 1 ?>;

        let filtro='estado='+estado+'&idcampania=<?= $idcampania ?>&nivel='+nivel;
        let extrapermiso = '&idopcion=<?= $idopcion ?>';

        setRun('presentacion/viewCampania',filtro+extrapermiso,'contenedorPrincipal','contenedorPrincipal',1);

		$.toast({'text': text,'icon': 'success', 'position':'top-right'});	
		verPagina<?php echo $sufijo; ?>(1);
	})
}

$("#ordenable").sortable({ 
    placeholder: "ui-state-highlight", 
    update: function(){ 
        var ordenElementos = $(this).sortable("toArray").toString(); 
        datax = [];

        datax.push({name: "accion",value: "ORDENAR_FENOLOGIA_CAMPANIA" });		 		
        datax.push({name: "ordenElementos",value: ordenElementos });		 		
        $.ajax({
            method: "POST",
            url: 'controlador/contCampania.php',
            data: datax 
        })
        .done(function( text ) {
            evaluarActividadSistema(text);
            if(text.substring(0,3)!="***"){

                let estado = 'N';
                let nivel = <?= $nivel - 1 ?>;

                let filtro='estado='+estado+'&idcampania=<?= $idcampania ?>&nivel='+nivel;
                let extrapermiso = '&idopcion=<?= $idopcion ?>';

                setRun('presentacion/viewCampania',filtro+extrapermiso,'contenedorPrincipal','contenedorPrincipal',1);
                
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

</script>