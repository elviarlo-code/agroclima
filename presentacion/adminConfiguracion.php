<?php
require_once('../logica/clsCase.php');
require_once('../logica/clsCompartido.php');
$objCase = new clsCase;

$sufijo = "configuracion";
$nivel = 1;
if(isset($_GET['nivel'])){
	$nivel = $_GET['nivel'];
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
			<div class="card-header" style="min-height: 50px">
				<div class="card-title">
					<span class="card-icon">
						<i class="flaticon2-user-1 text-primary"></i>
					</span>
					<h3 class="card-label">Configuración
					<small>Busqueda de Configuración</small></h3>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-4">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text font-weight-bolder">ID Config</span>
							</div>
							<input type="text" name="busquedaCodigo<?= $sufijo; ?>" id="busquedaCodigo<?= $sufijo; ?>" class="form-control" onKeyUp="if(event.keyCode=='13'){ verPagina<?php echo $sufijo;?>(1); }" autocomplete="off">
						</div>
					</div>
                    <div class="col-md-4">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text font-weight-bolder">Descripción</span>
							</div>
							<input type="text" name="busquedaDescripcion<?= $sufijo; ?>" id="busquedaDescripcion<?= $sufijo; ?>" class="form-control" onKeyUp="if(event.keyCode=='13'){ verPagina<?php echo $sufijo;?>(1); }" autocomplete="off">
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
	let descripcion = $('#busquedaDescripcion<?= $sufijo; ?>').val();
    let codigo = $('#busquedaCodigo<?= $sufijo; ?>').val();
	let nivel = <?= $nivel ?>;

	let filtro='estado='+estado+'&descripcion='+descripcion+'&codigo='+codigo+'&nivel='+nivel;

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

	setRun('presentacion/listaConfiguracion',filtro+extra+extrapermiso,'divLista<?php echo $sufijo;?>','divLista<?php echo $sufijo;?>',loading);	
}

verPagina<?php echo $sufijo;?>(1,0);

function NuevoRegistro<?= $sufijo ?>(){
	ViewModal('presentacion/mantConfiguracion','accion=NUEVO&idopcion=<?= $idopcion ?>&nivel=<?= $nivel + 1 ?>','divmodal<?= $nivel ?>','Registrar Configuración',0);

}

</script>