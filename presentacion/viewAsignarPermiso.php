<?php 
require_once('../logica/clsCase.php');
require_once("../logica/clsUsuario.php");
$objCase = new clsCase;
$objUsuario = new clsUsuario();

$idperfil = $_GET['idperfil'];
$rowPerfil = $objCase->getRowTableFiltroSimple('perfil', 'idperfil', $idperfil);

$datapermiso = $objUsuario->consultarOpcionesPorPerfil($idperfil);
$datapermiso = $datapermiso->fetchAll(PDO::FETCH_NAMED);

// echo '<pre>';
// print_r($datapermiso);
// echo '</pre>';
?>
<div class="row">
	<div class="col-md-12">
		<div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap p-0 mb-5">
			<!--begin::Info-->
	        <div class="d-flex align-items-center flex-wrap mr-1">
				
				<!--begin::Page Heading-->
				<div class="d-flex align-items-baseline flex-wrap mr-4">
					<!--begin::Page Title-->
		            <h5 class="text-primary font-weight-bold my-1 mr-4">
		                PERFIL:&nbsp;&nbsp;<span class="font-weight-light"><?= $rowPerfil['descripcion'] ?></span>
		            </h5>
					<!--end::Page Title-->
		        </div>
				<!--end::Page Heading-->
	        </div>
			<!--end::Info-->

			<!--begin::Toolbar-->
	        <div class="d-flex align-items-center">
				<!--begin::Actions-->
	            <a href="#" class="btn btn-light-primary font-weight-bolder btn-sm" onclick="CopiarPermisos(<?= $idperfil ?>)">
	                <i class="fa fas fa-paste"></i> Copiar Permiso
	            </a>
				<!--end::Actions-->
	        </div>
			<!--end::Toolbar-->
	    </div>
	    <hr>
	</div>

	<div class="col-md-12 mt-4">
		<table class="table table-bordered table-hover table-light-primary table-sm" id="tablaPermisoPerfil">
			<thead>
				<tr>
					<th >#</th>
					<th>Módulo</th>
					<th>Opción</th>
					<th>Asignar</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$total=0; 
					$index=1;
					foreach($datapermiso as $k=>$fila){
				?>
				<tr style="background: <?php if($fila['idacceso']>0){ echo '#f3fff3'; }else{ echo '#ffd6d2'; } ?>" id="tr<?php echo $fila['idopcion']; ?>">
					<td class="font-weight-boldest"><?php echo $index;?></td>
					<td><?php echo $fila['principal'];?></td>
					<td><?php echo $fila['descripcion'];?></td>
					<td data-sort="<?php echo intval($fila['idacceso']); ?>" class="text-center">
						<span class=""><input type="checkbox" name="permiso" id="permiso<?php echo $fila["idopcion"] ;?>" <?php if($fila['idacceso']>0 && $fila['estado']=='N'){ echo 'checked'; } ?> onclick="ActualizarPermiso('<?php echo $fila["idacceso"]; ?>','<?php echo $fila["idopcion"]; ?>')"></span>
					</td>
				</tr>
				<?php 
						$index++; 
					}
				?>
			</tbody>	
		</table>
	</div>
</div>
<script>
$('#tablaPermisoPerfil').DataTable({
	'paging'      : true,
	'lengthChange': true,
	'searching'   : true,
	'ordering'    : true,
	'info'        : true,
	'autoWidth'   : true,
	'responsive'  : true,
	"lengthMenu": [[-1, 10, 25, 50, 100], ["Todos", 10, 25, 50, 100]],
	"order": [[ 1, "asc" ], [ 2, "asc" ], [ 3, "asc" ]],
	"language": {
		"decimal":        "",
	    "emptyTable":     "Sin datos",
	    "info":           "Del _START_ al _END_ de _TOTAL_ filas",
	    "infoEmpty":      "Del 0 a 0 de 0 filas",
	    "infoFiltered":   "(filtro de _MAX_ filas totales)",
	    "infoPostFix":    "",
	    "thousands":      ",",
	    "lengthMenu":     "Ver _MENU_ filas",
	    "loadingRecords": "Cargando...",
	    "processing":     "Procesando...",
	    "search":         "Buscar:",
	    "zeroRecords":    "No se encontraron resultados",
	    "paginate": {
	        "first":      "Primero",
	        "last":       "Ultimo",
	        "next":       "Siguiente",
	        "previous":   "Anterior"
	    },
	    "aria": {
	        "sortAscending":  ": orden ascendente",
	        "sortDescending": ": orden descendente"
	    }
	}	  
});

function ActualizarPermiso(idacceso,idopcion){
	$.ajax({
		method: "POST",
		url: 'controlador/contUsuario.php',
		data: {"accion":"CAMBIAR_ESTADO_ACCESO",
		        "idperfil":'<?= $idperfil ?>',
				"idopcion": idopcion,
				"idacceso": idacceso,
				"permiso": ($("#permiso"+idopcion).is(':checked')?1:0)
			}
	})
	.done(function( text ) {
		evaluarActividadSistema(text);
		if(text.substring(0,3)!="***"){
			$.toast({'text': text,'icon': 'success', 'position':'top-right'});
			if($("#permiso"+idopcion).is(':checked')==1){
				$("#tr"+idopcion).css("background-color", "#f3fff3");
			}else{
				$("#tr"+idopcion).css("background-color", "#ffd6d2");
			}
		}else{
			$.toast({'text': text,'icon': 'error', 'position':'top-right'});
		}
	});
}

function CopiarPermisos(idperfil) {
	ViewModal('presentacion/viewCopiarPermisosPerfil','idperfil='+idperfil,'divmodalsm','Copiar Permisos');
}

</script>