<?php
require_once('../logica/clsCase.php');
$objCase = new clsCase();

$sufijoPadre = "configuracion";
$sufijo = "mantConfig";
$idopcion = $_GET['idopcion'];
$puedeeditar = 1;
$nivel = 2;
if(isset($_GET['nivel'])){
	$nivel = $_GET['nivel'];
}

$listamodulo = $objCase->getListTableFiltroSimple('opcion', 'idopcion_ref', 0, 'estado', 'N');

$id = 0;
if($_GET['accion']=='MODIFICAR'){
	$id = $_GET['codigo'];
	$puedeeditar = $_GET['puedeeditar'];
	$registro = $objCase->getRowTableById('mgconfig',$id,'codigo');
}

?>
<form name="formRegistro<?= $sufijo ?>" id="formRegistro<?= $sufijo ?>">
	<div class="row gutter-b">
		<div class="col-md-6">
			<div class="form-group row">
				<label class="col-3 col-form-label">Idconfig:</label>
				<div class="col-9">
					<input type="number" class="form-control" validar="SI" autocomplete="off" placeholder="Código de Configuración" name="idconfig<?= $sufijo ?>" id="idconfig<?= $sufijo ?>" value="<?= ($id>0)? $registro['idconfig'] : '' ?>" />
					<input type="hidden" class="form-control" name="codigo<?= $sufijo ?>" id="codigo<?= $sufijo ?>" value="<?= ($id>0)? $registro['codigo'] : 0 ?>" />
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Descripción:</label>
				<div class="col-9">
					<input type="text" class="form-control" validar="SI" autocomplete="off" placeholder="Descripción" name="descripcion<?= $sufijo ?>" id="descripcion<?= $sufijo ?>" value="<?= ($id>0)? $registro['descripcion'] : '' ?>" />
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Modulo:</label>
				<div class="col-9">
					<select class="form-control" id="modulo<?= $sufijo ?>" validar="SI" name="modulo<?= $sufijo ?>">
						<option value="0">- Seleccione -</option>
						<?php while($fila=$listamodulo->fetch(PDO::FETCH_NAMED)){ ?>
							<option value="<?= $fila['idopcion'] ?>"><?= $fila['descripcion'] ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Tipo Dato:</label>
				<div class="col-9">
					<select class="form-control" id="tipdat<?= $sufijo ?>" validar="SI" name="tipdat<?= $sufijo ?>">
						<option value="0">- Seleccione -</option>
						<option value="T">Texto</option>
						<option value="N">Numero</option>
					</select>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group row">
				<label class="col-3 col-form-label">Longitud:</label>
				<div class="col-9">
					<input type="number" class="form-control" validar="SI" autocomplete="off" name="longitud<?= $sufijo ?>" id="longitud<?= $sufijo ?>" value="<?= ($id>0)? $registro['longitud'] : '' ?>" />
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Valor:</label>
				<div class="col-9">
					<input type="text" class="form-control" validar="SI" autocomplete="off" placeholder="valor del Config" name="valor<?= $sufijo ?>" id="valor<?= $sufijo ?>" value="<?= ($id>0)? $registro['valor'] : '' ?>" />
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Observación:</label>
				<div class="col-9">
					<textarea class="form-control" name="observacion<?= $sufijo ?>" id="observacion<?= $sufijo ?>" style="height: 100px;" placeholder="Observacion..."><?= ($id>0)? $registro['observacion'] : '' ?></textarea>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
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
	$('#modulo<?= $sufijo ?>').val('<?= $registro['modulo'] ?>');
	$('#tipdat<?= $sufijo ?>').val('<?= $registro['tipdat'] ?>');
<?php } ?>


function registrar<?= $sufijo ?>(){
	if(verificarFormulario<?= $sufijo ?>()){
		$('#divmodal<?= $nivel-1 ?>Contenido').LoadingOverlay('show',{
			size: 20,
			maxSize: 40
		});
		let datax = $('#formRegistro<?= $sufijo ?>').serializeArray();
		datax.push({name: "accion",value:"<?= $_GET['accion'] ?>"});
		datax.push({name: "sufijo",value:"<?= $sufijo ?>"});
		datax.push({name: "idopcion",value:"<?= $idopcion ?>"});
		$.ajax({
			method: "POST",
			url: 'controlador/contConfiguracion.php',
			data: datax
		})
		.done(function( text ) {
			$('#divmodal<?= $nivel-1 ?>Contenido').LoadingOverlay('hide');
			evaluarActividadSistema(text);
			if(text.substring(0,3)!="***"){
				verPagina<?= $sufijoPadre ?>($('#txtNroPaginaFooter<?php echo $sufijoPadre; ?>').val());
				$.toast({'text': text,'icon': 'success', 'position':'top-right'});
				CloseModal("divmodal<?= $nivel-1 ?>");			
			}else{
				$.toast({'text': text,'icon': 'error', 'position':'top-right'});
			}
		});
	}
}
	
function verificarFormulario<?= $sufijo ?>(){
	let correcto=true;

	return ValidarCampos('formRegistro<?= $sufijo ?>');
}


$(document).ready(function(){
	$('#divmodal<?= $nivel-1 ?>').on('shown.bs.modal', function () {
	  
	}); 
})


</script>