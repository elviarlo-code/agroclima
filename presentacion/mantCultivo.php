<?php
require_once('../logica/clsCase.php');
require_once('../logica/clsCultivo.php');
$objCul= new clsCultivo();
$objCase = new clsCase();

$sufijoPadre = "cultivo";
$sufijo = "mantCultivo";
$idopcion = $_GET['idopcion'];
$puedeeditar = 1;
$nivel = 3;
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

$id = 0;
$foto = "assets/media/users/hoja1.jpg";
if($_GET['accion']=='MODIFICAR'){
	$id = $_GET['idcultivo'];
	$puedeeditar = $_GET['puedeeditar'];
	$cultivo = $objCase->getRowTableFiltroSimple('cultivo','idcultivo',$id);
	
	if($cultivo['imagen']!=""){
		if(file_exists("../files/imagenes/cultivos/".$cultivo['imagen'])){
			$foto = "files/imagenes/cultivos/".$cultivo['imagen'];
		}
	}
}

?>
<form name="formRegistro<?= $sufijo ?>" id="formRegistro<?= $sufijo ?>">
	<div class="row">
		<div class="col-md-6">

			<div class="form-group row">
				<label class="col-3 col-form-label text-left">Imagen Cultivo</label>
				<div class="col-9">
					<div class="image-input image-input-outline" id="imagen<?= $sufijo ?>" name="imagen<?= $sufijo ?>">
						<div class="image-input-wrapper" style="background-image: url(<?= $foto ?>)"></div>
						<label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Cambiar Imagen">
							<i class="fa fa-pen icon-sm text-muted"></i>
							<input type="file" name="profile_avatar" accept=".png, .jpg, .jpeg" />
							<input type="hidden" name="profile_avatar_remove" />
						</label>
						<span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="Cancelar Imagen">
							<i class="ki ki-bold-close icon-xs text-muted"></i>
						</span>
					</div>
					<span class="form-text text-muted">Tipos de archivos permitidos: png, jpg, jpeg.<br>Imagen de Cultivo</span>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Cultivo:</label>
				<div class="col-9">
					<input type="text" class="form-control" validar="SI" autocomplete="off" placeholder="" name="nombre<?= $sufijo ?>" id="nombre<?= $sufijo ?>" value="<?= ($id>0)? $cultivo['nombre'] : '' ?>" />
					<input type="hidden" class="form-control" name="idcultivo<?= $sufijo ?>" id="idcultivo<?= $sufijo ?>" value="<?= $id ?>" />
				</div>
			</div>
		</div>
		<div class="col-md-6">
        <div class="form-group row">
				<label class="col-3 col-form-label">Variedad:</label>
				<div class="col-9">
					<input type="text" class="form-control" validar="SI" name="variedad<?= $sufijo ?>" id="variedad<?= $sufijo ?>" autocomplete="off" value="<?= ($id>0)? $cultivo['variedad'] : '' ?>" />
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Altura:</label>
				<div class="col-9">
					<input type="text" class="form-control" name="altura<?= $sufijo ?>" id="altura<?= $sufijo ?>" onkeypress="return solo_decimal(event)" autocomplete="off" value="<?= ($id>0)? $cultivo['altura'] : '' ?>" />
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Raiz Max.:</label>
				<div class="col-9">
					<input type="text" class="form-control" name="raiz_maxima<?= $sufijo ?>" id="raiz_maxima<?= $sufijo ?>" onkeypress="return solo_decimal(event)" autocomplete="off" value="<?= ($id>0)? $cultivo['raiz_maxima'] : '' ?>" />
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Raiz Min.:</label>
				<div class="col-9">
					<input type="text" class="form-control" name="raiz_minima<?= $sufijo ?>" id="raiz_minima<?= $sufijo ?>" onkeypress="return solo_decimal(event)" autocomplete="off" value="<?= ($id>0)? $cultivo['raiz_minima'] : '' ?>" />
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
"use strict";

// Class definition
var KTProfile = function () {
	// Elements
	var _avatar;

	var _initAvatar = function () {
		_avatar = new KTImageInput('imagen<?= $sufijo ?>');
	}

	return {
		// public functions
		init: function () {
			_initAvatar();
		}
	};
}();

jQuery(document).ready(function() {
	KTProfile.init();
});

<?php if($_GET['accion']=='MODIFICAR'){ ?>
	
<?php } ?>


function registrar<?= $sufijo ?>(){
	if(verificarFormulario<?= $sufijo ?>()){
		$('#divmodal<?= $nivel-1 ?>Contenido').LoadingOverlay('show',{
			size: 20,
			maxSize: 40
		});

		var datax = new FormData($('#formRegistro<?= $sufijo ?>')[0]);
		datax.append('accion',"<?= $_GET['accion'] ?>");
		datax.append('sufijo',"<?= $sufijo ?>");		
		datax.append('idopcion',"<?= $idopcion ?>");		
		$.ajax({
			method: "POST",
			contentType: false, 
            processData: false,
			url: 'controlador/contCultivo.php',
			data: datax
		})
		.done(function( text ) {
			$('#divmodal<?= $nivel-1 ?>Contenido').LoadingOverlay('hide');
			var json = JSON.parse(text);
            text = json.mensaje;
			evaluarActividadSistema(text);
			if(text.substring(0,3)!="***"){
				<?php if($directo==1){ ?>
                    getCultivo<?= $sufijodirecto ?>(json.idcultivo);
                <?php }else{ ?>
                    verPagina<?= $sufijoPadre ?>($('#txtNroPaginaFooter<?php echo $sufijoPadre; ?>').val());
                <?php } ?>

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
	$('#divmodal1').on('shown.bs.modal', function () {
	    
	}); 
})

</script>