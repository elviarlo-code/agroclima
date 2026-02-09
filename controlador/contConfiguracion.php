<?php 
require_once("../logica/clsCase.php");
require_once("../logica/clsConfiguracion.php");
require_once("../logica/clsCompartido.php");
controlador($_POST['accion']);

function controlador($accion){
    $objCase = new clsCase();
    $objConfig = new clsConfiguracion();
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
				
				$existeConfig = $objConfig->verificarConfig($_POST['idconfig'.$sufijo]);

				if($existeConfig->rowCount()>=1){
					throw new Exception("Código de Config ya existe", 123);	
				}

				$values = $objConfig->getColumnTablaConfiguracion();
				$values[':idconfig'] = $_POST['idconfig'.$sufijo];
                $values[':descripcion'] = $_POST['descripcion'.$sufijo];
                $values[':modulo'] = $_POST['modulo'.$sufijo];
                $values[':tipdat'] = $_POST['tipdat'.$sufijo];
                $values[':longitud'] = $_POST['longitud'.$sufijo];
                $values[':valor'] = $_POST['valor'.$sufijo];
                $values[':observacion'] = $_POST['observacion'.$sufijo];
                $objCase->insertarWithoutUpper('mgconfig', $values);

				$cnx->commit();
				echo "Configuración Registrada de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al Registrar. ". $e->getMessage();
			}
			break;

		case "MODIFICAR": 
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$sufijo = $_POST['sufijo'];
				$codigo = $_POST['codigo'.$sufijo];
				$idopcion = $_POST['idopcion'];
				$permiso = validarPermisoPorPerfil($idopcion,"puedeeditar");

				if(!$permiso){
					throw new Exception("No tienes permiso para realizar esta operacion.", 123);
				}
				
				$existeConfig = $objConfig->verificarConfig($_POST['idconfig'.$sufijo],$codigo);

				if($existeConfig->rowCount()>=1){
					throw new Exception("Código de Config ya existe", 123);	
				}

				$rowConfig = $objCase->getRowTableFiltroSimple('mgconfig', 'codigo', $codigo);
				
				$values = $objConfig->getColumnTablaConfiguracion();
				$values[':codigo'] = $codigo;
				$values[':idconfig'] = $_POST['idconfig'.$sufijo];
                $values[':descripcion'] = $_POST['descripcion'.$sufijo];
                $values[':modulo'] = $_POST['modulo'.$sufijo];
                $values[':tipdat'] = $_POST['tipdat'.$sufijo];
                $values[':longitud'] = $_POST['longitud'.$sufijo];
                $values[':valor'] = $_POST['valor'.$sufijo];
                $values[':observacion'] = $_POST['observacion'.$sufijo];
                $values[':estado'] = $rowConfig['estado'];
                $objCase->actualizarWithoutUpper('mgconfig', 'codigo', $values);

				$cnx->commit();
				echo "Configuración Actualizada de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al Actualizar. ". $e->getMessage();
			}
			break;

		case "CAMBIAR_ESTADO_CONFIGURACION":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();
					
				$codigo=$_POST['codigo'];
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
                    
				$objCase->cambiarEstado('mgconfig', $estado, 'codigo', $codigo);

		 		$cnx->commit();
				echo "Configuración ".$arrayEstado[$estado]." de forma satisfactoria.";
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