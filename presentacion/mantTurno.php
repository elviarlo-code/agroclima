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

$idterreno = 0;
$solouno = 0;
if(isset($_GET['idterreno'])){
	$idterreno = $_GET['idterreno'];
	if($idterreno>0){
		$solouno = 1;
	}
}

$id = 0;
$coordenadas = array();
if($_GET['accion']=='MODIFICAR'){
	$id = $_GET['idturno'];
	$puedeeditar = $_GET['puedeeditar'];
	$registro = $objCase->getRowTableById('terreno_turno',$id,'idturno');

	$coordenadas = $objCase->getListTableFiltroSimple('terreno_turno_coordenada', 'idturno', $id, 'estado', 'N');
	$coordenadas = $coordenadas->fetchAll(PDO::FETCH_NAMED);
}

?>
<form name="formRegistro<?= $sufijo ?>" id="formRegistro<?= $sufijo ?>">
	<div class="row">
		<div class="col-md-6">
			<div class="form-group row <?= ($buscar==0)? 'mb-0':'' ?>">
				<label class="col-3 col-form-label">
					Terreno:
					<div class="btn-icon" <?= ($buscar==0)? '':'hidden' ?>>
						<span class="svg-icon svg-icon-success svg-icon-2x" data-toggle="tooltip" data-placement="top" title="Registrar Terreno" style="cursor: pointer;" onclick="NuevoTerreno<?= $sufijo ?>()"><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\Files\Folder-error.svg-->
							<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
								<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
							        <rect x="0" y="0" width="24" height="24"/>
							        <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10"/>
							        <path d="M11,11 L11,7 C11,6.44771525 11.4477153,6 12,6 C12.5522847,6 13,6.44771525 13,7 L13,11 L17,11 C17.5522847,11 18,11.4477153 18,12 C18,12.5522847 17.5522847,13 17,13 L13,13 L13,17 C13,17.5522847 12.5522847,18 12,18 C11.4477153,18 11,17.5522847 11,17 L11,13 L7,13 C6.44771525,13 6,12.5522847 6,12 C6,11.4477153 6.44771525,11 7,11 L11,11 Z" fill="#000000"/>
							    </g>
							</svg><!--end::Svg Icon-->
						</span>&nbsp;
						<span class="svg-icon svg-icon-warning svg-icon-2x" data-toggle="tooltip" data-placement="top" title="Actualizar Terreno" style="cursor: pointer;" onclick="editarTerreno<?= $sufijo ?>()"><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\Files\Folder-error.svg-->
							<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
								<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
							        <rect x="0" y="0" width="24" height="24"/>
							        <path d="M8.43296491,7.17429118 L9.40782327,7.85689436 C9.49616631,7.91875282 9.56214077,8.00751728 9.5959027,8.10994332 C9.68235021,8.37220548 9.53982427,8.65489052 9.27756211,8.74133803 L5.89079566,9.85769242 C5.84469033,9.87288977 5.79661753,9.8812917 5.74809064,9.88263369 C5.4720538,9.8902674 5.24209339,9.67268366 5.23445968,9.39664682 L5.13610134,5.83998177 C5.13313425,5.73269078 5.16477113,5.62729274 5.22633424,5.53937151 C5.384723,5.31316892 5.69649589,5.25819495 5.92269848,5.4165837 L6.72910242,5.98123382 C8.16546398,4.72182424 10.0239806,4 12,4 C16.418278,4 20,7.581722 20,12 C20,16.418278 16.418278,20 12,20 C7.581722,20 4,16.418278 4,12 L6,12 C6,15.3137085 8.6862915,18 12,18 C15.3137085,18 18,15.3137085 18,12 C18,8.6862915 15.3137085,6 12,6 C10.6885336,6 9.44767246,6.42282109 8.43296491,7.17429118 Z" fill="#000000" fill-rule="nonzero"/>
							    </g>
							</svg><!--end::Svg Icon-->
						</span>
					</div>
				</label>
				<div class="col-9">
					<select class="form-control" name="idterreno<?= $sufijo ?>" validar="SI" id="idterreno<?= $sufijo ?>" onchange="getEsquema<?= $sufijo ?>()">
						<option value="0" latitud="" longitud="">- Seleccione -</option>
					</select>
					<input type="text" hidden class="form-control" name="idturno<?= $sufijo ?>" id="idturno<?= $sufijo ?>" value="<?= ($id>0)? $registro['idturno'] : 0 ?>" />
				</div>
			</div>
			<div class="form-group row <?= ($buscar==0)? 'mb-0':'' ?>">
				<label class="col-3 col-form-label">
					Esquema:
					<div class="btn-icon" <?= ($buscar==0)? '':'hidden' ?>>
						<span class="svg-icon svg-icon-success svg-icon-2x" data-toggle="tooltip" data-placement="top" title="Registrar Esquema" style="cursor: pointer;" onclick="NuevoEsquema<?= $sufijo ?>()"><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\Files\Folder-error.svg-->
							<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
								<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
							        <rect x="0" y="0" width="24" height="24"/>
							        <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10"/>
							        <path d="M11,11 L11,7 C11,6.44771525 11.4477153,6 12,6 C12.5522847,6 13,6.44771525 13,7 L13,11 L17,11 C17.5522847,11 18,11.4477153 18,12 C18,12.5522847 17.5522847,13 17,13 L13,13 L13,17 C13,17.5522847 12.5522847,18 12,18 C11.4477153,18 11,17.5522847 11,17 L11,13 L7,13 C6.44771525,13 6,12.5522847 6,12 C6,11.4477153 6.44771525,11 7,11 L11,11 Z" fill="#000000"/>
							    </g>
							</svg><!--end::Svg Icon-->
						</span>&nbsp;
						<span class="svg-icon svg-icon-warning svg-icon-2x" data-toggle="tooltip" data-placement="top" title="Actualizar Esquema" style="cursor: pointer;" onclick="editarEsquema<?= $sufijo ?>()"><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\Files\Folder-error.svg-->
							<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
								<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
							        <rect x="0" y="0" width="24" height="24"/>
							        <path d="M8.43296491,7.17429118 L9.40782327,7.85689436 C9.49616631,7.91875282 9.56214077,8.00751728 9.5959027,8.10994332 C9.68235021,8.37220548 9.53982427,8.65489052 9.27756211,8.74133803 L5.89079566,9.85769242 C5.84469033,9.87288977 5.79661753,9.8812917 5.74809064,9.88263369 C5.4720538,9.8902674 5.24209339,9.67268366 5.23445968,9.39664682 L5.13610134,5.83998177 C5.13313425,5.73269078 5.16477113,5.62729274 5.22633424,5.53937151 C5.384723,5.31316892 5.69649589,5.25819495 5.92269848,5.4165837 L6.72910242,5.98123382 C8.16546398,4.72182424 10.0239806,4 12,4 C16.418278,4 20,7.581722 20,12 C20,16.418278 16.418278,20 12,20 C7.581722,20 4,16.418278 4,12 L6,12 C6,15.3137085 8.6862915,18 12,18 C15.3137085,18 18,15.3137085 18,12 C18,8.6862915 15.3137085,6 12,6 C10.6885336,6 9.44767246,6.42282109 8.43296491,7.17429118 Z" fill="#000000" fill-rule="nonzero"/>
							    </g>
							</svg><!--end::Svg Icon-->
						</span>
					</div>
				</label>
				<div class="col-9">
					<select class="form-control" name="idesquema<?= $sufijo ?>" validar="SI" id="idesquema<?= $sufijo ?>" onchange="dibujarTurnos<?= $sufijo ?>()"
						<option value="0" selected>- Seleccione -</option>
					</select>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Turno:</label>
				<div class="col-9">
					<input type="text" class="form-control" validar="SI" autocomplete="off" name="nombre<?= $sufijo ?>" id="nombre<?= $sufijo ?>" value="<?= ($id>0)? $registro['nombre'] : '' ?>" />
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group row">
				<label class="col-3 col-form-label">Área (mts2):</label>
				<div class="col-9">
					<input type="text" class="form-control" onkeypress="return solo_decimal(event)" validar="SI" autocomplete="off" name="area<?= $sufijo ?>" id="area<?= $sufijo ?>" value="<?= ($id>0)? $registro['area'] : '' ?>" />
				</div>
			</div>
			<div class="form-group row">
				<label class="col-3 col-form-label">Color:</label>
				<div class="col-9">
					<input type="color" class="form-control" validar="SI" autocomplete="off" name="color<?= $sufijo ?>" id="color<?= $sufijo ?>" value="<?= ($id>0)? $registro['color'] : '#229954' ?>" />
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
            <div class="form-group">
                <div id="map<?= $sufijo ?>" style="height: 400px"></div>
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


