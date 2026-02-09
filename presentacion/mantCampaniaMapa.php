<?php
require_once('../logica/clsCase.php');
require_once('../logica/clsTurno.php');
$objCase = new clsCase();
$objTurno = new clsTurno();

$sufijoPadre = "mantCampania";
$sufijo = "campaniaMapa";
$idopcion = $_GET['idopcion'];
$puedeeditar = 1;
$nivel = 2;
if(isset($_GET['nivel'])){
	$nivel = $_GET['nivel'];
}

$buscar = 0;
if(isset($_GET['buscar'])){
	$buscar = $_GET['buscar'];
}

$idesquema = 0;
if(isset($_GET['idesquema'])){
	$idesquema = $_GET['idesquema'];
}

$latitud = -5.038421;
if(isset($_GET['latitud'])){
	$latitud = $_GET['latitud'];
}

$longitud = -78.3842227;
if(isset($_GET['longitud'])){
	$longitud = $_GET['longitud'];
}

$idterreno = 0;
if(isset($_GET['idterreno'])){
	$idterreno = $_GET['idterreno'];
}

$idturno = 0;

$data = $objTurno->consultarTurnoPorTerrenoActivo($idterreno, $idesquema, $idturno, 1);
$data = $data->fetchAll(PDO::FETCH_NAMED);

$turnos = array();
foreach($data as $kx=>$vx){
	$existe = false;
	$posicion = -1;
	foreach($turnos as $ky=>$vy){
		if($vx['idturno'] == $vy['idturno']){
			$existe = true;
			$posicion = $ky;
			break;
		}
	}

	if($existe){
		$turnos[$posicion]['coordenadas'][] = array('lat'=>$vx['latitud'], 'lng'=>$vx['longitud']);
	}else{
		$turnos[] = array('idturno'=>$vx['idturno'], 'turno'=>$vx['nombre'], 'area'=>$vx['area'], 'color'=>$vx['color'], 'idcampania'=>$vx['idcampania'], 'fecha'=>$vx['fecha'], 'cultivo'=>$vx['cultivo'], 'coordenadas'=>array(array('lat'=>$vx['latitud'], 'lng'=>$vx['longitud'])));
	}
}

?>
<form name="formRegistro<?= $sufijo ?>" id="formRegistro<?= $sufijo ?>">
	<div class="row">
		<div class="col-md-12">
            <div class="form-group">
                <div id="map<?= $sufijo ?>" style="height: 400px"></div>
            </div>
        </div>
	</div>
</form>
<style>
    /* Ocultar el botón de cierre predeterminado del InfoWindow */
    .gm-ui-hover-effect {
        display: none !important;
    }
</style>
<script>

lat = <?= $latitud ?>;
lon = <?= $longitud ?>;
destroyMap();
if(lat!="" && lon!=""){
	
	initMap<?= $sufijo ?>(parseFloat(lat),parseFloat(lon), () => {
	    despuesDeCargarMapa();
	});
}

// Initialize and add the map
var mapa;
var polygon; // Variable para almacenar el polígono dibujado
var drawingManager; // Variable para el gestor de dibujo

async function initMap<?= $sufijo ?>(latitud, longitud, callback) {
    const position = { lat: latitud, lng: longitud };
    const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
    const initialZoom = isMobile ? 14 : 16;

    // Importar las bibliotecas necesarias
    const { Map, Marker } = await google.maps.importLibrary("maps");
    const { DrawingManager } = await google.maps.importLibrary("drawing");


    mapa = new Map(document.getElementById("map<?= $sufijo ?>"), {
        zoom: initialZoom,
        center: position,
        mapTypeId: google.maps.MapTypeId.SATELLITE,
        gestureHandling: isMobile ? "greedy" : "auto",
        disableDefaultUI: isMobile,
    });

    mapa.setOptions({
        mapTypeControl: false, // Oculta el control de tipo de mapa
        streetViewControl: false, // Deshabilita Street View
        fullscreenControl: true, // Oculta el botón de pantalla completa
    });

    // Llama al callback si está definido
    if (callback && typeof callback === "function") {
        callback();
    }
}

// Función para eliminar el polígono actual
function removePolygon() {
    if (polygon) {
        polygon.setMap(null); // Elimina el polígono del mapa
        polygon = null; // Limpia la referencia
    } else {
        //alert('No hay un polígono para eliminar.');
    }
}


