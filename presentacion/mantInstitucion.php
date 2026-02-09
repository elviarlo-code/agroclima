<?php
require_once('../logica/clsCase.php');
$objCase = new clsCase();

$sufijoPadre = "institucion";
$sufijo = "mantIns";
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

$tipo_doc = $objCase->getListTableFiltroSimple('mgtablagenerald', 'estado', 'N', 'idtablageneral', 1, 'codigo', 6);

$id = 0;
if($_GET['accion']=='MODIFICAR'){
	$id = $_GET['idinstitucion'];
	$puedeeditar = $_GET['puedeeditar'];
	$registro = $objCase->getRowTableById('mginstitucion',$id,'idinstitucion');
}

?>
<form name="formRegistro<?= $sufijo ?>" id="formRegistro<?= $sufijo ?>">
	<div class="row gutter-b">
		<div class="col-md-6">
			<div class="form-group row">
				<label class="col-3 col-form-label">Tipo Doc.:</label>
				<div class="col-9">
					<select class="form-control" name="tipodoc<?= $sufijo ?>" id="tipodoc<?= $sufijo ?>">
						<?php 
							foreach($tipo_doc as $k=>$v){ 
								$select = "";
								if($v['codigo']==0){
									$select = "select";
								}
						?>
						<option value="<?= $v['codigo'] ?>" <?= $select ?>><?= $v['descripcion'] ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Nro Doc.:</label>
				<div class="col-9">
					<div class="input-group">
						<input type="text" class="form-control" autocomplete="off" placeholder="Numero de Documento" name="ruc<?= $sufijo ?>" id="ruc<?= $sufijo ?>" validar="SI" onblur="validarTextoEntrada(this, '[0-9a-zA-ZñÑ]')" value="<?= ($id>0)? $registro['ruc'] : '' ?>" data-toggle="tooltip" data-placement="top" data-original-title="" />
						<input type="hidden" class="form-control" name="idinstitucion<?= $sufijo ?>" id="idinstitucion<?= $sufijo ?>" value="<?= ($id>0)? $registro['idinstitucion'] : 0 ?>" />
						<div class="input-group-append">
							<button class="btn btn-light-secondary" type="button" onclick="consultarRucSunatOnLine<?= $sufijo ?>()">
								<img src="files/imagenes/logo_sunat.png" width="26" data-toggle="tooltip" title="" data-original-title="Consultar en Sunat">
							</button>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Nombre:</label>
				<div class="col-9">
					<input type="text" class="form-control" validar="SI" autocomplete="off" placeholder="Razon Social" name="nombre<?= $sufijo ?>" id="nombre<?= $sufijo ?>" value="<?= ($id>0)? $registro['nombre'] : '' ?>" />
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Dirección:</label>
				<div class="col-9">
					<textarea class="form-control" name="direccion<?= $sufijo ?>" id="direccion<?= $sufijo ?>" validar="SI" style="height: 82px;" placeholder="Dirección..."><?= ($id>0)? $registro['direccion'] : '' ?></textarea>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group row">
				<label class="col-3 col-form-label">Ubigeo:</label>
				<div class="col-9">
					<div class="input-group">
						<input type="text" class="form-control" autocomplete="off" validar="SI" placeholder="Buscar Distrito" readonly name="direccion_distrito<?= $sufijo ?>" id="direccion_distrito<?= $sufijo ?>" value="<?= ($id>0)? $registro['direccion_distrito'] : '' ?>" data-toggle="tooltip" data-placement="top" data-original-title="" />
						<input class="form-control" type="hidden" name="direccion_provincia<?= $sufijo ?>" id="direccion_provincia<?= $sufijo ?>" value="<?= ($id>0)? $registro['direccion_provincia'] : '' ?>" />
						<input class="form-control" type="hidden" name="direccion_departamento<?= $sufijo ?>" id="direccion_departamento<?= $sufijo ?>" value="<?= ($id>0)? $registro['direccion_departamento'] : '' ?>" />
						<input class="form-control" type="hidden" name="codigo_ubigeo_distrito<?= $sufijo ?>" id="codigo_ubigeo_distrito<?= $sufijo ?>" value="<?= ($id>0)? $registro['codigo_ubigeo_distrito'] : '' ?>" />
						<input class="form-control" type="hidden" name="codigo_ubigeo_provincia<?= $sufijo ?>" id="codigo_ubigeo_provincia<?= $sufijo ?>" value="<?= ($id>0)? $registro['codigo_ubigeo_provincia'] : '' ?>" />
						<input class="form-control" type="hidden" name="codigo_ubigeo_departamento<?= $sufijo ?>" id="codigo_ubigeo_departamento<?= $sufijo ?>" value="<?= ($id>0)? $registro['codigo_ubigeo_departamento'] : '' ?>" />
						<div class="input-group-append">
							<button class="btn btn-light-dark" type="button" onclick="consultarUbigeo<?= $sufijo ?>()">
								<i class="fa fa-search"></i>
							</button>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">¿Para Facturación?:</label>
				<div class="col-9">
					<select class="form-control" name="parafact<?= $sufijo ?>" id="parafact<?= $sufijo ?>">
						<option value="1">SI</option>
						<option value="0" selected>NO</option>
					</select>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Doc. Representante:</label>
				<div class="col-9">
					<input type="text" class="form-control" autocomplete="off" placeholder="" name="nrodocrepresentante<?= $sufijo ?>" id="nrodocrepresentante<?= $sufijo ?>" value="<?= ($id>0)? $registro['nrodocrepresentante'] : '' ?>" />
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Representante:</label>
				<div class="col-9">
					<input type="text" class="form-control" autocomplete="off" placeholder="" name="nombrerepresentante<?= $sufijo ?>" id="nombrerepresentante<?= $sufijo ?>" value="<?= ($id>0)? $registro['nombrerepresentante'] : '' ?>" />
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

	$('#tipodoc<?= $sufijo ?>').val('<?= $registro['tipodoc'] ?>');
	$('#parafact<?= $sufijo ?>').val('<?= $registro['parafact'] ?>');

<?php } ?>

