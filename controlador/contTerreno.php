<?php 
require_once("../logica/clsCase.php");
require_once("../logica/clsTerreno.php");
require_once("../logica/clsCompartido.php");
controlador($_POST['accion']);

function controlador($accion){
    $objCase = new clsCase();
    $objTerreno = new clsTerreno();
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

				$values = $objTerreno->getColumnTablaTerreno();
				$values[':descripcion'] = $_POST['descripcion'.$sufijo];
                $values[':direccion'] = $_POST['direccion'.$sufijo];
                $values[':latitud'] = $_POST['latitud'.$sufijo];
                $values[':longitud'] = $_POST['longitud'.$sufijo];
                $values[':altitud'] = $_POST['altitud'.$sufijo];
                $values[':area'] = $_POST['area'.$sufijo];
                $values[':ubigeo'] = $_POST['ubigeo'.$sufijo];
                $values[':ubigeo_texto'] = $_POST['ubigeo_texto'.$sufijo];
                $values[':fhregistro'] = date('Y-m-d H:i:s');
				$values[':idpersonaregistro'] = $_SESSION['idpersona'];
                $objCase->insertar('terreno', $values);

                $idterreno = $objCase->getLastIdInsert('terreno', 'idterreno');
                $mensaje = "Terreno Registrado de forma satisfactoria.";

				$cnx->commit();
				$resultado = array("idterreno"=>$idterreno, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}catch(Exception $e){
				$cnx->rollBack();
				$mensaje = "*** Error al Registrar. ". $e->getMessage();
				$resultado = array("idterreno"=>0, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}
			break;

		case "MODIFICAR": 
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$sufijo = $_POST['sufijo'];
				$idterreno = $_POST['idterreno'.$sufijo];
				$idopcion = $_POST['idopcion'];
				$permiso = validarPermisoPorPerfil($idopcion,"puedeeditar");

				if(!$permiso){
					throw new Exception("No tienes permiso para realizar esta operacion.", 123);
				}

				$rowTerreno = $objCase->getRowTableFiltroSimple('terreno', 'idterreno', $idterreno);
				
				$values = $objTerreno->getColumnTablaTerreno();
				$values[':idterreno'] = $idterreno;
				$values[':descripcion'] = $_POST['descripcion'.$sufijo];
                $values[':direccion'] = $_POST['direccion'.$sufijo];
                $values[':latitud'] = $_POST['latitud'.$sufijo];
                $values[':longitud'] = $_POST['longitud'.$sufijo];
                $values[':altitud'] = $_POST['altitud'.$sufijo];
                $values[':area'] = $_POST['area'.$sufijo];
                $values[':ubigeo'] = $_POST['ubigeo'.$sufijo];
                $values[':ubigeo_texto'] = $_POST['ubigeo_texto'.$sufijo];
                $values[':fhregistro'] = $rowTerreno['fhregistro'];
				$values[':idpersonaregistro'] = $rowTerreno['idpersonaregistro'];
				$values[':fheditar'] = date('Y-m-d H:i:s');
				$values[':idpersonaeditar'] = $_SESSION['idpersona'];
                $objCase->actualizar('terreno', 'idterreno', $values);

				$mensaje = "Terreno Actualizado de forma satisfactoria.";

				$cnx->commit();
				$resultado = array("idterreno"=>$idterreno, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}catch(Exception $e){
				$cnx->rollBack();
				$mensaje = "*** Error al Actualizar. ". $e->getMessage();
				$resultado = array("idterreno"=>0, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}
			break;

		case "CAMBIAR_ESTADO_TERRENO":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();
					
				$idterreno=$_POST['idterreno'];
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
                    
				$objCase->cambiarEstado('terreno', $estado, 'idterreno', $idterreno);

		 		$cnx->commit();
				echo "Terreno ".$arrayEstado[$estado]." de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar. ". $e->getMessage();
			}
			break;

		case "NUEVO_ESQUEMA": 
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

				$verificaractivo = $objTerreno->verificarEsquemaActivo($_POST['idterreno'.$sufijo], 0);
				$activo = 1;
				if($verificaractivo->rowCount()>0){
					$activo = 0;
				}

				$values = $objTerreno->getColumnTablaTerrenoEsquema();
				$values[':descripcion'] = $_POST['descripcion'.$sufijo];
				$values[':activo'] = $activo;
				$values[':idterreno'] = $_POST['idterreno'.$sufijo];
				$values[':fhregistro'] = date('Y-m-d H:i:s');
				$values[':idpersonaregistro'] = $_SESSION['idpersona'];
                $objCase->insertar('terreno_esquema', $values);

                $idesquema = $objCase->getLastIdInsert('terreno_esquema', 'idesquema');
				$mensaje = "Esquema Registrado de forma satisfactoria.";

				$cnx->commit();
				$resultado = array("idesquema"=>$idesquema, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}catch(Exception $e){
				$cnx->rollBack();
				$mensaje = "*** Error al Registrar. ". $e->getMessage();
				$resultado = array("idesquema"=>0, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}
			break;

		case "MODIFICAR_ESQUEMA": 
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$sufijo = $_POST['sufijo'];
				$idesquema = $_POST['idesquema'.$sufijo];
				$idopcion = $_POST['idopcion'];
				$permiso = validarPermisoPorPerfil($idopcion,"puedeeditar");

				if(!$permiso){
					throw new Exception("No tienes permiso para realizar esta operacion.", 123);
				}

				$rowEsquema = $objCase->getRowTableFiltroSimple("terreno_esquema","idesquema", $idesquema);
				
				$values = $objTerreno->getColumnTablaTerrenoEsquema();
				$values[':idesquema'] = $idesquema;
				$values[':descripcion'] = $_POST['descripcion'.$sufijo];
				$values[':activo'] = $rowEsquema['activo'];
				$values[':idterreno'] = $_POST['idterreno'.$sufijo];
				$values[':fhregistro'] = $rowEsquema['fhregistro'];
				$values[':idpersonaregistro'] = $rowEsquema['idpersonaregistro'];
				$values[':fheditar'] = date('Y-m-d H:i:s');
				$values[':idpersonaeditar'] = $_SESSION['idpersona'];
                $objCase->actualizar('terreno_esquema', 'idesquema', $values);

				$mensaje = "Esquema Actualizado de forma satisfactoria.";

				$cnx->commit();
				$resultado = array("idesquema"=>$idesquema, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}catch(Exception $e){
				$cnx->rollBack();
				$mensaje = "*** Error al Actualizar. ". $e->getMessage();
				$resultado = array("idesquema"=>0, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}
			break;

		case "CAMBIAR_ESTADO_ESQUEMA":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();
					
				$idesquema=$_POST['idesquema'];
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
                    
				$objCase->cambiarEstado('terreno_esquema', $estado, 'idesquema', $idesquema);
				$objCase->actualizarDatoSimple('terreno_esquema', 'fheliminar', date('Y-m-d H:i:s'), 'idesquema', $idesquema);
				$objCase->actualizarDatoSimple('terreno_esquema', 'idpersonaeliminar', $_SESSION['idpersona'], 'idesquema', $idesquema);


		 		$cnx->commit();
				echo "Esquema ".$arrayEstado[$estado]." de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar. ". $e->getMessage();
			}
			break;

		case "ACTIVAR_DESACTIVAR_ESQUEMA":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();
				
				$idterreno=$_POST['idterreno'];
				$idesquema=$_POST['idesquema'];
				$activo=$_POST['activo'];
				$idopcion = $_POST['idopcion'];
				$opcionpermiso = "puedeanular";
				
				$permiso = validarPermisoPorPerfil($idopcion,$opcionpermiso);

				if(!$permiso){
					throw new Exception("No tienes permiso para realizar esta operacion.", 123);
				}

				if($activo==1){
					$verificaractivo = $objTerreno->verificarEsquemaActivo($idterreno, $idesquema);
					if($verificaractivo->rowCount()>0){
						throw new Exception("Ya existe un esquema hidráulico Activo", 123);
					}
				}

				$arrayEstado = array('Desactivado', 'Activado');
                    
				$objCase->actualizarDatoSimple('terreno_esquema', 'activo', $activo, 'idesquema', $idesquema);

		 		$cnx->commit();
				echo "Esquema ".$arrayEstado[$activo]." de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar. ". $e->getMessage();
			}
			break;

		default: 
			echo "Debe especificar alguna accion"; 
			break;
	}
	
}


?>