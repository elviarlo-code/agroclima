<?php
require_once('../logica/clsCase.php');
require_once('../logica/clsCompartido.php');
$objCase = new clsCase;

$sufijo = "cultivo";

$nivel = 1;
if(isset($_GET['nivel'])){
	$nivel = $_GET['nivel'];
}

$idopcion=0;
if(isset($_GET['idoptx'])){
	$idopcion=$_GET['idoptx'];
}
$opcionMenu = $objCase->getRowTableById("opcion",$idopcion);

$editar = verificarPermiso($opcionMenu["puedeeditar"]);
$anular = verificarPermiso($opcionMenu["puedeanular"]);
$eliminar = verificarPermiso($opcionMenu["puedeeliminar"]);
$permiso_especial = verificarPermiso($opcionMenu["opcion_especial"]);
$permiso_especial1 = verificarPermiso($opcionMenu["opcion_especial1"]);
$permiso_especial2 = verificarPermiso($opcionMenu["opcion_especial2"]);
$permisos["imprimir"]=boolval(verificarPermiso($opcionMenu["puedeimprimir"]));
$permisos["registrar"]=boolval(verificarPermiso($opcionMenu["puederegistrar"]));

?>
<div class="row">
	<div class="col-md-12">
		<div class="card card-custom gutter-b">
			<div class="card-header" style="min-height: 50px">
				<div class="card-title">
					<span class="card-icon">
						<i class="flaticon-shapes text-primary"></i>
					</span>
					<h3 class="card-label">Cultivo
					<small>Busqueda de Cultivo</small></h3>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-4">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text font-weight-bolder">Nombre</span>
							</div>
							<input type="text" name="busquedaNombre<?= $sufijo; ?>" id="busquedaNombre<?= $sufijo; ?>" class="form-control" onKeyUp="if(event.keyCode=='13'){ verPagina<?php echo $sufijo;?>(1); }" autocomplete="off">
						</div>
					</div>
					<div class="col-md-4">
                    <div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text font-weight-bolder">Variedad</span>
							</div>
							<input type="text" name="busquedaVariedad<?= $sufijo; ?>" id="busquedaVariedad<?= $sufijo; ?>" class="form-control" onKeyUp="if(event.keyCode=='13'){ verPagina<?php echo $sufijo;?>(1); }" autocomplete="off">
						</div>
					</div>
					<div class="col-md-4">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text font-weight-bolder">Estado</span>
							</div>
							<select class="form-control" name="busquedaEstado<?= $sufijo ?>" id="busquedaEstado<?= $sufijo ?>" onchange="verPagina<?php echo $sufijo;?>(1)">
								<option value="0">- Todos -</option>
								<option value="N">Activo</option>
								<option value="A">Anulado</option>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<button type="button" class="btn btn-light-primary btn-sm" id="btnBuscar" onclick="verPagina<?php echo $sufijo;?>(1)">
						    <i class="flaticon2-magnifier-tool"></i> Buscar
						</button>
						<?php if($permisos["registrar"]){ ?>
						<button type="button" class="btn btn-light-success btn-sm" onclick="NuevoRegistro<?= $sufijo ?>()">
						    <i class="flaticon2-add-1"></i> Nuevo
						</button>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="card card-custom gutter-b">
			<div class="card-body min-h-xl-400px" id="divLista<?php echo $sufijo;?>">
			</div>
		</div>
	</div>
</div>
<script>

function verPagina<?php echo $sufijo;?>(pagina, loading=1){

	$('#nroPagina<?php echo $sufijo; ?>').val(pagina);
	
	let estado = $('#busquedaEstado<?= $sufijo; ?>').val();
	let nombre = $('#busquedaNombre<?= $sufijo; ?>').val();
	let variedad = $('#busquedaVariedad<?= $sufijo ?>').val();
	let nivel = <?= $nivel ?>;

	let filtro='estado='+estado+'&nombre='+nombre+'&variedad='+variedad+'&nivel='+nivel;

	let extra='';
	if(document.getElementById('cboCantidadBusqueda<?php echo $sufijo;?>')){
		extra='&pagina='+pagina+'&cantidad='+$('#cboCantidadBusqueda<?php echo $sufijo;?>').val();
	}

	let editar='<?= $editar; ?>';
    let anular='<?= $anular; ?>';
    let eliminar='<?= $eliminar; ?>';
    let permiso_especial='<?= $permiso_especial; ?>';
    let permiso_especial1='<?= $permiso_especial1; ?>';
    let permiso_especial2='<?= $permiso_especial2; ?>';

	let extrapermiso = '&editar='+editar+'&anular='+anular+'&eliminar='+eliminar+'&permiso_especial='+permiso_especial+'&permiso_especial1='+permiso_especial1+'&permiso_especial2='+permiso_especial2+'&idopcion=<?= $idopcion ?>';

	setRun('presentacion/listaCultivo',filtro+extra+extrapermiso,'divLista<?php echo $sufijo;?>','divLista<?php echo $sufijo;?>',loading);	
}

verPagina<?php echo $sufijo;?>(1,0);

function NuevoRegistro<?= $sufijo ?>(){
	ViewModal('presentacion/mantCultivo','accion=NUEVO&idopcion=<?= $idopcion ?>&nivel=<?= $nivel + 1 ?>','divmodal1','Registro de Cultivo',0);
}

</script>