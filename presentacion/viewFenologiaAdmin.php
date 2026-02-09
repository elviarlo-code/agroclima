<?php
require_once('../logica/clsCase.php');
require_once('../logica/clsCompartido.php');
$objCase = new clsCase;

$sufijo = "fenologia";
$nivel = 1;
if(isset($_GET['nivel'])){
	$nivel = $_GET['nivel'];
}

$idcultivo = 0;
if(isset($_GET['idcultivo'])){
	$idcultivo = $_GET['idcultivo'];
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

$rowCultivo = $objCase->getRowTableFiltroSimple('cultivo', 'idcultivo', $idcultivo);
$nombre = $rowCultivo['nombre'].' - '.$rowCultivo['variedad'];

?>
<div class="row">
	<div class="col-md-12">
		<div class="card card-custom gutter-b">
			<div class="card-body p-0">
				<div class="row">
					<div class="col-md-5">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text font-weight-bolder">Cultivo</span>
							</div>
							<input type="text" name="busquedaCultivo<?= $sufijo; ?>" id="busquedaCultivo<?= $sufijo; ?>" value="<?= $nombre ?>" class="form-control" readonly="readonly" autocomplete="off">
						</div>
					</div>
					<div class="col-md-7">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text font-weight-bolder">Fenologia</span>
							</div>
							<input type="text" name="busquedaNombre<?= $sufijo; ?>" id="busquedaNombre<?= $sufijo; ?>" class="form-control" onKeyUp="if(event.keyCode=='13'){ verPagina<?php echo $sufijo;?>(1); }" autocomplete="off">
						</div>
					</div>
					<div class="col-md-12">
						<button type="button" class="btn btn-light-primary btn-sm" onclick="verPagina<?php echo $sufijo;?>(1)">
						    <i class="flaticon2-magnifier-tool"></i> Buscar
						</button>
						<?php if($permisos["registrar"]){ ?>
						<button type="button" class="btn btn-light-success btn-sm" onclick="NuevoRegistro<?= $sufijo ?>()">
						    <i class="flaticon2-add-1"></i> Nuevo
						</button>
						<?php } ?>
						<?php if($permisos["imprimir"]){ ?>
						<button type="button" class="btn btn-light-info btn-sm" onclick="downloadPNG<?= $sufijo ?>()">
						    <i class="flaticon-graph"></i> Descargar Grafico
						</button>
						<button type="button" class="btn btn-light-info btn-sm" onclick="saveImage<?= $sufijo ?>()">
						    <i class="flaticon-multimedia-4"></i> Exportar PDF
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
		<div class="card card-custom">
			<div class="card-body p-0" id="divLista<?php echo $sufijo;?>">
			</div>
		</div>
	</div>
</div>
<script>

function verPagina<?php echo $sufijo;?>(loading=1){
	let nombre = $('#busquedaNombre<?= $sufijo; ?>').val();
	let nivel = <?= $nivel ?>;

	let filtro='nombre='+nombre+'&nivel='+nivel+'&idcultivo=<?= $idcultivo ?>&cultivo=<?= $rowCultivo['nombre'] ?>';

	let editar='<?= $editar; ?>';
    let anular='<?= $anular; ?>';
    let eliminar='<?= $eliminar; ?>';
    let permiso_especial='<?= $permiso_especial; ?>';
    let permiso_especial1='<?= $permiso_especial1; ?>';
    let permiso_especial2='<?= $permiso_especial2; ?>';

	let extrapermiso = '&editar='+editar+'&anular='+anular+'&eliminar='+eliminar+'&permiso_especial='+permiso_especial+'&permiso_especial1='+permiso_especial1+'&permiso_especial2='+permiso_especial2+'&idopcion=<?= $idopcion ?>';

	setRun('presentacion/viewFenologiaLista',filtro+extrapermiso,'divLista<?php echo $sufijo;?>','divLista<?php echo $sufijo;?>',loading);	
}

verPagina<?php echo $sufijo;?>(0);

function NuevoRegistro<?= $sufijo ?>(){
	ViewModal('presentacion/viewFenologiaMant','accion=NUEVO&idopcion=<?= $idopcion ?>&nivel=<?= $nivel + 1 ?>&idcultivo=<?= $idcultivo ?>','divmodal<?= $nivel ?>','REGISTRAR FENOLOGIA - <?= htmlentities($rowCultivo['nombre']) ?>',0);
}

</script>