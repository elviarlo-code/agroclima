<?php 
require_once("../logica/clsCase.php");
require_once("../logica/clsInstitucion.php");
require_once("../logica/clsCompartido.php");
controlador($_POST['accion']);

function controlador($accion){
    $objCase = new clsCase();
    $objInstitucion = new clsInstitucion();
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
				
				$existeInstitucion = $objInstitucion->verificarInstitucion($_POST['ruc'.$sufijo]);

				if($existeInstitucion->rowCount()>=1){
					throw new Exception("RUC ya existe", 123);	
				}

				$valuesIns = $objInstitucion->getColumnTablaInstitucion();
				$valuesIns[':nombre'] = $_POST['nombre'.$sufijo];
                $valuesIns[':tipodoc'] = $_POST['tipodoc'.$sufijo];
                $valuesIns[':ruc'] = $_POST['ruc'.$sufijo];
                $valuesIns[':direccion'] = $_POST['direccion'.$sufijo];
                $valuesIns[':parafact'] = $_POST['parafact'.$sufijo];
                $valuesIns[':nrodocrepresentante'] = $_POST['nrodocrepresentante'.$sufijo];
                $valuesIns[':nombrerepresentante'] = $_POST['nombrerepresentante'.$sufijo];
                $valuesIns[':codigo_ubigeo_departamento'] = $_POST['codigo_ubigeo_departamento'.$sufijo];
                $valuesIns[':codigo_ubigeo_provincia'] = $_POST['codigo_ubigeo_provincia'.$sufijo];
                $valuesIns[':codigo_ubigeo_distrito'] = $_POST['codigo_ubigeo_distrito'.$sufijo];
                $valuesIns[':direccion_departamento'] = $_POST['direccion_departamento'.$sufijo];
                $valuesIns[':direccion_provincia'] = $_POST['direccion_provincia'.$sufijo];
                $valuesIns[':direccion_distrito'] = $_POST['direccion_distrito'.$sufijo];
                $objCase->insertar('mginstitucion', $valuesIns);

                $idinstitucion = $objCase->getLastIdInsert('mginstitucion', 'idinstitucion');
                $mensaje = "Institución Registrada de forma satisfactoria.";

				$cnx->commit();
				$resultado = array("idinstitucion"=>$idinstitucion, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}catch(Exception $e){
				$cnx->rollBack();
				$mensaje = "*** Error al Registrar. ". $e->getMessage();
				$resultado = array("idinstitucion"=>0, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}
			break;

		case "MODIFICAR": 
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$sufijo = $_POST['sufijo'];
				$idinstitucion = $_POST['idinstitucion'.$sufijo];
				$idopcion = $_POST['idopcion'];
				$permiso = validarPermisoPorPerfil($idopcion,"puedeeditar");

				if(!$permiso){
					throw new Exception("No tienes permiso para realizar esta operacion.", 123);
				}
				
				$existeInstitucion = $objInstitucion->verificarInstitucion($_POST['ruc'.$sufijo],$idinstitucion);

				if($existeInstitucion->rowCount()>=1){
					throw new Exception("RUC ya existe", 123);	
				}

				$rowInstitucion = $objCase->getRowTableFiltroSimple('mginstitucion', 'idinstitucion', $idinstitucion);
				
				$valuesIns = $objInstitucion->getColumnTablaInstitucion();
				$valuesIns[':idinstitucion'] = $idinstitucion;
				$valuesIns[':nombre'] = $_POST['nombre'.$sufijo];
                $valuesIns[':tipodoc'] = $_POST['tipodoc'.$sufijo];
                $valuesIns[':ruc'] = $_POST['ruc'.$sufijo];
                $valuesIns[':direccion'] = $_POST['direccion'.$sufijo];
                $valuesIns[':parafact'] = $_POST['parafact'.$sufijo];
                $valuesIns[':nrodocrepresentante'] = $_POST['nrodocrepresentante'.$sufijo];
                $valuesIns[':nombrerepresentante'] = $_POST['nombrerepresentante'.$sufijo];
                $valuesIns[':codigo_ubigeo_departamento'] = $_POST['codigo_ubigeo_departamento'.$sufijo];
                $valuesIns[':codigo_ubigeo_provincia'] = $_POST['codigo_ubigeo_provincia'.$sufijo];
                $valuesIns[':codigo_ubigeo_distrito'] = $_POST['codigo_ubigeo_distrito'.$sufijo];
                $valuesIns[':direccion_departamento'] = $_POST['direccion_departamento'.$sufijo];
                $valuesIns[':direccion_provincia'] = $_POST['direccion_provincia'.$sufijo];
                $valuesIns[':direccion_distrito'] = $_POST['direccion_distrito'.$sufijo];
                $objCase->actualizar('mginstitucion', 'idinstitucion', $valuesIns);

                $mensaje = "Institución Actualizada de forma satisfactoria.";

				$cnx->commit();
				$resultado = array("idinstitucion"=>$idinstitucion, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}catch(Exception $e){
				$cnx->rollBack();
				$mensaje = "*** Error al Actualizar. ". $e->getMessage();
				$resultado = array("idinstitucion"=>0, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}
			break;

		case "CAMBIAR_ESTADO_INSTITUCION":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();
					
				$idinstitucion=$_POST['idinstitucion'];
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
                    
				$objCase->cambiarEstado('mginstitucion', $estado, 'idinstitucion', $idinstitucion);

		 		$cnx->commit();
				echo "Institución ".$arrayEstado[$estado]." de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar. ". $e->getMessage();
			}
			break;

		case "LISTA_INSTITUCION": 
			try{

				$idinstitucion = $_POST['idinstitucion'];
				$data = $objCase->getListTableFiltroSimple('mginstitucion', 'estado', 'N');
				
				echo "<option value='0'>- Seleccione -</option>";
				while($fila=$data->fetch(PDO::FETCH_NAMED)){
					$selected = "";
					if($idinstitucion==$fila['idinstitucion']){
						$selected = "selected";
					}
					echo "<option value='".$fila['idinstitucion']."' ".$selected.">".$fila['nombre']."</option>";
				}
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