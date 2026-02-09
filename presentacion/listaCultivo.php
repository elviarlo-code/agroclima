<?php
require_once('../logica/clsCultivo.php');
$objCul = new clsCultivo();

$sufijo = "cultivo";
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

$data=$objCul->consultarCultivo($_GET['nombre'], $_GET['variedad'], $_GET['estado'], $inicio, $cantidad, false);
$total_datos=$objCul->consultarCultivo($_GET['nombre'], $_GET['variedad'], $_GET['estado'], $inicio, $cantidad, true);
	
$total_pag=ceil($total_datos/$cantidad);
?>
<div class="table-responsive">
	<table class="table table-bordered table-vertical-center table-hover table-sm font-size-sm">
	    <thead class="thead-light">
	        <tr>
	            <th scope="col">#</th>
	            <th scope="col">Imagen</th>
	            <th scope="col">Nombre</th>
	            <th scope="col">Variedad</th>
                <th scope="col">Altura</th>
                <th scope="col">Periodo Veg.</th>
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
					$foto = "assets/media/users/hoja1.jpg";
					if($fila['imagen']!=""){
						$foto = 'files/imagenes/cultivos/'.$fila['imagen'];
					}

	    	?>
	        <tr class="<?= $class ?>">
	            <td class="font-weight-boldest"><?= $fila['idcultivo'] ?></td>
	            <td class="w-xl-150px">
	            	<div class="symbol symbol-50 symbol-lg-120">
                    	<img src="<?= $foto ?>"></img>
                	</div>
                </td>
                <td><?= $fila['nombre'] ?></td>
	            <td><?= $fila['variedad'] ?></td>
	            <td><?= $fila['altura'] ?></td>
                <td><?= $fila['periodovegetativo'] ?></td>
	            <td><?php if($fila['estado']=='N'){echo "ACTIVO";}else{echo "ANULADO";};?></td>
	            <td>
	            	<button type="button" class="btn btn-light-primary font-weight-bold btn-sm" data-toggle="tooltip" data-placement="top" title="Editar Cultivo" href="javascript:void(0)" onclick="editarCultivo('<?php echo $fila['idcultivo'];?>')" ><li class="fa fa-edit"></li></button>
	            </td>
	            <td class="text-center">
	            	<button type="button" class="btn btn-light-success font-weight-bold btn-sm" href="javascript:void(0)" onclick="fenologia<?= $sufijo ?>('<?php echo $fila['idcultivo'];?>','<?= htmlentities($fila['nombre']) ?>')" >Fenologia</button>
	            </td>
	            <td>
				    <div class="dropdown">
						<a href="#" class="btn btn-light-info font-weight-bold dropdown-toggle btn-sm" data-toggle="dropdown" aria-expanded="false">Opciones</a>
						<div class="dropdown-menu dropdown-menu-sm">
							<ul class="navi">
								<?php if($puedeanular==1){ ?>
								<?php if($fila['estado']=='N'){ ?>
								<li class="navi-item">
									<a class="navi-link" href="#" onclick="cambiarEstadoCultivo(<?= $fila['idcultivo'] ?>,'A')">
										<span class="navi-icon">
											<i class="flaticon-close text-warning"></i>
										</span>
										<span class="navi-text">Anular</span>
									</a>
								</li>
								<?php }else if($fila['estado']=='A'){ ?>
								<li class="navi-item">
									<a class="navi-link" href="#" onclick="cambiarEstadoCultivo(<?= $fila['idcultivo'] ?>,'N')">
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
									<a class="navi-link" href="#" onclick="cambiarEstadoCultivo(<?= $fila['idcultivo'] ?>,'E')">
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

function editarCultivo(idcultivo){
	ViewModal('presentacion/mantCultivo','idcultivo='+idcultivo+'&accion=MODIFICAR&nivel=<?= $nivel + 1 ?>&idopcion=<?= $idopcion ?>&puedeeditar=<?= $puedeeditar ?>','divmodal1','Edición de Cultivo');
}

function cambiarEstadoCultivo(idcultivo, estado){
	let msj="";
	if(estado=="A"){msj="¿Esta seguro de anular el cultivo?";}
	if(estado=="N"){msj="¿Esta seguro de activar el cultivo?";}
	if(estado=="E"){msj="¿Esta seguro de eliminar el cultivo?";}
	confirm(msj,'ProcesoCambiarEstadoCultivo("'+idcultivo+'","'+estado+'")');
}

function ProcesoCambiarEstadoCultivo(idcultivo,estado){
	$('#divLista<?php echo $sufijo; ?>').LoadingOverlay('show',{
		size: 20,
		maxSize: 40
	});
	$.ajax({
		method: "POST",
		url: 'controlador/contCultivo.php',
		data: {accion: "CAMBIAR_ESTADO_CULTIVO",
				'idcultivo': idcultivo,
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

function fenologia<?= $sufijo ?>(idcultivo, cultivo){
	ViewModal('presentacion/viewFenologiaAdmin','nivel=<?= $nivel + 1 ?>&idopcion=<?= $idopcion ?>&idcultivo='+idcultivo,'divmodal<?= $nivel ?>','FENOLOGIA - '+cultivo);
}
</script>