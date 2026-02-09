<?php
require_once('../logica/clsCase.php');
$objCase = new clsCase();

$sufijoPadre = "fenologia";
$sufijo = "mantfenologia";
$idopcion = $_GET['idopcion'];
$puedeeditar = 1;
$nivel = 2;
if(isset($_GET['nivel'])){
	$nivel = $_GET['nivel'];
}

$idcultivo = $_GET['idcultivo'];
$id = 0;
if($_GET['accion']=='MODIFICAR'){
	$id = $_GET['idfenologia'];
	$puedeeditar = $_GET['puedeeditar'];
	$registro = $objCase->getRowTableById('cultivo_fenologia',$id,'idfenologia');
}

?>
<form name="formRegistro<?= $sufijo ?>" id="formRegistro<?= $sufijo ?>">
	<div class="row">
		<div class="col-md-6">
			<div class="form-group row">
				<label class="col-3 col-form-label">Descripcion:</label>
				<div class="col-9">
					<input type="text" class="form-control" validar="SI" autocomplete="off" placeholder="Nombre de la Fenología" name="nombre<?= $sufijo ?>" id="nombre<?= $sufijo ?>" value="<?= ($id>0)? $registro['nombre'] : '' ?>" />
					<input type="hidden" class="form-control" name="idcultivo<?= $sufijo ?>" id="idcultivo<?= $sufijo ?>" value="<?= $idcultivo ?>" />
					<input type="hidden" class="form-control" name="idfenologia<?= $sufijo ?>" id="idfenologia<?= $sufijo ?>" value="<?= ($id>0)? $registro['idfenologia'] : 0 ?>" />
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group row">
				<label class="col-3 col-form-label">Duracion:</label>
				<div class="col-9">
					<input type="number" class="form-control" validar="SI" autocomplete="off" placeholder="Duracion en dias" name="duracion<?= $sufijo ?>" id="durecion<?= $sufijo ?>" value="<?= ($id>0)? $registro['duracion'] : '' ?>" />
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<!--begin::Accordion-->
			<div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample6">
				<div class="card">
					<div class="card-header" id="headingOne6">
						<div class="card-title collapsed" data-toggle="collapse" data-target="#collapseOne6"><i class="flaticon2-contract"></i>Datos Adicionales</div>
					</div>
					<div id="collapseOne6" class="collapse" data-parent="#accordionExample6">
						<div class="card-body">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group row mb-0">
										<label class="col-6 col-form-label">Kc:</label>
										<div class="col-6">
											<input type="text" class="form-control" onkeypress="return solo_decimal(event)" autocomplete="off" name="kc<?= $sufijo ?>" id="kc<?= $sufijo ?>" value="<?= ($id>0)? $registro['kc'] : '' ?>" />
										</div>
									</div>
									<div class="form-group row">
										<label class="col-6 col-form-label">Factor Cobertura(%):</label>
										<div class="col-6">
											<input type="text" class="form-control" onkeypress="return solo_decimal(event)" autocomplete="off" name="cobertura<?= $sufijo ?>" id="cobertura<?= $sufijo ?>" value="<?= ($id>0)? $registro['cobertura'] : '' ?>" />
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group row mb-0">
										<label class="col-6 col-form-label">Prof. Radicular(m):</label>
										<div class="col-6">
											<input type="text" class="form-control" onkeypress="return solo_decimal(event)" autocomplete="off" name="raiz<?= $sufijo ?>" id="raiz<?= $sufijo ?>" value="<?= ($id>0)? $registro['raiz'] : '' ?>" />
										</div>
									</div>
									<div class="form-group row">
										<label class="col-6 col-form-label">Umbral de Riego(%):</label>
										<div class="col-6">
											<input type="text" class="form-control" onkeypress="return solo_decimal(event)" autocomplete="off" name="umbral<?= $sufijo ?>" id="umbral<?= $sufijo ?>" value="<?= ($id>0)? $registro['umbral'] : '' ?>" />
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card">
					<div class="card-header" id="headingTwo6">
						<div class="card-title collapsed" data-toggle="collapse" data-target="#collapseTwo6">
						<i class="flaticon2-wifi"></i>Condiciones Climáticas</div>
					</div>
					<div id="collapseTwo6" class="collapse" data-parent="#accordionExample6">
						<div class="card-body">
							<div class="row gutter-b">
								<div class="col-md-6">
									<div class="form-group row mb-0">
										<label class="col-5 col-form-label">Temperatura Min.:</label>
										<div class="col-7">
											<input type="text" class="form-control" onkeypress="return solo_decimal(event)" autocomplete="off" name="temp_min<?= $sufijo ?>" id="temp_min<?= $sufijo ?>" value="<?= ($id>0)? $registro['temp_min'] : '' ?>" />
										</div>
									</div>
									<div class="form-group row">
										<label class="col-5 col-form-label">Temperatura Max.:</label>
										<div class="col-7">
											<input type="text" class="form-control" onkeypress="return solo_decimal(event)" autocomplete="off" name="temp_max<?= $sufijo ?>" id="temp_max<?= $sufijo ?>" value="<?= ($id>0)? $registro['temp_max'] : '' ?>" />
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group row mb-0">
										<label class="col-5 col-form-label">Humedad Mínima:</label>
										<div class="col-7">
											<input type="text" class="form-control" onkeypress="return solo_decimal(event)" autocomplete="off" name="humd_min<?= $sufijo ?>" id="humd_min<?= $sufijo ?>" value="<?= ($id>0)? $registro['humd_min'] : '' ?>" />
										</div>
									</div>
									<div class="form-group row">
										<label class="col-5 col-form-label">Humedad Máxima:</label>
										<div class="col-7">
											<input type="text" class="form-control" onkeypress="return solo_decimal(event)" autocomplete="off" name="humd_max<?= $sufijo ?>" id="humd_max<?= $sufijo ?>" value="<?= ($id>0)? $registro['humd_max'] : '' ?>" />
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--end::Accordion-->
		</div>
	</div>
	<div class="row mt-10">
		<div class="col-md-12 d-flex justify-content-between">
			<?php if($puedeeditar==1){ ?>
			<button type="button" class="btn btn-light-success font-weight-bold" onclick="registrar<?= $sufijo ?>()"><i class="fa fa-save"></i> Registrar</button>
			<?php } ?>
			<button type="button" class="btn font-weight-bold btn-light-danger" onclick="CloseModal('divmodal<?= $nivel-1 ?>')"><i class="fa fa-times"></i> Cerrar</button>
		</div>
	</div>
