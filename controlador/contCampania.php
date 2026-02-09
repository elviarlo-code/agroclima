<?php 
require_once("../logica/clsCampania.php");
require_once("../logica/clsCultivo.php");
require_once("../logica/clsCase.php");
require_once("../logica/clsCompartido.php");
controlador($_POST['accion']);

function controlador($accion){
	$objCam = new clsCampania();
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

				$rowEsquema = $objCase->getRowTableFiltroSimple("terreno_esquema","idterreno", $_POST['idterreno'.$sufijo],"activo",1,"estado","N");

				$values = $objCam->getColumnTablaCampania();
				$values[':fechaini'] = $_POST['fechaini'.$sufijo];
				$values[':fechasiembra'] = $_POST['fechaini'.$sufijo];
				$values[':descripcion'] = $_POST['descripcion'.$sufijo];
				$values[':idcultivo'] = $_POST['idcultivo'.$sufijo];
				$values[':idturno'] = $_POST['idturno'.$sufijo];
				$values[':idesquema'] = $rowEsquema['idesquema'];
				$values[':idterreno'] = $_POST['idterreno'.$sufijo];
				
				$values[':fhregistro'] = date('Y-m-d H:i:s');
				$values[':idpersonaregistro'] = $_SESSION['idpersona'];
                $objCase->insertar('campania', $values);

                $idcampania = $objCase->getLastIdInsert('campania', 'idcampania');

                $fenologia = $objCul->consultarFenologia($_POST['idcultivo'.$sufijo], '');
                $fenologia = $fenologia->fetchAll(PDO::FETCH_NAMED);

                foreach($fenologia as $k=>$v){
					$valuesFenologia = $objCam->getColumnTablaCampaniaFenologia();
					$valuesFenologia[':nombre'] = $v['nombre'];
					$valuesFenologia[':duracion'] = $v['duracion'];
					$valuesFenologia[':kc'] = $v['kc'];
					$valuesFenologia[':raiz'] = $v['raiz'];
					$valuesFenologia[':cobertura'] = $v['cobertura'];
					$valuesFenologia[':umbral'] = $v['umbral'];
					$valuesFenologia[':temp_min'] = $v['temp_min'];
					$valuesFenologia[':temp_max'] = $v['temp_max'];
					$valuesFenologia[':humd_min'] = $v['humd_min'];
					$valuesFenologia[':humd_max'] = $v['humd_max'];
					$valuesFenologia[':idcultivo'] = $_POST['idcultivo'.$sufijo];
					$valuesFenologia[':idcampania'] = $idcampania;
					$valuesFenologia[':fhregistro'] = date('Y-m-d H:i:s');
					$valuesFenologia[':idpersonaregistro'] = $_SESSION['idpersona'];
	                $objCase->insertar('campania_fenologia', $valuesFenologia);
				}


                $mensaje = "Campaña Registrada de forma satisfactoria.";

				$cnx->commit();
				$resultado = array("idcampania"=>$idcampania, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}catch(Exception $e){
				$cnx->rollBack();
				$mensaje = "*** Error al Registrar. ". $e->getMessage();
				$resultado = array("idcampania"=>0, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}
			break;

		case "MODIFICAR": 
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$sufijo = $_POST['sufijo'];
				$idcampania = $_POST['idcampania'.$sufijo];
				$idopcion = $_POST['idopcion'];
				$permiso = validarPermisoPorPerfil($idopcion,"puedeeditar");

				if(!$permiso){
					throw new Exception("No tienes permiso para realizar esta operacion.", 123);
				}

				$rowCampania = $objCase->getRowTableFiltroSimple("campania","idcampania", $idcampania);

				$modificar_fenologia = false;
				if($rowCampania['idcultivo']!=$_POST['idcultivo'.$sufijo]){
					$modificar_fenologia = true;
				}

				$fechasiembra = $rowCampania['fechasiembra'];
				if($rowCampania['fechasiembra']<=$rowCampania['fechaini']){
					$fechasiembra = $_POST['fechaini'.$sufijo];
				}
				
				$values = $objCam->getColumnTablaCampania();
				$values[':idcampania'] = $idcampania;
				$values[':fechaini'] = $_POST['fechaini'.$sufijo];
				$values[':fechasiembra'] = $fechasiembra;
				$values[':fechafin'] = $rowCampania['fechafin'];
				$values[':descripcion'] = $_POST['descripcion'.$sufijo];
				$values[':finalizado'] = $rowCampania['finalizado'];
				$values[':idcultivo'] = $_POST['idcultivo'.$sufijo];
				$values[':idturno'] = $_POST['idturno'.$sufijo];
				$values[':idesquema'] = $rowCampania['idesquema'];
				$values[':idterreno'] = $_POST['idterreno'.$sufijo];
				$values[':iddispositivo'] = $rowCampania['iddispositivo'];
				$values[':fhregistro'] = $rowCampania['fhregistro'];
				$values[':idpersonaregistro'] = $rowCampania['idpersonaregistro'];
				$values[':fheditar'] = date('Y-m-d H:i:s');
				$values[':idpersonaeditar'] = $_SESSION['idpersona'];
                $objCase->actualizar('campania', 'idcampania', $values);

                if($modificar_fenologia){
                	$objCase->actualizarDatoSimple('campania_fenologia', 'estado', 'E', 'idcampania', $idcampania);

                	$fenologia = $objCul->consultarFenologia($_POST['idcultivo'.$sufijo], '');
	                $fenologia = $fenologia->fetchAll(PDO::FETCH_NAMED);

	                foreach($fenologia as $k=>$v){
						$valuesFenologia = $objCam->getColumnTablaCampaniaFenologia();
						$valuesFenologia[':nombre'] = $v['nombre'];
						$valuesFenologia[':duracion'] = $v['duracion'];
						$valuesFenologia[':kc'] = $v['kc'];
						$valuesFenologia[':raiz'] = $v['raiz'];
						$valuesFenologia[':cobertura'] = $v['cobertura'];
						$valuesFenologia[':umbral'] = $v['umbral'];
						$valuesFenologia[':temp_min'] = $v['temp_min'];
						$valuesFenologia[':temp_max'] = $v['temp_max'];
						$valuesFenologia[':humd_min'] = $v['humd_min'];
						$valuesFenologia[':humd_max'] = $v['humd_max'];
						$valuesFenologia[':idcultivo'] = $_POST['idcultivo'.$sufijo];
						$valuesFenologia[':idcampania'] = $idcampania;
						$valuesFenologia[':fhregistro'] = date('Y-m-d H:i:s');
						$valuesFenologia[':idpersonaregistro'] = $_SESSION['idpersona'];
		                $objCase->insertar('campania_fenologia', $valuesFenologia);
					}
                }

				$mensaje = "Campaña Actualizada de forma satisfactoria.";

				$cnx->commit();
				$resultado = array("idcampania"=>$idcampania, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}catch(Exception $e){
				$cnx->rollBack();
				$mensaje = "*** Error al Actualizar. ". $e->getMessage();
				$resultado = array("idcampania"=>0, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}
			break;

		case "CAMBIAR_ESTADO_CAMPANIA":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();
					
				$idcampania=$_POST['idcampania'];
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
                    
				$objCase->cambiarEstado('campania', $estado, 'idcampania', $idcampania);
				$objCase->actualizarDatoSimple('campania', 'fheliminar', date('Y-m-d H:i:s'), 'idcampania', $idcampania);
				$objCase->actualizarDatoSimple('campania', 'idpersonaeliminar', $_SESSION['idpersona'], 'idcampania', $idcampania);
				
				if($estado=='N'){
					$fenologia = $objCase->getListTableFiltroSimple('campania_fenologia', 'estado', 'A', 'idcampania', $idcampania);
					while($fila = $fenologia->fetch(PDO::FETCH_NAMED)){
						$objCase->actualizarDatoSimple('campania_fenologia', 'estado', $estado, 'idfenologia', $fila['idfenologia']);
					}
				}else{
					$objCase->actualizarDatoSimple('campania_fenologia', 'estado', $estado, 'idcampania', $idcampania);
				}

		 		$cnx->commit();
				echo "Campaña ".$arrayEstado[$estado]." de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar. ". $e->getMessage();
			}
			break;

		case "NUEVA_FENOLOGIA_CAMPANIA": 
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
				
				$existeCultivo = $objCam->verificarCultivoFenologiaCampania($_POST['nombre'.$sufijo],$_POST['idcampania'.$sufijo]);
				if($existeCultivo->rowCount()>=1){
					throw new Exception("La fenologia ya existe", 123);	
				}

				$valuesFenologia = $objCam->getColumnTablaCampaniaFenologia();
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
				$valuesFenologia[':idcampania'] = $_POST['idcampania'.$sufijo];
				$valuesFenologia[':fhregistro'] = date('Y-m-d H:i:s');
				$valuesFenologia[':idpersonaregistro'] = $_SESSION['idpersona'];
                $objCase->insertar('campania_fenologia', $valuesFenologia);

                $idfenologia = $objCase->getLastIdInsert('campania_fenologia', 'idfenologia');

				$cnx->commit();
				echo "Fenologia Registrada de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al Registrar. ". $e->getMessage();
			}
			break;

		case "MODIFICAR_FENOLOGIA_CAMPANIA": 
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

				$existeCultivo = $objCam->verificarCultivoFenologiaCampania($_POST['nombre'.$sufijo],$_POST['idcampania'.$sufijo],$idfenologia);
				if($existeCultivo->rowCount()>=1){
					throw new Exception("La fenologia ya existe", 123);	
				}

				$rowFenologia = $objCase->getRowTableFiltroSimple("campania_fenologia","idfenologia", $idfenologia);
				
				$valuesFenologia = $objCam->getColumnTablaCampaniaFenologia();
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
				$valuesFenologia[':idcampania'] = $_POST['idcampania'.$sufijo];
				$valuesFenologia[':fhregistro'] = $rowFenologia['fhregistro'];
				$valuesFenologia[':idpersonaregistro'] = $rowFenologia['idpersonaregistro'];
				$valuesFenologia[':fheditar'] = date('Y-m-d H:i:s');
				$valuesFenologia[':idpersonaeditar'] = $_SESSION['idpersona'];
                $objCase->actualizar('campania_fenologia', 'idfenologia', $valuesFenologia);

				$cnx->commit();
				echo "Fenologia Actualizada de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al Actualizar. ". $e->getMessage();
			}
			break;

		case "CAMBIAR_ESTADO_FENOLOGIA_CAMPANIA":
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
                    
				$objCase->cambiarEstado('campania_fenologia', $estado, 'idfenologia', $idfenologia);
				$objCase->actualizarDatoSimple('campania_fenologia', 'fheliminar', date('Y-m-d H:i:s'), 'idfenologia', $idfenologia);
				$objCase->actualizarDatoSimple('campania_fenologia', 'idpersonaeliminar', $_SESSION['idpersona'], 'idfenologia', $idfenologia);


		 		$cnx->commit();
				echo "Fenologia ".$arrayEstado[$estado]." de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar. ". $e->getMessage();
			}
			break;

		case "ORDENAR_FENOLOGIA_CAMPANIA":
			try {
				$ordenElementos = $_POST['ordenElementos'];
				$arrayorden = array();
				$arrayorden = explode(",", $ordenElementos);
				$pos=1;
				for ($i=0; $i < count($arrayorden) ; $i++){
					$objCase->actualizarDatoSimple('campania_fenologia', 'orden', $pos, 'idfenologia', $arrayorden[$i]);
					$pos++;
				}

				echo 'Orden actualizado satisfactoriamente';
			} catch (Exception $e) {
				echo "***Lo sentimos, datos no pudieron ser actualizados";
			}
			break;

		case "ACTUALIZAR_FECHA_CAMPANIA": 
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
				
				$objCase->actualizarDatoSimple('campania', 'fechaini', $_POST['fechaini'], 'idcampania', $_POST['idcampania'.$sufijo]);
				$objCase->actualizarDatoSimple('campania', 'fechasiembra', $_POST['fechasiembra'], 'idcampania', $_POST['idcampania'.$sufijo]);
				$objCase->actualizarDatoSimple('campania', 'iddispositivo', $_POST['iddispositivo'.$sufijo], 'idcampania', $_POST['idcampania'.$sufijo]);

				$cnx->commit();
				echo "Campaña Actualizada de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al Registrar. ". $e->getMessage();
			}
			break;

		default: 
			echo "***Debe especificar alguna accion"; 
			break;
	}
	
}


?>