<?php
require_once('../logica/clsCase.php');
$objCase = new clsCase();

$sufijoPadre = "dispositivo";
$sufijo = "mantDispositivo";
$idopcion = $_GET['idopcion'];
$puedeeditar = 1;
$nivel = 2;
if(isset($_GET['nivel'])){
	$nivel = $_GET['nivel'];
}

$id = $_GET['iddispositivo'];
$puedeeditar = $_GET['puedeeditar'];
$registro = $objCase->getRowTableById('perfil',$id,'idperfil');

?>
<form name="formRegistro<?= $sufijo ?>" id="formRegistro<?= $sufijo ?>">
	<div class="row gutter-b">
		<div class="col-md-12">
			<div class="form-group row">
				<label class="col-4 col-form-label">Adjuntar TXT:</label>
				<div class="col-8">
					<div class="input-group">
	                    <input type="text" class="form-control" readonly>
	                    <label class="input-group-btn">
	                        <span class="btn btn-primary" onchange="return fileValidation()">
	                            <i class="fa fa-folder-open"></i> Seleccionar<input type="file" style="display: none;" id="filePCG" >
	                        </span>
	                    </label>
	                </div>
					<input type="hidden" class="form-control" name="iddispositivo<?= $sufijo ?>" id="iddispositivo<?= $sufijo ?>" value="<?= $id ?>" />
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 d-flex justify-content-between">
			<?php if($puedeeditar==1){ ?>
			<button type="button" class="btn btn-light-success font-weight-bold" onclick="ImportarData<?= $sufijo ?>()"><i class="fa fa-save"></i> Importar</button>
			<?php } ?>
			<button type="button" class="btn font-weight-bold btn-light-danger" onclick="CloseModal('divmodalmediano')"><i class="fa fa-times"></i> Cerrar</button>
		</div>
	</div>
</form>
<script>
    
    function fileValidation(){
        var fileInput = document.getElementById('filePCG');
        var file = fileInput.files[0];
        var fileName = file.name;
        var fileSize = file.size;
        var ext = fileName.split('.').pop();
        var fileSizeKb = fileSize/1024;
        //console.log(fileSizeKb);
        switch (ext) {
            case 'TXT':
            case 'txt':
            break;
            default:
            alert('El archivo no tiene la extensión adecuada');
                fileInput.value = '';
        }
    }

    function ImportarData<?= $sufijo ?>() {
        $('#divmodalmedianoContenido').LoadingOverlay('show',{
            size: 20,
            maxSize: 40
        });

        var inputFileImage = document.getElementById("filePCG");
        var file = inputFileImage.files[0];
        var data = new FormData();
        
        data.append('accion' , 'IMPORTAR_DATA_DAVIS');
        data.append('file', file);
        data.append('iddispositivo' , '<?= $id ?>');
        $.ajax({
            method: "POST",
            url: 'controlador/contDispositivo.php',
            data: data,
            contentType: false,
            processData: false,
            cache: false
        }).done(function(text){
            // alert(text);
            $('#divmodalmedianoContenido').LoadingOverlay('hide');
            if(text.substring(0,3)!="***"){
                $.toast({'text': text,'icon': 'success', 'position':'top-right'});
                CloseModal("divmodalmediano");
            }else{
                texto_error = text.substr(-18);
                if(texto_error=="for key 'fecha_id'"){
                    $.toast({'text': 'Error al importar los datos, existen datos ya importados','icon': 'error', 'position':'top-right'});
                }else{
                    $.toast({'text': text,'icon': 'error', 'position':'top-right'});
                }
            }
        });
    }


    $(function() {
        $(document).on('change', ':file', function() {
            var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
            input.trigger('fileselect', [numFiles, label]);
        });

        $(document).ready( function() {
            $(':file').on('fileselect', function(event, numFiles, label) {

                var input = $(this).parents('.input-group').find(':text'),
                log = numFiles > 1 ? numFiles + ' files selected' : label;

                if( input.length ) {
                    input.val(log);
                }else {
                    if( log ) alert(log);
                }
            });
        }); 
    });

</script>