<?php
require_once('../logica/clsDispositivo.php');
$objDis = new clsDispositivo();

$sufijo = "dispositivo";
$nivel = 1;
if(isset($_GET['nivel'])){
	$nivel = $_GET['nivel'];
}

if(!isset($_GET['cantidad'])){
	$pagina=1;
	$inicio=0;
	$cantidad=12;
}else{
	$pagina=$_GET['pagina'];
	$cantidad=$_GET['cantidad'];
	$inicio=($pagina-1)*$cantidad;
}

$idopcion = (isset($_GET['idopcion']))? $_GET['idopcion']:0;
$puedeeditar = (isset($_GET['editar']))? $_GET['editar']:1;
$puedeanular = (isset($_GET['anular']))? $_GET['anular']:1;
$puedeeliminar = (isset($_GET['eliminar']))? $_GET['eliminar']:1;
$permiso_especial = (isset($_GET['permiso_especial']))? $_GET['permiso_especial']:1;
$permiso_especial1 = (isset($_GET['permiso_especial1']))? $_GET['permiso_especial1']:1;
$permiso_especial2 = (isset($_GET['permiso_especial2']))? $_GET['permiso_especial2']:1;

$data=$objDis->consultarDispositivo($_GET['nombre'], $_GET['estado'], $inicio, $cantidad, false);
$data=$data->fetchAll(PDO::FETCH_NAMED);
$total_datos=$objDis->consultarDispositivo($_GET['nombre'], $_GET['estado'], $inicio, $cantidad, true);
	
$total_pag=ceil($total_datos/$cantidad);
?>
<style>
	div[id^="map-"] {
    	width: 300px;
    	height: 200px;
	}
</style>
<div class="table-responsive">
	<table class="table table-bordered table-vertical-center table-hover table-sm font-size-sm">
	    <thead class="thead-light">
	        <tr>
	            <th scope="col">#</th>
	            <th scope="col">Descripción</th>
	            <th scope="col">Tipo</th>
	            <th scope="col">Ubigeo</th>
	            <th scope="col">Estado</th>
	            <th scope="col" colspan="4">Opciones</th>
	        </tr>
	    </thead>
	    <tbody>
	    	<?php foreach($data as $indice=>$fila){
	    			$class="";
	    			if($fila['estado']=='A'){
						$class="text-danger";
					}

	    	?>
	        <tr class="<?= $class ?>">
	            <td class="font-weight-boldest"><?= $fila['iddispositivo'] ?></td>
	            <td>
	            	<div>
						<span class="font-weight-bolder"><?= $fila['nombre'] ?></span>
					</div>
	            	<div>
						<span class="font-weight-bolder">Codigo:</span>
						<span class="text-muted font-weight-bold"><?= $fila['codigo'] ?></span>
					</div>
					<div>
						<span class="font-weight-bolder">Latitud:</span>
						<span class="text-muted font-weight-bold"><?= $fila['latitud'] ?></span>
					</div>
					<div>
						<span class="font-weight-bolder">Longitud:</span>
						<span class="text-muted font-weight-bold"><?= $fila['longitud'] ?></span>
					</div>
	            </td>
	            <td><?= $fila['tipodispositivo'] ?></td>
	            <td><?= $fila['ubigeo_texto'] ?></td>
	            <td><?php if($fila['estado']=='N'){echo "ACTIVO";}else{echo "ANULADO";};?></td>
	            <td>
	            	<button type="button" class="btn btn-light-primary font-weight-bold btn-sm" data-toggle="tooltip" data-placement="top" title="Editar Dispositivo" href="javascript:void(0)" onclick="editarDispositivo('<?php echo $fila['iddispositivo'];?>')" ><li class="fa fa-edit"></li></button>
	            </td>
	            <td class="text-center">
	            	<button type="button" class="btn btn-light-warning font-weight-bold btn-sm" href="javascript:void(0)" onclick="verDatos<?= $sufijo ?>('<?php echo $fila['iddispositivo'];?>','<?= $fila['tipo'] ?>')" >Ver Datos</button>
	            </td>
	            <td class="text-center">
	            	<?php if($fila['tipo']=='DAVIS01'){ ?>
	            	<button type="button" class="btn btn-light-success font-weight-bold btn-sm" href="javascript:void(0)" onclick="importar<?= $sufijo ?>('<?php echo $fila['iddispositivo'];?>')" >Importar</button>
	            	<?php } ?>
	            </td>
	            <td>
				    <div class="dropdown">
						<a href="#" class="btn btn-light-info font-weight-bold dropdown-toggle btn-sm" data-toggle="dropdown" aria-expanded="false">Opciones</a>
						<div class="dropdown-menu dropdown-menu-sm">
							<ul class="navi">
								<?php if($puedeanular==1){ ?>
								<?php if($fila['estado']=='N'){ ?>
								<li class="navi-item">
									<a class="navi-link" href="#" onclick="cambiarEstadoDispositivo(<?= $fila['iddispositivo'] ?>,'A')">
										<span class="navi-icon">
											<i class="flaticon-close text-warning"></i>
										</span>
										<span class="navi-text">Anular</span>
									</a>
								</li>
								<?php }else if($fila['estado']=='A'){ ?>
								<li class="navi-item">
									<a class="navi-link" href="#" onclick="cambiarEstadoDispositivo(<?= $fila['iddispositivo'] ?>,'N')">
										<span class="navi-icon">
											<i class="flaticon2-check-mark text-success"></i>
										</span>
										<span class="navi-text">Activar</span>
									</a>
								</li>
								<?php } ?>
								<?php } ?>
								<?php if($puedeeliminar==1){ ?>
								<li class="navi-item">
									<a class="navi-link" href="#" onclick="cambiarEstadoDispositivo(<?= $fila['iddispositivo'] ?>,'E')">
										<span class="navi-icon">
											<i class="flaticon-delete text-danger"></i>
										</span>
										<span class="navi-text">Eliminar</span>
									</a>
								</li>
								<?php } ?>
							</ul>
						</div>
					</div>
	            </td>
	        </tr>
	    	<?php } ?>
	    </tbody>
	</table>
