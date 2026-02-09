<?php
require_once('../logica/clsCase.php');
require_once('../logica/clsUsuario.php');
$objUser= new clsUsuario();
$objCase = new clsCase();

$sufijoPadre = "usuario";
$sufijo = "mantUsuario";
$idopcion = $_GET['idopcion'];
$puedeeditar = 1;
$nivel = 3;

$listaPerfil = $objCase->getListTableFiltroSimple('perfil','estado','N');

$id = 0;
$foto = "assets/media/users/blank.png";
if($_GET['accion']=='MODIFICAR'){
	$id = $_GET['idusuario'];
	$puedeeditar = $_GET['puedeeditar'];
	$user = $objUser->consultarUsuarioById($id);
	$user = $user->fetch(PDO::FETCH_NAMED);
	
	if($user['foto']!=""){
		if(file_exists("../files/imagenes/".$user['foto'])){
			$foto = "files/imagenes/".$user['foto'];
		}
	}
}

?>
<form name="formRegistro<?= $sufijo ?>" id="formRegistro<?= $sufijo ?>">
	<div class="row">
		<div class="col-md-6" id="divTrabajador">

			<div class="form-group row">
				<label class="col-3 col-form-label text-left">Avatar</label>
				<div class="col-9">
					<div class="image-input image-input-outline" id="foto<?= $sufijo ?>" name="foto<?= $sufijo ?>">
						<div class="image-input-wrapper" style="background-image: url(<?= $foto ?>)"></div>
						<label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar">
							<i class="fa fa-pen icon-sm text-muted"></i>
							<input type="file" name="profile_avatar" accept=".png, .jpg, .jpeg" />
							<input type="hidden" name="profile_avatar_remove" />
						</label>
						<span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="Cancel avatar">
							<i class="ki ki-bold-close icon-xs text-muted"></i>
						</span>
					</div>
					<span class="form-text text-muted">Tipos de archivos permitidos: png, jpg, jpeg.<br>Foto de Usuario</span>
				</div>
			</div>

			<div class="form-group row">
				<label class="col-3 col-form-label">Nro Doc.:</label>
				<div class="col-9">
					<div class="input-group">
						<input type="text" class="form-control" autocomplete="off" placeholder="Numero de Documento" readonly="readonly" name="nrodoc<?= $sufijo ?>" id="nrodoc<?= $sufijo ?>" validar="SI" onblur="indicarTipoDocumento()" value="<?= ($id>0)? $user['nro_documento'] : '' ?>" data-toggle="tooltip" data-placement="top" data-original-title="" />
						<input type="hidden" class="form-control" name="tipodoc<?= $sufijo ?>" id="tipodoc<?= $sufijo ?>" value="<?= ($id>0)? $user['tipo_documento'] : 0 ?>" />
						<input type="hidden" class="form-control" name="idpersona<?= $sufijo ?>" id="idpersona<?= $sufijo ?>" value="<?= ($id>0)? $user['idpersona'] : 0 ?>" />
						<input type="hidden" class="form-control" name="idusuario<?= $sufijo ?>" id="idusuario<?= $sufijo ?>" value="<?= $id ?>" />
						<div class="input-group-append">
							<button class="btn btn-light-primary" type="button" data-toggle="tooltip" data-placement="top" data-original-title="Consultar en Sistema" onclick="consultarDirectorio()"><i class="fa fa-search"></i></button>
						</div>
						<div class="input-group-append" style="display: none;">
							<button class="btn btn-light-secondary" type="button" onclick="consultarRucSunatOnLine()">
								<img src="files/imagenes/logo_sunat.png" width="26" data-toggle="tooltip" title="" data-original-title="Consultar en Sunat">
							</button>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Nombre:</label>
				<div class="col-9">
					<input type="text" readonly="readonly" class="form-control" validar="SI" autocomplete="off" placeholder="Nombre Apellido/Razon Social" name="nombre<?= $sufijo ?>" id="nombre<?= $sufijo ?>" value="<?= ($id>0)? trim($user['nombres'].' '.$user['apellidos']) : '' ?>" />
					<input type="hidden" class="form-control" name="nombreusuario<?= $sufijo ?>" id="nombreusuario<?= $sufijo ?>" value="<?= ($id>0)? $user['nombres'] : '' ?>" />
					<input type="hidden" class="form-control" name="apellidousuario<?= $sufijo ?>" id="apellidousuario<?= $sufijo ?>" value="<?= ($id>0)? $user['apellidos'] : '' ?>" />
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group row">
				<label class="col-3 col-form-label">Dirección:</label>
				<div class="col-9">
					<input type="text" class="form-control" name="direccion<?= $sufijo ?>" id="direccion<?= $sufijo ?>" autocomplete="off" value="<?= ($id>0)? $user['direccion'] : '' ?>" />
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Perfil:</label>
				<div class="col-9">
					<select class="form-control" name="idperfil<?= $sufijo ?>" validar="SI" id="idperfil<?= $sufijo?>">
						<option value="0">- SELECCIONE -</option>
						<?php while($fila=$listaPerfil->fetch(PDO::FETCH_NAMED)){ ?>
							<option value="<?= $fila['idperfil'] ?>"><?= $fila['descripcion'] ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Login:</label>
				<div class="col-9">
					<input type="text" class="form-control" validar="SI" name="login<?= $sufijo ?>" id="login<?= $sufijo ?>" autocomplete="off" value="<?= ($id>0)? $user['login'] : '' ?>" />
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Password:</label>
				<div class="col-9">
					<input type="text" class="form-control" <?php if($id=0){ ?> validar="SI" <?php } ?> name="primeraclave<?= $sufijo ?>" id="primeraclave<?= $sufijo ?>" autocomplete="off" />
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Password:</label>
				<div class="col-9">
					<input type="text" class="form-control" name="segundaclave<?= $sufijo ?>" id="segundaclave<?= $sufijo ?>" />
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 d-flex justify-content-between">
			<?php if($puedeeditar==1){ ?>
			<button type="button" class="btn btn-light-success font-weight-bold" onclick="registrarUsuario<?= $sufijo ?>()"><i class="fa fa-save"></i> Registrar</button>
			<?php } ?>
			<button type="button" class="btn font-weight-bold btn-light-danger" onclick="CloseModal('divmodal1')"><i class="fa fa-times"></i> Cerrar</button>
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
		_avatar = new KTImageInput('foto<?= $sufijo ?>');
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
	$('#idperfil<?= $sufijo ?>').val('<?= $user['idperfil'] ?>');
<?php } ?>

