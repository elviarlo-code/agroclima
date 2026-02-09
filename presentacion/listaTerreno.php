<?php
require_once('../logica/clsTerreno.php');
$objTerr = new clsTerreno();

$sufijo = "terreno";
$nivel = 1;
if(isset($_GET['nivel'])){
	$nivel = $_GET['nivel'];
}

if(!isset($_GET['cantidad'])){
	$pagina=1;
	$inicio=0;
	$cantidad=12;
}else{
	$pagina=$_GET['pagina'];
	$cantidad=$_GET['cantidad'];
	$inicio=($pagina-1)*$cantidad;
}

$idopcion = (isset($_GET['idopcion']))? $_GET['idopcion']:0;
$puedeeditar = (isset($_GET['editar']))? $_GET['editar']:1;
$puedeanular = (isset($_GET['anular']))? $_GET['anular']:1;
$puedeeliminar = (isset($_GET['eliminar']))? $_GET['eliminar']:1;
$permiso_especial = (isset($_GET['permiso_especial']))? $_GET['permiso_especial']:1;
$permiso_especial1 = (isset($_GET['permiso_especial1']))? $_GET['permiso_especial1']:1;
$permiso_especial2 = (isset($_GET['permiso_especial2']))? $_GET['permiso_especial2']:1;

$data=$objTerr->consultarTerreno($_GET['nombre'], $_GET['estado'], $inicio, $cantidad, false);
$data=$data->fetchAll(PDO::FETCH_NAMED);
$total_datos=$objTerr->consultarTerreno($_GET['nombre'], $_GET['estado'], $inicio, $cantidad, true);
	
$total_pag=ceil($total_datos/$cantidad);
?>
<style>
	div[id^="map-"] {
    	width: 300px;
    	height: 200px;
	}
</style>
<div class="table-responsive">
	<table class="table table-bordered table-vertical-center table-hover table-sm font-size-sm">
	    <thead class="thead-light">
	        <tr>
	            <th scope="col">#</th>
	            <th scope="col">Ubicación</th>
	            <th scope="col">Descripción del Terreno</th>
	            <th scope="col">Estado</th>
	            <th scope="col" colspan="3">Opciones</th>
	        </tr>
	    </thead>
	    <tbody>
	    	<?php foreach($data as $indice=>$fila){
	    			$class="";
	    			if($fila['estado']=='A'){
						$class="text-danger";
					}

	    	?>
	        <tr class="<?= $class ?>">
	            <td class="font-weight-boldest"><?= $fila['idterreno'] ?></td>
	            <td>
            		<div id="map-<?= $fila['idterreno'] ?>"></div>
        		</td>
	            <td>
	            	<div>
						<span class="font-weight-bolder"><?= $fila['descripcion'] ?></span>
					</div>
	            	<div>
						<span class="font-weight-bolder">Área:</span>
						<span class="text-muted font-weight-bold"><?= $fila['area'] ?>ha</span>
					</div>
					<div>
						<span class="font-weight-bolder">Latitud:</span>
						<span class="text-muted font-weight-bold"><?= $fila['latitud'] ?></span>
					</div>
					<div>
						<span class="font-weight-bolder">Longitud:</span>
						<span class="text-muted font-weight-bold"><?= $fila['longitud'] ?></span>
					</div>
					<div>
						<span class="font-weight-bolder">Altitud:</span>
						<span class="text-muted font-weight-bold"><?= $fila['altitud'] ?></span>
					</div>
					<div>
						<span class="font-weight-bolder">Ubigeo:</span>
						<span class="text-muted font-weight-bold" href="#"><?= $fila['ubigeo_texto'] ?></span>
					</div>
					<div>
						<span class="font-weight-bolder">Dirección:</span>
						<span class="text-muted font-weight-bold"><?= $fila['direccion'] ?></span>
					</div>
	            </td>
	            <td><?php if($fila['estado']=='N'){echo "ACTIVO";}else{echo "ANULADO";};?></td>
	            <td>
	            	<button type="button" class="btn btn-light-primary font-weight-bold btn-sm" data-toggle="tooltip" data-placement="top" title="Editar Terreno" href="javascript:void(0)" onclick="editarTerreno('<?php echo $fila['idterreno'];?>')" ><li class="fa fa-edit"></li></button>
	            </td>
	            <td class="text-center">
	            	<button type="button" class="btn btn-light-success font-weight-bold btn-sm" href="javascript:void(0)" onclick="esquema<?= $sufijo ?>('<?php echo $fila['idterreno'];?>')" >Esquemas</button>
	            </td>
	            <td>
				    <div class="dropdown">
						<a href="#" class="btn btn-light-info font-weight-bold dropdown-toggle btn-sm" data-toggle="dropdown" aria-expanded="false">Opciones</a>
						<div class="dropdown-menu dropdown-menu-sm">
							<ul class="navi">
								<?php if($puedeanular==1){ ?>
								<?php if($fila['estado']=='N'){ ?>
								<li class="navi-item">
									<a class="navi-link" href="#" onclick="cambiarEstadoTerreno(<?= $fila['idterreno'] ?>,'A')">
										<span class="navi-icon">
											<i class="flaticon-close text-warning"></i>
										</span>
										<span class="navi-text">Anular</span>
									</a>
								</li>
								<?php }else if($fila['estado']=='A'){ ?>
								<li class="navi-item">
									<a class="navi-link" href="#" onclick="cambiarEstadoTerreno(<?= $fila['idterreno'] ?>,'N')">
										<span class="navi-icon">
											<i class="flaticon2-check-mark text-success"></i>
										</span>
										<span class="navi-text">Activar</span>
									</a>
								</li>
								<?php } ?>
								<?php } ?>
								<?php if($puedeeliminar==1){ ?>
								<li class="navi-item">
									<a class="navi-link" href="#" onclick="cambiarEstadoTerreno(<?= $fila['idterreno'] ?>,'E')">
										<span class="navi-icon">
											<i class="flaticon-delete text-danger"></i>
										</span>
										<span class="navi-text">Eliminar</span>
									</a>
								</li>
								<?php } ?>
							</ul>
						</div>
					</div>
	            </td>
	        </tr>
	    	<?php } ?>
	    </tbody>
	</table>