</div>
<div class="text-right">
<?php 
	require_once('compaginacion.php');
	$fin=$inicio+count($data);
	echo compaginarTabla($pagina,$total_pag,$total_datos,$cantidad,($inicio+1),$fin,$sufijo);
?>
</div>
<script>
$(document).on('shown.bs.dropdown', '#divLista<?php echo $sufijo;?> .table-responsive, .modal .table-responsive', function (e) {
    // El contenedor del dropdown
    var $container = $(e.target);

    // Encontrar el menú desplegable real
    var $dropdown = $container.find('.dropdown-menu');
    if ($dropdown.length) {
        // Guardar una referencia para usarla después al adjuntar al cuerpo
        $container.data('dropdown-menu', $dropdown);
    } else {
        $dropdown = $container.data('dropdown-menu');
    }

    // Ajustar la posición del menú desplegable
    $dropdown.css('top', ($container.offset().top + $container.outerHeight()) + 'px');
    $dropdown.css('left', $container.offset().left + 'px');
    $dropdown.css('position', 'absolute');
    $dropdown.css('display', 'block');
    $dropdown.css('z-index', '1060'); // Asegurar que esté sobre el modal

    // Asegurarse de que el menú desplegable esté sobre el resto del contenido
    $dropdown.appendTo('body');
});

$(document).on('hide.bs.dropdown', '#divLista<?php echo $sufijo;?> .table-responsive, .modal .table-responsive', function (e) {
    // Ocultar el menú desplegable vinculado a este botón
    $(e.target).data('dropdown-menu').css('display', 'none');
});

function editarDispositivo(iddispositivo){
	ViewModal('presentacion/mantDispositivo','iddispositivo='+iddispositivo+'&accion=MODIFICAR&nivel=<?= $nivel + 1 ?>&idopcion=<?= $idopcion ?>&puedeeditar=<?= $puedeeditar ?>','divmodal1','Edición de Dispositivo');
}

function cambiarEstadoDispositivo(iddispositivo, estado){
	let msj="";
	if(estado=="A"){msj="¿Esta seguro de anular el Dispositivo?";}
	if(estado=="N"){msj="¿Esta seguro de activar el Dispositivo?";}
	if(estado=="E"){msj="¿Esta seguro de eliminar el Dispositivo?";}
	confirm(msj,'ProcesoCambiarEstadoDispositivo("'+iddispositivo+'","'+estado+'")');
}

function ProcesoCambiarEstadoDispositivo(iddispositivo,estado){
	$('#divLista<?php echo $sufijo; ?>').LoadingOverlay('show',{
		size: 20,
		maxSize: 40
	});
	$.ajax({
		method: "POST",
		url: 'controlador/contDispositivo.php',
		data: {accion: "CAMBIAR_ESTADO_DISPOSITIVO",
				'iddispositivo': iddispositivo,
				'estado': estado,
				'idopcion': <?= $idopcion ?>
			}
	})
	.done(function( text ) {
		evaluarActividadSistema(text);
		$('#divLista<?php echo $sufijo; ?>').LoadingOverlay('hide');
		$.toast({'text': text,'icon': 'success', 'position':'top-right'});	
		let pagina=document.getElementById('txtNroPaginaFooter<?php echo $sufijo; ?>').value;
		verPagina<?php echo $sufijo; ?>(pagina);
	})
}

function importar<?= $sufijo ?>(iddispositivo){
	ViewModal('presentacion/mantDispositivo_Importar','nivel=<?= $nivel + 1 ?>&idopcion=<?= $idopcion ?>&iddispositivo='+iddispositivo+'&puedeeditar=<?= $puedeeditar ?>','divmodalmediano','IMPORTAR DATOS');
}

function verDatos<?= $sufijo ?>(iddispositivo, tipo){
	console.log(tipo);
	let estado = 'N';
	let nivel = <?= $nivel + 1 ?>;

	let filtro='estado='+estado+'&iddispositivo='+iddispositivo+'&nivel='+nivel;
	let extrapermiso = '&idopcion=<?= $idopcion ?>';

	setRun('presentacion/adminReporteRitecClima',filtro+extrapermiso,'contenedorPrincipal','contenedorPrincipal',0);
}

</script>