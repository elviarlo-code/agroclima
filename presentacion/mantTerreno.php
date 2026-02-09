<?php
require_once('../logica/clsCase.php');
$objCase = new clsCase();

$sufijoPadre = "terreno";
$sufijo = "mantTerreno";
$idopcion = $_GET['idopcion'];
$puedeeditar = 1;
$nivel = 3;
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

$configUbicacion = $objCase->getListTableFiltroSimple("mgtablagenerald","idtablageneral",6,"estado","N");
$configUbicacion = $configUbicacion->fetchAll(PDO::FETCH_NAMED);

$posicion = array();
foreach ($configUbicacion as $key => $value){
    $posicion[$value['codigo']] = floatval($value['descripcion1']);
}

$lat = $posicion['lat'];
$lon = $posicion['lon'];

$id = 0;
if($_GET['accion']=='MODIFICAR'){
    $id = $_GET['idterreno'];
    $puedeeditar = $_GET['puedeeditar'];
    $registro = $objCase->getRowTableFiltroSimple('terreno','idterreno',$id);

    if($registro['latitud']!=""){
        $lat = $registro['latitud'];
    }
    if($registro['longitud']!=""){
        $lon = $registro['longitud'];
    }
}

?>
<form name="formRegistro<?= $sufijo ?>" id="formRegistro<?= $sufijo ?>">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Fundo/Terreno:</label>
                <input type="text" class="form-control" validar="SI" name="descripcion<?= $sufijo ?>" id="descripcion<?= $sufijo ?>" autocomplete="off" value="<?= ($id>0)? $registro['descripcion'] : '' ?>" />
                <input type="hidden" class="form-control" name="idterreno<?= $sufijo ?>" id="idterreno<?= $sufijo ?>" value="<?= $id ?>" />
            </div>
            <div class="form-group">
                <label>Área (hectárea):</label>
                <input type="text" class="form-control" validar="SI" name="area<?= $sufijo ?>" id="area<?= $sufijo ?>" onkeypress="return solo_decimal(event)" autocomplete="off" value="<?= ($id>0)? $registro['area'] : '' ?>" />
            </div>
            <div class="form-group">
                <label>Ubigeo:</label>
                <div class="input-group">
                    <input type="text" class="form-control" autocomplete="off" validar="SI" placeholder="Buscar Distrito" readonly name="ubigeo_texto<?= $sufijo ?>" id="ubigeo_texto<?= $sufijo ?>" value="<?= ($id>0)? $registro['ubigeo_texto'] : '' ?>" data-toggle="tooltip" data-placement="top" data-original-title="" />
                    <input class="form-control" type="hidden" name="ubigeo<?= $sufijo ?>" id="ubigeo<?= $sufijo ?>" value="<?= ($id>0)? $registro['ubigeo'] : '' ?>" />
                    <div class="input-group-append">
                        <button class="btn btn-light-dark" type="button" onclick="consultarUbigeo<?= $sufijo ?>()">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Dirección:</label>
                <textarea class="form-control" name="direccion<?= $sufijo ?>" id="direccion<?= $sufijo ?>" style="height: 105px;" placeholder="Dirección..."><?= ($id>0)? $registro['direccion'] : '' ?></textarea>
            </div>
        </div>
        <div class="col-md-8">
            <div class="form-group">
                <div id="map" style="height: 400px"></div>
                <input type="hidden" class="form-control" name="latitud<?= $sufijo ?>" id="latitud<?= $sufijo ?>" autocomplete="off" value="<?= ($id>0)? $registro['latitud'] : null ?>" />
                <input type="hidden" class="form-control" name="longitud<?= $sufijo ?>" id="longitud<?= $sufijo ?>" autocomplete="off" value="<?= ($id>0)? $registro['longitud'] : null ?>" />
                <input type="hidden" class="form-control" name="altitud<?= $sufijo ?>" id="altitud<?= $sufijo ?>" autocomplete="off" value="<?= ($id>0)? $registro['altitud'] : null ?>" />
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
"use strict";

// Class definition
var KTProfile = function () {
    // Elements
    var _avatar;

    var _initAvatar = function () {
        _avatar = new KTImageInput('imagen<?= $sufijo ?>');
    }

    return {
        // public functions
        init: function () {
            _initAvatar();
        }
    };
}();

jQuery(document).ready(function() {
    KTProfile.init();
});

<?php if($_GET['accion']=='MODIFICAR'){ ?>
    
<?php } ?>