function despuesDeCargarMapa(){
	dibujarTurnos<?= $sufijo ?>();
}

function destroyMap() {
    // Limpia el contenedor del mapa
    document.getElementById("map<?= $sufijo ?>").innerHTML = "";
    // Elimina referencias del mapa y otros objetos
    if (mapa) {
        mapa = null;
    }
	if (polygon) {
        polygon = null; // Limpia la referencia
    }
}

turnosPolygonos = []; // Array para guardar los polígonos

function clearTurnosPolygons() {
    turnosPolygonos.forEach(polygon => {
        polygon.setMap(null); // Elimina el polígono del mapa
    });
    turnosPolygonos = []; // Limpia el array de referencia
}

function dibujarTurnos<?= $sufijo ?>() {
    if (!mapa) {
        console.error("El mapa no está inicializado");
        return;
    }
    clearTurnosPolygons();
    var turnos = <?php echo json_encode($turnos); ?>;

    turnos.forEach(turno => {
        const coordenadas = Object.values(turno.coordenadas).map(coord => ({
            lat: parseFloat(coord.lat),
            lng: parseFloat(coord.lng)
        }));

        const color = turno.color || '#FF0000';

        const turnoPolygon = new google.maps.Polygon({
            paths: coordenadas,
            strokeColor: color,
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: color,
            fillOpacity: 0.35,
            editable: false,
            draggable: false,
        });

        turnoPolygon.setMap(mapa);
        turnosPolygonos.push(turnoPolygon);

        // Crear el InfoWindow individual para cada polígono
        const infoWindow = new google.maps.InfoWindow();

        google.maps.event.addListener(turnoPolygon, 'click', (event) => {
	        let contentString = `
	            <div style="position: relative; padding: 3px;">
	                <button id="customCloseBtn_${turno.idturno}" type="button" style="
	                    position: absolute; 
	                    top: 0px; 
	                    right: 0px; 
	                    border: none; 
	                    background-color: white;
	                    font-size: 20px; 
	                    cursor: pointer;" 
	                    aria-label="Cerrar">
	                    <i class="fa fa-times text-danger"></i>
	                </button>
	                <h3 style="margin: 0px 25px 10px 0px;">Información</h3>
					<div class="py-0">
						<div class="d-flex align-items-center justify-content-between mb-2">
							<span class="font-weight-bold mr-2">TURNO:</span>
							<span class="text-hover-primary">${turno.turno}</span>
						</div>
						<div class="d-flex align-items-center justify-content-between mb-2">
							<span class="font-weight-bold mr-2">ÁREA:</span>
							<span class="text-hover-primary">${turno.area} m<sup>2</sup></span>
						</div>
	        `;
	        if(turno.idcampania>0){
	        contentString += `
	        			<div class="d-flex align-items-center justify-content-between mb-2">
							<span class="font-weight-bold mr-2">FECHA:</span>
							<span class="text-hover-primary">${turno.fecha}</span>
						</div>
						<div class="d-flex align-items-center justify-content-between mb-2">
							<span class="font-weight-bold mr-2">CULTIVO:</span>
							<span class="text-hover-primary">${turno.cultivo}</span>
						</div>
	        `;
	    	}

	        if(turno.idcampania==0){
	        contentString += `
	        			<div class="text-center">
							<button type="button" class="btn btn-light-success font-weight-bold btn-sm" onclick="getTurnoMapa(${turno.idturno})" ><i class="fa fa-check"></i> Seleccionar</button>
						</div>
	        `;
	    	}

	        contentString += `
	        	</div>
	        </div>
	        `;

            infoWindow.setContent(contentString);
            infoWindow.setPosition(event.latLng);
            infoWindow.open(mapa);

            // Asignar evento al botón de cierre
            google.maps.event.addListenerOnce(infoWindow, 'domready', () => {
                const closeButton = document.getElementById(`customCloseBtn_${turno.idturno}`);
                if (closeButton) {
                    closeButton.addEventListener('click', () => {
                        infoWindow.close();
                    });
                }
            });
        });
    });
}



$(document).ready(function(){
	$('#divmodal<?= $nivel-1 ?>').on('shown.bs.modal', function () {
	   
	}); 
})

</script>