function consultarRucSunatOnLine(){
	let nrodoc = $('#nrodoc<?= $sufijo ?>').val();
	if(verificarDocumento()){
		if(nrodoc.length==8 || nrodoc.length==11){
			indicarTipoDocumento();
			$('#divTrabajador').LoadingOverlay('show',{
				size: 20,
				maxSize: 40
			});
			$.ajax({
				method: "POST",
				url: "controlador/contUsuario.php",
				data: {	
					'accion': "CONSULTAR_DOCUMENTO_WS",	
					'doc': nrodoc,
					'tipodoc': $('#tipodoc<?= $sufijo ?>').val()
				},
				error: function(request, status, error){
		        	$('#divTrabajador').LoadingOverlay('hide');
		    	}
			})
			.done(function( text ) {
				$('#divTrabajador').LoadingOverlay('hide');
				if(text!='[]'){
					var json = JSON.parse(text);
					let nombre = json.nombre+' '+json.apellido_paterno+' '+json.apellido_materno;
					$('#nombre<?= $sufijo ?>').val(nombre.trim());
					$('#nombreusuario<?= $sufijo ?>').val(json.nombre);
					$('#apellidousuario<?= $sufijo ?>').val(json.apellido_paterno+' '+json.apellido_materno);
					$('#direccion<?= $sufijo ?>').val(json.direccion);
					$('#idpersona<?= $sufijo ?>').val(0);
				}else{
					Swal.fire("Error de Sistema", "RUC no encontrado en SUNAT. Consulte directamente en SUNAT: <a href='http://www.sunat.gob.pe/cl-ti-itmrconsruc/FrameCriterioBusquedaMovil.jsp' target='_blank'>CONSULTA RUC SUNAT</a>", "error");
				}
			});
		}else{
			Swal.fire("Error de Sistema", "Ingrese un DNI / RUC válido", "error");
		}
	}
}

