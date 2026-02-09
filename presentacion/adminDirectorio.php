<?php
require_once('../logica/clsCase.php');
require_once('../logica/clsCompartido.php');
$objCase = new clsCase;

$sufijo = "directorio";
$nivel = 1;
if(isset($_GET['nivel'])){
	$nivel = $_GET['nivel'];
}

$buscar = 0;
if(isset($_GET['buscar'])){
	$buscar = $_GET['buscar'];
}

$idopcion=0;
if(isset($_GET['idoptx'])){
	$idopcion=$_GET['idoptx'];
}
$opcionMenu = $objCase->getRowTableById("opcion",$idopcion);

$editar = ($opcionMenu!=NULL)? verificarPermiso($opcionMenu["puedeeditar"]):1;
$anular = ($opcionMenu!=NULL)? verificarPermiso($opcionMenu["puedeanular"]):1;
$eliminar = ($opcionMenu!=NULL)? verificarPermiso($opcionMenu["puedeeliminar"]):1;
$permiso_especial = ($opcionMenu!=NULL)? verificarPermiso($opcionMenu["opcion_especial"]):1;
$permiso_especial1 = ($opcionMenu!=NULL)? verificarPermiso($opcionMenu["opcion_especial1"]):1;
$permiso_especial2 = ($opcionMenu!=NULL)? verificarPermiso($opcionMenu["opcion_especial2"]):1;
$permisos["imprimir"]= ($opcionMenu!=NULL)? boolval(verificarPermiso($opcionMenu["puedeimprimir"])):true;
$permisos["registrar"]= ($opcionMenu!=NULL)? boolval(verificarPermiso($opcionMenu["puederegistrar"])):true;

?>
<div class="row">
	<div class="col-md-12">
		<div class="card card-custom gutter-b">
			<?php if($buscar==0){ ?>
			<div class="card-header" style="min-height: 50px">
				<div class="card-title">
					<span class="card-icon">
						<i class="flaticon-folder-1 text-primary"></i>
					</span>
					<h3 class="card-label">Directorio
					<small>Busqueda de Directorio</small></h3>
				</div>
			</div>
			<?php } ?>
			<div class="card-body <?php if($buscar==1){ ?> p-0 <?php } ?>">
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
								<span class="input-group-text font-weight-bolder">DNI/RUC</span>
							</div>
							<input type="text" name="busquedaDocumento<?= $sufijo; ?>" id="busquedaDocumento<?= $sufijo; ?>" class="form-control" onKeyUp="if(event.keyCode=='13'){ verPagina<?php echo $sufijo;?>(1); }" autocomplete="off">
						</div>
					</div>
					<div class="col-md-4">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text font-weight-bolder">Tipo</span>
							</div>
							<select class="form-control" name="busquedaTipo<?= $sufijo ?>" id="busquedaTipo<?= $sufijo ?>" onchange="verPagina<?php echo $sufijo;?>(1)">
								<option value="0">- Todos -</option>
								<option value="C">Cliente</option>
								<option value="P">Proveedor</option>
								<option value="T">Trabajador</option>
							</select>
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
					<div class="col-md-8">
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
		<div class="card card-custom <?php if($buscar==0){ ?> gutter-b <?php } ?>">
			<div class="card-body <?php if($buscar==1){ ?> p-0 <?php } ?> min-h-xl-400px" id="divLista<?php echo $sufijo;?>">
			</div>
		</div>
	</div>
</div>
<script>

function verPagina<?php echo $sufijo;?>(pagina, loading=1){

	$('#nroPagina<?php echo $sufijo; ?>').val(pagina);
	
	let estado = $('#busquedaEstado<?= $sufijo; ?>').val();
	let nombre = $('#busquedaNombre<?= $sufijo; ?>').val();
	let documento = $('#busquedaDocumento<?= $sufijo ?>').val();
	let tipo = $('#busquedaTipo<?= $sufijo ?>').val();
	let nivel = <?= $nivel ?>;
	let buscar = <?= $buscar ?>;

	let filtro='estado='+estado+'&nombre='+nombre+'&documento='+documento+'&tipo='+tipo+'&nivel='+nivel+'&buscar='+buscar;

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

	setRun('presentacion/listaDirectorio',filtro+extra+extrapermiso,'divLista<?php echo $sufijo;?>','divLista<?php echo $sufijo;?>',loading);	
}

verPagina<?php echo $sufijo;?>(1,0);

function NuevoRegistro<?= $sufijo ?>(){
	ViewModal('presentacion/mantDirectorio','accion=NUEVO&idopcion=<?= $idopcion ?>&nivel=<?= $nivel + 1 ?>','divmodal<?= $nivel ?>','Registrar Directorio',0);
}

</script>