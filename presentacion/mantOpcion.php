<?php
require_once('../logica/clsCase.php');
$objCase = new clsCase();

$sufijoPadre = "opcion";
$sufijo = "mantOpcion";
$idopcion = $_GET['idopcion'];
$puedeeditar = 1;
$nivel = 2;
if(isset($_GET['nivel'])){
	$nivel = $_GET['nivel'];
}

$listamodulo = $objCase->getListTableFiltroSimple('opcion', 'idopcion_ref', 0, 'estado', 'N');
$listaperfil = $objCase->getListTableFiltroSimple('perfil', 'estado', 'N');
$listaperfil = $listaperfil->fetchAll(PDO::FETCH_NAMED);

$id = 0;
if($_GET['accion']=='MODIFICAR'){
	$id = $_GET['id'];
	$puedeeditar = $_GET['puedeeditar'];
	$registro = $objCase->getRowTableById('opcion',$id,'idopcion');
}

?>
<form name="formRegistro<?= $sufijo ?>" id="formRegistro<?= $sufijo ?>">
	<div class="row gutter-b">
		<div class="col-md-3">
			<ul class="nav flex-column nav-light-success nav-pills" id="myTab3" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" id="home-tab-5" data-toggle="tab" href="#home-5">
						<span class="nav-icon">
							<i class="flaticon2-pen"></i>
						</span>
						<span class="nav-text">Datos Generales</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="profile-tab-5" data-toggle="tab" href="#profile-5" aria-controls="profile">
						<span class="nav-icon">
							<i class="flaticon2-lock"></i>
						</span>
						<span class="nav-text">Permisos Básicos</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="contact-tab-5" data-toggle="tab" href="#contact-5" aria-controls="contact">
						<span class="nav-icon">
							<i class="flaticon2-clip-symbol"></i>
						</span>
						<span class="nav-text">Permisos Espciales</span>
					</a>
				</li>
			</ul>
		</div>
		<div class="col-md-9">
			<div class="tab-content" id="myTabContent5">
				<div class="tab-pane fade show active" id="home-5" role="tabpanel" aria-labelledby="home-tab-5">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Módulo</label>
								<select class="form-control" id="idopcion_ref<?= $sufijo ?>" name="idopcion_ref<?= $sufijo ?>">
									<option value="0">- Seleccione -</option>
									<?php while($fila=$listamodulo->fetch(PDO::FETCH_NAMED)){ ?>
										<option value="<?= $fila['idopcion'] ?>"><?= $fila['descripcion'] ?></option>
									<?php } ?>
								</select>
								<input type="hidden" class="form-control" name="idopcion<?= $sufijo ?>" id="idopcion<?= $sufijo ?>" value="<?= ($id>0)? $registro['idopcion'] : 0 ?>" />
							</div>
							<div class="form-group">
								<label>Descripción</label>
								<input type="text" class="form-control" name="descripcion<?= $sufijo ?>" id="descripcion<?= $sufijo ?>" validar="SI" value="<?= ($id>0)? $registro['descripcion'] : '' ?>" autocomplete="off" placeholder="Nombre de la Opción">
							</div>
							<div class="form-group">
								<label>Link</label>
								<input type="text" class="form-control" name="link<?= $sufijo ?>" id="link<?= $sufijo ?>" validar="SI" autocomplete="off" value="<?= ($id>0)? $registro['link'] : '' ?>" placeholder="Link de la Opción">
							</div>
							<div class="form-group">
								<label>Orden</label>
								<input type="number" class="form-control" name="orden<?= $sufijo ?>" id="orden<?= $sufijo ?>" validar="SI" value="<?= ($id>0)? $registro['orden'] : '' ?>" placeholder="">
							</div>
							<div class="form-group">
								<label>Número de Registro</label>
								<input type="number" class="form-control" name="nro_registro<?= $sufijo ?>" id="nro_registro<?= $sufijo ?>" value="<?= ($id>0)? $registro['nro_registro'] : '' ?>" placeholder="">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Titulo</label>
								<input type="text" class="form-control" name="title<?= $sufijo ?>" id="title<?= $sufijo ?>" autocomplete="off" value="<?= ($id>0)? $registro['title'] : '' ?>" placeholder="Titulo de la Opción">
							</div>
							<div class="form-group">
								<label>Icono(Solo para el Módulo)</label>
								<textarea class="form-control" style="height: 300px" name="icon<?= $sufijo ?>" id="icon<?= $sufijo ?>" placeholder="Icono SVG"><?= ($id>0)? $registro['icon'] : '' ?></textarea>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade" id="profile-5" role="tabpanel" aria-labelledby="profile-tab-5">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label>Perfiles que Pueden Imprimir</label>
								<select class="form-control select2" style="width: 100%;" name="puedeimprimir<?= $sufijo ?>" id="puedeimprimir<?= $sufijo ?>" multiple="multiple">
									<?php foreach($listaperfil as $k=>$v){ ?>
										<option value="<?= $v['idperfil'] ?>"><?= $v['descripcion'] ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="form-group">
								<label>Perfiles que pueden Registrar</label>
								<select class="form-control select2" style="width: 100%;" name="puederegistrar<?= $sufijo ?>" id="puederegistrar<?= $sufijo ?>" multiple="multiple">
									<?php foreach($listaperfil as $k=>$v){ ?>
										<option value="<?= $v['idperfil'] ?>"><?= $v['descripcion'] ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="form-group">
								<label>Perfiles que pueden Editar</label>
								<select class="form-control select2" style="width: 100%;" name="puedeeditar<?= $sufijo ?>" id="puedeeditar<?= $sufijo ?>" multiple="multiple">
									<?php foreach($listaperfil as $k=>$v){ ?>
										<option value="<?= $v['idperfil'] ?>"><?= $v['descripcion'] ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="form-group">
								<label>Perfiles que pueden Anular</label>
								<select class="form-control select2" style="width: 100%;" name="puedeanular<?= $sufijo ?>" id="puedeanular<?= $sufijo ?>" multiple="multiple">
									<?php foreach($listaperfil as $k=>$v){ ?>
										<option value="<?= $v['idperfil'] ?>"><?= $v['descripcion'] ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="form-group">
								<label>Perfiles que pueden Eliminar</label>
								<select class="form-control select2" style="width: 100%;" name="puedeeliminar<?= $sufijo ?>" id="puedeeliminar<?= $sufijo ?>" multiple="multiple">
									<?php foreach($listaperfil as $k=>$v){ ?>
										<option value="<?= $v['idperfil'] ?>"><?= $v['descripcion'] ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade" id="contact-5" role="tabpanel" aria-labelledby="contact-tab-5">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label>Perfiles con Permiso Especial</label>
								<select class="form-control select2" style="width: 100%;" name="opcion_especial<?= $sufijo ?>" id="opcion_especial<?= $sufijo ?>" multiple="multiple">
									<?php foreach($listaperfil as $k=>$v){ ?>
										<option value="<?= $v['idperfil'] ?>"><?= $v['descripcion'] ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="form-group">
								<label>Perfiles con Permiso Especial1</label>
								<select class="form-control select2" style="width: 100%;" name="opcion_especial1<?= $sufijo ?>" id="opcion_especial1<?= $sufijo ?>" multiple="multiple">
									<?php foreach($listaperfil as $k=>$v){ ?>
										<option value="<?= $v['idperfil'] ?>"><?= $v['descripcion'] ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="form-group">
								<label>Perfiles con Permiso Especial2</label>
								<select class="form-control select2" style="width: 100%;" name="opcion_especial2<?= $sufijo ?>" id="opcion_especial2<?= $sufijo ?>" multiple="multiple">
									<?php foreach($listaperfil as $k=>$v){ ?>
										<option value="<?= $v['idperfil'] ?>"><?= $v['descripcion'] ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="form-group">
								<label>¿Opción con Acceso Directo?</label>
								<select class="form-control" name="accesodirecto<?= $sufijo ?>" id="accesodirecto<?= $sufijo ?>">
									<option value="0">NO</option>
									<option value="1">SI</option>
								</select>
							</div>
							<div class="form-group">
								<label>Titulo del Acceso Directo</label>
								<input type="text" class="form-control" name="tituloaccesodirecto<?= $sufijo ?>" id="tituloaccesodirecto<?= $sufijo ?>" autocomplete="off" value="<?= ($id>0)? $registro['tituloaccesodirecto'] : '' ?>" placeholder="">
							</div>
						</div>
					</div>
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
$(function () {
	$('#puedeimprimir<?= $sufijo ?>').select2();
	$('#puederegistrar<?= $sufijo ?>').select2();
	$('#puedeeditar<?= $sufijo ?>').select2();
	$('#puedeanular<?= $sufijo ?>').select2();
	$('#puedeeliminar<?= $sufijo ?>').select2();
	$('#opcion_especial<?= $sufijo ?>').select2();
	$('#opcion_especial1<?= $sufijo ?>').select2();
	$('#opcion_especial2<?= $sufijo ?>').select2();
});

<?php if($_GET['accion']=='MODIFICAR'){ ?>
	puederegistrar = '<?= $registro['puederegistrar'] ?>';
	puederegistrar = puederegistrar.split(",");

	puedeimprimir = '<?= $registro['puedeimprimir'] ?>';
	puedeimprimir = puedeimprimir.split(",");

	puedeeditar = '<?= $registro['puedeeditar'] ?>';
	puedeeditar = puedeeditar.split(",");

	puedeanular = '<?= $registro['puedeanular'] ?>';
	puedeanular = puedeanular.split(",");

	puedeeliminar = '<?= $registro['puedeeliminar'] ?>';
	puedeeliminar = puedeeliminar.split(",");

	opcion_especial = '<?= $registro['opcion_especial'] ?>';
	opcion_especial = opcion_especial.split(",");

	opcion_especial1 = '<?= $registro['opcion_especial1'] ?>';
	opcion_especial1 = opcion_especial1.split(",");

	opcion_especial2 = '<?= $registro['opcion_especial2'] ?>';
	opcion_especial2 = opcion_especial2.split(",");

	$('#puederegistrar<?= $sufijo ?>').val(puederegistrar).trigger('change');
	$('#puedeeliminar<?= $sufijo ?>').val(puedeeliminar).trigger('change');
	$('#puedeimprimir<?= $sufijo ?>').val(puedeimprimir).trigger('change');
	$('#puedeeditar<?= $sufijo ?>').val(puedeeditar).trigger('change');
	$('#puedeanular<?= $sufijo ?>').val(puedeanular).trigger('change');
	$('#opcion_especial<?= $sufijo ?>').val(opcion_especial).trigger('change');
	$('#opcion_especial1<?= $sufijo ?>').val(opcion_especial1).trigger('change');
	$('#opcion_especial2<?= $sufijo ?>').val(opcion_especial2).trigger('change');
	$('#idopcion_ref<?= $sufijo ?>').val(<?= $registro['idopcion_ref'] ?>);
	$('#accesodirecto<?= $sufijo ?>').val(<?= $registro['accesodirecto'] ?>);
<?php } ?>

function registrar<?= $sufijo ?>(){
	if(verificarFormulario<?= $sufijo ?>()){
		$('#divmodal<?= $nivel-1 ?>Contenido').LoadingOverlay('show',{
			size: 20,
			maxSize: 40
		});
		let datax = $('#formRegistro<?= $sufijo ?>').serializeArray();
		let puedeimprimir = $('#puedeimprimir<?= $sufijo ?>').val();
		let puederegistrar = $('#puederegistrar<?= $sufijo ?>').val();
		let puedeeditar = $('#puedeeditar<?= $sufijo ?>').val();
		let puedeanular = $('#puedeanular<?= $sufijo ?>').val();
		let puedeeliminar = $('#puedeeliminar<?= $sufijo ?>').val();
		let opcion_especial = $('#opcion_especial<?= $sufijo ?>').val();
		let opcion_especial1 = $('#opcion_especial1<?= $sufijo ?>').val();
		let opcion_especial2 = $('#opcion_especial2<?= $sufijo ?>').val();
		datax.push({name: "accion",value:"<?= $_GET['accion'] ?>"});
		datax.push({name: "sufijo",value:"<?= $sufijo ?>"});
		datax.push({name: "idopcion",value:"<?= $idopcion ?>"});
		datax.push({name: "puedeimprimir",value: puedeimprimir});
		datax.push({name: "puederegistrar",value: puederegistrar});
		datax.push({name: "puedeeditar",value: puedeeditar});
		datax.push({name: "puedeanular",value: puedeanular});
		datax.push({name: "puedeeliminar",value: puedeeliminar});
		datax.push({name: "opcion_especial",value: opcion_especial});
		datax.push({name: "opcion_especial1",value: opcion_especial1});
		datax.push({name: "opcion_especial2",value: opcion_especial2});
		$.ajax({
			method: "POST",
			url: 'controlador/contOpcion.php',
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
	return ValidarCampos('formRegistro<?= $sufijo ?>');
}

$(document).ready(function(){
	$('#divmodal<?= $nivel-1 ?>').on('shown.bs.modal', function () {
	   
	}); 
})

</script>