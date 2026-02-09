<?php
require_once("logica/clsUsuario.php");
require_once("logica/clsCase.php");
require_once("logica/clsCompartido.php");

$objUsu = new clsUsuario();
$objCase = new clsCase();

$permisos=$objUsu->consultarOpciones($_SESSION['idperfil']);
$permisos=$permisos->fetchAll(PDO::FETCH_NAMED);

$img="";

$subheader = "subheader-fixed"; //cabecera fija
$subheader_estilo = "subheader-solid";
if(true){
	$subheader = "";
	$subheader_estilo = "subheader-transparent";
}

$template = "light.css";
if(true){
	$template = "dark.css";
}

$accesosDirectos = array();
foreach($permisos as $k=>$v){
	if($v['accesodirecto']==1){
		$accesosDirectos[] = $v;
		if(count($accesosDirectos)==3){
			break;
		}
	}
}

?>
<!DOCTYPE html>
<html lang="es">
	<!--begin::Head-->
	<head>
		<meta charset="utf-8" />
		<title>Ritec | Riegos Tecnificados</title>
		<meta name="description" content="Updates and statistics" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<!--begin::Fonts-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<!--end::Fonts-->
		<!--begin::Page Vendors Styles(used by this page)-->
		<link href="assets/plugins/custom/datatables/datatables.bundle.css?v=7.0.3" rel="stylesheet" type="text/css" />
		<!--end::Page Vendors Styles-->
		<!--begin::Page Custom Styles(used by this page)-->
		<link href="assets/plugins/custom/kanban/kanban.bundle.css?v=7.0.3" rel="stylesheet" type="text/css" />
		<!--end::Page Custom Styles-->
		<!--begin::Page Vendors Styles(used by this page)-->
		<link href="assets/plugins/custom/fullcalendar/fullcalendar.bundle.css?v=7.0.3" rel="stylesheet" type="text/css" />
		<link href="assets/plugins/custom/jquery-ui/jquery-ui.css" rel="stylesheet" type="text/css" />
		<!--end::Page Vendors Styles-->
		<!--begin::Global Theme Styles(used by all pages)-->
		<link href="assets/plugins/global/plugins.bundle.css?v=7.0.3" rel="stylesheet" type="text/css" />
		<link href="assets/plugins/custom/prismjs/prismjs.bundle.css?v=7.0.3" rel="stylesheet" type="text/css" />
		<link href="assets/css/style.bundle.css?v=7.0.3" rel="stylesheet" type="text/css" />
		<!--end::Global Theme Styles-->
		<!--begin::Layout Themes(used by all pages)-->
		<link href="assets/css/themes/layout/header/base/<?= $template ?>?v=7.0.3" rel="stylesheet" type="text/css" />
		<link href="assets/css/themes/layout/header/menu/<?= $template ?>?v=7.0.3" rel="stylesheet" type="text/css" />
		<link href="assets/css/themes/layout/brand/dark.css?v=7.0.3" rel="stylesheet" type="text/css" />
		<link href="assets/css/themes/layout/aside/dark.css?v=7.0.3" rel="stylesheet" type="text/css" />
		<!--begin::Page jquery toast-->
		<link href="assets/plugins/custom/jquery-toast/src/jquery.toast.css" rel="stylesheet" type="text/css" />

		<!--end::Page Vendors Styles-->
		<!--end::Layout Themes-->
		<link rel="shortcut icon" href="assets/media/logos/logoo.ico" />
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled <?= $subheader ?> aside-enabled aside-fixed aside-minimize-hoverable page-loading">
		<!--begin::Main-->
		<!--begin::Header Mobile-->
		<div id="kt_header_mobile" class="header-mobile align-items-center header-mobile-fixed">
			<!--begin::Logo-->
			<a href="index.html">
				<img alt="Logo" src="assets/media/logos/logoLight.png" />
			</a>
			<!--end::Logo-->
			<!--begin::Toolbar-->
			<div class="d-flex align-items-center">
				<!--begin::Aside Mobile Toggle-->
				<button class="btn p-0 burger-icon burger-icon-left" id="kt_aside_mobile_toggle">
					<span></span>
				</button>
				<!--end::Aside Mobile Toggle-->
				<!--begin::Header Menu Mobile Toggle-->
				<button class="btn p-0 burger-icon ml-4" id="kt_header_mobile_toggle" hidden>
					<span></span>
				</button>
				<!--end::Header Menu Mobile Toggle-->
				<!--begin::Topbar Mobile Toggle-->
				<button class="btn btn-hover-text-primary p-0 ml-2" id="kt_header_mobile_topbar_toggle">
					<span class="svg-icon svg-icon-xl">
						<!--begin::Svg Icon | path:assets/media/svg/icons/General/User.svg-->
						<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
							<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
								<polygon points="0 0 24 0 24 24 0 24" />
								<path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
								<path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
							</g>
						</svg>
						<!--end::Svg Icon-->
					</span>
				</button>
				<!--end::Topbar Mobile Toggle-->
			</div>
			<!--end::Toolbar-->
		</div>
		<!--end::Header Mobile-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Page-->
			<div class="d-flex flex-row flex-column-fluid page">
				<!--begin::Aside-->
				<div class="aside aside-left aside-fixed d-flex flex-column flex-row-auto" id="kt_aside">
					<!--begin::Brand-->
					<div class="brand flex-column-auto" id="kt_brand">
						<!--begin::Logo-->
						<a href="index.html" class="brand-logo">
							<img alt="Logo" src="assets/media/logos/logoLight.png" />
						</a>
						<!--end::Logo-->
						<!--begin::Toggle-->
						<button class="brand-toggle btn btn-sm px-0" id="kt_aside_toggle">
							<span class="svg-icon svg-icon svg-icon-xl">
								<!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Angle-double-left.svg-->
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<polygon points="0 0 24 0 24 24 0 24" />
										<path d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z" fill="#000000" fill-rule="nonzero" transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999)" />
										<path d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999)" />
									</g>
								</svg>
								<!--end::Svg Icon-->
							</span>
						</button>
						<!--end::Toolbar-->
					</div>
					<!--end::Brand-->
					<!--begin::Aside Menu-->
					<div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">
						<!--begin::Menu Container-->
						<div id="kt_aside_menu" class="aside-menu my-4" data-menu-vertical="1" data-menu-scroll="1" data-menu-dropdown-timeout="500">
							<!--begin::Menu Nav-->
							<ul class="menu-nav">
								<li class="menu-item" aria-haspopup="true">
									<a href="#" class="menu-link" onclick="inicioAdmin();  verificarSeleccionadoInicio(this); actualizarTituloSistema('Dashboard','Inicio')">
										<span class="svg-icon menu-icon">
											<!--begin::Svg Icon | path:assets/media/svg/icons/Design/Layers.svg-->
											<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
												<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
													<polygon points="0 0 24 0 24 24 0 24" />
													<path d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z" fill="#000000" fill-rule="nonzero" />
													<path d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z" fill="#000000" opacity="0.3" />
												</g>
											</svg>
											<!--end::Svg Icon-->
										</span>
										<span class="menu-text">Inicio</span>
									</a>
								</li>
								<?php
									reset($permisos);
									$idprincipal='';
									$siguiente=next($permisos);
									foreach($permisos as $k=>$v){
										$ultimo=false;
										if((isset($siguiente['idprincipal']) && ($siguiente['idprincipal']!=$v['idprincipal'])) || !isset($siguiente['idprincipal'])){
											$ultimo=true;
										}

										if($v['idprincipal']!=$idprincipal){
											echo '<li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
													<a href="javascript:;" class="menu-link menu-toggle">
														'.$v['iconprincipal'].'
														<span class="menu-text">'.$v['principal'].'</span>
														<i class="menu-arrow"></i>
													</a>
													<div class="menu-submenu">
														<i class="menu-arrow"></i>
														<ul class="menu-subnav">
															<li class="menu-item menu-item-parent" aria-haspopup="true">
																<span class="menu-link">
																	<span class="menu-text">'.$v['descripcion'].'</span>
																</span>
															</li>';
											$idprincipal=$v['idprincipal'];
										}

										echo '<li class="menu-item" aria-haspopup="true">
												<a href="#" class="menu-link" onclick=\'setRun("'.$v['link'].'","idoptx='.$v['idopcion'].'&idinst='.$v["idinstitucion"].'&idsuc='.$v["idsucursal"].'","contenedorPrincipal","contenedorPrincipal",0); verificarSeleccionado(this); actualizarTituloSistema("'.$v['principal'].'","'.$v['descripcion'].'")\'>
													<i class="menu-bullet menu-bullet-dot">
														<span></span>
													</i>
													<span class="menu-text">'.$v['descripcion'].'</span>
												</a>
											</li>';
										if($ultimo){	
				   							echo '</ul>
												</div>
											</li>';
				   						}


										$siguiente=next($permisos);
									}

								?>
							</ul>
							<!--end::Menu Nav-->
						</div>
						<!--end::Menu Container-->
					</div>
					<!--end::Aside Menu-->
				</div>
				<!--end::Aside-->
				<!--begin::Wrapper-->
				<div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
					<!--begin::Header-->
					<div id="kt_header" class="header header-fixed">
						<!--begin::Container-->
						<div class="container-fluid d-flex align-items-stretch justify-content-between">
							<!--begin::Header Menu Wrapper-->
							<div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
								<!--begin::Header Menu-->
								<div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default" hidden>
									<!--begin::Header Nav-->
									<ul class="menu-nav">
										<li class="menu-item menu-item-open menu-item-here menu-item-submenu menu-item-rel menu-item-open menu-item-here menu-item-active" data-menu-toggle="click" aria-haspopup="true">
											<a href="javascript:;" class="menu-link menu-toggle">
												<span class="menu-text">Pages</span>
												<i class="menu-arrow"></i>
											</a>
											<div class="menu-submenu menu-submenu-classic menu-submenu-left">
												<ul class="menu-subnav">
													<li class="menu-item menu-item-active" aria-haspopup="true">
														<a href="index.html" class="menu-link">
															<span class="svg-icon menu-icon">
																<!--begin::Svg Icon | path:assets/media/svg/icons/Clothes/Briefcase.svg-->
																<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																	<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																		<rect x="0" y="0" width="24" height="24" />
																		<path d="M5.84026576,8 L18.1597342,8 C19.1999115,8 20.0664437,8.79732479 20.1528258,9.83390904 L20.8194924,17.833909 C20.9112219,18.9346631 20.0932459,19.901362 18.9924919,19.9930915 C18.9372479,19.9976952 18.8818364,20 18.8264009,20 L5.1735991,20 C4.0690296,20 3.1735991,19.1045695 3.1735991,18 C3.1735991,17.9445645 3.17590391,17.889153 3.18050758,17.833909 L3.84717425,9.83390904 C3.93355627,8.79732479 4.80008849,8 5.84026576,8 Z M10.5,10 C10.2238576,10 10,10.2238576 10,10.5 L10,11.5 C10,11.7761424 10.2238576,12 10.5,12 L13.5,12 C13.7761424,12 14,11.7761424 14,11.5 L14,10.5 C14,10.2238576 13.7761424,10 13.5,10 L10.5,10 Z" fill="#000000" />
																		<path d="M10,8 L8,8 L8,7 C8,5.34314575 9.34314575,4 11,4 L13,4 C14.6568542,4 16,5.34314575 16,7 L16,8 L14,8 L14,7 C14,6.44771525 13.5522847,6 13,6 L11,6 C10.4477153,6 10,6.44771525 10,7 L10,8 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
																	</g>
																</svg>
																<!--end::Svg Icon-->
															</span>
															<span class="menu-text">My Account</span>
														</a>
													</li>
													<li class="menu-item" aria-haspopup="true">
														<a href="javascript:;" class="menu-link">
															<span class="svg-icon menu-icon">
																<!--begin::Svg Icon | path:assets/media/svg/icons/Code/Compiling.svg-->
																<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																	<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																		<rect x="0" y="0" width="24" height="24" />
																		<path d="M2.56066017,10.6819805 L4.68198052,8.56066017 C5.26776695,7.97487373 6.21751442,7.97487373 6.80330086,8.56066017 L8.9246212,10.6819805 C9.51040764,11.267767 9.51040764,12.2175144 8.9246212,12.8033009 L6.80330086,14.9246212 C6.21751442,15.5104076 5.26776695,15.5104076 4.68198052,14.9246212 L2.56066017,12.8033009 C1.97487373,12.2175144 1.97487373,11.267767 2.56066017,10.6819805 Z M14.5606602,10.6819805 L16.6819805,8.56066017 C17.267767,7.97487373 18.2175144,7.97487373 18.8033009,8.56066017 L20.9246212,10.6819805 C21.5104076,11.267767 21.5104076,12.2175144 20.9246212,12.8033009 L18.8033009,14.9246212 C18.2175144,15.5104076 17.267767,15.5104076 16.6819805,14.9246212 L14.5606602,12.8033009 C13.9748737,12.2175144 13.9748737,11.267767 14.5606602,10.6819805 Z" fill="#000000" opacity="0.3" />
																		<path d="M8.56066017,16.6819805 L10.6819805,14.5606602 C11.267767,13.9748737 12.2175144,13.9748737 12.8033009,14.5606602 L14.9246212,16.6819805 C15.5104076,17.267767 15.5104076,18.2175144 14.9246212,18.8033009 L12.8033009,20.9246212 C12.2175144,21.5104076 11.267767,21.5104076 10.6819805,20.9246212 L8.56066017,18.8033009 C7.97487373,18.2175144 7.97487373,17.267767 8.56066017,16.6819805 Z M8.56066017,4.68198052 L10.6819805,2.56066017 C11.267767,1.97487373 12.2175144,1.97487373 12.8033009,2.56066017 L14.9246212,4.68198052 C15.5104076,5.26776695 15.5104076,6.21751442 14.9246212,6.80330086 L12.8033009,8.9246212 C12.2175144,9.51040764 11.267767,9.51040764 10.6819805,8.9246212 L8.56066017,6.80330086 C7.97487373,6.21751442 7.97487373,5.26776695 8.56066017,4.68198052 Z" fill="#000000" />
																	</g>
																</svg>
																<!--end::Svg Icon-->
															</span>
															<span class="menu-text">Task Manager</span>
															<span class="menu-label">
																<span class="label label-success label-rounded">2</span>
															</span>
														</a>
													</li>
													<li class="menu-item menu-item-submenu" data-menu-toggle="hover" aria-haspopup="true">
														<a href="javascript:;" class="menu-link menu-toggle">
															<span class="svg-icon menu-icon">
																<!--begin::Svg Icon | path:assets/media/svg/icons/Code/CMD.svg-->
																<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																	<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																		<rect x="0" y="0" width="24" height="24" />
																		<path d="M9,15 L7.5,15 C6.67157288,15 6,15.6715729 6,16.5 C6,17.3284271 6.67157288,18 7.5,18 C8.32842712,18 9,17.3284271 9,16.5 L9,15 Z M9,15 L9,9 L15,9 L15,15 L9,15 Z M15,16.5 C15,17.3284271 15.6715729,18 16.5,18 C17.3284271,18 18,17.3284271 18,16.5 C18,15.6715729 17.3284271,15 16.5,15 L15,15 L15,16.5 Z M16.5,9 C17.3284271,9 18,8.32842712 18,7.5 C18,6.67157288 17.3284271,6 16.5,6 C15.6715729,6 15,6.67157288 15,7.5 L15,9 L16.5,9 Z M9,7.5 C9,6.67157288 8.32842712,6 7.5,6 C6.67157288,6 6,6.67157288 6,7.5 C6,8.32842712 6.67157288,9 7.5,9 L9,9 L9,7.5 Z M11,13 L13,13 L13,11 L11,11 L11,13 Z M13,11 L13,7.5 C13,5.56700338 14.5670034,4 16.5,4 C18.4329966,4 20,5.56700338 20,7.5 C20,9.43299662 18.4329966,11 16.5,11 L13,11 Z M16.5,13 C18.4329966,13 20,14.5670034 20,16.5 C20,18.4329966 18.4329966,20 16.5,20 C14.5670034,20 13,18.4329966 13,16.5 L13,13 L16.5,13 Z M11,16.5 C11,18.4329966 9.43299662,20 7.5,20 C5.56700338,20 4,18.4329966 4,16.5 C4,14.5670034 5.56700338,13 7.5,13 L11,13 L11,16.5 Z M7.5,11 C5.56700338,11 4,9.43299662 4,7.5 C4,5.56700338 5.56700338,4 7.5,4 C9.43299662,4 11,5.56700338 11,7.5 L11,11 L7.5,11 Z" fill="#000000" fill-rule="nonzero" />
																	</g>
																</svg>
																<!--end::Svg Icon-->
															</span>
															<span class="menu-text">Team Manager</span>
															<i class="menu-arrow"></i>
														</a>
														<div class="menu-submenu menu-submenu-classic menu-submenu-right">
															<ul class="menu-subnav">
																<li class="menu-item" aria-haspopup="true">
																	<a href="javascript:;" class="menu-link">
																		<i class="menu-bullet menu-bullet-dot">
																			<span></span>
																		</i>
																		<span class="menu-text">Add Team Member</span>
																	</a>
																</li>
																<li class="menu-item" aria-haspopup="true">
																	<a href="javascript:;" class="menu-link">
																		<i class="menu-bullet menu-bullet-dot">
																			<span></span>
																		</i>
																		<span class="menu-text">Edit Team Member</span>
																	</a>
																</li>
																<li class="menu-item" aria-haspopup="true">
																	<a href="javascript:;" class="menu-link">
																		<i class="menu-bullet menu-bullet-dot">
																			<span></span>
																		</i>
																		<span class="menu-text">Delete Team Member</span>
																	</a>
																</li>
																<li class="menu-item" aria-haspopup="true">
																	<a href="javascript:;" class="menu-link">
																		<i class="menu-bullet menu-bullet-dot">
																			<span></span>
																		</i>
																		<span class="menu-text">Team Member Reports</span>
																	</a>
																</li>
																<li class="menu-item" aria-haspopup="true">
																	<a href="javascript:;" class="menu-link">
																		<i class="menu-bullet menu-bullet-dot">
																			<span></span>
																		</i>
																		<span class="menu-text">Assign Tasks</span>
																	</a>
																</li>
																<li class="menu-item" aria-haspopup="true">
																	<a href="javascript:;" class="menu-link">
																		<i class="menu-bullet menu-bullet-dot">
																			<span></span>
																		</i>
																		<span class="menu-text">Promote Team Member</span>
																	</a>
																</li>
															</ul>
														</div>
													</li>
													<li class="menu-item menu-item-submenu" data-menu-toggle="hover" aria-haspopup="true">
														<a href="#" class="menu-link menu-toggle">
															<span class="svg-icon menu-icon">
																<!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Mail-box.svg-->
																<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																	<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																		<rect x="0" y="0" width="24" height="24" />
																		<path d="M22,15 L22,19 C22,20.1045695 21.1045695,21 20,21 L4,21 C2.8954305,21 2,20.1045695 2,19 L2,15 L6.27924078,15 L6.82339262,16.6324555 C7.09562072,17.4491398 7.8598984,18 8.72075922,18 L15.381966,18 C16.1395101,18 16.8320364,17.5719952 17.1708204,16.8944272 L18.118034,15 L22,15 Z" fill="#000000" />
																		<path d="M2.5625,13 L5.92654389,7.01947752 C6.2807805,6.38972356 6.94714834,6 7.66969497,6 L16.330305,6 C17.0528517,6 17.7192195,6.38972356 18.0734561,7.01947752 L21.4375,13 L18.118034,13 C17.3604899,13 16.6679636,13.4280048 16.3291796,14.1055728 L15.381966,16 L8.72075922,16 L8.17660738,14.3675445 C7.90437928,13.5508602 7.1401016,13 6.27924078,13 L2.5625,13 Z" fill="#000000" opacity="0.3" />
																	</g>
																</svg>
																<!--end::Svg Icon-->
															</span>
															<span class="menu-text">Projects Manager</span>
															<i class="menu-arrow"></i>
														</a>
														<div class="menu-submenu menu-submenu-classic menu-submenu-right">
															<ul class="menu-subnav">
																<li class="menu-item" aria-haspopup="true">
																	<a href="javascript:;" class="menu-link">
																		<i class="menu-bullet menu-bullet-line">
																			<span></span>
																		</i>
																		<span class="menu-text">Latest Projects</span>
																	</a>
																</li>
																<li class="menu-item" aria-haspopup="true">
																	<a href="javascript:;" class="menu-link">
																		<i class="menu-bullet menu-bullet-line">
																			<span></span>
																		</i>
																		<span class="menu-text">Ongoing Projects</span>
																	</a>
																</li>
																<li class="menu-item" aria-haspopup="true">
																	<a href="javascript:;" class="menu-link">
																		<i class="menu-bullet menu-bullet-line">
																			<span></span>
																		</i>
																		<span class="menu-text">Urgent Projects</span>
																	</a>
																</li>
																<li class="menu-item" aria-haspopup="true">
																	<a href="javascript:;" class="menu-link">
																		<i class="menu-bullet menu-bullet-line">
																			<span></span>
																		</i>
																		<span class="menu-text">Completed Projects</span>
																	</a>
																</li>
																<li class="menu-item" aria-haspopup="true">
																	<a href="javascript:;" class="menu-link">
																		<i class="menu-bullet menu-bullet-line">
																			<span></span>
																		</i>
																		<span class="menu-text">Dropped Projects</span>
																	</a>
																</li>
															</ul>
														</div>
													</li>
													<li class="menu-item" aria-haspopup="true">
														<a href="javascript:;" class="menu-link">
															<span class="svg-icon menu-icon">
																<!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Spam.svg-->
																<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																	<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																		<rect x="0" y="0" width="24" height="24" />
																		<path d="M4.5,3 L19.5,3 C20.3284271,3 21,3.67157288 21,4.5 L21,19.5 C21,20.3284271 20.3284271,21 19.5,21 L4.5,21 C3.67157288,21 3,20.3284271 3,19.5 L3,4.5 C3,3.67157288 3.67157288,3 4.5,3 Z M8,5 C7.44771525,5 7,5.44771525 7,6 C7,6.55228475 7.44771525,7 8,7 L16,7 C16.5522847,7 17,6.55228475 17,6 C17,5.44771525 16.5522847,5 16,5 L8,5 Z M10.5857864,14 L9.17157288,15.4142136 C8.78104858,15.8047379 8.78104858,16.4379028 9.17157288,16.8284271 C9.56209717,17.2189514 10.1952621,17.2189514 10.5857864,16.8284271 L12,15.4142136 L13.4142136,16.8284271 C13.8047379,17.2189514 14.4379028,17.2189514 14.8284271,16.8284271 C15.2189514,16.4379028 15.2189514,15.8047379 14.8284271,15.4142136 L13.4142136,14 L14.8284271,12.5857864 C15.2189514,12.1952621 15.2189514,11.5620972 14.8284271,11.1715729 C14.4379028,10.7810486 13.8047379,10.7810486 13.4142136,11.1715729 L12,12.5857864 L10.5857864,11.1715729 C10.1952621,10.7810486 9.56209717,10.7810486 9.17157288,11.1715729 C8.78104858,11.5620972 8.78104858,12.1952621 9.17157288,12.5857864 L10.5857864,14 Z" fill="#000000" />
																	</g>
																</svg>
																<!--end::Svg Icon-->
															</span>
															<span class="menu-text">Create New Project</span>
														</a>
													</li>
												</ul>
											</div>
										</li>
									</ul>
									<!--end::Header Nav-->
								</div>
								<!--end::Header Menu-->
							</div>
							<!--end::Header Menu Wrapper-->
							<!--begin::Topbar-->
							<div class="topbar">
								<!--begin::Notifications-->
								<div class="dropdown" hidden>
									<!--begin::Toggle-->
									<div class="topbar-item" data-toggle="dropdown" data-offset="10px,0px">
										<div class="btn btn-icon btn-clean btn-dropdown btn-lg mr-1 pulse pulse-primary">
											<span class="svg-icon svg-icon-xl svg-icon-primary">
												<!--begin::Svg Icon | path:assets/media/svg/icons/Code/Compiling.svg-->
												<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
													<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
														<rect x="0" y="0" width="24" height="24" />
														<path d="M2.56066017,10.6819805 L4.68198052,8.56066017 C5.26776695,7.97487373 6.21751442,7.97487373 6.80330086,8.56066017 L8.9246212,10.6819805 C9.51040764,11.267767 9.51040764,12.2175144 8.9246212,12.8033009 L6.80330086,14.9246212 C6.21751442,15.5104076 5.26776695,15.5104076 4.68198052,14.9246212 L2.56066017,12.8033009 C1.97487373,12.2175144 1.97487373,11.267767 2.56066017,10.6819805 Z M14.5606602,10.6819805 L16.6819805,8.56066017 C17.267767,7.97487373 18.2175144,7.97487373 18.8033009,8.56066017 L20.9246212,10.6819805 C21.5104076,11.267767 21.5104076,12.2175144 20.9246212,12.8033009 L18.8033009,14.9246212 C18.2175144,15.5104076 17.267767,15.5104076 16.6819805,14.9246212 L14.5606602,12.8033009 C13.9748737,12.2175144 13.9748737,11.267767 14.5606602,10.6819805 Z" fill="#000000" opacity="0.3" />
														<path d="M8.56066017,16.6819805 L10.6819805,14.5606602 C11.267767,13.9748737 12.2175144,13.9748737 12.8033009,14.5606602 L14.9246212,16.6819805 C15.5104076,17.267767 15.5104076,18.2175144 14.9246212,18.8033009 L12.8033009,20.9246212 C12.2175144,21.5104076 11.267767,21.5104076 10.6819805,20.9246212 L8.56066017,18.8033009 C7.97487373,18.2175144 7.97487373,17.267767 8.56066017,16.6819805 Z M8.56066017,4.68198052 L10.6819805,2.56066017 C11.267767,1.97487373 12.2175144,1.97487373 12.8033009,2.56066017 L14.9246212,4.68198052 C15.5104076,5.26776695 15.5104076,6.21751442 14.9246212,6.80330086 L12.8033009,8.9246212 C12.2175144,9.51040764 11.267767,9.51040764 10.6819805,8.9246212 L8.56066017,6.80330086 C7.97487373,6.21751442 7.97487373,5.26776695 8.56066017,4.68198052 Z" fill="#000000" />
													</g>
												</svg>
												<!--end::Svg Icon-->
											</span>
											<span class="pulse-ring"></span>
										</div>
									</div>
									<!--end::Toggle-->
									<!--begin::Dropdown-->
									<div class="dropdown-menu p-0 m-0 dropdown-menu-right dropdown-menu-anim-up dropdown-menu-lg">
										<form>
											<!--begin::Header-->
											<div class="d-flex flex-column pt-12 bgi-size-cover bgi-no-repeat rounded-top" style="background-image: url(assets/media/misc/bg-1.jpg)">
												<!--begin::Title-->
												<h4 class="d-flex flex-center rounded-top">
													<span class="text-white">User Notifications</span>
													<span class="btn btn-text btn-success btn-sm font-weight-bold btn-font-md ml-2">23 new</span>
												</h4>
												<!--end::Title-->
												<!--begin::Tabs-->
												<ul class="nav nav-bold nav-tabs nav-tabs-line nav-tabs-line-3x nav-tabs-line-transparent-white nav-tabs-line-active-border-success mt-3 px-8" role="tablist">
													<li class="nav-item">
														<a class="nav-link active show" data-toggle="tab" href="#topbar_notifications_notifications">Alerts</a>
													</li>
													<li class="nav-item">
														<a class="nav-link" data-toggle="tab" href="#topbar_notifications_events">Events</a>
													</li>
													<li class="nav-item">
														<a class="nav-link" data-toggle="tab" href="#topbar_notifications_logs">Logs</a>
													</li>
												</ul>
												<!--end::Tabs-->
											</div>
											<!--end::Header-->
											<!--begin::Content-->
											<div class="tab-content">
												<!--begin::Tabpane-->
												<div class="tab-pane active show p-8" id="topbar_notifications_notifications" role="tabpanel">
													<!--begin::Scroll-->
													<div class="scroll pr-7 mr-n7" data-scroll="true" data-height="300" data-mobile-height="200">
														<!--begin::Item-->
														<div class="d-flex align-items-center mb-6">
															<!--begin::Symbol-->
															<div class="symbol symbol-40 symbol-light-primary mr-5">
																<span class="symbol-label">
																	<span class="svg-icon svg-icon-lg svg-icon-primary">
																		<!--begin::Svg Icon | path:assets/media/svg/icons/Home/Library.svg-->
																		<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																			<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																				<rect x="0" y="0" width="24" height="24" />
																				<path d="M5,3 L6,3 C6.55228475,3 7,3.44771525 7,4 L7,20 C7,20.5522847 6.55228475,21 6,21 L5,21 C4.44771525,21 4,20.5522847 4,20 L4,4 C4,3.44771525 4.44771525,3 5,3 Z M10,3 L11,3 C11.5522847,3 12,3.44771525 12,4 L12,20 C12,20.5522847 11.5522847,21 11,21 L10,21 C9.44771525,21 9,20.5522847 9,20 L9,4 C9,3.44771525 9.44771525,3 10,3 Z" fill="#000000" />
																				<rect fill="#000000" opacity="0.3" transform="translate(17.825568, 11.945519) rotate(-19.000000) translate(-17.825568, -11.945519)" x="16.3255682" y="2.94551858" width="3" height="18" rx="1" />
																			</g>
																		</svg>
																		<!--end::Svg Icon-->
																	</span>
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Text-->
															<div class="d-flex flex-column font-weight-bold">
																<a href="#" class="text-dark text-hover-primary mb-1 font-size-lg">Cool App</a>
																<span class="text-muted">Marketing campaign planning</span>
															</div>
															<!--end::Text-->
														</div>
														<!--end::Item-->
														<!--begin::Item-->
														<div class="d-flex align-items-center mb-6">
															<!--begin::Symbol-->
															<div class="symbol symbol-40 symbol-light-warning mr-5">
																<span class="symbol-label">
																	<span class="svg-icon svg-icon-lg svg-icon-warning">
																		<!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Write.svg-->
																		<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																			<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																				<rect x="0" y="0" width="24" height="24" />
																				<path d="M12.2674799,18.2323597 L12.0084872,5.45852451 C12.0004303,5.06114792 12.1504154,4.6768183 12.4255037,4.38993949 L15.0030167,1.70195304 L17.5910752,4.40093695 C17.8599071,4.6812911 18.0095067,5.05499603 18.0083938,5.44341307 L17.9718262,18.2062508 C17.9694575,19.0329966 17.2985816,19.701953 16.4718324,19.701953 L13.7671717,19.701953 C12.9505952,19.701953 12.2840328,19.0487684 12.2674799,18.2323597 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.701953, 10.701953) rotate(-135.000000) translate(-14.701953, -10.701953)" />
																				<path d="M12.9,2 C13.4522847,2 13.9,2.44771525 13.9,3 C13.9,3.55228475 13.4522847,4 12.9,4 L6,4 C4.8954305,4 4,4.8954305 4,6 L4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 L2,6 C2,3.790861 3.790861,2 6,2 L12.9,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
																			</g>
																		</svg>
																		<!--end::Svg Icon-->
																	</span>
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Text-->
															<div class="d-flex flex-column font-weight-bold">
																<a href="#" class="text-dark-75 text-hover-primary mb-1 font-size-lg">Awesome SAAS</a>
																<span class="text-muted">Project status update meeting</span>
															</div>
															<!--end::Text-->
														</div>
														<!--end::Item-->
													</div>
													<!--end::Scroll-->
												</div>
												<!--end::Tabpane-->
												<!--begin::Tabpane-->
												<div class="tab-pane" id="topbar_notifications_events" role="tabpanel">
													<!--begin::Nav-->
													<div class="navi navi-hover scroll my-4" data-scroll="true" data-height="300" data-mobile-height="200">
														<!--begin::Item-->
														<a href="#" class="navi-item">
															<div class="navi-link">
																<div class="navi-icon mr-2">
																	<i class="flaticon2-settings text-success"></i>
																</div>
																<div class="navi-text">
																	<div class="font-weight-bold">System reboot has been successfully completed</div>
																	<div class="text-muted">12 hrs ago</div>
																</div>
															</div>
														</a>
														<!--end::Item-->
														<!--begin::Item-->
														<a href="#" class="navi-item">
															<div class="navi-link">
																<div class="navi-icon mr-2">
																	<i class="flaticon-safe-shield-protection text-primary"></i>
																</div>
																<div class="navi-text">
																	<div class="font-weight-bold">New order has been placed</div>
																	<div class="text-muted">15 hrs ago</div>
																</div>
															</div>
														</a>
														<!--end::Item-->
														<!--begin::Item-->
														<a href="#" class="navi-item">
															<div class="navi-link">
																<div class="navi-icon mr-2">
																	<i class="flaticon2-notification text-primary"></i>
																</div>
																<div class="navi-text">
																	<div class="font-weight-bold">Company meeting canceled</div>
																	<div class="text-muted">19 hrs ago</div>
																</div>
															</div>
														</a>
														<!--end::Item-->
														<!--begin::Item-->
														<a href="#" class="navi-item">
															<div class="navi-link">
																<div class="navi-icon mr-2">
																	<i class="flaticon2-fax text-success"></i>
																</div>
																<div class="navi-text">
																	<div class="font-weight-bold">New report has been received</div>
																	<div class="text-muted">23 hrs ago</div>
																</div>
															</div>
														</a>
														<!--end::Item-->
														<!--begin::Item-->
														<a href="#" class="navi-item">
															<div class="navi-link">
																<div class="navi-icon mr-2">
																	<i class="flaticon-download-1 text-danger"></i>
																</div>
																<div class="navi-text">
																	<div class="font-weight-bold">Finance report has been generated</div>
																	<div class="text-muted">25 hrs ago</div>
																</div>
															</div>
														</a>
														<!--end::Item-->
														<!--begin::Item-->
														<a href="#" class="navi-item">
															<div class="navi-link">
																<div class="navi-icon mr-2">
																	<i class="flaticon-security text-warning"></i>
																</div>
																<div class="navi-text">
																	<div class="font-weight-bold">New customer comment recieved</div>
																	<div class="text-muted">2 days ago</div>
																</div>
															</div>
														</a>
														<!--end::Item-->
														<!--begin::Item-->
														<a href="#" class="navi-item">
															<div class="navi-link">
																<div class="navi-icon mr-2">
																	<i class="flaticon2-analytics-1 text-success"></i>
																</div>
																<div class="navi-text">
																	<div class="font-weight-bold">New customer is registered</div>
																	<div class="text-muted">3 days ago</div>
																</div>
															</div>
														</a>
														<!--end::Item-->
													</div>
													<!--end::Nav-->
												</div>
												<!--end::Tabpane-->
												<!--begin::Tabpane-->
												<div class="tab-pane" id="topbar_notifications_logs" role="tabpanel">
													<!--begin::Nav-->
													<div class="d-flex flex-center text-center text-muted min-h-200px">All caught up!
													<br />No new notifications.</div>
													<!--end::Nav-->
												</div>
												<!--end::Tabpane-->
											</div>
											<!--end::Content-->
										</form>
									</div>
									<!--end::Dropdown-->
								</div>
								<!--end::Notifications-->
								<!--begin::Quick Actions-->
								<?php foreach($accesosDirectos as $k=>$v){ 
										$tituloaccesodirecto = $v['tituloaccesodirecto'];
										if($tituloaccesodirecto==""){
											$tituloaccesodirecto = $v['descripcion'];
										}
										$icono = $v['icon'];
										if($icono==""){
											$icono = '<span class="svg-icon svg-icon-primary svg-icon-2x">
	<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
	    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
	        <rect x="0" y="0" width="24" height="24"/>
	        <circle fill="#000000" cx="12" cy="12" r="8"/>
	    </g>
	</svg>
</span>';
										}
								?>
								<div class="dropdown mr-5">
									<div class="topbar-item" data-toggle="dropdown" data-offset="10px,0px">
										<div class="btn btn-icon btn-clean btn-dropdown btn-lg mr-1" style="width: 100%" onclick="setRun('<?= $v['link'] ?>','<?= 'idoptx='.$v['idopcion'].'&idinst='.$v["idinstitucion"].'&idsuc='.$v["idsucursal"] ?>','contenedorPrincipal','contenedorPrincipal',1); actualizarTituloSistema('<?= $v['principal'] ?>','<?= $v['descripcion'] ?>')">
											<?= $icono ?> &nbsp;<?= $tituloaccesodirecto ?>
										</div>
									</div>
								</div>
								<?php } ?>
								<!--end::Quick Actions-->
								<!--begin::User-->
								<div class="topbar-item">
									<div class="btn btn-icon w-auto btn-clean d-flex align-items-center btn-lg px-2" id="kt_quick_user_toggle">
										<span class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Hola,</span>
										<span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3" id="adminNombre"><?= $_SESSION['nombre'] ?></span>
										<span class="symbol symbol-35 symbol-light-success">
											<span class="symbol-label font-size-h5 font-weight-bold"><?= substr($_SESSION['nombre'],0,1) ?></span>
										</span>
									</div>
								</div>
								<!--end::User-->
							</div>
							<!--end::Topbar-->
						</div>
						<!--end::Container-->
					</div>
					<!--end::Header-->
					<!--begin::Content-->
					<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
						<!--begin::Subheader-->
						<div class="subheader py-2 py-lg-4 <?= $subheader_estilo ?>" id="kt_subheader">
							<div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
								<!--begin::Info-->
								<div class="d-flex align-items-center flex-wrap mr-2">
									<!--begin::Page Title-->
									<h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5" id="textMenuPrincipal">Dashboard</h5>
									<!--end::Page Title-->
								</div>
								<!--end::Info-->
								<!--begin::Toolbar-->
								<div class="d-flex align-items-center">
									<!--begin::Actions-->
									<a href="#" class="btn btn-clean btn-hover-light-primary- btn-sm font-weight-bold font-size-base mr-1" id="textMenu">Dashboard</a>
									<a href="#" class="btn btn-clean btn-hover-light-primary- active btn-sm font-weight-bold font-size-base mr-1" id="textOpcion">Inicio</a>
									<!--end::Actions-->
								</div>
								<!--end::Toolbar-->
							</div>
						</div>
						<!--end::Subheader-->
						<!--begin::Entry-->
						<div class="d-flex flex-column-fluid">
							<!--begin::Container-->
							<div class="container" id="contenedorPrincipal">
								
								
								

							</div>
							<!--end::Container-->
						</div>
						<!--end::Entry-->
					</div>
					<!--end::Content-->
					<!--begin::Footer-->
					<div class="footer bg-white py-4 d-flex flex-lg-column" id="kt_footer">
						<!--begin::Container-->
						<div class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between">
							<!--begin::Copyright-->
							<div class="text-dark order-2 order-md-1">
								<span class="text-muted font-weight-bold mr-2">2020©</span>
								<a href="http://keenthemes.com/metronic" target="_blank" class="text-dark-75 text-hover-primary">Keenthemes</a>
							</div>
							<!--end::Copyright-->
							<!--begin::Nav-->
							<div class="nav nav-dark">
								<a href="http://keenthemes.com/metronic" target="_blank" class="nav-link pl-0 pr-5">About</a>
								<a href="http://keenthemes.com/metronic" target="_blank" class="nav-link pl-0 pr-5">Team</a>
								<a href="http://keenthemes.com/metronic" target="_blank" class="nav-link pl-0 pr-0">Contact</a>
							</div>
							<!--end::Nav-->
						</div>
						<!--end::Container-->
					</div>
					<!--end::Footer-->
				</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Page-->
		</div>
		<!--end::Main-->
		<!-- begin::User Panel-->
		<div id="kt_quick_user" class="offcanvas offcanvas-right p-10">
			<!--begin::Header-->
			<div class="offcanvas-header d-flex align-items-center justify-content-between pb-5">
				<h3 class="font-weight-bold m-0">Perfil de Usuario
				<small class="text-muted font-size-sm ml-2">Información</small></h3>
				<a href="#" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_quick_user_close">
					<i class="ki ki-close icon-xs text-muted"></i>
				</a>
			</div>
			<!--end::Header-->
			<!--begin::Content-->
			<div class="offcanvas-content pr-5 mr-n5">
				<!--begin::Header-->
				<div class="d-flex align-items-center mt-5">
					<div class="symbol symbol-100 mr-5">
						<div class="symbol-label" id="adminImagenPerfil" style="background-image:url('<?= $_SESSION['foto'] ?>')"></div>
						<i class="symbol-badge bg-success"></i>
					</div>
					<div class="d-flex flex-column">
						<a href="#" class="font-weight-bold font-size-h5 text-dark-75 text-hover-primary" id="admintextNombre"><?= $_SESSION['nombre'] ?></a>
						<div class="text-muted mt-1"><?= $_SESSION['perfil'] ?></div>
						<div class="navi mt-2">
							<a href="#" class="navi-item">
								<span class="navi-link p-0 pb-2">
									<span class="navi-icon mr-1">
										<span class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\Communication\Mail-at.svg-->
											<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
										   		<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										        	<rect x="0" y="0" width="24" height="24"/>
										        	<path d="M11.575,21.2 C6.175,21.2 2.85,17.4 2.85,12.575 C2.85,6.875 7.375,3.05 12.525,3.05 C17.45,3.05 21.125,6.075 21.125,10.85 C21.125,15.2 18.825,16.925 16.525,16.925 C15.4,16.925 14.475,16.4 14.075,15.65 C13.3,16.4 12.125,16.875 11,16.875 C8.25,16.875 6.85,14.925 6.85,12.575 C6.85,9.55 9.05,7.1 12.275,7.1 C13.2,7.1 13.95,7.35 14.525,7.775 L14.625,7.35 L17,7.35 L15.825,12.85 C15.6,13.95 15.85,14.825 16.925,14.825 C18.25,14.825 19.025,13.725 19.025,10.8 C19.025,6.9 15.95,5.075 12.5,5.075 C8.625,5.075 5.05,7.75 5.05,12.575 C5.05,16.525 7.575,19.1 11.575,19.1 C13.075,19.1 14.625,18.775 15.975,18.075 L16.8,20.1 C15.25,20.8 13.2,21.2 11.575,21.2 Z M11.4,14.525 C12.05,14.525 12.7,14.35 13.225,13.825 L14.025,10.125 C13.575,9.65 12.925,9.425 12.3,9.425 C10.65,9.425 9.45,10.7 9.45,12.375 C9.45,13.675 10.075,14.525 11.4,14.525 Z" fill="#000000"/>
										    	</g>
											</svg>
											<!--end::Svg Icon-->
										</span>
									</span>
									<span class="navi-text text-muted text-hover-primary"><?= $_SESSION['login'] ?></span>
								</span>
							</a>
							<a href="#" class="btn btn-sm btn-light-danger font-weight-bolder py-2 px-5" onClick="Salir();">Cerrar Sesion</a>
						</div>
					</div>
				</div>
				<!--end::Header-->
				<!--begin::Separator-->
				<div class="separator separator-dashed mt-8 mb-5"></div>
				<!--end::Separator-->
				<!--begin::Nav-->
				<div class="navi navi-spacer-x-0 p-0">
					<!--begin::Item-->
					<a href="#" class="navi-item" onclick="verUsuarioInfo(); actualizarTituloSistema('Perfil','Información');">
						<div class="navi-link">
							<div class="symbol symbol-40 bg-light mr-3">
								<div class="symbol-label">
									<span class="svg-icon svg-icon-md svg-icon-success">
										<!--begin::Svg Icon | path:assets/media/svg/icons/General/Notification2.svg-->
										<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
											<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
												<rect x="0" y="0" width="24" height="24" />
												<path d="M13.2070325,4 C13.0721672,4.47683179 13,4.97998812 13,5.5 C13,8.53756612 15.4624339,11 18.5,11 C19.0200119,11 19.5231682,10.9278328 20,10.7929675 L20,17 C20,18.6568542 18.6568542,20 17,20 L7,20 C5.34314575,20 4,18.6568542 4,17 L4,7 C4,5.34314575 5.34314575,4 7,4 L13.2070325,4 Z" fill="#000000" />
												<circle fill="#000000" opacity="0.3" cx="18.5" cy="5.5" r="2.5" />
											</g>
										</svg>
										<!--end::Svg Icon-->
									</span>
								</div>
							</div>
							<div class="navi-text">
								<div class="font-weight-bold">Mi Perfil</div>
								<div class="text-muted">Configuración de cuenta y más...</div>
							</div>
						</div>
					</a>
					<!--end:Item-->
					<!--begin::Item-->
					<a href="#" class="navi-item" hidden>
						<div class="navi-link">
							<div class="symbol symbol-40 bg-light mr-3">
								<div class="symbol-label">
									<span class="svg-icon svg-icon-md svg-icon-warning">
										<!--begin::Svg Icon | path:assets/media/svg/icons/Shopping/Chart-bar1.svg-->
										<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
											<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
												<rect x="0" y="0" width="24" height="24" />
												<rect fill="#000000" opacity="0.3" x="12" y="4" width="3" height="13" rx="1.5" />
												<rect fill="#000000" opacity="0.3" x="7" y="9" width="3" height="8" rx="1.5" />
												<path d="M5,19 L20,19 C20.5522847,19 21,19.4477153 21,20 C21,20.5522847 20.5522847,21 20,21 L4,21 C3.44771525,21 3,20.5522847 3,20 L3,4 C3,3.44771525 3.44771525,3 4,3 C4.55228475,3 5,3.44771525 5,4 L5,19 Z" fill="#000000" fill-rule="nonzero" />
												<rect fill="#000000" opacity="0.3" x="17" y="11" width="3" height="6" rx="1.5" />
											</g>
										</svg>
										<!--end::Svg Icon-->
									</span>
								</div>
							</div>
							<div class="navi-text">
								<div class="font-weight-bold">Mis Mensajes</div>
								<div class="text-muted">Bandeja de entrada y tareas...</div>
							</div>
						</div>
					</a>
					<!--end:Item-->
				</div>
				<!--end::Nav-->
				<!--begin::Separator-->
				<div class="separator separator-dashed my-7"></div>
				<!--end::Separator-->
			</div>
			<!--end::Content-->
		</div>
		<!-- end::User Panel-->
		<!--begin::Scrolltop-->
		<div id="kt_scrolltop" class="scrolltop">
			<span class="svg-icon">
				<!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Up-2.svg-->
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
					<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
						<polygon points="0 0 24 0 24 24 0 24" />
						<rect fill="#000000" opacity="0.3" x="11" y="10" width="2" height="10" rx="1" />
						<path d="M6.70710678,12.7071068 C6.31658249,13.0976311 5.68341751,13.0976311 5.29289322,12.7071068 C4.90236893,12.3165825 4.90236893,11.6834175 5.29289322,11.2928932 L11.2928932,5.29289322 C11.6714722,4.91431428 12.2810586,4.90106866 12.6757246,5.26284586 L18.6757246,10.7628459 C19.0828436,11.1360383 19.1103465,11.7686056 18.7371541,12.1757246 C18.3639617,12.5828436 17.7313944,12.6103465 17.3242754,12.2371541 L12.0300757,7.38413782 L6.70710678,12.7071068 Z" fill="#000000" fill-rule="nonzero" />
					</g>
				</svg>
				<!--end::Svg Icon-->
			</span>
		</div>
		<!--end::Scrolltop-->

		<!--begin::Modal-->
		<div class="modal fade" id="divmodal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalSizeLg" aria-hidden="true" inert>
		    <div class="modal-dialog modal-lg" role="document">
		        <div class="modal-content">
		            <div class="modal-header">
		                <h5 class="modal-title" id="divmodal1Titulo">Modal Titulo</h5>
		                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" onclick="CloseModal('divmodal1')">
		                    <i class="ki ki-close"></i>
		                </button>
		            </div>
		            <div class="modal-body" id="divmodal1Contenido">
		                <p>Este es el contenido inicial del modal.</p>
		            </div>
		        </div>
		    </div>
		</div>



		<!--end::Modal-->

		<!--begin::Modal-->
		<div class="modal fade" id="divmodal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalSizeLg" aria-hidden="true" inert>
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header btn-primary">
						<h5 class="modal-title text-white" id="divmodal2Titulo">Modal Titulo</h5>
						<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" onclick="CloseModal('divmodal2')">
		                    <i class="ki ki-close text-white"></i>
		                </button>
					</div>
					<div class="modal-body" id="divmodal2Contenido">
						...
					</div>
				</div>
			</div>
		</div>
		<!--end::Modal-->

		<!--begin::Modal-->
		<div class="modal fade" id="divmodal3" tabindex="-1" role="dialog" aria-labelledby="exampleModalSizeLg" aria-hidden="true" inert>
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header btn-primary">
						<h5 class="modal-title text-white" id="divmodal3Titulo">Modal Titulo</h5>
						<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" onclick="CloseModal('divmodal3')">
		                    <i class="ki ki-close text-white"></i>
		                </button>
					</div>
					<div class="modal-body" id="divmodal3Contenido">
						...
					</div>
				</div>
			</div>
		</div>
		<!--end::Modal-->

		<!--begin::Modal-->
		<div class="modal fade" id="divmodal4" tabindex="-1" role="dialog" aria-labelledby="exampleModalSizeLg" aria-hidden="true" inert>
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header btn-primary">
						<h5 class="modal-title text-white" id="divmodal4Titulo">Modal Titulo</h5>
						<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" onclick="CloseModal('divmodal4')">
		                    <i class="ki ki-close text-white"></i>
		                </button>
					</div>
					<div class="modal-body" id="divmodal4Contenido">
						...
					</div>
				</div>
			</div>
		</div>
		<!--end::Modal-->

		<!--begin::Modal-->
		<div class="modal fade" id="divmodal5" tabindex="-1" role="dialog" aria-labelledby="exampleModalSizeLg" aria-hidden="true" inert>
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header btn-primary">
						<h5 class="modal-title text-white" id="divmodal5Titulo">Modal Titulo</h5>
						<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" onclick="CloseModal('divmodal5')">
		                    <i class="ki ki-close text-white"></i>
		                </button>
					</div>
					<div class="modal-body" id="divmodal5Contenido">
						...
					</div>
				</div>
			</div>
		</div>
		<!--end::Modal-->

		<!--begin::Modal-->
		<div class="modal fade" id="divmodalmediano" tabindex="-1" role="dialog" aria-hidden="true" inert>
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header btn-primary">
						<h5 class="modal-title text-white" id="divmodalmedianoTitulo">Modal Titulo</h5>
						<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" onclick="CloseModal('divmodalmediano')">
		                    <i class="ki ki-close text-white"></i>
		                </button>
					</div>
					<div class="modal-body" id="divmodalmedianoContenido">
						...
					</div>
				</div>
			</div>
		</div>
		<!--end::Modal-->

		<!--begin::Modal-->
		<div class="modal fade" id="divmodalsm" tabindex="-1" role="dialog" aria-hidden="true" inert>
			<div class="modal-dialog modal-sm" role="document">
				<div class="modal-content">
					<div class="modal-header btn-primary">
						<h5 class="modal-title text-white" id="divmodalsmTitulo">Modal Titulo</h5>
						<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" onclick="CloseModal('divmodalsm')">
		                    <i class="ki ki-close text-white"></i>
		                </button>
					</div>
					<div class="modal-body" id="divmodalsmContenido">
						...
					</div>
				</div>
			</div>
		</div>
		<!--end::Modal-->

		<!--begin::Modal-->
		<div class="modal fade" id="divConfirmar" tabindex="-1" role="dialog" aria-labelledby="exampleModalSizeSm" aria-hidden="true" inert>
			<div class="modal-dialog modal-dialog-centered modal-sm" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="divConfirmarTitulo">Modal Titulo</h5>
						<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" onclick="CloseModal('divConfirmar')">
		                    <i class="ki ki-close"></i>
		                </button>
					</div>
					<div class="modal-body" id="divConfirmarContenido">
						...
					</div>
					<div class="modal-body" id="divConfirmarFooter">
						<div class="row">
							<div class="col-md-12 d-flex justify-content-between">
								<button type="button" class="btn btn-light-success font-weight-bold" id="divConfirmarAceptar"><i class="fas fa-check"></i> Aceptar</button>
								<button type="button" class="btn font-weight-bold btn-light-danger" id="divConfirmarCancelar" onclick="CloseModal('divConfirmar')"><i class="fa fa-times"></i> Cancelar</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!--end::Modal-->

		<script>var HOST_URL = "https://keenthemes.com/metronic/tools/preview";</script>
		<!--begin::Global Config(global config for global JS scripts)-->
		<script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1200 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#F3F6F9", "dark": "#212121" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#ECF0F3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#212121", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#ECF0F3", "gray-300": "#E5EAEE", "gray-400": "#D6D6E0", "gray-500": "#B5B5C3", "gray-600": "#80808F", "gray-700": "#464E5F", "gray-800": "#1B283F", "gray-900": "#212121" } }, "font-family": "Poppins" };</script>
		<!--end::Global Config-->
		<!--begin::Global Theme Bundle(used by all pages)-->
		<script src="assets/plugins/global/plugins.bundle.js?v=7.0.3"></script>
		<script src="assets/plugins/custom/prismjs/prismjs.bundle.js?v=7.0.3"></script>
		<script src="assets/js/scripts.bundle.js?v=7.0.3"></script>
		<!--end::Global Theme Bundle-->
		<!--begin::Page Vendors(used by this page)-->
		<script src="assets/plugins/custom/fullcalendar/fullcalendar.bundle.js?v=7.0.3"></script>
		<!--end::Page Vendors-->
		<!--begin::Page Scripts(used by this page)-->
		<script src="assets/js/pages/widgets.js?v=7.0.3"></script>
		<!--end::Page Scripts-->
		<!--begin::jquery-->
		<!-- <script src="assets/plugins/custom/jquery-ui/jquery-ui.js"></script> -->
		<!--end::Page Scripts-->
		<!--begin::jquery toast-->
		<script src="assets/plugins/custom/jquery-toast/src/jquery.toast.js"></script>
		<!--end::Page Scripts-->
		<!--begin::jquery loading-->
		<script src="assets/plugins/custom/jquery-loading/dist/loadingoverlay.min.js"></script>
		<!--end::Page Scripts-->
		<script>
      		// $.widget.bridge('uibutton', $.ui.button);
      		// $.widget.bridge('uitooltip', $.ui.tooltip);
    	</script>
    	<!--begin::Page Scripts(used by this page)-->
		<script src="assets/js/pages/features/miscellaneous/sweetalert2.js?v=7.0.3"></script>
		<!--end::Page Scripts-->
		<!--begin::Page Vendors(used by this page)-->
		<script src="assets/plugins/custom/datatables/datatables.bundle.js?v=7.0.3"></script>
		<!--end::Page Vendors-->
		<!--begin::Page Scripts(used by this page)-->
		<script src="assets/js/pages/crud/datatables/basic/basic.js?v=7.0.3"></script>
		<!--end::Page Scripts-->
		<script src="assets/plugins/custom/jquery-ui/jquery-ui.js"></script>
		<!--begin::Page Scripts(used by this page)-->
		<script src="assets/plugins/custom/kanban/kanban.bundle.js?v=7.0.3"></script>
		<!-- <script src="assets/js/pages/features/miscellaneous/kanban-board.js?v=7.0.3"></script> -->
		<!--end::Page Scripts-->
		<!--begin::highcharts-->
		<script src="assets/plugins/custom/highcharts/highcharts.js"></script>
		<script src="assets/plugins/custom/highcharts/highcharts-more.js"></script>
		<script src="assets/plugins/custom/highcharts/modules/series-label.js"></script>
		<script src="assets/plugins/custom/highcharts/modules/data.js"></script>
		<script src="assets/plugins/custom/highcharts/modules/exporting.js"></script>
		<script src="assets/plugins/custom/highcharts/modules/export-data.js"></script>
		<script src="assets/plugins/custom/highcharts/modules/accessibility.js"></script>
		<script>
			(g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})
        ({key: "AIzaSyC5kiHY_yYxwG2rSwTygPmpglUCATsRRbs", v: "weekly"});
		</script>
		<!--end::highcharts-->
		<script>
			// Función para generar un código único de 16 bytes y convertirlo a una cadena hexadecimal
			function generarCodigoUnicoSessionKey() {
				var array = new Uint8Array(16);
				window.crypto.getRandomValues(array);
				return Array.from(array, byte => ('0' + byte.toString(16)).slice(-2)).join('');
			}
			var sessionKey = generarCodigoUnicoSessionKey();
			var ajaxOriginal = $.ajax;
			$.ajax = function(options) {
			    if (options.data) {
			        if (options.data instanceof FormData) {
			            options.data.append("sessionKey", sessionKey);
			        } else if (Array.isArray(options.data)) {
			            options.data.push({name: "sessionKey", value: sessionKey});
			        } else if (typeof options.data === 'string') {
			            var params = new URLSearchParams(options.data);
			            params.append("sessionKey", sessionKey);
			            options.data = params.toString();
			        } else {
			            options.data["sessionKey"] = sessionKey;
			        }
			    } else {
			        options.data = { sessionKey: sessionKey };
			    }
			    return ajaxOriginal(options);
			}
			
			var xError="%$u$V$%+c";
			var msjError="***";
			function setRun(urlx, parx, divx, msjx, loading=1){
				var s = "", r = /<script>([\s\S]+)<\/script>/mi;
				if(loading==1){
					$('#'+divx).LoadingOverlay('show',{
						size: 20,
						maxSize: 40
					});
				}
				$.ajax({
					method: "POST",
					url: urlx+'.php?ajax=true&'+parx
				})
				.done(function( html ) {	
					evaluarActividadSistema(html);
					if(html.indexOf(xError)==-1){
						if (html.match(r)){
							s = RegExp.$1;
							html = html.replace(r, "");
						}
						$( "#"+divx ).html( html );
						var etiquetaScript=document.createElement("script");
						document.getElementsByTagName("head")[0].appendChild(etiquetaScript);
						etiquetaScript.text=s;
					}
				})
			  	.fail(function(jqXHR, textStatus, errorThrown){    
			    	// $.toast({'text': 'Error al obtener datos','icon': 'error' })
			    	$.toast({
					    heading: 'Error',
					    text: 'No se obtuvieron los datos',
					    showHideTransition: 'plain',
					    icon: 'error'
					});
			  	})
			  	.always(function(){
			  		if(loading==1){
			    		$('#'+divx).LoadingOverlay('hide');
			    	}
			  	});	
			}

			function actualizarTituloSistema(menu, opcion){
				$('#textMenuPrincipal').html(menu);
				$('#textMenu').html(menu);
				$('#textOpcion').html(opcion);
			}

			function evaluarActividadSistema(xform){
				if(xform.length>9){
					if(xform.substring(0,9)==xError){
						Swal.fire("Error de Sistema", xform.substring(9,xform.length), "error").then((result) => {
							/* Read more about isConfirmed, isDenied below */
							if (result.isConfirmed) {
							    Salir();
							}
						});
					}
				}
			}

			function Salir(){
				window.open("index.php","_self")
			}

			function inicioAdmin(){
				setRun('presentacion/dashboard','','contenedorPrincipal','');
			}

			inicioAdmin();

			function verUsuarioInfo(){
				setRun('presentacion/viewUsuarioInformacion','','contenedorPrincipal','');
			}

			var opcionActiva=null;
			function verificarSeleccionado(element){
				$(opcionActiva).parent().removeClass("menu-item-active");
				$(element).parent().addClass("menu-item-active");
				
				opcionActiva=element;
			}

			function verificarSeleccionadoInicio(element){
				$(opcionActiva).parent().removeClass("menu-item-active");
				$(opcionActiva).parent().parent().parent().parent().removeClass("menu-item-open");
				$(element).parent().addClass("menu-item-active");
				
				opcionActiva=element;
			}

			$(function () {
				$(document).tooltip({
					tooltipClass: "custom-tooltip",
					position: {
					    my: "center bottom",
			    		at: "center top-10"
					}
				});
			})

			function ViewModal(page,param,divmodal,title,loading=1){
				$('#'+divmodal).on('show.bs.modal', function(e) {
					$('#'+divmodal+"Titulo").html(title);

					$('#'+divmodal).removeAttr('aria-hidden').removeAttr('inert');
    				$('#'+divmodal).find('button.close').focus(); 

					setRun(page,param,divmodal+'Contenido',divmodal+'Contenido',loading);
					$(e.currentTarget).unbind();
					$('#'+divmodal).on('hidden.bs.modal', function (e) {
						

						$('#'+divmodal).attr('aria-hidden', 'true').attr('inert', '');
    					//$('#'+divmodal).focus(); // Enfoca un elemento visible fuera del modal.

						$('#'+divmodal+"Titulo").html('');
						$('#'+divmodal+'Contenido').html("...");
						$("#"+divmodal+"Aceptar").prop("onclick",null);
						$(e.currentTarget).unbind();
						if($('.modal:visible').length) {
							$('body').addClass('modal-open');
						}
					});
				}).modal({
					keyboard: false,
					backdrop: 'static'
				});
			}

			function CloseModal(divmodal, focusElementId=''){
				$('#'+divmodal).attr('aria-hidden', 'true').attr('inert', '');
				$('#busquedaIdterrenoturno').focus();
				$('#'+divmodal).modal('hide');
				$('#'+divmodal+"Titulo").html('');
				$('#'+divmodal+'Contenido').html("...");
				// Verifica si el elemento al que se quiere enfocar existe y es visible
			    if(focusElementId!=''){
				    if($('#' + focusElementId).is(':visible')) {
				        $('#' + focusElementId).focus();
				    }else{
				        //console.warn(`Elemento con ID "${focusElementId}" no es visible o no existe.`);
				    }
				}else{
					if($('#btnBuscar').is(':visible')) {
				        $('#btnBuscar').focus();
				    }else{
				        $('#textOpcion').focus();
				    }
				}
			}

			function AgregarError(nameElement,mensaje,noparent){
	  			
				$('#'+nameElement).addClass('is-invalid');
	  			$('#'+nameElement).attr("data-toggle","tooltip");
	  			$('#'+nameElement).attr("data-original-title",mensaje);
	  			$('#'+nameElement).attr("data-placement","top");
  			}
  
  			function QuitarError(nameElement, isCorrecto,noparent){
				
				$('#'+nameElement).removeClass('is-invalid');
	  			$('#'+nameElement).attr("data-original-title","");
	  			if(isCorrecto){
		  			$('#'+nameElement).parent().addClass('is-invalid');
	  			}
  			}

  			function ValidarCampos(formulario){
			  	let correcto = true;
			  	$("#"+formulario).find("[validar='SI']").each(function(){
			  		if(!$(this).val() || $(this).val().trim()=="" || $(this).val().trim()=="0"){
			  			$(this).addClass('is-invalid');
			  			$(this).attr("data-toggle","tooltip");
			  			$(this).attr("data-original-title","Campo Obligatorio");
			  			correcto=false;
			  		}else{
			  			$(this).removeClass('is-invalid');
			  			$(this).attr("data-original-title","");
			  		}
			  	});
			  	$("#"+formulario).find("[validar='NO']").each(function(){
			  			$(this).removeClass('is-invalid');
			  			$(this).attr("data-original-title","");
			  	});

			  	if(!correcto){
					Swal.fire("Error de Sistema", "Existen errores en el formulario, verifique", "error");
			  	}
			  	return correcto;
			}

			function solo_numero(evt) {
				let charCode = (evt.which) ? evt.which : event.keyCode;
				if(charCode > 31 && (charCode < 48 || charCode > 57)){
					return false;
				}
				return true;
			} 

			function solo_decimal(evt){
				let charCode = (evt.which) ? evt.which : event.keyCode;
				if((charCode > 31 && (charCode < 46 || charCode > 57))||charCode==47){
					return false;
				}
				return true;
			}

			function validarTextoEntrada(input, patron) {
			  	//funcion para validar que solo ingrese numeros y letras tanto minusculas y mayusculas
			   	let texto = input.value;
			   	let letras = texto.split("");
			   	for (let x in letras) {
			   		let letra = letras[x]
			   		if (!(new RegExp(patron, "i")).test(letra)) {
			   			letras[x] = "";
			   		}
			   	}
				input.value = letras.join("");
			}

			function QuitarSaltoLinea(texto){
				let cadena = '';
				if (texto) {
					cadena = texto.replaceAll("\n", " ");
				}
				return cadena;	
			}

			(function(proxied) {
			 	window.confirm = function() {
					NuevoConfirmar(arguments[0], arguments[1]);
			  	};
			})(window.confirm);

			function NuevoConfirmar(text,accionOk){
				var divmodal='divConfirmar';
				var icon="fa-question-circle";
				
				$('#'+divmodal).on('hidden.bs.modal', function (e) {
					$('#'+divmodal).attr('aria-hidden', 'true').attr('inert', '');
					$(e.currentTarget).unbind();
					$("#"+divmodal+"Titulo").html('');
					$("#"+divmodal+'Contenido').html("...");
					if($('.modal:visible').length) {
						$('body').addClass('modal-open');
					}
				}).on('show.bs.modal', function(e) {
					$('#'+divmodal).removeAttr('aria-hidden').removeAttr('inert');
    				$('#'+divmodal).find('button.close').focus(); 
					$("#"+divmodal+"Titulo").html('<i class="fa '+icon+'"></i> Mensaje de Confirmación');
					$("#"+divmodal+"Contenido").html(text);
					$("#"+divmodal+"Aceptar").attr("onclick",accionOk+';CloseModal("'+divmodal+'");');
				}).modal({
					keyboard: false,
					backdrop: 'static'
				});	
			}
		</script>
	</body>
	<!--end::Body-->
</html>