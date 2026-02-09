<?php
require_once('../logica/clsCase.php');
$objCase = new clsCase();

$sufijoPadre = "esquema";
$sufijo = "mantesquema";
$idopcion = $_GET['idopcion'];
$puedeeditar = 1;
$nivel = 2;
if(isset($_GET['nivel'])){
	$nivel = $_GET['nivel'];
}

$directo = 0;
if(isset($_GET['directo'])){
    $directo = $_GET['directo'];
}

$sufijodirecto = "";
if(isset($_GET['sufijodirecto'])){
    $sufijodirecto = $_GET['sufijodirecto'];
}

$idterreno = $_GET['idterreno'];
$id = 0;
if($_GET['accion']=='MODIFICAR'){
	$id = $_GET['idesquema'];
	$puedeeditar = $_GET['puedeeditar'];
	$registro = $objCase->getRowTableById('terreno_esquema',$id,'idesquema');
}

?>
<form name="formRegistro<?= $sufijo ?>" id="formRegistro<?= $sufijo ?>">
	<div class="row gutter-b">
		<div class="col-md-12">
			<div class="form-group row">
				<label class="col-3 col-form-label">Esquema:</label>
				<div class="col-9">
					<input type="text" class="form-control" validar="SI" autocomplete="off" placeholder="" name="descripcion<?= $sufijo ?>" id="descripcion<?= $sufijo ?>" value="<?= ($id>0)? $registro['descripcion'] : '' ?>" />
					<input type="hidden" class="form-control" name="idterreno<?= $sufijo ?>" id="idterreno<?= $sufijo ?>" value="<?= $idterreno ?>" />
					<input type="hidden" class="form-control" name="idesquema<?= $sufijo ?>" id="idesquema<?= $sufijo ?>" value="<?= ($id>0)? $registro['idesquema'] : 0 ?>" />
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 d-flex justify-content-between">
			<?php if($puedeeditar==1){ ?>
			<button type="button" class="btn btn-light-success font-weight-bold" onclick="registrar<?= $sufijo ?>()"><i class="fa fa-save"></i> Registrar</button>
			<?php } ?>
			<button type="button" class="btn font-weight-bold btn-light-danger" onclick="CloseModal('divmodalmediano')"><i class="fa fa-times"></i> Cerrar</button>
		</div>
	</div>
</form>
<script>
<?php if($_GET['accion']=='MODIFICAR'){ ?>
	
<?php } ?>


function registrar<?= $sufijo ?>(){
	if(verificarFormulario<?= $sufijo ?>()){
		$('#divmodalmedianoContenido').LoadingOverlay('show',{
			size: 20,
			maxSize: 40
		});
		let datax = $('#formRegistro<?= $sufijo ?>').serializeArray();
		<?php if($_GET['accion']=="NUEVO"){ ?>
			datax.push({name: "accion",value:"NUEVO_ESQUEMA"});
		<?php }else{ ?>
			datax.push({name: "accion",value:"MODIFICAR_ESQUEMA"});
		<?php } ?>
		datax.push({name: "sufijo",value:"<?= $sufijo ?>"});
		datax.push({name: "idopcion",value:"<?= $idopcion ?>"});
		$.ajax({
			method: "POST",
			url: 'controlador/contTerreno.php',
			data: datax
		})
		.done(function( text ) {
			$('#divmodalmedianoContenido').LoadingOverlay('hide');
			var json = JSON.parse(text);
            text = json.mensaje;
			evaluarActividadSistema(text);
			if(text.substring(0,3)!="***"){
				<?php if($directo==1){ ?>
                    getEsquema<?= $sufijodirecto ?>(json.idesquema);
                <?php }else{ ?>
                    verPagina<?= $sufijoPadre ?>(1);
                <?php } ?>
				$.toast({'text': text,'icon': 'success', 'position':'top-right'});
				CloseModal("divmodalmediano");			
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
	$('#divmodalmediano').on('shown.bs.modal', function () {
	  
	}); 
})


</script>