<?php } ?>

function getTerreno<?= $sufijo ?>(idterreno=0,idesquema=0){
	if(idterreno>0){
		$('#divmodal<?= $nivel-1 ?>Contenido').LoadingOverlay('show',{
			size: 20,
			maxSize: 40
		});
	}

	<?php if($idterreno>0){ ?>
		idterreno = <?= $idterreno ?>;
	<?php } ?>

	$.ajax({
		method: "POST",
		url: 'controlador/contTurno.php',
		data: {
				'accion' : 'LISTA_TERRENO',
				'idterreno' : idterreno,
				'solouno': <?= $solouno ?>
		}
	})
	.done(function( text ) {
		if(idterreno>0){
			$('#divmodal<?= $nivel-1 ?>Contenido').LoadingOverlay('hide');
		}
		evaluarActividadSistema(text);
		if(text.substring(0,3)!="***"){
			$('#idterreno<?= $sufijo ?>').html(text);
			<?php if($_GET['accion']=='MODIFICAR'){ ?>
				$('#idterreno<?= $sufijo ?>').val('<?= $registro['idterreno'] ?>');
			<?php } ?>
			getEsquema<?= $sufijo ?>(idesquema);
		}else{
			$.toast({'text': text,'icon': 'error', 'position':'top-right'});
		}
	});
}