function verificarDocumento(){
	let correcto=true;
	let msj=" Existe errores en su formulario! Verifíquelo!";
	let nrodoc = $('#nrodoc<?= $sufijo ?>').val();

	if(nrodoc.trim()==''){
		correcto=false;
		AgregarError('nrodoc<?= $sufijo ?>','Especifique Documento del Usuario');
	}else{
		QuitarError('nrodoc<?= $sufijo ?>');
	}
	
	if(!correcto){
		Swal.fire("Error de Sistema", msj, "error");
	}
	return correcto;
}

function indicarTipoDocumento(){
	let nrodoc = $('#nrodoc<?= $sufijo ?>').val();

	if(nrodoc != ""){
		if(nrodoc.length==8){
			$('#tipodoc<?= $sufijo ?>').val(1);
		}else if(nrodoc.length==11){
			$('#tipodoc<?= $sufijo ?>').val(6);
		}
	}
}

function registrarUsuario<?= $sufijo ?>(){
	if(verificarFormulario<?= $sufijo ?>()){
		$('#divmodal1Contenido').LoadingOverlay('show',{
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
			url: 'controlador/contUsuario.php',
			data: datax
		})
		.done(function( text ) {
			$('#divmodal1Contenido').LoadingOverlay('hide');
			evaluarActividadSistema(text);
			if(text.substring(0,3)!="***"){
				verPagina<?= $sufijoPadre ?>($('#txtNroPaginaFooter<?php echo $sufijoPadre; ?>').val());
				$.toast({'text': text,'icon': 'success', 'position':'top-right'});
				CloseModal("divmodal1");				
			}else{
				$.toast({'text': text,'icon': 'error', 'position':'top-right'});
			}
		});
	}
}
	
function verificarFormulario<?= $sufijo ?>(){
	let correcto=true;
	
	if($('#primeraclave<?= $sufijo ?>').val()!=$('#segundaclave<?= $sufijo ?>').val()){
		correcto=false;
		AgregarError('segundaclave<?= $sufijo ?>','Passwords ingresados no coinciden');
		Swal.fire("Error de Sistema", "Existe errores en su formulario! Verifíquelo!", "error");
		return correcto;
	}else{
		QuitarError('segundaclave<?= $sufijo ?>');
	}

	return ValidarCampos('formRegistro<?= $sufijo ?>');
}

function consultarDirectorio(){
	ViewModal('presentacion/adminDirectorio','buscar=1&nivel=<?= $nivel; ?>','divmodal2','Buscar directorio');
}

function SeleccionarDirectorio(idpersona, tipo_documento, nrodoc, nombres, apellidos, razonsocial, direccion, celular, email){
	$('#nombre<?= $sufijo ?>').val(razonsocial);
	$('#nombreusuario<?= $sufijo ?>').val(nombres);
	$('#apellidousuario<?= $sufijo ?>').val(apellidos);
	$('#direccion<?= $sufijo ?>').val(direccion);
	$('#nrodoc<?= $sufijo ?>').val(nrodoc);
	$('#idpersona<?= $sufijo ?>').val(idpersona);
	$('#tipodoc<?= $sufijo ?>').val(tipo_documento);
	CloseModal("divmodal2");
}

$(document).ready(function(){
	$('#divmodal1').on('shown.bs.modal', function () {
	    $('#primeraclave<?= $sufijo ?>').prop('type', 'password');
	    $('#segundaclave<?= $sufijo ?>').prop('type', 'password');
	}); 
})

</script>