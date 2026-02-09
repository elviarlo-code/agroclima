<?php
require_once('../logica/clsPersona.php');
require_once('../logica/clsCompartido.php');
$objPer = new clsPersona();

$sufijo = "directorio";
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

$filtro=$_GET['nombre'];
$data=$objPer->consultarPersona($_GET['nombre'], $_GET['documento'], $_GET['tipo'], $_GET['estado'], $inicio, $cantidad);
$total_datos=$objPer->consultarPersona($_GET['nombre'], $_GET['documento'], $_GET['tipo'], $_GET['estado'], $inicio, $cantidad, true);
	
$total_pag=ceil($total_datos/$cantidad);
?>
<div class="table-responsive">
	<table class="table table-bordered table-vertical-center table-hover table-sm font-size-sm">
	    <thead class="thead-light">
	        <tr>
	            <th scope="col">#</th>
	            <th scope="col">Documento</th>
	            <th scope="col">Nombre</th>
	            <th scope="col">Telefono</th>
	            <th scope="col">Dirección</th>
	            <th scope="col">Observación</th>
	            <th scope="col">Estado</th>
	            <th scope="col" colspan="2">Opciones</th>
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
	            <td class="font-weight-boldest"><?= $fila['idpersona'] ?></td>
	            <td><?= $fila['nro_documento'] ?></td>
	            <td><?= trim($fila['nombres'].' '.$fila['apellidos']) ?></td>
	            <td><?= $fila['telcelular'] ?></td>
	            <td><?= $fila['direccion'] ?></td>
	            <td><?= $fila['observacion'] ?></td>
	            <td><?php if($fila['estado']=='N'){echo "ACTIVO";}else{echo "ANULADO";};?></td>
	            <td>
	            	<button type="button" class="btn btn-light-primary font-weight-bold btn-sm" data-toggle="tooltip" data-placement="top" title="Editar Persona" href="javascript:void(0)" onclick="editar<?= $sufijo ?>('<?php echo $fila['idpersona'];?>')" ><li class="fa fa-edit"></li></button>
	            </td>
	            <td>
	            	<?php if($buscar==0){ ?>
				    <div class="dropdown">
						<a href="#" class="btn btn-light-info font-weight-bold dropdown-toggle btn-sm" data-toggle="dropdown" aria-expanded="false">Opciones</a>
						<div class="dropdown-menu dropdown-menu-sm" style="">
							<ul class="navi">
								<?php if($puedeanular==1){ ?>
								<?php if($fila['estado']=='N'){ ?>
								<li class="navi-item">
									<a class="navi-link" href="#" onclick="cambiarEstado<?= $sufijo ?>(<?= $fila['idpersona'] ?>,'A')">
										<span class="navi-icon">
											<i class="flaticon-close text-warning"></i>
										</span>
										<span class="navi-text">Anular</span>
									</a>
								</li>
								<?php }else if($fila['estado']=='A'){ ?>
								<li class="navi-item">
									<a class="navi-link" href="#" onclick="cambiarEstado<?= $sufijo ?>(<?= $fila['idpersona'] ?>,'N')">
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
									<a class="navi-link" href="#" onclick="cambiarEstado<?= $sufijo ?>(<?= $fila['idpersona'] ?>,'E')">
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
					<?php }else{ ?>
						<button type="button" class="btn btn-sm btn-light-primary font-weight-bold" onclick="SeleccionarDirectorio(`<?php echo $fila['idpersona']; ?>`,`<?php echo $fila['tipo_documento']?>`,`<?php echo $fila['nro_documento']?>`,`<?php echo htmlspecialchars(isset($fila['nombres'])?$fila['nombres']:'');?>`,`<?php echo htmlspecialchars($fila['apellidos']);?>`,`<?php echo htmlspecialchars(isset($fila['razon_social'])?$fila['razon_social']:'');?>`,`<?php echo htmlspecialchars(quitarSaltoLinea($fila['direccion']));?>`,`<?php echo $fila['telcelular']?>`,`<?php echo $fila['email']?>`)" > Seleccionar</button>
					<?php } ?>
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

function editar<?= $sufijo ?>(idpersona){
	ViewModal('presentacion/mantDirectorio','idpersona='+idpersona+'&accion=MODIFICAR&idopcion=<?= $idopcion ?>&nivel=<?= $nivel + 1 ?>&puedeeditar=<?= $puedeeditar ?>','divmodal<?= $nivel ?>','Edición de Directorio');
}

// function cambiarEstado<?= $sufijo ?>(idpersona,estado){
// 	let msj="";
// 	if(estado=="A"){msj="¿Esta seguro de anular la persona?";}
// 	if(estado=="N"){msj="¿Esta seguro de activar la persona?";}
// 	if(estado=="E"){msj="¿Esta seguro de eliminar la persona?";}
// 	Swal.fire({
// 		title: "MENSAJE DE CONFIRMACIÓN",
// 		text: msj,
// 		icon: "question",
// 		buttonsStyling: false,
// 		confirmButtonText: "<i class='fas fa-check'></i> Aceptar",
// 		showCancelButton: true,
// 		cancelButtonText: "<i class='fas fa-times'></i> Cancelar",
// 		customClass: {
// 			confirmButton: "btn btn-success",
// 			cancelButton: "btn btn-danger"
// 		}
// 	}).then((result) => {
// 		/* Read more about isConfirmed, isDenied below */
// 		if (result.isConfirmed) {
// 		    ProcesoCambiarEstado<?= $sufijo ?>(idpersona,estado);
// 		}
// 	});
// }

function cambiarEstado<?= $sufijo ?>(idpersona, estado){
	let msj="";
	if(estado=="A"){msj="¿Esta seguro de anular la persona?";}
	if(estado=="N"){msj="¿Esta seguro de activar la persona?";}
	if(estado=="E"){msj="¿Esta seguro de eliminar la persona?";}
	confirm(msj,'ProcesoCambiarEstado<?= $sufijo ?>("'+idpersona+'","'+estado+'")');
}

function ProcesoCambiarEstado<?= $sufijo ?>(idpersona,estado){
	$('#divLista<?php echo $sufijo; ?>').LoadingOverlay('show',{
		size: 20,
		maxSize: 40
	});
	$.ajax({
		method: "POST",
		url: 'controlador/contPersona.php',
		data: {accion: "CAMBIAR_ESTADO_PERSONA",
				'idpersona': idpersona,
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
</script>