function registrar<?= $sufijo ?>(){
    if(verificarFormulario<?= $sufijo ?>()){
        $('#divmodal<?= $nivel-1 ?>Contenido').LoadingOverlay('show',{
            size: 20,
            maxSize: 40
        });

        var datax = new FormData($('#formRegistro<?= $sufijo ?>')[0]);
        datax.append('accion',"<?= $_GET['accion'] ?>");
        datax.append('sufijo',"<?= $sufijo ?>");        
        datax.append('idopcion',"<?= $idopcion ?>");        
        $.ajax({
            method: "POST",
            contentType: false, 
            processData: false,
            url: 'controlador/contTerreno.php',
            data: datax
        })
        .done(function( text ) {
            $('#divmodal<?= $nivel-1 ?>Contenido').LoadingOverlay('hide');
            var json = JSON.parse(text);
            text = json.mensaje;
            evaluarActividadSistema(text);
            if(text.substring(0,3)!="***"){
                <?php if($directo==1){ ?>
                    getTerreno<?= $sufijodirecto ?>(json.idterreno);
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
    return ValidarCampos('formRegistro<?= $sufijo ?>');
}

// Initialize and add the map
var map;
var marker;
var elevationService;
var geocoder;

async function initMap() {
    const position = { lat: <?= $lat ?>, lng: <?= $lon ?> };
    const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
    const initialZoom = isMobile ? 10 : 12;

    const { Map, Marker } = await google.maps.importLibrary("maps");

    map = new Map(document.getElementById("map"), {
        zoom: initialZoom,
        center: position,
        mapTypeId: google.maps.MapTypeId.SATELLITE,
        gestureHandling: isMobile ? "greedy" : "auto",
        disableDefaultUI: isMobile,
    });

    // Crear un marcador estándar
    marker = new google.maps.Marker({
        map: map,
        position: position,
        draggable: true,
    });

    map.setOptions({
        mapTypeControl: false, // Oculta el control de tipo de mapa
        streetViewControl: false, // Deshabilita Street View
        fullscreenControl: true, // Oculta el botón de pantalla completa
    });

    // Crear el servicio de elevación
    elevationService = new google.maps.ElevationService();
    // Crear instancia del servicio de geocodificación
    geocoder = new google.maps.Geocoder();

    // Escuchar el evento dragend para obtener la nueva posición del marcador
    marker.addListener("dragend", () => {
        const position = marker.getPosition();
        const lat = position.lat();
        const lng = position.lng();
        $('#latitud<?= $sufijo ?>').val(lat);
        $('#longitud<?= $sufijo ?>').val(lng);

        // console.log("Nueva posición después de arrastrar: Latitud:", lat, "Longitud:", lng);
        getElevation(lat, lng); // Obtener la altitud en la nueva ubicación
    });

    // Evento click en el mapa para mover el marcador a la posición del clic
    map.addListener("click", (event) => {
        const clickedPosition = event.latLng; // Obtener la posición del clic
        marker.setPosition(clickedPosition); // Mover el marcador a la posición del clic

        // Obtener la latitud y longitud después de mover el marcador
        const lat = clickedPosition.lat();
        const lng = clickedPosition.lng();
        $('#latitud<?= $sufijo ?>').val(lat);
        $('#longitud<?= $sufijo ?>').val(lng);

        // console.log("Posición del marcador después de clic: Latitud:", lat, "Longitud:", lng);
        getElevation(lat, lng); // Obtener la altitud en la nueva ubicación
    });
}

// Función para obtener la altitud de un lugar
function getElevation(lat, lng) {
    const location = new google.maps.LatLng(lat, lng);

    elevationService.getElevationForLocations({ locations: [location] }, (results, status) => {
        if (status === google.maps.ElevationStatus.OK) {
            if (results[0]) {
                const elevation = results[0].elevation; // Altitud en metros
                $('#altitud<?= $sufijo ?>').val(elevation);
                // console.log("Altitud:", elevation, "metros");
            }
        } else {
            console.error("No se pudo obtener la altitud:", status);
        }
    });
}

function reubicarMarcadorPorUbicacion(department, province, district) {
    const location = `${district}, ${province}, ${department}, Perú`;

    geocoder.geocode({ address: location }, (results, status) => {
        if (status === "OK" && results[0]) {
            const position = results[0].geometry.location;
            const lat = position.lat();
            const lng = position.lng();
            $('#latitud<?= $sufijo ?>').val(lat);
            $('#longitud<?= $sufijo ?>').val(lng);

            console.log(`Latitud: ${lat}, Longitud: ${lng}`);

            getElevation(lat, lng); // Obtener altitud

            if (marker) {
                marker.setPosition(position); // Reubicar marcador

            } else {
                console.error("El marcador no está definido.");
            }

            map.setCenter(position); // Centrar mapa
            map.setZoom(15); // Ajustar zoom

            // console.log(`Marcador reubicado en: ${location}`);
        } else {
            console.error("Error al geocodificar:", status);
            alert("No se pudo encontrar la ubicación. Verifica los datos ingresados.");
        }
    });
}

initMap();


function consultarUbigeo<?= $sufijo ?>(){
    ViewModal('presentacion/viewBusquedaUbigeo','accion=BUSQUEDA&nivel=<?php echo ($nivel+1); ?>&sufijo=<?= $sufijo ?>','divmodal<?php echo $nivel; ?>','Buscar Ubigeo');
}

function seleccionaUbigeoPersona<?= $sufijo ?>(iddistrito, idprovincia, iddepartamento, distrito, provincia, departamento, input, sufijo){

    $("#ubigeo"+sufijo).val(iddistrito);

    var ubigeo_texto = departamento+'/'+provincia+'/'+distrito;
    $("#ubigeo_texto"+sufijo).val(ubigeo_texto);
    reubicarMarcadorPorUbicacion(departamento, provincia, distrito);
    CloseModal("divmodal<?php echo $nivel; ?>");
}


$(document).ready(function(){
    $('#divmodal<?= $nivel-1 ?>').on('shown.bs.modal', function () {
        
    }); 
})

</script>