flgesquema = 1;
function getEsquema<?= $sufijo ?>(idesquema=0){
	$('#divmodal<?= $nivel-1 ?>Contenido').LoadingOverlay('show',{
		size: 20,
		maxSize: 40
	});
	<?php if($idesquema>0){ ?>
		idesquema = <?= $idesquema ?>;
	<?php } ?>
	$.ajax({
		method: "POST",
		url: 'controlador/contTurno.php',
		data: {
				'accion' : 'LISTA_ESQUEMA',
				'idterreno' : $('#idterreno<?= $sufijo; ?>').val(),
				'idesquema' : idesquema,
				'vista' : 'MANT',
				'solouno': <?= $solouno ?>
		}
	})
	.done(function( text ) {
		$('#divmodal<?= $nivel-1 ?>Contenido').LoadingOverlay('hide');
		evaluarActividadSistema(text);
		if(text.substring(0,3)!="***"){
			lat = $("#idterreno<?= $sufijo; ?> option:selected" ).attr("latitud");
			lon = $("#idterreno<?= $sufijo; ?> option:selected" ).attr("longitud");
			destroyMap();
			if(lat!="" && lon!=""){
				
				initMap<?= $sufijo ?>(parseFloat(lat),parseFloat(lon), () => {
				    despuesDeCargarMapa();
				});
			}

			$('#idesquema<?= $sufijo ?>').html(text);
			<?php if($_GET['accion']=='MODIFICAR'){ ?>
				if(flgesquema){
					$('#idesquema<?= $sufijo ?>').val('<?= $registro['idesquema'] ?>');
					// Dispara el evento onchange
					//$('#idesquema<?= $sufijo ?>').trigger('change');
					flgesquema = 0;
				}
			<?php } ?>	
		}else{
			$.toast({'text': text,'icon': 'error', 'position':'top-right'});
		}
	});
}

getTerreno<?= $sufijo ?>();

function NuevoTerreno<?= $sufijo ?>(){
	ViewModal('presentacion/mantTerreno','accion=NUEVO&idopcion=16&nivel=<?= $nivel + 1 ?>&directo=1&sufijodirecto=<?= $sufijo ?>','divmodal<?= $nivel ?>','Registrar Terreno',0);
}

