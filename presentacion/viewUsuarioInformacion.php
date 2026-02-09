<?php
require_once('../logica/clsCase.php');
require_once('../logica/clsUsuario.php');
$objUser= new clsUsuario();
$objCase = new clsCase();

$id = $_SESSION['idusuario'];
$user = $objUser->consultarUsuarioById($id);
$user = $user->fetch(PDO::FETCH_NAMED);

$foto = "assets/media/users/blank.png";
if($user['foto']!=""){
	if(file_exists("../files/imagenes/".$user['foto'])){
		$foto = "files/imagenes/".$user['foto'];
	}
}

?>
<!--begin::Profile Personal Information-->
<div class="d-flex flex-row">
	<!--begin::Aside-->
	<div class="flex-row-auto offcanvas-mobile w-250px w-xxl-350px" id="kt_profile_aside">
		<!--begin::Profile Card-->
		<div class="card card-custom card-stretch">
			<!--begin::Body-->
			<div class="card-body pt-4">
				<!--begin::Toolbar-->
				<div class="d-flex justify-content-end" >
					<div class="dropdown dropdown-inline" hidden>
						<a href="#" class="btn btn-clean btn-hover-light-primary btn-sm btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="ki ki-bold-more-hor"></i>
						</a>
						<div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
							<!--begin::Navigation-->
							<ul class="navi navi-hover py-5">
								<li class="navi-item">
									<a href="#" class="navi-link">
										<span class="navi-icon">
											<i class="flaticon2-list-3"></i>
										</span>
										<span class="navi-text">Directorio</span>
									</a>
								</li>
								<li class="navi-item">
									<a href="#" class="navi-link">
										<span class="navi-icon">
											<i class="flaticon2-bell-2"></i>
										</span>
										<span class="navi-text">Llamadas</span>
									</a>
								</li>
								<li class="navi-item">
									<a href="#" class="navi-link">
										<span class="navi-icon">
											<i class="flaticon2-gear"></i>
										</span>
										<span class="navi-text">Configuración</span>
									</a>
								</li>
								<li class="navi-separator my-3"></li>
								<li class="navi-item">
									<a href="#" class="navi-link">
										<span class="navi-icon">
											<i class="flaticon2-magnifier-tool"></i>
										</span>
										<span class="navi-text">Ayuda</span>
									</a>
								</li>
							</ul>
							<!--end::Navigation-->
						</div>
					</div>
				</div>
				<!--end::Toolbar-->
				<!--begin::User-->
				<div class="d-flex align-items-center mb-2 mt-5">
					<div class="symbol symbol-60 symbol-xxl-100 mr-5 align-self-start align-self-xxl-center">
						<div class="symbol-label" id="viewImagenPerfil" style="background-image:url('<?= $foto ?>')"></div>
						<i class="symbol-badge bg-success"></i>
					</div>
					<div>
						<a href="#" class="font-weight-bolder font-size-h5 text-dark-75 text-hover-primary" id="textNombre"><?= $user['nombres'] ?></a>
						<div class="text-muted"><?= $_SESSION['perfil'] ?></div>
						<div class="mt-2">
							<?php if($user['escliente']==1){ ?>
							<a href="#" class="btn btn-sm btn-primary font-weight-bold mr-2 py-2 px-3 px-xxl-5 my-1">Cliente</a>
							<?php } ?>
							<?php if($user['estrabajador']==1){ ?>
							<a href="#" class="btn btn-sm btn-success font-weight-bold py-2 px-3 px-xxl-5 my-1">Trabajador</a>
							<?php } ?>
							<?php if($user['esproveedor']==1){ ?>
							<a href="#" class="btn btn-sm btn-info font-weight-bold py-2 px-3 px-xxl-5 my-1">Proveedor</a>
							<?php } ?>
						</div>
					</div>
				</div>
				<!--end::User-->
				<!--begin::Contact-->
				<div class="py-9">
					<div class="d-flex align-items-center mb-2">
						<span class="font-weight-bold mr-2">Email:</span>
						<a href="#" class="text-muted" id="textEmail"><?= substr($user['email'],0,18).(strlen($user['email'])>18?'...':''); ?></a>
					</div>
					<div class="d-flex align-items-center mb-2">
						<span class="font-weight-bold mr-2">Celular:</span>
						<span class="text-muted" id="textCelular"><?= $user['telcelular'] ?></span>
					</div>
					<div class="d-flex align-items-center mb-2">
						<span class="font-weight-bold mr-2">Facebook:</span>
						<span class="text-muted" id="textFacebook"><?= substr($user['facebook'],0,15).(strlen($user['facebook'])>15?'...':''); ?></span>
					</div>
				</div>
				<!--end::Contact-->
				<ul class="nav flex-column nav-pills">
					<li class="nav-item mb-2">
						<a class="nav-link" id="infoPersonal" data-toggle="tab" href="#tab1">
							<span class="nav-icon">
								<i class="flaticon2-chat-1"></i>
							</span>
							<span class="nav-text">Información Personal</span>
						</a>
					</li>
					<li class="nav-item mb-2">
						<a class="nav-link active" id="infoUsuario" data-toggle="tab" href="#tab2" onclick="resetFormularioUsuario(0)">
							<span class="nav-icon">
								<i class="flaticon2-layers-1"></i>
							</span>
							<span class="nav-text">Información Usuario</span>
						</a>
					</li>
					<li class="nav-item" hidden>
						<a class="nav-link" id="infoContacto" data-toggle="tab" href="#tab3">
							<span class="nav-icon">
								<i class="flaticon2-rocket-1"></i>
							</span>
							<span class="nav-text">Contacto</span>
						</a>
					</li>
				</ul>
			</div>
			<!--end::Body-->
		</div>
		<!--end::Profile Card-->
	</div>
	<!--end::Aside-->
	<!--begin::Content-->
	<div class="flex-row-fluid ml-lg-8">
		<div class="tab-content" id="myTabContent5">
			<div class="tab-pane fade" id="tab1" role="tabpanel" aria-labelledby="infoPersonal">
				<div class="card card-custom card-stretch">
					<!--begin::Header-->
					<div class="card-header py-3">
						<div class="card-title align-items-start flex-column">
							<h3 class="card-label font-weight-bolder text-dark">Información Persona</h3>
							<span class="text-muted font-weight-bold font-size-sm mt-1">Actualiza tu información personal</span>
						</div>
						<div class="card-toolbar">
							<button type="button" class="btn btn-light-success font-weight-bold mr-2" onclick="registrarPersona()">Guardar Cambios</button>
							<!-- <button type="button" class="btn btn-light-danger font-weight-bold" onclick="resetFormularioPersona()">Cancelar</button> -->
						</div>
					</div>
					<!--end::Header-->
					<!--begin::Form-->
					<form class="form" name="formRegistroPersona" id="formRegistroPersona">
						<!--begin::Body-->
						<div class="card-body">
							<div class="form-group row">
								<label class="col-xl-3 col-lg-3 col-form-label text-right">Avatar</label>
								<div class="col-lg-9 col-xl-6">
									<div class="image-input image-input-outline" id="fotoUsuario" name="fotoUsuario">
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
								<label class="col-xl-3 col-lg-3 col-form-label text-right">Nombre</label>
								<div class="col-lg-9 col-xl-6">
									<input class="form-control form-control-lg form-control-solid" validar="SI" name="nombres" id="nombres" type="text" value="<?= $user['nombres'] ?>" autocomplete="off" />
									<input class="form-control form-control-lg form-control-solid" name="idpersona" id="idpersona" type="text" hidden value="<?= $user['idpersona'] ?>" autocomplete="off" />
								</div>
							</div>
							<div class="form-group row">
								<label class="col-xl-3 col-lg-3 col-form-label text-right">Apellidos</label>
								<div class="col-lg-9 col-xl-6">
									<input class="form-control form-control-lg form-control-solid" validar="SI" name="apellidos" id="apellidos" type="text" value="<?= $user['apellidos'] ?>" autocomplete="off" />
								</div>
							</div>
							<div class="row">
								<label class="col-xl-3"></label>
								<div class="col-lg-9 col-xl-6">
									<h5 class="font-weight-bold mt-10 mb-6">Información de Contacto</h5>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-xl-3 col-lg-3 col-form-label text-right">Celular</label>
								<div class="col-lg-9 col-xl-6">
									<div class="input-group input-group-lg input-group-solid">
										<div class="input-group-prepend">
											<span class="input-group-text">
												<i class="la la-phone"></i>
											</span>
										</div>
										<input type="text" class="form-control form-control-lg form-control-solid" name="telcelular" id="telcelular" value="<?= $user['telcelular'] ?>" placeholder="Celular" autocomplete="off" />
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-xl-3 col-lg-3 col-form-label text-right">Email</label>
								<div class="col-lg-9 col-xl-6">
									<div class="input-group input-group-lg input-group-solid">
										<div class="input-group-prepend">
											<span class="input-group-text">
												<i class="la la-at"></i>
											</span>
										</div>
										<input type="text" class="form-control form-control-lg form-control-solid" name="email" id="email" value="<?= $user['email'] ?>" autocomplete="off" placeholder="Email" />
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-xl-3 col-lg-3 col-form-label text-right">Facebook</label>
								<div class="col-lg-9 col-xl-6">
									<div class="input-group input-group-lg input-group-solid">
										<input type="text" class="form-control form-control-lg form-control-solid" name="facebook" id="facebook" autocomplete="off" placeholder="Facebook" value="<?= $user['facebook'] ?>" />
									</div>
								</div>
							</div>
						</div>
						<!--end::Body-->
					</form>
					<!--end::Form-->
				</div>
			</div>
			<div class="tab-pane fade show active" id="tab2" role="tabpanel" aria-labelledby="infoUsuario">
				<!--begin::Card-->
				<div class="card card-custom">
					<!--begin::Header-->
					<div class="card-header py-3">
						<div class="card-title align-items-start flex-column">
							<h3 class="card-label font-weight-bolder text-dark">Cambiar Password</h3>
							<span class="text-muted font-weight-bold font-size-sm mt-1">Actualiza tu contraseña de Usuario</span>
						</div>
						<div class="card-toolbar">
							<button type="button" class="btn btn-light-success font-weight-bold mr-2" onclick="registrarUsuario()">Guardar Cambios</button>
							<!-- <button type="button" class="btn btn-light-danger font-weight-bold" onclick="resetFormularioUsuario()">Cancelar</button> -->
						</div>
					</div>
					<!--end::Header-->
					<!--begin::Form-->
					<form class="form" name="formRegistroUsuario" id="formRegistroUsuario">
						<div class="card-body">
							<!--begin::Alert-->
							<div class="alert alert-custom alert-light-danger fade show mb-10" role="alert">
								<div class="alert-icon">
									<span class="svg-icon svg-icon-3x svg-icon-danger">
										<!--begin::Svg Icon | path:assets/media/svg/icons/Code/Info-circle.svg-->
										<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
											<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
												<rect x="0" y="0" width="24" height="24" />
												<circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10" />
												<rect fill="#000000" x="11" y="10" width="2" height="7" rx="1" />
												<rect fill="#000000" x="11" y="7" width="2" height="2" rx="1" />
											</g>
										</svg>
										<!--end::Svg Icon-->
									</span>
								</div>
								<div class="alert-text font-weight-bold">Configure su contraseña de usuario periódicamente. Por favor apunte su nueva contraseña para no olvidarla ¡o podría quedar sin acceso al sistema!</div>
								<div class="alert-close">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close">
										<span aria-hidden="true">
											<i class="ki ki-close"></i>
										</span>
									</button>
								</div>
							</div>
							<!--end::Alert-->
							<div class="form-group row">
								<label class="col-xl-3 col-lg-3 col-form-label text-alert">Usuario</label>
								<div class="col-lg-9 col-xl-6">
									<div class="input-group input-group-lg input-group-solid">
										<input type="text" readonly class="form-control form-control-lg form-control-solid" name="login" id="login" placeholder="Usuario" value="<?= $user['login'] ?>" />
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-xl-3 col-lg-3 col-form-label text-alert">Password Actual</label>
								<div class="col-lg-9 col-xl-6">
									<input type="text" name="password_actual" id="password_actual" validar="SI" class="form-control form-control-lg form-control-solid" value="" placeholder="Contraseña Actual" />
								</div>
							</div>
							<div class="form-group row">
								<label class="col-xl-3 col-lg-3 col-form-label text-alert">Nuevo Password</label>
								<div class="col-lg-9 col-xl-6">
									<input type="text" name="password_nuevo" id="password_nuevo" validar="SI" class="form-control form-control-lg form-control-solid" value="" placeholder="Nueva Contraseña" />
								</div>
							</div>
							<div class="form-group row">
								<label class="col-xl-3 col-lg-3 col-form-label text-alert">Verificar Password</label>
								<div class="col-lg-9 col-xl-6">
									<input type="text" name="password_verificar" id="password_verificar" validar="SI" class="form-control form-control-lg form-control-solid" value="" placeholder="Verificar Contraseña" />
								</div>
							</div>
						</div>
					</form>
					<!--end::Form-->
				</div>
			</div>
			<div class="tab-pane fade" id="tab3" role="tabpanel" aria-labelledby="infoContacto">
			
			</div>
		</div>
		<!--begin::Card-->
		
	</div>
	<!--end::Content-->
