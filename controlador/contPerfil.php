<?php 
require_once("../logica/clsCase.php");
require_once("../logica/clsPerfil.php");
require_once("../logica/clsCompartido.php");
controlador($_POST['accion']);

function controlador($accion){
    $objCase = new clsCase();
    $objPerfil = new clsPerfil();
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
				
				$existePerfil = $objPerfil->verificarPerfil($_POST['descripcion'.$sufijo]);

				if($existePerfil->rowCount()>=1){
					throw new Exception("Perfil ya existe", 123);	
				}

				$valuesPerfil = $objPerfil->getColumnTablaPerfil();
				$valuesPerfil[':descripcion'] = $_POST['descripcion'.$sufijo];
                $objCase->insertar('perfil', $valuesPerfil);

                $idperfil = $objCase->getLastIdInsert('perfil', 'idperfil');

				$cnx->commit();
				echo "Perfil Registrado de forma satisfactoria.";
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
				$idperfil = $_POST['idperfil'.$sufijo];
				$idopcion = $_POST['idopcion'];
				$permiso = validarPermisoPorPerfil($idopcion,"puedeeditar");

				if(!$permiso){
					throw new Exception("No tienes permiso para realizar esta operacion.", 123);
				}
				
				$existePerfil = $objPerfil->verificarPerfil($_POST['descripcion'.$sufijo],$idperfil);

				if($existePerfil->rowCount()>=1){
					throw new Exception("Perfil ya existe", 123);	
				}

				$rowPerfil = $objCase->getRowTableFiltroSimple('perfil', 'idperfil', $idperfil);
				
				$valuesPerfil = $objPerfil->getColumnTablaPerfil();
				$valuesPerfil[':idperfil'] = $idperfil;
				$valuesPerfil[':descripcion'] = $_POST['descripcion'.$sufijo];
				$valuesPerfil[':estado'] = $rowPerfil['estado'];
                $objCase->actualizar('perfil', 'idperfil', $valuesPerfil);

				$cnx->commit();
				echo "Perfil Actualizado de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al Actualizar. ". $e->getMessage();
			}
			break;

		case "CAMBIAR_ESTADO_PERFIL":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();
					
				$idperfil=$_POST['idperfil'];
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
                    
				$objCase->cambiarEstado('perfil', $estado, 'idperfil', $idperfil);

		 		$cnx->commit();
				echo "Perfil ".$arrayEstado[$estado]." de forma satisfactoria.";
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