function editarTerreno<?= $sufijo ?>(){
	if($('#idterreno<?= $sufijo ?>').val()>0){
		ViewModal('presentacion/mantTerreno','idterreno='+$('#idterreno<?= $sufijo ?>').val()+'&accion=MODIFICAR&idopcion=19&nivel=<?= $nivel + 1 ?>&puedeeditar=<?= $puedeeditar ?>&directo=1&sufijodirecto=<?= $sufijo ?>','divmodal<?= $nivel ?>','Edición de Terreno');
	}else{
		$.toast({'text': 'Seleccione el Terreno que desea actualizar.','icon': 'error', 'position':'top-right'});
	}
}

function NuevoEsquema<?= $sufijo ?>(){
	if($('#idterreno<?= $sufijo ?>').val()>0){
		ViewModal('presentacion/viewEsquemaMant','accion=NUEVO&idopcion=17&nivel=<?= $nivel + 1 ?>&directo=1&sufijodirecto=<?= $sufijo ?>&idterreno='+$('#idterreno<?= $sufijo ?>').val(),'divmodalmediano','Registrar Esquema',0);
	}else{
		$.toast({'text': 'Seleccione primero el terreno para registrar el esquema.','icon': 'error', 'position':'top-right'});
	}
}

function editarEsquema<?= $sufijo ?>(){
	if($('#idesquema<?= $sufijo ?>').val()>0){
		ViewModal('presentacion/viewEsquemaMant','idesquema='+$('#idesquema<?= $sufijo ?>').val()+'&accion=MODIFICAR&idopcion=19&nivel=<?= $nivel + 1 ?>&puedeeditar=<?= $puedeeditar ?>&directo=1&sufijodirecto=<?= $sufijo ?>&idterreno='+$('#idterreno<?= $sufijo ?>').val(),'divmodalmediano','Edición de Esquema');
	}else{
		$.toast({'text': 'Seleccione el Esquema que desea actualizar.','icon': 'error', 'position':'top-right'});
	}
}