</div>
<!--end::Profile Personal Information-->
<script>
"use strict";

// Class definition
var KTProfile = function () {
	// Elements
	var _avatar;

	var _initAvatar = function () {
		_avatar = new KTImageInput('fotoUsuario');
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


$(document).ready(function(){
	setTimeout(function(){
		$('#password_nuevo').prop('type', 'password');
    	$('#password_actual').prop('type', 'password');
    	$('#password_verificar').prop('type', 'password');
	}, 1000);
})

function resetFormularioPersona(){
	$('#formRegistroPersona').trigger("reset");
	$('#contenedorPrincipal').LoadingOverlay('show',{
		size: 20,
		maxSize: 40
	});

	setTimeout(function(){
		$('#contenedorPrincipal').LoadingOverlay('hide');
	}, 100);
}

function registrarPersona(){
	if(verificarFormularioPersona()){
		$('#contenedorPrincipal').LoadingOverlay('show',{
			size: 20,
			maxSize: 40
		});
		var datax = new FormData($('#formRegistroPersona')[0]);
		datax.append('accion',"ACTUALIZAR_PERFIL_INFORMACION");	
		datax.append('idusuario',"<?= $id ?>");
		$.ajax({
			method: "POST",
			contentType: false, 
            processData: false,
			url: 'controlador/contUsuario.php',
			data: datax,
			dataType: 'json'
		})
		.done(function( json ) {
			var text = json.mensaje;
			$('#contenedorPrincipal').LoadingOverlay('hide');
			evaluarActividadSistema(text);
			if(text.substring(0,3)!="***"){
				$.toast({'text': text,'icon': 'success', 'position':'top-right'});

				if(json.foto!=""){
        			$("#viewImagenPerfil").css("background-image", "url("+json.foto+")");
        			$("#adminImagenPerfil").css("background-image", "url("+json.foto+")");
        		}

        		if(json.nombre!=""){
        			$("#textNombre").text(json.nombre);
        			$("#adminNombre").text(json.nombre);
        			$("#admintextNombre").text(json.nombre);
        		}

        		if(json.email!=""){
        			$("#textEmail").text(json.email);
        		}

        		if(json.celular!=""){
        			$("#textCelular").text(json.celular);
        		}

        		if(json.facebook!=""){
        			$("#textFacebook").text(json.facebook);
        		}
			}else{
				$.toast({'text': text,'icon': 'error', 'position':'top-right'});
			}
		});
	}
}
	
function verificarFormularioPersona(){
	return ValidarCampos('formRegistroPersona');
}

function resetFormularioUsuario(loadin=1){
	$('#formRegistroUsuario').trigger("reset");
	if(loadin==1){
		$('#contenedorPrincipal').LoadingOverlay('show',{
			size: 20,
			maxSize: 40
		});
	}

	setTimeout(function(){
		$('#contenedorPrincipal').LoadingOverlay('hide');
	}, 100);
}

function registrarUsuario(){
	if(verificarFormularioUsuario()){
		$('#contenedorPrincipal').LoadingOverlay('show',{
			size: 20,
			maxSize: 40
		});

		var datax = new FormData($('#formRegistroUsuario')[0]);
		datax.append('accion',"ACTUALIZAR_PERFIL_USUARIO");
		datax.append('idusuario',"<?= $id ?>");	
		datax.append('idpersona',"<?= $user['idpersona'] ?>");		
		$.ajax({
			method: "POST",
			contentType: false, 
            processData: false,
			url: 'controlador/contUsuario.php',
			data: datax
		})
		.done(function( text ) {
			$('#contenedorPrincipal').LoadingOverlay('hide');
			evaluarActividadSistema(text);
			if(text.substring(0,3)!="***"){
				resetFormularioUsuario();
				$.toast({'text': text,'icon': 'success', 'position':'top-right'});				
			}else{
				$.toast({'text': text,'icon': 'error', 'position':'top-right'});
			}
		});
	}
}

function verificarFormularioUsuario(){
	let correcto=true;
	
	if($('#password_nuevo').val()!=$('#password_verificar').val()){
		correcto=false;
		AgregarError('password_verificar','Passwords ingresados no coinciden');
		Swal.fire("Error de Sistema", "Existe errores en su formulario! Verifíquelo!", "error");
		return correcto;
	}else{
		QuitarError('password_verificar');
	}

	return ValidarCampos('formRegistroUsuario');
}
</script>