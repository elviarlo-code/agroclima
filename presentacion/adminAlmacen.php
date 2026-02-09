<?php
require_once('../logica/clsCase.php');
require_once('../logica/clsCompartido.php');
$objCase = new clsCase;

$sufijo = "almacen";
$nivel = 1;
if(isset($_GET['nivel'])){
	$nivel = $_GET['nivel'];
}

$idopcion=0;
if(isset($_GET['idoptx'])){
	$idopcion=$_GET['idoptx'];
}
$opcionMenu = $objCase->getRowTableById("opcion",$idopcion);

$listainstitucion = $objCase->getListTableFiltroSimple('mginstitucion', 'estado', 'N');

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
						<i class="flaticon2-box-1 text-primary"></i>
					</span>
					<h3 class="card-label">Almacén
					<small>Busqueda de Almacén</small></h3>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-4">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text font-weight-bolder">Almacén</span>
							</div>
							<input type="text" name="busquedaNombre<?= $sufijo; ?>" id="busquedaNombre<?= $sufijo; ?>" class="form-control" onKeyUp="if(event.keyCode=='13'){ verPagina<?php echo $sufijo;?>(1); }" autocomplete="off">
						</div>
					</div>
					<div class="col-md-4">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text font-weight-bolder">Institución</span>
							</div>
							<select class="form-control" name="busquedaInstitucion<?= $sufijo ?>" id="busquedaInstitucion<?= $sufijo ?>" onchange="getSucursal<?= $sufijo ?>();">
								<option value="0">- Todos -</option>
								<?php while($fila = $listainstitucion->fetch(PDO::FETCH_NAMED)){ ?>
									<option value="<?= $fila['idinstitucion'] ?>"><?= $fila['nombre'] ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text font-weight-bolder">Sucursal</span>
							</div>
							<select class="form-control" name="busquedaSucursal<?= $sufijo ?>" id="busquedaSucursal<?= $sufijo ?>" onchange="verPagina<?php echo $sufijo;?>(1)">
								<option value="0">- Todos -</option>
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
	let idinstitucion = $('#busquedaInstitucion<?= $sufijo; ?>').val();
	let idsucursal = $('#busquedaSucursal<?= $sufijo; ?>').val();
	let nivel = <?= $nivel ?>;

	let filtro='estado='+estado+'&nombre='+nombre+'&idinstitucion='+idinstitucion+'&idsucursal='+idsucursal+'&nivel='+nivel;

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

	setRun('presentacion/listaAlmacen',filtro+extra+extrapermiso,'divLista<?php echo $sufijo;?>','divLista<?php echo $sufijo;?>',loading);	
}

verPagina<?php echo $sufijo;?>(1,0);

function getSucursal<?= $sufijo ?>(){
	$.ajax({
		method: "POST",
		url: 'controlador/contSucursal.php',
		data: {
				'accion' : 'LISTA_SUCURSAL',
				'idinstitucion' : $('#busquedaInstitucion<?= $sufijo; ?>').val(),
				'vista' : 'ADMIN'
		}
	})
	.done(function( text ) {
		evaluarActividadSistema(text);
		if(text.substring(0,3)!="***"){
			$('#busquedaSucursal<?= $sufijo ?>').html(text);
			verPagina<?php echo $sufijo;?>(1);
		}else{
			$.toast({'text': text,'icon': 'error', 'position':'top-right'});
		}
	});
}

function NuevoRegistro<?= $sufijo ?>(){
	ViewModal('presentacion/mantAlmacen','accion=NUEVO&idopcion=<?= $idopcion ?>&nivel=<?= $nivel + 1 ?>','divmodal<?= $nivel ?>','Registrar Almacén',0);
}

</script>