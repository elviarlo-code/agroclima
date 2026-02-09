<?php
require_once('../logica/clsCase.php');
$objCase = new clsCase();

$sufijoPadre = "turno";
$sufijo = "mantTurno";
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
$solouno = 0;
if(isset($_GET['idterreno'])){
	$idterreno = $_GET['idterreno'];
	if($idterreno>0){
		$solouno = 1;
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

function dibujarTurnos<?= $sufijo ?>(){
	$('#divmodal<?= $nivel-1 ?>Contenido').LoadingOverlay('show',{
		size: 20,
		maxSize: 40
	});
	$.ajax({
		method: "POST",
		url: 'controlador/contTurno.php',
		data: {
				'accion' : 'CONSULTAR_COORDENADAS_TURNOS',
				'idterreno' : <?= $idterreno ?>,
				'idesquema' : <?= $idesquema ?>
		}
	})
	.done(function( text ) {
		$('#divmodal<?= $nivel-1 ?>Contenido').LoadingOverlay('hide');
		var json = JSON.parse(text);
		text = json.mensaje;
		evaluarActividadSistema(text);
		if(text.substring(0,3)!="***"){
			if(!mapa){
		        console.error("El mapa no está inicializado");
		        return;
		    }
			clearTurnosPolygons();
			var turnos = json.data; // Suponiendo que las coordenadas están en json.data
            turnos.forEach(turno => {
				const coordenadas = Object.values(turno.coordenadas).map(coord => ({
                    lat: parseFloat(coord.lat),
                    lng: parseFloat(coord.lng)
                }));

                const color = turno.color || '#FF0000'; // Color predeterminado si no se especifica

                // Dibujar el polígono
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

                // Agregar el polígono al mapa
                turnoPolygon.setMap(mapa);

				// Guardar el polígono en el array
				turnosPolygonos.push(turnoPolygon);

                // Agregar un evento de clic (opcional) para mostrar información del turno
                google.maps.event.addListener(turnoPolygon, 'click', () => {
                    alert(`Información del turno:\nID: ${turno.idturno}\nTurno: ${turno.turno}\nÁrea: ${turno.area}`);
                });
            });
		}else{
			$.toast({'text': text,'icon': 'error', 'position':'top-right'});
		}
	});
}


$(document).ready(function(){
	$('#divmodal<?= $nivel-1 ?>').on('shown.bs.modal', function () {
	   
	}); 
})

</script>