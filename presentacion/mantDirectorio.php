<?php
require_once('../logica/clsCase.php');
$objCase = new clsCase();

$sufijoPadre = "directorio";
$sufijo = "mantDirectorio";
$idopcion = $_GET['idopcion'];
$puedeeditar = 1;
$nivel = 2;
if(isset($_GET['nivel'])){
	$nivel = $_GET['nivel'];
}

$tipo_doc = $objCase->getListTableFiltroSimple('mgtablagenerald', 'estado', 'N', 'idtablageneral', 1);
$genero = $objCase->getListTableFiltroSimple('mgtablagenerald', 'estado', 'N', 'idtablageneral', 2);
$comunicacion = $objCase->getListTableFiltroSimple('mgtablagenerald', 'estado', 'N', 'idtablageneral', 3);


$id = 0;
if($_GET['accion']=='MODIFICAR'){
	$id = $_GET['idpersona'];
	$puedeeditar = $_GET['puedeeditar'];
	$registro = $objCase->getRowTableById('persona',$id,'idpersona');
}

?>
<form name="formRegistro<?= $sufijo ?>" id="formRegistro<?= $sufijo ?>">
	<div class="row gutter-b">
		<div class="col-md-6">
			<div class="form-group row">
				<label class="col-3 col-form-label">Tipo Doc.:</label>
				<div class="col-9">
					<select class="form-control" name="tipo_documento<?= $sufijo ?>" id="tipo_documento<?= $sufijo ?>">
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
						<input type="text" class="form-control" autocomplete="off" placeholder="Numero de Documento" name="nro_documento<?= $sufijo ?>" id="nro_documento<?= $sufijo ?>" validar="SI" onblur="VerificarNroDocumento<?= $sufijo ?>(); validarTextoEntrada(this, '[0-9a-zA-ZñÑ]')" value="<?= ($id>0)? $registro['nro_documento'] : '' ?>" data-toggle="tooltip" data-placement="top" data-original-title="" />
						<input class="form-control" type="hidden" name="nro_documento<?= $sufijo ?>Origen" id="nro_documento<?= $sufijo ?>Origen" value="<?= ($id>0)? $registro['nro_documento'] : '' ?>" />
						<input type="hidden" class="form-control" name="idpersona<?= $sufijo ?>" id="idpersona<?= $sufijo ?>" value="<?= ($id>0)? $registro['idpersona'] : 0 ?>" />
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
					<input type="text" class="form-control" validar="SI" autocomplete="off" placeholder="Nombre/Razon Social" name="nombres<?= $sufijo ?>" id="nombres<?= $sufijo ?>" value="<?= ($id>0)? $registro['nombres'] : '' ?>" />
					<input type="hidden" class="form-control" name="razon_social<?= $sufijo ?>" id="razon_social<?= $sufijo ?>" value="<?= ($id>0)? $registro['razon_social'] : '' ?>" />
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Apellidos:</label>
				<div class="col-9">
					<input type="text" class="form-control" name="apellidos<?= $sufijo ?>" id="apellidos<?= $sufijo ?>" value="<?= ($id>0)? $registro['apellidos'] : '' ?>" autocomplete="off" />
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Dirección:</label>
				<div class="col-9">
					<textarea class="form-control" name="direccion<?= $sufijo ?>" id="direccion<?= $sufijo ?>" style="height: 78px;" placeholder="Dirección..."><?= ($id>0)? $registro['direccion'] : '' ?></textarea>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Tipo:</label>
				<div class="col-9">
					<select class="form-control select2" style="width: 100%;" id="tipo<?= $sufijo ?>" name="tipo<?= $sufijo ?>" multiple="multiple">
						<option value="C">Cliente</option>
						<option value="P">Proveedor</option>
						<option value="T">Trabajador</option>
					</select>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Ubigeo:</label>
				<div class="col-9">
					<div class="input-group">
						<input type="text" class="form-control" autocomplete="off" placeholder="Buscar Distrito" readonly name="ubigeo<?= $sufijo ?>" id="ubigeo<?= $sufijo ?>" value="<?= ($id>0)? $registro['ubigeo'] : '' ?>" data-toggle="tooltip" data-placement="top" data-original-title="" />
						<input class="form-control" type="hidden" name="ubigeo_dir_dis<?= $sufijo ?>" id="ubigeo_dir_dis<?= $sufijo ?>" value="<?= ($id>0)? $registro['ubigeo_dir_dis'] : '' ?>" />	
						<input class="form-control" type="hidden" name="ubigeo_dir_pro<?= $sufijo ?>" id="ubigeo_dir_pro<?= $sufijo ?>" value="<?= ($id>0)? $registro['ubigeo_dir_pro'] : '' ?>" />	
						<input class="form-control" type="hidden" name="ubigeo_dir_dep<?= $sufijo ?>" id="ubigeo_dir_dep<?= $sufijo ?>" value="<?= ($id>0)? $registro['ubigeo_dir_dep'] : '' ?>" />	
						<div class="input-group-append">
							<button class="btn btn-light-dark" type="button" onclick="consultarUbigeo<?= $sufijo ?>()">
								<i class="fa fa-search"></i>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group row">
				<label class="col-3 col-form-label">Sexo:</label>
				<div class="col-9">
					<select class="form-control" name="sexo<?= $sufijo ?>" id="sexo<?= $sufijo ?>">
						<option value="0">- SELECCIONE -</option>
						<?php foreach($genero as $k=>$v){ ?>
							<option value="<?= $v['codigo'] ?>"><?= $v['descripcion'] ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Email:</label>
				<div class="col-9">
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text">
								<i class="la la-at"></i>
							</span>
						</div>
						<input type="text" class="form-control" name="email<?= $sufijo ?>" id="email<?= $sufijo ?>" placeholder="" value="<?= ($id>0)? $registro['email'] : '' ?>" autocomplete="off" />
					</div>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Celular:</label>
				<div class="col-9">
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text">
								<i class="la la-phone"></i>
							</span>
						</div>
						<input type="text" class="form-control" name="telcelular<?= $sufijo ?>" id="telcelular<?= $sufijo ?>" value="<?= ($id>0)? $registro['telcelular'] : '' ?>" placeholder="" autocomplete="off" />
					</div>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Nacimiento:</label>
				<div class="col-9">
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text">
								<i class="la la-calendar"></i>
							</span>
						</div>
						<input type="date" class="form-control" name="fnacimiento<?= $sufijo ?>" id="fnacimiento<?= $sufijo ?>" value="<?= ($id>0)? $registro['fnacimiento'] : '' ?>" placeholder="" />
					</div>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Facebook:</label>
				<div class="col-9">
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text">
								<i class="la fab la-facebook"></i>
							</span>
						</div>
						<input type="text" class="form-control" name="facebook<?= $sufijo ?>" id="facebook<?= $sufijo ?>" value="<?= ($id>0)? $registro['facebook'] : '' ?>" placeholder="" />
					</div>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Comunicación:</label>
				<div class="col-9">
					<select class="form-control" name="medio_comunicacion<?= $sufijo ?>" id="medio_comunicacion<?= $sufijo ?>">
						<option value="0">- SELECCIONE -</option>
						<?php foreach($comunicacion as $k=>$v){ ?>
							<option value="<?= $v['codigo'] ?>"><?= $v['descripcion'] ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Observación:</label>
				<div class="col-9">
					<textarea class="form-control" name="observacion<?= $sufijo ?>" id="observacion<?= $sufijo ?>" style="height: 78px;" placeholder="Observacion..."><?= ($id>0)? $registro['observacion'] : '' ?></textarea>
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
	var escliente = <?= ($registro['escliente']=='')? 0 : $registro['escliente'] ?>;
	var estrabajador = <?= ($registro['estrabajador']=='')? 0 : $registro['estrabajador'] ?>;
	var esproveedor = <?= ($registro['esproveedor']=='')? 0 : $registro['esproveedor'] ?>;

	$('#tipo_documento<?= $sufijo ?>').val('<?= $registro['tipo_documento'] ?>');
	$('#sexo<?= $sufijo ?>').val('<?= ($registro['sexo']=='')? '0' : $registro['sexo'] ?>');
	$('#medio_comunicacion<?= $sufijo ?>').val('<?= ($registro['medio_comunicacion']=='')? '0' : $registro['medio_comunicacion'] ?>');

	var tipo = [];
	if (escliente==1) { tipo.push('T'); }
    if (estrabajador==1) { tipo.push('P'); }
    if (esproveedor==1) { tipo.push('C'); }
    
    $('#tipo<?= $sufijo ?>').val(tipo).trigger('change');
<?php } ?>

function consultarRucSunatOnLine<?= $sufijo ?>(){
	let nrodoc = $('#nro_documento<?= $sufijo ?>').val();
	if(verificarDocumento<?= $sufijo ?>()){
		if(nrodoc.length==8 || nrodoc.length==11){
			indicarTipoDocumento<?= $sufijo ?>();
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
					'tipodoc': $('#tipo_documento<?= $sufijo ?>').val()
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
					$('#razon_social<?= $sufijo ?>').val(nombre.trim());
					$('#nombres<?= $sufijo ?>').val(json.nombre);
					$('#apellidos<?= $sufijo ?>').val(json.apellido_paterno+' '+json.apellido_materno);
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

function verificarDocumento<?= $sufijo ?>(){
	let correcto=true;
	let msj=" Existe errores en su formulario! Verifíquelo!";
	let nrodoc = $('#nro_documento<?= $sufijo ?>').val();

	if(nrodoc.trim()==''){
		correcto=false;
		AgregarError('nro_documento<?= $sufijo ?>','Especifique Documento de la persona');
	}else{
		QuitarError('nro_documento<?= $sufijo ?>');
	}
	
	if(!correcto){
		Swal.fire("Error de Sistema", msj, "error");
	}
	return correcto;
}

function indicarTipoDocumento<?= $sufijo ?>(){
	let nrodoc = $('#nro_documento<?= $sufijo ?>').val();
	let tipo_documento = $('#tipo_documento<?= $sufijo ?>').val();

	if(nrodoc != ""){
		if(tipo_documento=='0'){
			if(nrodoc.length==8){
				$('#tipo_documento<?= $sufijo ?>').val(1);
			}else if(nrodoc.length==11){
				$('#tipo_documento<?= $sufijo ?>').val(6);
			}
		}
	}
}

function registrar<?= $sufijo ?>(){
	if(verificarFormulario<?= $sufijo ?>()){
		$('#divmodal<?= $nivel-1 ?>Contenido').LoadingOverlay('show',{
			size: 20,
			maxSize: 40
		});
		let datax = $('#formRegistro<?= $sufijo ?>').serializeArray();
		let tipo = $('#tipo<?= $sufijo ?>').val();
		datax.push({name: "accion",value:"<?= $_GET['accion'] ?>"});
		datax.push({name: "sufijo",value:"<?= $sufijo ?>"});
		datax.push({name: "idopcion",value:"<?= $idopcion ?>"});
		datax.push({name: "tipo_persona",value: tipo});
		$.ajax({
			method: "POST",
			url: 'controlador/contPersona.php',
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

	if($('#tipo<?= $sufijo ?>').val()=='' || $('#tipo<?= $sufijo ?>').val()==null){
		correcto=false;
		AgregarError('tipo<?= $sufijo ?>','Seleccione tipo de Persona');
		Swal.fire("Error de Sistema", "Existe errores en su formulario! Verifíquelo!", "error");
		return correcto;
	}else{
		QuitarError('tipo<?= $sufijo ?>');
	}

	return ValidarCampos('formRegistro<?= $sufijo ?>');
}

function consultarUbigeo<?= $sufijo ?>(){
	ViewModal('presentacion/viewBusquedaUbigeo','accion=BUSQUEDA&nivel=<?php echo ($nivel+1); ?>&sufijo=<?= $sufijo ?>','divmodal<?php echo $nivel; ?>','Buscar Ubigeo');
}

function seleccionaUbigeoPersona<?= $sufijo ?>(iddistrito, idprovincia, iddepartamento, distrito, provincia, departamento, input, sufijo){

	$("#ubigeo_dir_dis"+sufijo).val(iddistrito);
	$("#ubigeo_dir_pro"+sufijo).val(idprovincia);
	$("#ubigeo_dir_dep"+sufijo).val(iddepartamento);

	ubigeo_text = distrito;
	$("#ubigeo"+sufijo).val(ubigeo_text);

	CloseModal("divmodal<?php echo $nivel; ?>");
}

function VerificarNroDocumento<?= $sufijo ?>(){
	let nro_documento = $("#nro_documento<?= $sufijo ?>").val();
	indicarTipoDocumento<?= $sufijo ?>();
	if($("#nro_documento<?= $sufijo ?>").val() != $("#nro_documento<?= $sufijo ?>Origen").val()){
		$.ajax({
			method: "POST",
			url: 'controlador/contPersona.php',
			data: {
					'accion':'GET_NRO_DOCUMENTO', 
					'nro_documento':nro_documento,
					'sufijo': '<?= $sufijo ?>'
				}
		})
		.done(function( text ) {			
			if(text!='[]'){
				var json = JSON.parse(text);
				$('#razon_social<?= $sufijo ?>').val(json.razon_social);
				$('#nombres<?= $sufijo ?>').val(json.nombres);
				$('#apellidos<?= $sufijo ?>').val(json.apellidos);
				$('#telcelular<?= $sufijo ?>').val(json.telcelular);
				
				$('#fnacimiento<?= $sufijo ?>').val(json.fnacimiento);
				$('#sexo<?= $sufijo ?>').val(json.sexo);
				$('#direccion<?= $sufijo ?>').val(QuitarSaltoLinea(json.direccion));
				$('#email<?= $sufijo ?>').val(json.email);
				$('#observacion<?= $sufijo ?>').val(json.observacion);
				$('#idpersona<?= $sufijo ?>').val(json.idpersona);
				$('#tipo_documento<?= $sufijo ?>').val(json.tipo_documento);
				$('#facebook<?= $sufijo ?>').val(json.facebook);

				$("#ubigeo<?= $sufijo ?>").val(json.ubigeo);
				$("#ubigeo_dir_dis<?= $sufijo ?>").val(json.ubigeo_dir_dis);
				$("#ubigeo_dir_pro<?= $sufijo ?>").val(json.ubigeo_dir_pro);
				$("#ubigeo_dir_dep<?= $sufijo ?>").val(json.ubigeo_dir_dep);

				let tipo = [];
				if (json.estrabajador==1) { tipo.push('T'); }
	            if (json.esproveedor==1) { tipo.push('P'); }
	            if (json.escliente==1) { tipo.push('C'); }
	            
	            $('#tipo<?= $sufijo ?>').val(tipo).trigger('change');
			}			
		});
	}
}

<?php if($_GET['accion'] != 'MODIFICAR'){ ?>
	$('#tipo<?= $sufijo ?>').val(['T']).trigger('change');
<?php } ?>

$(document).ready(function(){
	$('#divmodal<?= $nivel-1 ?>').on('shown.bs.modal', function () {
	    $('#tipo<?= $sufijo ?>').select2({
			placeholder: "Seleccionar Tipo",
		});
	}); 
})


</script>