</form>
<script>
<?php if($_GET['accion']=='MODIFICAR'){ ?>
	
<?php } ?>


function registrar<?= $sufijo ?>(){
	if(verificarFormulario<?= $sufijo ?>()){
		$('#divmodal<?= $nivel-1 ?>Contenido').LoadingOverlay('show',{
			size: 20,
			maxSize: 40
		});
		let datax = $('#formRegistro<?= $sufijo ?>').serializeArray();
		<?php if($_GET['accion']=="NUEVO"){ ?>
			datax.push({name: "accion",value:"NUEVA_FENOLOGIA"});
		<?php }else{ ?>
			datax.push({name: "accion",value:"MODIFICAR_FENOLOGIA"});
		<?php } ?>
		datax.push({name: "sufijo",value:"<?= $sufijo ?>"});
		datax.push({name: "idopcion",value:"<?= $idopcion ?>"});
		$.ajax({
			method: "POST",
			url: 'controlador/contCultivo.php',
			data: datax
		})
		.done(function( text ) {
			$('#divmodal<?= $nivel-1 ?>Contenido').LoadingOverlay('hide');
			evaluarActividadSistema(text);
			if(text.substring(0,3)!="***"){
				verPagina<?= $sufijoPadre ?>(1);
				$.toast({'text': text,'icon': 'success', 'position':'top-right'});
				CloseModal("divmodal<?= $nivel-1 ?>");			
			}else{
				$.toast({'text': text,'icon': 'error', 'position':'top-right'});
			}
		});
	}
}
	
function verificarFormulario<?= $sufijo ?>(){
	return ValidarCampos('formRegistro<?= $sufijo ?>');
}


$(document).ready(function(){
	$('#divmodal<?= $nivel-1 ?>').on('shown.bs.modal', function () {
	  
	}); 
})


</script>