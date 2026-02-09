<?php
require_once('../logica/clsCase.php');
require_once('../logica/clsCompartido.php');

$objCase = new clsCase();
$perfiles = $objCase->getListTableFiltroSimple("perfil","estado","N","","","","","","","","",'idperfil',1,'idperfil',$_GET['idperfil']);

?>
<div class="form-group row mb-8">
	<label class="col-3 col-form-label">Perfil:</label>
	<div class="col-9">
		<select class="form-control" id="cboPerfil" name="cboPerfil" data-toggle="tooltip" data-placement="top" data-original-title="">
			<option value="">- SELECCIONE -</option>
			<?php while($fila = $perfiles->fetch(PDO::FETCH_NAMED)){ ?>
				<option value="<?php echo $fila['idperfil'];?>"><?php echo $fila['descripcion'];?></option>
			<?php } ?>
		</select>
	</div>
</div>
<div class="row">
	<div class="col-md-12 d-flex justify-content-between">
		<button type="button" class="btn btn-light-success font-weight-bold" onclick="CopiarPermisosPerfil()"><i class="fa fa-save"></i> Copiar</button>
		<button type="button" class="btn font-weight-bold btn-light-danger" onclick="CloseModal('divmodalsm')"><i class="fa fa-times"></i> Cerrar</button>
	</div>
</div>
<script>
function CopiarPermisosPerfil(){
	if($("#cboPerfil").val()!=""){
		$.ajax({
			method: "POST",
			url: 'controlador/contUsuario.php',
			data: {"accion":"COPIAR_PERMISOS_PERFIL",
					"idperfil":$("#cboPerfil").val(),
					"idperfilDestino":'<?php echo $_GET['idperfil']; ?>'
				}
		})
		.done(function( text ) {
			evaluarActividadSistema(text);
			if(text.substring(0,3)!="***"){
				$.toast({'text': text,'icon': 'success', 'position':'top-right'});
				setRun('presentacion/viewAsignarPermiso','idperfil='+'<?php echo $_GET['idperfil']; ?>','divmodal1Contenido','divmodal1Contenido');
				CloseModal('divmodalsm');
			}else{
				$.toast({'text': text,'icon': 'error', 'position':'top-right'});
			}
		});
	}else{
		$("#cboPerfil").addClass('is-invalid');
			$("#cboPerfil").attr("data-toggle","tooltip");
			$("#cboPerfil").attr("data-original-title","Campo Obligatorio");
		Swal.fire("Error de Sistema", "Existen errores en el formulario, verifique", "error");
	}
}

</script>