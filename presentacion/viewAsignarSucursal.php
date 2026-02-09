<?php 
require_once('../logica/clsCase.php');
require_once("../logica/clsUsuario.php");
$objCase = new clsCase;
$objUsuario = new clsUsuario();

$idperfil = $_GET['idperfil'];
$rowPerfil = $objCase->getRowTableFiltroSimple('perfil', 'idperfil', $idperfil);

$data = $objUsuario->consultaPerfilSucursal($idperfil);

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
	    </div>
	    <hr>
	</div>

	<div class="col-md-12 mt-4">
		<table class="table table-bordered table-hover table-light-primary table-sm" id="tabla_permiso">
			<thead>
				<tr>
					<th >#</th>
					<th>Institución</th>
					<th>Sucursal</th>
					<th>Asignar</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$total=0; 
					$index=1;
					while($fila = $data->fetch(PDO::FETCH_NAMED)){
				?>
				<tr style="background: <?php echo ($fila['idconfiguracion']>0)? '#f3fff3':'#ffd6d2'; ?>" id="tr<?= $fila['idinstitucion'].'-'.$fila['idsucursal'] ?>">
					<td class="font-weight-boldest"><?php echo $index;?></td>
					<td><?php echo $fila['institucion'];?></td>
					<td><?php echo $fila['sucursal'];?></td>
					<td data-sort="<?php echo intval($fila['idconfiguracion']); ?>" class="text-center">
						<span class=""><input type="checkbox" name="permiso" id="permiso<?php echo $fila["idinstitucion"].'-'.$fila['idsucursal']; ?>" <?php if($fila['idconfiguracion']>0 && $fila['estado']=='N'){ echo 'checked'; } ?> onclick="ActualizarPermisoSucursal('<?php echo $fila["idconfiguracion"]; ?>','<?php echo $fila["idinstitucion"]; ?>','<?php echo $fila["idsucursal"]; ?>')"></span>
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
	
$('#tabla_permiso').DataTable({
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

function ActualizarPermisoSucursal(idconfiguracion,idinstitucion,idsucursal){
	$.ajax({
		method: "POST",
		url: 'controlador/contUsuario.php',
		data: {"accion":"CAMBIAR_ESTADO_SUCURSAL",
		        "idperfil":'<?= $idperfil ?>',
				"idinstitucion": idinstitucion,
				"idsucursal": idsucursal,
				"idconfiguracion": idconfiguracion,
				"permiso": ($("#permiso"+idinstitucion+'-'+idsucursal).is(':checked')?1:0)
			}
	})
	.done(function( text ) {
		evaluarActividadSistema(text);
		if(text.substring(0,3)!="***"){
			$.toast({'text': text,'icon': 'success', 'position':'top-right'});
			if($("#permiso"+idinstitucion+'-'+idsucursal).is(':checked')==1){
				$("#tr"+idinstitucion+'-'+idsucursal).css("background-color", "#f3fff3");
			}else{
				$("#tr"+idinstitucion+'-'+idsucursal).css("background-color", "#ffd6d2");
			}
		}else{
			$.toast({'text': text,'icon': 'error', 'position':'top-right'});
		}
	});
}

</script>