function consultarRucSunatOnLine<?= $sufijo ?>(){
	let nrodoc = $('#ruc<?= $sufijo ?>').val();
	if(verificarDocumento<?= $sufijo ?>()){
		if(nrodoc.length==8 || nrodoc.length==11){
			$('#formRegistro<?= $sufijo ?>').LoadingOverlay('show',{
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
		        	$('#formRegistro<?= $sufijo ?>').LoadingOverlay('hide');
		    	}
			})
			.done(function( text ) {
				$('#formRegistro<?= $sufijo ?>').LoadingOverlay('hide');
				if(text!='[]'){
					var json = JSON.parse(text);
					let nombre = json.nombre+' '+json.apellido_paterno+' '+json.apellido_materno;
					$('#nombre<?= $sufijo ?>').val(nombre.trim());
					$('#direccion<?= $sufijo ?>').val(json.direccion);
				}else{
					Swal.fire("Error de Sistema", "RUC no encontrado en SUNAT. Consulte directamente en SUNAT: <a href='http://www.sunat.gob.pe/cl-ti-itmrconsruc/FrameCriterioBusquedaMovil.jsp' target='_blank'>CONSULTA RUC SUNAT</a>", "error");
				}
			});
		}else{
			Swal.fire("Error de Sistema", "Ingrese un DNI / RUC válido", "error");
		}
	}
}

function verificarDocumento<?= $sufijo ?>(){
	let correcto=true;
	let msj=" Existe errores en su formulario! Verifíquelo!";
	let nrodoc = $('#ruc<?= $sufijo ?>').val();

	if(nrodoc.trim()==''){
		correcto=false;
		AgregarError('ruc<?= $sufijo ?>','Especifique Documento de la persona');
	}else{
		QuitarError('ruc<?= $sufijo ?>');
	}
	
	if(!correcto){
		Swal.fire("Error de Sistema", msj, "error");
	}
	return correcto;
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
			url: 'controlador/contInstitucion.php',
			data: datax
		})
		.done(function( text ) {
			$('#divmodal<?= $nivel-1 ?>Contenido').LoadingOverlay('hide');
			var json = JSON.parse(text);
			text = json.mensaje;
			evaluarActividadSistema(text);
			if(text.substring(0,3)!="***"){
				<?php if($directo==1){ ?>
					getInstitucion<?= $sufijodirecto ?>(json.idinstitucion);
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

function consultarUbigeo<?= $sufijo ?>(){
	ViewModal('presentacion/viewBusquedaUbigeo','accion=BUSQUEDA&nivel=<?php echo ($nivel+1); ?>&sufijo=<?= $sufijo ?>','divmodal<?php echo $nivel; ?>','Buscar Ubigeo');
}

function seleccionaUbigeoPersona<?= $sufijo ?>(iddistrito, idprovincia, iddepartamento, distrito, provincia, departamento, input, sufijo){

	$("#codigo_ubigeo_distrito"+sufijo).val(iddistrito);
	$("#codigo_ubigeo_provincia"+sufijo).val(idprovincia);
	$("#codigo_ubigeo_departamento"+sufijo).val(iddepartamento);

	$("#direccion_distrito"+sufijo).val(distrito);
	$("#direccion_provincia"+sufijo).val(provincia);
	$("#direccion_departamento"+sufijo).val(departamento);

	CloseModal("divmodal<?php echo $nivel; ?>");
}

$(document).ready(function(){
	$('#divmodal<?= $nivel-1 ?>').on('shown.bs.modal', function () {
	   
	}); 
})


</script>