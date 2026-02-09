<?php
require_once('../logica/clsPerfil.php');
$objPer = new clsPerfil();

$sufijo = "perfil";
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

$data=$objPer->consultarPerfil($_GET['descripcion'], $_GET['estado'], $inicio, $cantidad);
$total_datos=$objPer->consultarPerfil($_GET['descripcion'], $_GET['estado'], $inicio, $cantidad, true);
	
$total_pag=ceil($total_datos/$cantidad);
?>
<div class="table-responsive">
	<table class="table table-bordered table-vertical-center table-hover table-sm font-size-sm">
	    <thead class="thead-light">
	        <tr>
	            <th scope="col">#</th>
	            <th scope="col">Descripción</th>
	            <th scope="col">Estado</th>
	            <th scope="col" colspan="4">Opciones</th>
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
	            <td class="font-weight-boldest"><?= $fila['idperfil'] ?></td>
	            <td><?= $fila['descripcion'] ?></td>
	            <td><?php if($fila['estado']=='N'){echo "ACTIVO";}else{echo "ANULADO";};?></td>
	            <td class="text-center w-xl-70px">
	            	<button type="button" class="btn btn-light-primary font-weight-bold btn-sm" data-toggle="tooltip" data-placement="top" title="Editar Perfil" href="javascript:void(0)" onclick="editar<?= $sufijo ?>('<?php echo $fila['idperfil'];?>')" ><li class="fa fa-edit"></li></button>
	            </td>
	            <td class="text-center w-xl-125px">
	            	<button type="button" class="btn btn-light-warning font-weight-bold btn-sm" href="javascript:void(0)" onclick="permiso<?= $sufijo ?>('<?php echo $fila['idperfil'];?>')" >Permisos</button>
	            </td>
	            <td class="text-center w-xl-125px">
	            	<button type="button" class="btn btn-light-success font-weight-bold btn-sm" href="javascript:void(0)" onclick="sucursal<?= $sufijo ?>('<?php echo $fila['idperfil'];?>')" >Sucursal</button>
	            </td>
	            <td class="text-center w-xl-125px">
				    <div class="dropdown">
						<a href="#" class="btn btn-light-info font-weight-bold dropdown-toggle btn-sm" data-toggle="dropdown" aria-expanded="false">Opciones</a>
						<div class="dropdown-menu dropdown-menu-sm" style="">
							<ul class="navi">
								<?php if($puedeanular==1){ ?>
								<?php if($fila['estado']=='N'){ ?>
								<li class="navi-item">
									<a class="navi-link" href="#" onclick="cambiarEstado<?= $sufijo ?>(<?= $fila['idperfil'] ?>,'A')">
										<span class="navi-icon">
											<i class="flaticon-close text-warning"></i>
										</span>
										<span class="navi-text">Anular</span>
									</a>
								</li>
								<?php }else if($fila['estado']=='A'){ ?>
								<li class="navi-item">
									<a class="navi-link" href="#" onclick="cambiarEstado<?= $sufijo ?>(<?= $fila['idperfil'] ?>,'N')">
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
									<a class="navi-link" href="#" onclick="cambiarEstado<?= $sufijo ?>(<?= $fila['idperfil'] ?>,'E')">
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

function editar<?= $sufijo ?>(idperfil){
	ViewModal('presentacion/mantPerfil','idperfil='+idperfil+'&accion=MODIFICAR&idopcion=<?= $idopcion ?>&nivel=<?= $nivel + 1 ?>&puedeeditar=<?= $puedeeditar ?>','divmodalmediano','Edición de Perfil');
}

function cambiarEstado<?= $sufijo ?>(idperfil, estado){
	let msj="";
	if(estado=="A"){msj="¿Esta seguro de anular el perfil?";}
	if(estado=="N"){msj="¿Esta seguro de activar el perfil?";}
	if(estado=="E"){msj="¿Esta seguro de eliminar el perfil?";}
	confirm(msj,'ProcesoCambiarEstado<?= $sufijo ?>("'+idperfil+'","'+estado+'")');
}

function ProcesoCambiarEstado<?= $sufijo ?>(idperfil,estado){
	$('#divLista<?php echo $sufijo; ?>').LoadingOverlay('show',{
		size: 20,
		maxSize: 40
	});
	$.ajax({
		method: "POST",
		url: 'controlador/contPerfil.php',
		data: {accion: "CAMBIAR_ESTADO_PERFIL",
				'idperfil': idperfil,
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

function permiso<?= $sufijo ?>(idperfil){
	ViewModal('presentacion/viewAsignarPermiso','idperfil='+idperfil+'&idopcion=<?= $idopcion ?>&nivel=<?= $nivel + 1 ?>','divmodal1','Asignación de Permisos');
}

function sucursal<?= $sufijo ?>(idperfil){
	ViewModal('presentacion/viewAsignarSucursal','idperfil='+idperfil+'&idopcion=<?= $idopcion ?>&nivel=<?= $nivel + 1 ?>','divmodal1','Asignación de Sucursal');
}
</script>