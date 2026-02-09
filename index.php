<?php
	session_start();
	session_destroy();
?>
<!DOCTYPE html>
<html lang="es">
	<!--begin::Head-->
	<head>
		<meta charset="utf-8" />
		<title>Ritec | Riegos Tecnificados</title>
		<meta name="description" content="Login" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<!--begin::Fonts-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<!--end::Fonts-->
		<!--begin::Page Custom Styles(used by this page)-->
		<link href="assets/css/pages/login/classic/login-1.css?v=7.0.3" rel="stylesheet" type="text/css" />
		<!--end::Page Custom Styles-->
		<!--begin::Global Theme Styles(used by all pages)-->
		<link href="assets/plugins/global/plugins.bundle.css?v=7.0.3" rel="stylesheet" type="text/css" />
		<link href="assets/plugins/custom/prismjs/prismjs.bundle.css?v=7.0.3" rel="stylesheet" type="text/css" />
		<link href="assets/css/style.bundle.css?v=7.0.3" rel="stylesheet" type="text/css" />
		<!--end::Global Theme Styles-->
		<!--begin::Layout Themes(used by all pages)-->
		<link href="assets/css/themes/layout/header/base/light.css?v=7.0.3" rel="stylesheet" type="text/css" />
		<link href="assets/css/themes/layout/header/menu/light.css?v=7.0.3" rel="stylesheet" type="text/css" />
		<link href="assets/css/themes/layout/brand/dark.css?v=7.0.3" rel="stylesheet" type="text/css" />
		<link href="assets/css/themes/layout/aside/dark.css?v=7.0.3" rel="stylesheet" type="text/css" />
		<!--end::Layout Themes-->
		<link rel="shortcut icon" href="assets/media/logos/logoo.ico" />
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
		<!--begin::Main-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Login-->
			<div class="login login-1 login-signin-on d-flex flex-column flex-lg-row flex-row-fluid bg-white" id="kt_login">
				<!--begin::Aside-->
				<div class="login-aside d-flex flex-row-auto bgi-size-cover bgi-no-repeat p-10 p-lg-10" style="background-image: url(assets/media/bg/bg-2.jpg);">
					<!--begin: Aside Container-->
					<div class="d-flex flex-row-fluid flex-column justify-content-between">
						<!--begin: Aside header-->
						<a href="#" class="flex-column-auto mt-5">
							<img src="assets/media/logos/logo_ritec.png" class="max-h-70px" alt="" />
						</a>
						<!--end: Aside header-->
						<!--begin: Aside content-->
						<div class="flex-column-fluid d-flex flex-column justify-content-center">
							<h3 class="font-size-h1 mb-5 text-white">Sistema Agroclimático</h3>
							<p class="font-weight-lighter text-white opacity-80">Mide el clima y el suelo con precisión para decidir mejor el riego y el manejo del cultivo.</p>
						</div>
						<!--end: Aside content-->
						<!--begin: Aside footer for desktop-->
						<div class="d-none flex-column-auto d-lg-flex justify-content-between mt-10">
							<div class="opacity-70 font-weight-bold text-white">© 2025 Ritec</div>
							<div class="d-flex">
								<a href="#" class="text-white">Privacidad</a>
								<a href="#" class="text-white ml-10">Legal</a>
								<a href="#" class="text-white ml-10">Contacto</a>
							</div>
						</div>
						<!--end: Aside footer for desktop-->
					</div>
					<!--end: Aside Container-->
				</div>
				<!--begin::Aside-->
				<!--begin::Content-->
				<div class="flex-row-fluid d-flex flex-column position-relative p-7 overflow-hidden">
					<!--begin::Content body-->
					<div class="d-flex flex-column-fluid flex-center mt-30 mt-lg-0">
						<!--begin::Signin-->
						<div class="login-form login-signin">
							<div class="text-center mb-10 mb-lg-20">
								<h3 class="font-size-h1">Iniciar sesión</h3>
								<p class="text-muted font-weight-bold">Introduzca su nombre de usuario y contraseña</p>
							</div>
							<!--begin::Form-->
							<form class="form" id="frmLogin" name="frmLogin" autocomplete="off" novalidate>
								<div class="form-group">
									<input class="form-control form-control-solid h-auto py-5 px-6" type="text" placeholder="Usuario" name="txtuser" id="txtuser" value="" onKeyUp="if(event.keyCode=='13'){$('#txtclave').focus(); $('#txtclave').select();}" autocomplete="off" />
									<input type="hidden" name="accion" id="accion" value="INGRESAR" />
								</div>
								<div class="form-group">
									<input class="form-control form-control-solid h-auto py-5 px-6" onKeyUp="if(event.keyCode=='13'){LogIn();}" type="password" value="" placeholder="Contraseña" name="txtclave" id="txtclave" autocomplete="off" />
								</div>
								<!--begin::Action-->
								<div class="form-group d-flex flex-wrap justify-content-between align-items-center">
									<a href="javascript:;" class="text-dark-50 text-hover-primary my-3 mr-2" id=""></a>
									<button type="button" onclick="LogIn()" class="btn btn-primary font-weight-bold px-9 py-4 my-3">Iniciar sesión</button>
								</div>
								<!--end::Action-->
							</form>
							<!--end::Form-->
						</div>
						<!--end::Signin-->
					</div>
					<!--end::Content body-->
					<!--begin::Content footer for mobile-->
					<div class="d-flex d-lg-none flex-column-auto flex-column flex-sm-row justify-content-between align-items-center mt-5 p-5">
						<div class="text-dark-50 font-weight-bold order-2 order-sm-1 my-2">© 2020 Ritec</div>
						<div class="d-flex order-1 order-sm-2 my-2">
							<a href="#" class="text-dark-75 text-hover-primary">Privacidad</a>
							<a href="#" class="text-dark-75 text-hover-primary ml-4">Legal</a>
							<a href="#" class="text-dark-75 text-hover-primary ml-4">Contacto</a>
						</div>
					</div>
					<!--end::Content footer for mobile-->
				</div>
				<!--end::Content-->
			</div>
			<!--end::Login-->
		</div>
		<!--end::Main-->
		<script>var HOST_URL = "https://keenthemes.com/metronic/tools/preview";</script>
		<!--begin::Global Config(global config for global JS scripts)-->
		<script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1200 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#F3F6F9", "dark": "#212121" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#ECF0F3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#212121", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#ECF0F3", "gray-300": "#E5EAEE", "gray-400": "#D6D6E0", "gray-500": "#B5B5C3", "gray-600": "#80808F", "gray-700": "#464E5F", "gray-800": "#1B283F", "gray-900": "#212121" } }, "font-family": "Poppins" };</script>
		<!--end::Global Config-->
		<!--begin::Global Theme Bundle(used by all pages)-->
		<script src="assets/plugins/global/plugins.bundle.js?v=7.0.3"></script>
		<script src="assets/plugins/custom/prismjs/prismjs.bundle.js?v=7.0.3"></script>
		<script src="assets/js/scripts.bundle.js?v=7.0.3"></script>
		<!--begin::jquery loading-->
		<script src="assets/plugins/custom/jquery-loading/dist/loadingoverlay.js"></script>
		<!--end::Page Scripts-->
		<!--begin::Page Scripts(used by this page)-->
		<script src="assets/js/pages/features/miscellaneous/sweetalert2.js?v=7.0.3"></script>
		<script>
			function inicio(){
				document.getElementById('txtuser').value = '';
    			document.getElementById('txtclave').value = '';
			  	document.getElementById('txtuser').focus();
			}

			$(document).ready(function(){
				setTimeout(function(){
					$('#txtclave').prop('type', 'password');
				}, 1000);
			})

			inicio();
			
			function LogIn(){
				$('#kt_body').LoadingOverlay('show');
				var datax = $('#frmLogin').serializeArray();
				$.ajax({
					method: "POST",
					url: 'controlador/contUsuario.php',
					data: datax
				})
				.done(function( text ) {
					$('#kt_body').LoadingOverlay('hide');
					if(text.substring(0,3)=='***'){
						// alert(text,'warning');
						Swal.fire("Error de Sistema", text, "error");
					}else{
						window.open(text,'_self');
					}
				});
			}
		</script>
	</body>
	<!--end::Body-->
</html>