<?php
require_once('../logica/clsCase.php');
$objCase = new clsCase();

$sufijoPadre = "vistaCampania";
$sufijo = "campConfiguracion";
$idopcion = $_GET['idopcion'];
$puedeeditar = 1;
$nivel = 2;
if(isset($_GET['nivel'])){
	$nivel = $_GET['nivel'];
}

$idcampania = $_GET['idcampania'];
$fechaini = $_GET['fhini'];
$fechasiembra = $_GET['fsiembra'];
$fechafin = $_GET['fhfin'];
$iddispositivo = $_GET['iddispositivo'];

$registro = $objCase->getRowTableFiltroSimple('campania','idcampania',$idcampania);

$listaDis = $objCase->getListTableFiltroSimple('dispositivo','estado','N');
$listaDis = $listaDis->fetchAll(PDO::FETCH_NAMED);
	

?>
<form name="formRegistro<?= $sufijo ?>" id="formRegistro<?= $sufijo ?>">
	<div class="row">
		<div class="col-md-12">
			<div class="form-group row mb-2">
				<label class="col-4 col-form-label">Fecha de Inicio:</label>
				<div class="col-8">
					<div class="input-group">
						<div class="input-group-prepend" data-toggle="tooltip" data-placement="top" title="Activar Campo">
							<span class="input-group-text">
								<label class="checkbox checkbox-single checkbox-success">
									<input type="checkbox" id="checkinicio" onclick="quitarDisabled(this.id, 'fechaini<?= $sufijo ?>')">
									<span></span>
								</label>
							</span>
						</div>
						<input type="date" class="form-control" validar="SI" disabled autocomplete="off" placeholder="" name="fechaini<?= $sufijo ?>" id="fechaini<?= $sufijo ?>" value="<?= $fechaini ?>" />
					</div>
					<input type="hidden" class="form-control" name="idcampania<?= $sufijo ?>" id="idcampania<?= $sufijo ?>" value="<?= $idcampania ?>" />
				</div>
			</div>
		</div>
		<div class="col-md-12">
			<div class="form-group row mb-2">
				<label class="col-4 col-form-label">Fecha de Siembra:</label>
				<div class="col-8">
					<div class="input-group">
						<div class="input-group-prepend" data-toggle="tooltip" data-placement="top" title="Activar Campo">
							<span class="input-group-text">
								<label class="checkbox checkbox-single checkbox-success">
									<input type="checkbox" id="checksiembra" onclick="quitarDisabled(this.id,'fechasiembra<?= $sufijo ?>')">
									<span></span>
								</label>
							</span>
						</div>
						<input type="date" class="form-control" validar="SI" disabled autocomplete="off" placeholder="" name="fechasiembra<?= $sufijo ?>" id="fechasiembra<?= $sufijo ?>" value="<?= $fechasiembra ?>" />
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-12">
			<div class="form-group row">
				<label class="col-4 col-form-label">Dispositivo:</label>
				<div class="col-8">
				<select class="form-control" name="iddispositivo<?= $sufijo ?>" id="iddispositivo<?= $sufijo ?>">
					<option value="0">- Seleccione -</option>
					<?php foreach($listaDis as $k=>$v){ ?>
						<option value="<?= $v['iddispositivo'] ?>"><?= $v['nombre'] ?></option>
					<?php } ?>
				</select>
				</div>
			</div>
		</div>
	</div>
	<div class="row" <?= ($registro['finalizado']==1)? 'hidden':'' ?>>
		<div class="col-md-12 d-flex justify-content-between">
			<?php if($puedeeditar==1){ ?>
			<button type="button" class="btn btn-light-success font-weight-bold" onclick="registrar<?= $sufijo ?>()"><i class="fa fa-save"></i> Registrar</button>
			<?php } ?>
			<button type="button" class="btn font-weight-bold btn-light-danger" onclick="CloseModal('divmodalmediano')"><i class="fa fa-times"></i> Cerrar</button>
		</div>
	</div>
</form>
<script>
<?php if($iddispositivo>0){ ?>
	$('#iddispositivo<?= $sufijo ?>').val(<?= $iddispositivo ?>);
<?php } ?>

function registrar<?= $sufijo ?>(){
	if(verificarFormulario<?= $sufijo ?>()){
		$('#divmodalmedianoContenido').LoadingOverlay('show',{
			size: 20,
			maxSize: 40
		});
		let datax = $('#formRegistro<?= $sufijo ?>').serializeArray();
		datax.push({name: "accion",value:"ACTUALIZAR_FECHA_CAMPANIA"});
		datax.push({name: "sufijo",value:"<?= $sufijo ?>"});
		datax.push({name: "idopcion",value:"<?= $idopcion ?>"});
		datax.push({name: "fechaini",value:$('#fechaini<?= $sufijo ?>').val()});
		datax.push({name: "fechasiembra",value:$('#fechasiembra<?= $sufijo ?>').val()});
		$.ajax({
			method: "POST",
			url: 'controlador/contCampania.php',
			data: datax
		})
		.done(function( text ) {
			$('#divmodalmedianoContenido').LoadingOverlay('hide');
			evaluarActividadSistema(text);
			if(text.substring(0,3)!="***"){
				let estado = 'N';
				let nivel = <?= $nivel - 1 ?>;

				let filtro='estado='+estado+'&idcampania=<?= $idcampania ?>&nivel='+nivel;
				let extrapermiso = '&idopcion=<?= $idopcion ?>';

				setRun('presentacion/viewCampania',filtro+extrapermiso,'contenedorPrincipal','contenedorPrincipal',1);	

				$.toast({'text': text,'icon': 'success', 'position':'top-right'});
				CloseModal("divmodalmediano");				
			}else{
				$.toast({'text': text,'icon': 'error', 'position':'top-right'});
			}
		});
	}
}
	
function verificarFormulario<?= $sufijo ?>(){
	let correcto=true;
	let dateini = new Date($('#fechaini<?= $sufijo ?>').val()); 
	let datesiembra = new Date($('#fechasiembra<?= $sufijo ?>').val());

	if(dateini>datesiembra){
		correcto=false;
		AgregarError('fechaini<?= $sufijo ?>','La fecha de inicio debe ser antes de la fecha de siembra');
		Swal.fire("Error de Sistema", "Existe errores en su formulario! Verifíquelo!", "error");
		return correcto;
	}else{
		QuitarError('tipo<?= $sufijo ?>');
	}

	return ValidarCampos('formRegistro<?= $sufijo ?>');
}

function quitarDisabled(check,id){
	if($("#"+check).is(":checked")){
		$('#'+id).removeAttr("disabled");
	}else{
		$('#'+id).prop("disabled", true);
	}
}

$(document).ready(function(){
	$('#divmodalmediano').on('shown.bs.modal', function () {
	    
	}); 
})

</script>