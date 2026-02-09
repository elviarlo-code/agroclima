<?php
require_once('../logica/clsCase.php');
require_once('../logica/clsCompartido.php');
$objCase = new clsCase;

$sufijo = "campania";

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

$listaTerreno = $objCase->getListTableFiltroSimple('terreno', 'estado', 'N');

?>
<div class="row">
	<div class="col-md-12">
		<div class="card card-custom gutter-b">
			<?php if($buscar==0){ ?>
			<div class="card-header" style="min-height: 50px">
				<div class="card-title">
					<span class="card-icon">
						<i class="flaticon-layers text-primary"></i>
					</span>
					<h3 class="card-label">Campaña
					<small>Busqueda de Campaña</small></h3>
				</div>
			</div>
			<?php } ?>
			<div class="card-body <?php if($buscar==1){ ?> p-0 <?php } ?>">
				<div class="row">
					<div class="col-md-4">
						<div class="input-group input-group-sm">
							<input type="date" name="busquedaDesde<?= $sufijo; ?>" id="busquedaDesde<?= $sufijo; ?>" class="form-control" onKeyUp="if(event.keyCode=='13'){ verPagina<?php echo $sufijo;?>(1); }" autocomplete="off">
							<div class="input-group-prepend">
								<span class="input-group-text font-weight-bolder">a</span>
							</div>
							<input type="date" name="busquedaHasta<?= $sufijo; ?>" id="busquedaHasta<?= $sufijo; ?>" class="form-control" onKeyUp="if(event.keyCode=='13'){ verPagina<?php echo $sufijo;?>(1); }" autocomplete="off">
						</div>
					</div>
					<div class="col-md-4">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text font-weight-bolder">Terreno</span>
							</div>
							<select class="form-control" name="busquedaIdterreno<?= $sufijo ?>" id="busquedaIdterreno<?= $sufijo ?>" onchange="consultarEsquema<?php echo $sufijo;?>()">
								<option value="0">- Todos -</option>
								<?php while($fila = $listaTerreno->fetch(PDO::FETCH_NAMED)){ ?>
									<option value="<?= $fila['idterreno'] ?>"><?= $fila['descripcion'] ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text font-weight-bolder">Esquema</span>
							</div>
							<select class="form-control" name="busquedaIdesquema<?= $sufijo ?>" id="busquedaIdesquema<?= $sufijo ?>" onchange="verPagina<?php echo $sufijo;?>(1)">
								<option value="0">- Todos -</option>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text font-weight-bolder">Turno</span>
							</div>
							<input type="text" name="busquedaNombre<?= $sufijo; ?>" id="busquedaNombre<?= $sufijo; ?>" class="form-control" onKeyUp="if(event.keyCode=='13'){ verPagina<?php echo $sufijo;?>(1); }" autocomplete="off">
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
		<div class="card card-custom <?php if($buscar==0){ ?> gutter-b <?php } ?>">
			<div class="card-body <?php if($buscar==1){ ?> p-0 <?php }else{ ?> min-h-xl-400px <?php } ?>" id="divLista<?php echo $sufijo;?>">
			</div>
		</div>
	</div>
</div>
<script>

function verPagina<?php echo $sufijo;?>(pagina, loading=1){

	$('#nroPagina<?php echo $sufijo; ?>').val(pagina);
	
	let desde = $('#busquedaDesde<?= $sufijo; ?>').val();
	let hasta = $('#busquedaHasta<?= $sufijo; ?>').val();
	let estado = $('#busquedaEstado<?= $sufijo; ?>').val();
	let nombre = $('#busquedaNombre<?= $sufijo; ?>').val();
	let idterreno = $('#busquedaIdterreno<?= $sufijo ?>').val();
	let idesquema = $('#busquedaIdesquema<?= $sufijo ?>').val();
	let nivel = <?= $nivel ?>;
	let buscar = <?= $buscar ?>;

	let filtro='desde='+desde+'&hasta='+hasta+'&estado='+estado+'&nombre='+nombre+'&idterreno='+idterreno+'&idesquema='+idesquema+'&nivel='+nivel+'&buscar='+buscar;

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

	setRun('presentacion/listaCampania',filtro+extra+extrapermiso,'divLista<?php echo $sufijo;?>','divLista<?php echo $sufijo;?>',loading);	
}

verPagina<?php echo $sufijo;?>(1,0);

function NuevoRegistro<?= $sufijo ?>(){
	ViewModal('presentacion/mantCampania','accion=NUEVO&idopcion=<?= $idopcion ?>&buscar=<?= $buscar ?>&nivel=<?= $nivel + 1 ?>','divmodal<?= $nivel ?>','Registro Campaña',0);
}

function consultarEsquema<?php echo $sufijo;?>(){
	$.ajax({
		method: "POST",
		url: 'controlador/contTurno.php',
		data: {
				'accion' : 'LISTA_ESQUEMA',
				'idterreno' : $('#busquedaIdterreno<?= $sufijo; ?>').val(),
				'vista' : 'ADMIN'
		}
	})
	.done(function( text ) {
		evaluarActividadSistema(text);
		if(text.substring(0,3)!="***"){
			$('#busquedaIdesquema<?= $sufijo ?>').html(text);
			verPagina<?php echo $sufijo;?>(1);
		}else{
			$.toast({'text': text,'icon': 'error', 'position':'top-right'});
		}
	});
}

</script>