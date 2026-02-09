<?php
require_once('../logica/clsCampania.php');
require_once('../logica/clsCompartido.php');
$objCam = new clsCampania();

$sufijo = "campania";
$nivel = 1;
if(isset($_GET['nivel'])){
	$nivel = $_GET['nivel'];
}

$buscar=0;
if(isset($_GET['buscar'])){
	$buscar = $_GET['buscar'];
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

$data=$objCam->consultarCampania($_GET['desde'], $_GET['hasta'], $_GET['nombre'], $_GET['idterreno'], $_GET['idesquema'], $_GET['estado'], $inicio, $cantidad);
$total_datos=$objCam->consultarCampania($_GET['desde'], $_GET['hasta'], $_GET['nombre'], $_GET['idterreno'], $_GET['idesquema'], $_GET['estado'], $inicio, $cantidad, true);
	
$total_pag=ceil($total_datos/$cantidad);
?>
<div class="table-responsive">
	<table class="table table-bordered table-vertical-center table-hover table-sm font-size-sm">
	    <thead class="thead-light">
	        <tr>
	            <th scope="col">#</th>
	            <th scope="col">Campaña</th>
	            <th scope="col">Inicio</th>
	            <th scope="col">Termino</th>
	            <th scope="col">Cultivo</th>
	            <th scope="col">Turno</th>
	            <th scope="col">Estado</th>
	            <th scope="col" colspan="3">Opciones</th>
	        </tr>
	    </thead>
	    <tbody>
	    	<?php while($fila = $data->fetch(PDO::FETCH_NAMED)){ 
	    			$class="";
	    			if($fila['estado']=='A'){
						$class="text-danger";
					}

	    	?>
	        <tr class="<?= $class ?>">
	            <td class="font-weight-boldest"><?= $fila['idcampania'] ?></td>
	            <td>
	            	<div>
						<span class="font-weight-bolder"><?= $fila['descripcion'] ?></span>
					</div>
	            	<div>
						<span class="font-weight-bolder">Terreno:</span>
						<span class="text-muted font-weight-bold"><?= $fila['terreno'] ?></span>
					</div>
					<div>
						<span class="font-weight-bolder">Esquema:</span>
						<span class="text-muted font-weight-bold"><?= $fila['esquema'] ?></span>
					</div>
					<div>
						<span class="font-weight-bolder">Turno:</span>
						<span class="text-muted font-weight-bold"><?= $fila['turno'] ?></span>
					</div>
					<div>
						<span class="font-weight-bolder">Área:</span>
						<span class="text-muted font-weight-bold"><?= $fila['area'] ?> m<sup>2</sup></span>
					</div>
	            </td>
	            <td><?= formatoCortoFecha($fila['fechaini']) ?></td>
	            <td><?= formatoCortoFecha($fila['fechafin']) ?></td>
	            <td><?= $fila['cultivo'] ?></td>
	            <td><?= $fila['turno'] ?></td>
	            <td><?php if($fila['finalizado']==0){echo "EN PROCESO";}else{echo "FINALIZADO";};?></td>
	            <td>
	            	<button type="button" class="btn btn-light-primary font-weight-bold btn-sm" data-toggle="tooltip" data-placement="top" title="Editar Campaña" href="javascript:void(0)" onclick="editar<?= $sufijo ?>('<?php echo $fila['idcampania'];?>')" ><li class="fa fa-edit"></li></button>
	            </td>
	            <td class="text-center">
	            	<button type="button" class="btn btn-light-success font-weight-bold btn-sm" href="javascript:void(0)" onclick="verCampania<?= $sufijo ?>('<?php echo $fila['idcampania'];?>')" >Ver Campaña</button>
	            </td>
	            <td>
				    <div class="dropdown">
						<a href="#" class="btn btn-light-info font-weight-bold dropdown-toggle btn-sm" data-toggle="dropdown" aria-expanded="false">Opciones</a>
						<div class="dropdown-menu dropdown-menu-sm" style="">
							<ul class="navi">
								<?php if($puedeanular==1){ ?>
								<?php if($fila['estado']=='N'){ ?>
								<li class="navi-item">
									<a class="navi-link" href="#" onclick="cambiarEstado<?= $sufijo ?>(<?= $fila['idcampania'] ?>,'A')">
										<span class="navi-icon">
											<i class="flaticon-close text-warning"></i>
										</span>
										<span class="navi-text">Anular</span>
									</a>
								</li>
								<?php }else if($fila['estado']=='A'){ ?>
								<li class="navi-item">
									<a class="navi-link" href="#" onclick="cambiarEstado<?= $sufijo ?>(<?= $fila['idcampania'] ?>,'N')">
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
									<a class="navi-link" href="#" onclick="cambiarEstado<?= $sufijo ?>(<?= $fila['idcampania'] ?>,'E')">
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
	$fin=$inicio+$data->rowCount();
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

function editar<?= $sufijo ?>(idcampania){
	ViewModal('presentacion/mantCampania','idcampania='+idcampania+'&accion=MODIFICAR&idopcion=<?= $idopcion ?>&buscar=<?= $buscar ?>&nivel=<?= $nivel + 1 ?>&puedeeditar=<?= $puedeeditar ?>','divmodal<?= $nivel ?>','Edición de Campaña');
}


function cambiarEstado<?= $sufijo ?>(idcampania, estado){
	let msj="";
	if(estado=="A"){msj="¿Esta seguro de anular la Campaña?";}
	if(estado=="N"){msj="¿Esta seguro de activar la Campaña?";}
	if(estado=="E"){msj="¿Esta seguro de eliminar la Campaña?";}
	confirm(msj,'ProcesoCambiarEstado<?= $sufijo ?>("'+idcampania+'","'+estado+'")');
}

function ProcesoCambiarEstado<?= $sufijo ?>(idcampania,estado){
	$('#divLista<?php echo $sufijo; ?>').LoadingOverlay('show',{
		size: 20,
		maxSize: 40
	});
	$.ajax({
		method: "POST",
		url: 'controlador/contCampania.php',
		data: {accion: "CAMBIAR_ESTADO_CAMPANIA",
				'idcampania': idcampania,
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


function verCampania<?= $sufijo ?>(idcampania, loading=1){
	
	let estado = 'N';
	let nivel = <?= $nivel + 1 ?>;

	let filtro='estado='+estado+'&idcampania='+idcampania+'&nivel='+nivel;
	let extrapermiso = '&idopcion=<?= $idopcion ?>';

	setRun('presentacion/viewCampania',filtro+extrapermiso,'contenedorPrincipal','contenedorPrincipal',loading);	
}
</script>