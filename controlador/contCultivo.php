<?php 
require_once("../logica/clsCultivo.php");
require_once("../logica/clsCase.php");
require_once("../logica/clsCompartido.php");
controlador($_POST['accion']);

function controlador($accion){
	$objCul = new clsCultivo();
    $objCase = new clsCase();

	switch ($accion){

		case "NUEVO": 
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$sufijo = $_POST['sufijo'];
				$idopcion = $_POST['idopcion'];
				$permiso = validarPermisoPorPerfil($idopcion,"puederegistrar");

				if(!$permiso){
					throw new Exception("No tienes permiso para realizar esta operacion.", 123);
				}
				
				$existeCultivo = $objCul->verificarCultivo($_POST['nombre'.$sufijo],$_POST['variedad'.$sufijo]);
				if($existeCultivo->rowCount()>=1){
					throw new Exception("El cultivo ya existe", 123);	
				}

				$valuesCultivo = $objCul->getColumnTablaCultivo();
				$valuesCultivo[':nombre'] = $_POST['nombre'.$sufijo];
				$valuesCultivo[':variedad'] = $_POST['variedad'.$sufijo];
				$valuesCultivo[':altura'] = (is_numeric($_POST['altura'.$sufijo]))?$_POST['altura'.$sufijo]:null;
				$valuesCultivo[':raiz_maxima'] = (is_numeric($_POST['raiz_maxima'.$sufijo]))?$_POST['raiz_maxima'.$sufijo]:null;
				$valuesCultivo[':raiz_minima'] = (is_numeric($_POST['raiz_minima'.$sufijo]))?$_POST['raiz_minima'.$sufijo]:null;
				$valuesCultivo[':fhregistro'] = date('Y-m-d H:i:s');
				$valuesCultivo[':idpersonaregistro'] = $_SESSION['idpersona'];
                $objCase->insertar('cultivo', $valuesCultivo);

                $idcultivo = $objCase->getLastIdInsert('cultivo', 'idcultivo');

				$imagen="";
				//guardar file
				if ($_FILES['profile_avatar']['name']!='') {
					
                   	$urlbase="../files/imagenes";
                   	if (!file_exists($urlbase) && !is_dir($urlbase)){
                    	mkdir($urlbase);
                    	mkdir($urlbase."/cultivos",0777);
                    }else{
               	    	if (!file_exists($urlbase."/cultivos") && !is_dir($urlbase."/cultivos")) {
               		    	mkdir($urlbase."/cultivos",0777);
               	    	}
                   }
                   $archivos = $_FILES['profile_avatar']; 
                   $ruta = $urlbase."/cultivos/IMG_".$idcultivo."_".$archivos["name"];
                   move_uploaded_file($archivos["tmp_name"], $ruta);

                   $imagen="IMG_".$idcultivo."_".$archivos["name"];
				}
				//fin guardar file
				
				$objCase->actualizarDatoSimple('cultivo', 'imagen', $imagen, 'idcultivo', $idcultivo);


                $mensaje = "Cultivo Registrado de forma satisfactoria.";

				$cnx->commit();
				$resultado = array("idcultivo"=>$idcultivo, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}catch(Exception $e){
				$cnx->rollBack();
				$mensaje = "*** Error al Registrar. ". $e->getMessage();
				$resultado = array("idcultivo"=>0, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}
			break;

		case "MODIFICAR": 
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$sufijo = $_POST['sufijo'];
				$idcultivo = $_POST['idcultivo'.$sufijo];
				$idopcion = $_POST['idopcion'];
				$permiso = validarPermisoPorPerfil($idopcion,"puedeeditar");

				if(!$permiso){
					throw new Exception("No tienes permiso para realizar esta operacion.", 123);
				}

				$existeCultivo = $objCul->verificarCultivo($_POST['nombre'.$sufijo],$_POST['variedad'.$sufijo],$idcultivo);
				if($existeCultivo->rowCount()>=1){
					throw new Exception("El cultivo ya existe", 123);	
				}

				$rowCultivo = $objCase->getRowTableFiltroSimple("cultivo","idcultivo", $idcultivo);
				$imagen = "";
				//guardar file
				if ($_FILES['profile_avatar']['name']!='') {
					
					$urlbase="../files/imagenes";
                   	if (!file_exists($urlbase) && !is_dir($urlbase)){
                    	mkdir($urlbase);
                    	mkdir($urlbase."/cultivos",0777);
                    }else{
               	    	if (!file_exists($urlbase."/cultivos") && !is_dir($urlbase."/cultivos")) {
               		    	mkdir($urlbase."/cultivos",0777);
               	    	}
                   	}

                   	if(file_exists($urlbase.$rowCultivo['imagen'])){
					    @unlink($urlbase.$rowCultivo['imagen']);
				    }

                   	$archivos = $_FILES['profile_avatar']; 
                   	$ruta = $urlbase."/cultivos/IMG_".$idcultivo."_".$archivos["name"];
                   	move_uploaded_file($archivos["tmp_name"], $ruta);

                   	$imagen="IMG_".$idcultivo."_".$archivos["name"];
                    
				}
				//fin guardar file
				
				$valuesCultivo = $objCul->getColumnTablaCultivo();
				$valuesCultivo[':idcultivo'] = $_POST['idcultivo'.$sufijo];
				$valuesCultivo[':nombre'] = $_POST['nombre'.$sufijo];
				$valuesCultivo[':variedad'] = $_POST['variedad'.$sufijo];
				$valuesCultivo[':altura'] = (is_numeric($_POST['altura'.$sufijo]))?$_POST['altura'.$sufijo]:null;
				$valuesCultivo[':raiz_maxima'] = (is_numeric($_POST['raiz_maxima'.$sufijo]))?$_POST['raiz_maxima'.$sufijo]:null;
				$valuesCultivo[':raiz_minima'] = (is_numeric($_POST['raiz_minima'.$sufijo]))?$_POST['raiz_minima'.$sufijo]:null;
				$valuesCultivo[':periodovegetativo'] = $rowCultivo['periodovegetativo'];
				if($imagen!=""){
					$valuesCultivo[':imagen'] = $imagen;
				}else{
					$valuesCultivo[':imagen'] = $rowCultivo['imagen'];
				}
				$valuesCultivo[':fhregistro'] = $rowCultivo['fhregistro'];
				$valuesCultivo[':idpersonaregistro'] = $rowCultivo['idpersonaregistro'];
				$valuesCultivo[':fheditar'] = date('Y-m-d H:i:s');
				$valuesCultivo[':idpersonaeditar'] = $_SESSION['idpersona'];
                $objCase->actualizar('cultivo', 'idcultivo', $valuesCultivo);

                $imagenCultivo = $rowCultivo['imagen'];
                if($imagen!=""){
					$imagenCultivo = $imagen;
				}
                $objCase->actualizarDatoSimple('cultivo', 'imagen', $imagenCultivo, 'idcultivo', $idcultivo);

				$mensaje = "Cultivo Actualizado de forma satisfactoria.";

				$cnx->commit();
				$resultado = array("idcultivo"=>$idcultivo, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}catch(Exception $e){
				$cnx->rollBack();
				$mensaje = "*** Error al Actualizar. ". $e->getMessage();
				$resultado = array("idcultivo"=>0, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}
			break;

		case "CAMBIAR_ESTADO_CULTIVO":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();
					
				$idcultivo=$_POST['idcultivo'];
				$estado=$_POST['estado'];
				$idopcion = $_POST['idopcion'];
				$opcionpermiso = "puedeeliminar";
				if($estado=="A"){
					$opcionpermiso = "puedeanular";
				}
				$permiso = validarPermisoPorPerfil($idopcion,$opcionpermiso);

				if(!$permiso){
					throw new Exception("No tienes permiso para realizar esta operacion.", 123);
				}

				$arrayEstado = array('N'=>'Activado', 'A'=>'Anulado', 'E'=>'Eliminado');
                    
				$objCase->cambiarEstado('cultivo', $estado, 'idcultivo', $idcultivo);
				$objCase->actualizarDatoSimple('cultivo', 'fheliminar', date('Y-m-d H:i:s'), 'idcultivo', $idcultivo);
				$objCase->actualizarDatoSimple('cultivo', 'idpersonaeliminar', $_SESSION['idpersona'], 'idcultivo', $idcultivo);

		 		$cnx->commit();
				echo "Cultivo ".$arrayEstado[$estado]." de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar. ". $e->getMessage();
			}
			break;

		case "NUEVA_FENOLOGIA": 
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$sufijo = $_POST['sufijo'];
				$idopcion = $_POST['idopcion'];
				$permiso = validarPermisoPorPerfil($idopcion,"puederegistrar");

				if(!$permiso){
					throw new Exception("No tienes permiso para realizar esta operacion.", 123);
				}
				
				$existeCultivo = $objCul->verificarCultivoFenologia($_POST['nombre'.$sufijo],$_POST['idcultivo'.$sufijo]);
				if($existeCultivo->rowCount()>=1){
					throw new Exception("La fenologia ya existe", 123);	
				}

				$valuesFenologia = $objCul->getColumnTablaCultivoFenologia();
				$valuesFenologia[':nombre'] = $_POST['nombre'.$sufijo];
				$valuesFenologia[':duracion'] = $_POST['duracion'.$sufijo];
				$valuesFenologia[':kc'] = (is_numeric($_POST['kc'.$sufijo]))?$_POST['kc'.$sufijo]:null;
				$valuesFenologia[':raiz'] = (is_numeric($_POST['raiz'.$sufijo]))?$_POST['raiz'.$sufijo]:null;
				$valuesFenologia[':cobertura'] = (is_numeric($_POST['cobertura'.$sufijo]))?$_POST['cobertura'.$sufijo]:null;
				$valuesFenologia[':umbral'] = (is_numeric($_POST['umbral'.$sufijo]))?$_POST['umbral'.$sufijo]:null;
				$valuesFenologia[':temp_min'] = (is_numeric($_POST['temp_min'.$sufijo]))?$_POST['temp_min'.$sufijo]:null;
				$valuesFenologia[':temp_max'] = (is_numeric($_POST['temp_max'.$sufijo]))?$_POST['temp_max'.$sufijo]:null;
				$valuesFenologia[':humd_min'] = (is_numeric($_POST['humd_min'.$sufijo]))?$_POST['humd_min'.$sufijo]:null;
				$valuesFenologia[':humd_max'] = (is_numeric($_POST['humd_max'.$sufijo]))?$_POST['humd_max'.$sufijo]:null;
				$valuesFenologia[':idcultivo'] = $_POST['idcultivo'.$sufijo];
				$valuesFenologia[':fhregistro'] = date('Y-m-d H:i:s');
				$valuesFenologia[':idpersonaregistro'] = $_SESSION['idpersona'];
                $objCase->insertar('cultivo_fenologia', $valuesFenologia);

                $idfenologia = $objCase->getLastIdInsert('cultivo_fenologia', 'idfenologia');

				$cnx->commit();
				echo "Fenologia Registrada de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al Registrar. ". $e->getMessage();
			}
			break;

		case "MODIFICAR_FENOLOGIA": 
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$sufijo = $_POST['sufijo'];
				$idfenologia = $_POST['idfenologia'.$sufijo];
				$idopcion = $_POST['idopcion'];
				$permiso = validarPermisoPorPerfil($idopcion,"puedeeditar");

				if(!$permiso){
					throw new Exception("No tienes permiso para realizar esta operacion.", 123);
				}

				$existeCultivo = $objCul->verificarCultivoFenologia($_POST['nombre'.$sufijo],$_POST['idcultivo'.$sufijo],$idfenologia);
				if($existeCultivo->rowCount()>=1){
					throw new Exception("La fenologia ya existe", 123);	
				}

				$rowFenologia = $objCase->getRowTableFiltroSimple("cultivo_fenologia","idfenologia", $idfenologia);
				
				$valuesFenologia = $objCul->getColumnTablaCultivoFenologia();
				$valuesFenologia[':idfenologia'] = $idfenologia;
				$valuesFenologia[':orden'] = $rowFenologia['orden'];
				$valuesFenologia[':nombre'] = $_POST['nombre'.$sufijo];
				$valuesFenologia[':duracion'] = $_POST['duracion'.$sufijo];
				$valuesFenologia[':kc'] = (is_numeric($_POST['kc'.$sufijo]))?$_POST['kc'.$sufijo]:null;
				$valuesFenologia[':raiz'] = (is_numeric($_POST['raiz'.$sufijo]))?$_POST['raiz'.$sufijo]:null;
				$valuesFenologia[':cobertura'] = (is_numeric($_POST['cobertura'.$sufijo]))?$_POST['cobertura'.$sufijo]:null;
				$valuesFenologia[':umbral'] = (is_numeric($_POST['umbral'.$sufijo]))?$_POST['umbral'.$sufijo]:null;
				$valuesFenologia[':temp_min'] = (is_numeric($_POST['temp_min'.$sufijo]))?$_POST['temp_min'.$sufijo]:null;
				$valuesFenologia[':temp_max'] = (is_numeric($_POST['temp_max'.$sufijo]))?$_POST['temp_max'.$sufijo]:null;
				$valuesFenologia[':humd_min'] = (is_numeric($_POST['humd_min'.$sufijo]))?$_POST['humd_min'.$sufijo]:null;
				$valuesFenologia[':humd_max'] = (is_numeric($_POST['humd_max'.$sufijo]))?$_POST['humd_max'.$sufijo]:null;
				$valuesFenologia[':idcultivo'] = $_POST['idcultivo'.$sufijo];
				$valuesFenologia[':fhregistro'] = $rowFenologia['fhregistro'];
				$valuesFenologia[':idpersonaregistro'] = $rowFenologia['idpersonaregistro'];
				$valuesFenologia[':fheditar'] = date('Y-m-d H:i:s');
				$valuesFenologia[':idpersonaeditar'] = $_SESSION['idpersona'];
                $objCase->actualizar('cultivo_fenologia', 'idfenologia', $valuesFenologia);

				$cnx->commit();
				echo "Fenologia Actualizada de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al Actualizar. ". $e->getMessage();
			}
			break;

		case "CAMBIAR_ESTADO_FENOLOGIA":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();
					
				$idfenologia=$_POST['idfenologia'];
				$estado=$_POST['estado'];
				$idopcion = $_POST['idopcion'];
				$opcionpermiso = "puedeeliminar";
				if($estado=="A"){
					$opcionpermiso = "puedeanular";
				}
				$permiso = validarPermisoPorPerfil($idopcion,$opcionpermiso);

				if(!$permiso){
					throw new Exception("No tienes permiso para realizar esta operacion.", 123);
				}

				$arrayEstado = array('N'=>'Activada', 'A'=>'Anulada', 'E'=>'Eliminada');
                    
				$objCase->cambiarEstado('cultivo_fenologia', $estado, 'idfenologia', $idfenologia);
				$objCase->actualizarDatoSimple('cultivo_fenologia', 'fheliminar', date('Y-m-d H:i:s'), 'idfenologia', $idfenologia);
				$objCase->actualizarDatoSimple('cultivo_fenologia', 'idpersonaeliminar', $_SESSION['idpersona'], 'idfenologia', $idfenologia);


		 		$cnx->commit();
				echo "Fenologia ".$arrayEstado[$estado]." de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar. ". $e->getMessage();
			}
			break;

		case "ORDENAR_FENOLOGIA":
			try {
				$ordenElementos = $_POST['ordenElementos'];
				$arrayorden = array();
				$arrayorden = explode(",", $ordenElementos);
				$pos=1;
				for ($i=0; $i < count($arrayorden) ; $i++){
					$objCase->actualizarDatoSimple('cultivo_fenologia', 'orden', $pos, 'idfenologia', $arrayorden[$i]);
					$pos++;
				}

				echo 'Orden actualizado satisfactoriamente';
			} catch (Exception $e) {
				echo "***Lo sentimos, datos no pudieron ser actualizados";
			}
			break;

		case "GUARDAR_IMAGEN_SERVIDOR": 
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$sufijo = $_POST['sufijo'];
				$id = $_POST['id'];
				$pngBase64 = '';
				if(!isset($_POST['pngBase64'])){
					throw new Exception("No existe la imagen.", 123);
				}
				$pngBase64 = $_POST['pngBase64'];

				// Extraer el base64 de la imagen y eliminar el encabezado de tipo MIME
    			$base64_str = preg_replace('#^data:image/\w+;base64,#i', '', $pngBase64);
    			$grafico = base64_decode($base64_str);

    			$nombreArchivo = 'grafico_'.$sufijo.$id.'.png';

				//guardar file
				$urlbase="../files/imagenes";
               	if (!file_exists($urlbase) && !is_dir($urlbase)){
                	mkdir($urlbase,0777);
                }

                $urlbase .= '/' . $nombreArchivo;
               	if(file_exists($urlbase)){
				    @unlink($urlbase);
			    }

               	file_put_contents($urlbase, $grafico);
				//fin guardar file

				$cnx->commit();
				echo "Imagen subida";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error. ". $e->getMessage();
			}
			break;

		case "LISTA_CULTIVO": 
			try{

				$idcultivo = $_POST['idcultivo'];

				$data = $objCase->getListTableFiltroSimple('cultivo', 'estado', 'N');
				
				$opciones = "<option value='0'>- Todos -</option>";
				if(isset($_POST['vista']) && $_POST['vista']=='MANT'){
					$opciones = "<option value='0'>- Seleccione -</option>";
				}
				while($fila=$data->fetch(PDO::FETCH_NAMED)){
					$selected = "";
					if($idcultivo==$fila['idcultivo']){
						$selected="selected";
					}
					$opciones .= "<option value='".$fila['idcultivo']."' ".$selected.">".$fila['nombre']." - ".$fila['variedad']."</option>";
				}

				echo $opciones;
			}catch(Exception $e){
				echo "***Lo sentimos, datos no pudieron ser obtenidos";
			}
			break;

		default: 
			echo "Debe especificar alguna accion"; 
			break;
	}
	
}


?>