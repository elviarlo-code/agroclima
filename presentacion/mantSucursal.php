<?php
require_once('../logica/clsCase.php');
$objCase = new clsCase();

$sufijoPadre = "sucursal";
$sufijo = "mantSuc";
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

$idinstitucionori = 0;
if(isset($_GET['idinstitucionori'])){
	$idinstitucionori = $_GET['idinstitucionori'];
}

$id = 0;
if($_GET['accion']=='MODIFICAR'){
	$id = $_GET['idsucursal'];
	$puedeeditar = $_GET['puedeeditar'];
	$registro = $objCase->getRowTableById('mgsucursal',$id,'idsucursal');
}

?>
<form name="formRegistro<?= $sufijo ?>" id="formRegistro<?= $sufijo ?>">
	<div class="row gutter-b">
		<div class="col-md-6">
			<div class="form-group row mb-0">
				<label class="col-3 col-form-label">
					Institución:
					<div class="btn-icon">
						<span class="svg-icon svg-icon-success svg-icon-2x" data-toggle="tooltip" title="" data-placement="right" data-original-title="Registrar Institución" style="cursor: pointer;" onclick="NuevaInstitucion<?= $sufijo ?>()"><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\Files\Folder-error.svg-->
							<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
								<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
							        <rect x="0" y="0" width="24" height="24"/>
							        <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10"/>
							        <path d="M11,11 L11,7 C11,6.44771525 11.4477153,6 12,6 C12.5522847,6 13,6.44771525 13,7 L13,11 L17,11 C17.5522847,11 18,11.4477153 18,12 C18,12.5522847 17.5522847,13 17,13 L13,13 L13,17 C13,17.5522847 12.5522847,18 12,18 C11.4477153,18 11,17.5522847 11,17 L11,13 L7,13 C6.44771525,13 6,12.5522847 6,12 C6,11.4477153 6.44771525,11 7,11 L11,11 Z" fill="#000000"/>
							    </g>
							</svg><!--end::Svg Icon-->
						</span>&nbsp;
						<span class="svg-icon svg-icon-warning svg-icon-2x" data-toggle="tooltip" title="" data-placement="right" data-original-title="Actualizar Institución" style="cursor: pointer;" onclick="editarInstitucion<?= $sufijo ?>()"><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\Files\Folder-error.svg-->
							<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
								<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
							        <rect x="0" y="0" width="24" height="24"/>
							        <path d="M8.43296491,7.17429118 L9.40782327,7.85689436 C9.49616631,7.91875282 9.56214077,8.00751728 9.5959027,8.10994332 C9.68235021,8.37220548 9.53982427,8.65489052 9.27756211,8.74133803 L5.89079566,9.85769242 C5.84469033,9.87288977 5.79661753,9.8812917 5.74809064,9.88263369 C5.4720538,9.8902674 5.24209339,9.67268366 5.23445968,9.39664682 L5.13610134,5.83998177 C5.13313425,5.73269078 5.16477113,5.62729274 5.22633424,5.53937151 C5.384723,5.31316892 5.69649589,5.25819495 5.92269848,5.4165837 L6.72910242,5.98123382 C8.16546398,4.72182424 10.0239806,4 12,4 C16.418278,4 20,7.581722 20,12 C20,16.418278 16.418278,20 12,20 C7.581722,20 4,16.418278 4,12 L6,12 C6,15.3137085 8.6862915,18 12,18 C15.3137085,18 18,15.3137085 18,12 C18,8.6862915 15.3137085,6 12,6 C10.6885336,6 9.44767246,6.42282109 8.43296491,7.17429118 Z" fill="#000000" fill-rule="nonzero"/>
							    </g>
							</svg><!--end::Svg Icon-->
						</span>
					</div>
				</label>
				<div class="col-9">
					<select class="form-control" name="idinstitucion<?= $sufijo ?>" validar="SI" id="idinstitucion<?= $sufijo ?>">
						<option value="0" selected>- Seleccione -</option>
					</select>
					<input type="hidden" class="form-control" name="idsucursal<?= $sufijo ?>" id="idsucursal<?= $sufijo ?>" value="<?= ($id>0)? $registro['idsucursal'] : 0 ?>" />
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Nombre:</label>
				<div class="col-9">
					<input type="text" class="form-control" validar="SI" autocomplete="off" placeholder="Sucursal" name="nombre<?= $sufijo ?>" id="nombre<?= $sufijo ?>" value="<?= ($id>0)? $registro['nombre'] : '' ?>" />
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group row">
				<label class="col-3 col-form-label">Dirección:</label>
				<div class="col-9">
					<textarea class="form-control" name="direccion<?= $sufijo ?>" id="direccion<?= $sufijo ?>" validar="SI" style="height: 105px;" placeholder="Dirección..."><?= ($id>0)? $registro['direccion'] : '' ?></textarea>
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