</div>
<div class="text-right">
<?php 
	require_once('compaginacion.php');
	$fin=$inicio+count($data);
	echo compaginarTabla($pagina,$total_pag,$total_datos,$cantidad,($inicio+1),$fin,$sufijo);
?>
</div>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC5kiHY_yYxwG2rSwTygPmpglUCATsRRbs&callback=initMaps" async defer></script>
<script>
$(document).on('shown.bs.dropdown', '#divLista<?php echo $sufijo;?> .table-responsive, .modal .table-responsive', function (e) {
    // El contenedor del dropdown
    var $container = $(e.target);

    // Encontrar el menú desplegable real
    var $dropdown = $container.find('.dropdown-menu');
    if ($dropdown.length) {
        // Guardar una referencia para usarla después al adjuntar al cuerpo
        $container.data('dropdown-menu', $dropdown);
    } else {
        $dropdown = $container.data('dropdown-menu');
    }

    // Ajustar la posición del menú desplegable
    $dropdown.css('top', ($container.offset().top + $container.outerHeight()) + 'px');
    $dropdown.css('left', $container.offset().left + 'px');
    $dropdown.css('position', 'absolute');
    $dropdown.css('display', 'block');
    $dropdown.css('z-index', '1060'); // Asegurar que esté sobre el modal

    // Asegurarse de que el menú desplegable esté sobre el resto del contenido
    $dropdown.appendTo('body');
});

$(document).on('hide.bs.dropdown', '#divLista<?php echo $sufijo;?> .table-responsive, .modal .table-responsive', function (e) {
    // Ocultar el menú desplegable vinculado a este botón
    $(e.target).data('dropdown-menu').css('display', 'none');
});

function editarTerreno(idterreno){
	ViewModal('presentacion/mantTerreno','idterreno='+idterreno+'&accion=MODIFICAR&nivel=<?= $nivel + 1 ?>&idopcion=<?= $idopcion ?>&puedeeditar=<?= $puedeeditar ?>','divmodal1','Edición de Terreno');
}

function cambiarEstadoTerreno(idterreno, estado){
	let msj="";
	if(estado=="A"){msj="¿Esta seguro de anular el Terreno?";}
	if(estado=="N"){msj="¿Esta seguro de activar el Terreno?";}
	if(estado=="E"){msj="¿Esta seguro de eliminar el Terreno?";}
	confirm(msj,'ProcesoCambiarEstadoTerreno("'+idterreno+'","'+estado+'")');
}

function ProcesoCambiarEstadoTerreno(idterreno,estado){
	$('#divLista<?php echo $sufijo; ?>').LoadingOverlay('show',{
		size: 20,
		maxSize: 40
	});
	$.ajax({
		method: "POST",
		url: 'controlador/contTerreno.php',
		data: {accion: "CAMBIAR_ESTADO_TERRENO",
				'idterreno': idterreno,
				'estado': estado,
				'idopcion': <?= $idopcion ?>
			}
	})
	.done(function( text ) {
		evaluarActividadSistema(text);
		$('#divLista<?php echo $sufijo; ?>').LoadingOverlay('hide');
		$.toast({'text': text,'icon': 'success', 'position':'top-right'});	
		let pagina=document.getElementById('txtNroPaginaFooter<?php echo $sufijo; ?>').value;
		verPagina<?php echo $sufijo; ?>(pagina);
	})
}

function esquema<?= $sufijo ?>(idterreno){
	ViewModal('presentacion/viewEsquemaAdmin','nivel=<?= $nivel + 1 ?>&idopcion=<?= $idopcion ?>&idterreno='+idterreno,'divmodal<?= $nivel ?>','ESQUEMAS HIDRÁULICOS');
}

function initMaps() {
    const terrenos = <?php echo json_encode($data); ?>;

    terrenos.forEach(fila => {
        const mapElement = document.getElementById(`map-${fila.idterreno}`);
        if (mapElement) {
            // Crear el mapa
            const map = new google.maps.Map(mapElement, {
                center: { lat: parseFloat(fila.latitud), lng: parseFloat(fila.longitud) },
                zoom: 12,
                mapTypeId: google.maps.MapTypeId.SATELLITE,
            });

            map.setOptions({
                disableDefaultUI: true, // Ejemplo: desactivar UI predeterminada
                zoomControl: true,      // Habilitar solo el control de zoom
                styles: [
                    {
                        featureType: "all",
                        stylers: [{ saturation: -80 }]
                    },
                    {
                        featureType: "road",
                        elementType: "geometry",
                        stylers: [{ visibility: "simplified" }]
                    }
                ] // Ejemplo: estilos personalizados
            });

            // Colocar el marcador
            new google.maps.Marker({
                position: { lat: parseFloat(fila.latitud), lng: parseFloat(fila.longitud) },
                map: map,
                title: fila.descripcion // Texto que aparece al pasar el mouse sobre el marcador
            });
        }
    });
}


// Llama a initMaps después de que la página haya cargado completamente
window.onload = initMaps;

</script>