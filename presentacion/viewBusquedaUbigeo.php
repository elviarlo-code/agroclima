<?php 
require_once("../logica/clsPersona.php");
$objPersona = new clsPersona();
$listado = $objPersona->listarUbigeo();
$input="";
if(isset($_GET['input'])){
	$input = $_GET['input'];
}
$sufijo="";
if(isset($_GET['sufijo'])){
	$sufijo = $_GET['sufijo'];
}
?>
<div class="table-responsive">
<table class="table table-bordered table-hover table-light-primary table-sm" id="tabla_ubigeo">
	<thead>
		<tr>
			<th>Ubigeo</th>
			<th>Departamento</th>
			<th>Provincia</th>
			<th>Distrito</th>
			<th>*</th>
		</tr>
	</thead>
	<tbody>
		<?php while($fila = $listado->fetch(PDO::FETCH_NAMED)){ ?>
		<tr>
			<td><?php echo $fila['iddistrito'];?></td>
			<td><?php echo $fila['departamento'];?></td>
			<td><?php echo $fila['provincia'];?></td>
			<td><?php echo $fila['distrito'];?></td>
			<td class="text-center">
				<button type="button" class="btn btn-sm btn-light-primary font-weight-bold"
				onclick="seleccionaUbigeoPersona<?= $sufijo ?>('<?php echo $fila['iddistrito'];?>','<?php echo $fila['idprovincia'];?>','<?php echo $fila['iddepartamento'];?>','<?php echo $fila['distrito'];?>','<?php echo $fila['provincia'];?>','<?php echo $fila['departamento'];?>','<?php echo $input; ?>','<?= $sufijo ?>')" 
				><i class="fas fa-check-square"></i> Seleccionar</button>
			</td>
		</tr>
		<?php } ?>
	</tbody>	
</table>
</div>
<script>
	
$('#tabla_ubigeo').DataTable({
	'paging'      : true,
	'lengthChange': true,
	'searching'   : true,
	'ordering'    : true,
	'info'        : true,
	'autoWidth'   : true,
	'responsive'  : true,
	"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
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

</script>