<?php } ?>

function getInstitucion<?= $sufijo ?>(idinstitucion=0){
	if(idinstitucion>0){
		$('#divmodal<?= $nivel-1 ?>Contenido').LoadingOverlay('show',{
			size: 20,
			maxSize: 40
		});
	}
	$.ajax({
		method: "POST",
		url: 'controlador/contInstitucion.php',
		data: {
				'accion' : 'LISTA_INSTITUCION',
				'idinstitucion' : idinstitucion,
		}
	})
	.done(function( text ) {
		if(idinstitucion>0){
			$('#divmodal<?= $nivel-1 ?>Contenido').LoadingOverlay('hide');
		}
		evaluarActividadSistema(text);
		if(text.substring(0,3)!="***"){
			$('#idinstitucion<?= $sufijo ?>').html(text);
			<?php if($_GET['accion']=='MODIFICAR'){ ?>
				$('#idinstitucion<?= $sufijo ?>').val('<?= $registro['idinstitucion'] ?>');
			<?php } ?>

			<?php if($idinstitucionori>0){ ?>
				if(idinstitucion==0){
					$('#idinstitucion<?= $sufijo ?>').val('<?= $idinstitucionori ?>');
				}
			<?php } ?>		
		}else{
			$.toast({'text': text,'icon': 'error', 'position':'top-right'});
		}
	});
}

getInstitucion<?= $sufijo ?>();

function NuevaInstitucion<?= $sufijo ?>(){
	ViewModal('presentacion/mantInstitucion','accion=NUEVO&idopcion=16&nivel=<?= $nivel + 1 ?>&directo=1&sufijodirecto=<?= $sufijo ?>','divmodal<?= $nivel ?>','Registrar Institución',0);
}

function editarInstitucion<?= $sufijo ?>(){
	if($('#idinstitucion<?= $sufijo ?>').val()>0){
		ViewModal('presentacion/mantInstitucion','idinstitucion='+$('#idinstitucion<?= $sufijo ?>').val()+'&accion=MODIFICAR&idopcion=16&nivel=<?= $nivel + 1 ?>&puedeeditar=<?= $puedeeditar ?>&directo=1&sufijodirecto=<?= $sufijo ?>','divmodal<?= $nivel ?>','Edición de Institucion');
	}else{
		$.toast({'text': 'Seleccione la Institucion que desea actualizar.','icon': 'error', 'position':'top-right'});
	}
}

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
			url: 'controlador/contSucursal.php',
			data: datax
		})
		.done(function( text ) {
			$('#divmodal<?= $nivel-1 ?>Contenido').LoadingOverlay('hide');
			var json = JSON.parse(text);
			text = json.mensaje;
			evaluarActividadSistema(text);
			if(text.substring(0,3)!="***"){
				<?php if($directo==1){ ?>
					var idinstitucionori = <?= $idinstitucionori ?>;
					if(idinstitucionori!=json.idinstitucion){
						getInstitucion<?= $sufijodirecto ?>(json.idinstitucion,json.idsucursal);
					}else{
						getSucursal<?= $sufijodirecto ?>(json.idsucursal);
					}
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
	let correcto=true;

	return ValidarCampos('formRegistro<?= $sufijo ?>');
}


$(document).ready(function(){
	$('#divmodal<?= $nivel-1 ?>').on('shown.bs.modal', function () {
	   
	}); 
})

</script>