function registrar<?= $sufijo ?>(){
	if(verificarFormulario<?= $sufijo ?>()){
		$('#divmodal<?= $nivel-1 ?>Contenido').LoadingOverlay('show',{
			size: 20,
			maxSize: 40
		});
		let datax = $('#formRegistro<?= $sufijo ?>').serializeArray();

		let coordenadas = getPolygonCoordinates();
		coordenadas = JSON.stringify({coordenadas});

		datax.push({name: "accion",value:"<?= $_GET['accion'] ?>"});
		datax.push({name: "sufijo",value:"<?= $sufijo ?>"});
		datax.push({name: "idopcion",value:"<?= $idopcion ?>"});
		datax.push({name: "coordenadas",value: coordenadas});
		$.ajax({
			method: "POST",
			url: 'controlador/contTurno.php',
			data: datax
		})
		.done(function( text ) {
			$('#divmodal<?= $nivel-1 ?>Contenido').LoadingOverlay('hide');
			var json = JSON.parse(text);
			text = json.mensaje;
			evaluarActividadSistema(text);
			if(text.substring(0,3)!="***"){
				verPagina<?= $sufijoPadre ?>($('#txtNroPaginaFooter<?php echo $sufijoPadre; ?>').val());
				if(document.getElementById('idterrenomantCampania')) {
				    getTurnomantCampania();
				}
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

    // Configura el DrawingManager para dibujar polígonos
    drawingManager = new DrawingManager({
        drawingMode: null, // No activa dibujo al inicio
        drawingControl: true, // Muestra los controles de dibujo
        drawingControlOptions: {
            position: google.maps.ControlPosition.TOP_CENTER,
            drawingModes: ['polygon'], // Permite solo el dibujo de polígonos
        },
        polygonOptions: {
            fillColor: '#FF0000',
            fillOpacity: 0.5,
            strokeWeight: 2,
            strokeColor: '#FF0000',
            clickable: true,
            editable: true,
            draggable: true,
        },
    });

    // Asigna el DrawingManager al mapa
    drawingManager.setMap(mapa);

    // Evento que se activa al finalizar el dibujo de un polígono
    google.maps.event.addListener(drawingManager, 'polygoncomplete', (drawnPolygon) => {
        if (polygon) {
            polygon.setMap(null); // Elimina el polígono anterior si existe
        }
        polygon = drawnPolygon; // Guarda el nuevo polígono
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

flagDibujar = 1;
function despuesDeCargarMapa(){
	addRemovePolygonButton();
	CargarTurno();
	<?php if($_GET['accion']=='MODIFICAR'){ ?>
		if(flagDibujar){
			dibujarTurnos<?= $sufijo ?>();
			flagDibujar = 0;
		}
	<?php } ?>
}

function addRemovePolygonButton() {

    const checkDrawingControl = () => {
    	const map1Container = document.getElementById('map<?= $sufijo ?>');
        const drawingControlContainer = map1Container.querySelector('.gmnoprint');
        if (drawingControlContainer) {
            // Crear el botón
            let button = document.createElement("button");
            button.setAttribute('aria-label', 'Eliminar Polígono');
            button.type = "button"; // Define el tipo como "button" para evitar el comportamiento predeterminado de "submit"
            button.style.backgroundColor = "#fff";
            button.style.border = "0px solid #ccc";
            button.style.borderRadius = "1px";
            button.style.boxShadow = "0 2px 6px rgba(0,0,0,.3)";
            button.style.cursor = "pointer";
            button.style.marginLeft = "0px";
            button.style.padding = "0px"; // Reducir padding para hacerlo más pequeño
            button.style.display = "flex";
            button.style.alignItems = "center";
            button.style.justifyContent = "center";
            button.style.width = "24px"; // Tamaño reducido
            button.style.height = "24px"; // Tamaño reducido

            // Agregar tooltip usando el atributo title
            button.title = "Eliminar Polígono"; // Texto que aparece al pasar el mouse

            // Agregar ícono SVG al botón
            button.innerHTML = `
                <img src="assets/media/svg/icons/Code/Error-circle.svg" style="width: 16px; height: 16px;" />
            `;

            // Agregar evento de clic al botón
            button.addEventListener("click", () => {
                removePolygon();
            });

            // Insertar el botón al lado del DrawingManager
            drawingControlContainer.appendChild(button);
        } else {
            // Si no encuentra el contenedor, vuelve a intentarlo después de 100ms
            setTimeout(checkDrawingControl, 100);
        }
    };

    // Inicia la comprobación del contenedor
    checkDrawingControl();
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

function getPolygonCoordinates() {
    if (polygon) {
        let path = polygon.getPath();
        if (path && path.forEach) { // Verifica que path no sea null o undefined y tenga forEach
            let coordinates = [];
            path.forEach((vertex) => {
                coordinates.push({ lat: vertex.lat(), lng: vertex.lng() });
            });

            // console.log("Coordenadas del polígono:", coordinates);
            return coordinates;
        } else {
            // console.log('El polígono no tiene un camino válido.');
            return null;
        }
    } else {
        // console.log('No hay un polígono para obtener coordenadas.');
        return null;
    }
}

destroyMap();

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
				'idterreno' : $('#idterreno<?= $sufijo; ?>').val(),
				'idesquema' : $('#idesquema<?= $sufijo; ?>').val(),
				'idturno': <?= $id ?>
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

function CargarTurno() {
    if (!mapa) {
        console.error("El mapa no está inicializado");
        return;
    }

    var turnoCoordenadas = <?php echo json_encode($coordenadas); ?>; // Datos PHP pasados a JS

    // Mapea las coordenadas
    var datacoor = turnoCoordenadas.map(punto => ({
        lat: parseFloat(punto.latitud), // Convierte la latitud
        lng: parseFloat(punto.longitud) // Convierte la longitud
    }));

    // Eliminar cualquier polígono previo
    removePolygon();

    // Dibujar el nuevo polígono
    polygon = new google.maps.Polygon({
        paths: datacoor,
        strokeColor: '#FF0000',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: '#FF0000',
        fillOpacity: 0.35,
        editable: true, // Permite editar el polígono
        draggable: true // Permite mover el polígono
    });

    // Agregar el polígono al mapa
    polygon.setMap(mapa); // Asegúrate de que 'mapa' esté correctamente inicializado
}

$(document).ready(function(){
	$('#divmodal<?= $nivel-1 ?>').on('shown.bs.modal', function () {
	   
	}); 
})

</script>