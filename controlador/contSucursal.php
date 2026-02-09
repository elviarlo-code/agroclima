<?php 
require_once("../logica/clsCase.php");
require_once("../logica/clsSucursal.php");
require_once("../logica/clsCompartido.php");
controlador($_POST['accion']);

function controlador($accion){
    $objCase = new clsCase();
    $objSucursal = new clsSucursal();
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

				$valuesSuc = $objSucursal->getColumnTablaSucursal();
				$valuesSuc[':nombre'] = $_POST['nombre'.$sufijo];
                $valuesSuc[':direccion'] = $_POST['direccion'.$sufijo];
                $valuesSuc[':idinstitucion'] = $_POST['idinstitucion'.$sufijo];
                $objCase->insertar('mgsucursal', $valuesSuc);

                $idsucursal = $objCase->getLastIdInsert('mgsucursal', 'idsucursal');
                $mensaje = "Sucursal Registrada de forma satisfactoria.";

				$cnx->commit();
				$resultado = array("idinstitucion"=>$_POST['idinstitucion'.$sufijo], "idsucursal"=>$idsucursal, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}catch(Exception $e){
				$cnx->rollBack();
				$mensaje = "*** Error al Registrar. ". $e->getMessage();
				$resultado = array("idinstitucion"=>0, "idsucursal"=>0, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}
			break;

		case "MODIFICAR": 
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$sufijo = $_POST['sufijo'];
				$idsucursal = $_POST['idsucursal'.$sufijo];
				$idopcion = $_POST['idopcion'];
				$permiso = validarPermisoPorPerfil($idopcion,"puedeeditar");

				if(!$permiso){
					throw new Exception("No tienes permiso para realizar esta operacion.", 123);
				}

				$rowSucursal = $objCase->getRowTableFiltroSimple('mgsucursal', 'idsucursal', $idsucursal);
				
				$valuesSuc = $objSucursal->getColumnTablaSucursal();
				$valuesSuc[':idsucursal'] = $idsucursal;
				$valuesSuc[':nombre'] = $_POST['nombre'.$sufijo];
                $valuesSuc[':direccion'] = $_POST['direccion'.$sufijo];
                $valuesSuc[':idinstitucion'] = $_POST['idinstitucion'.$sufijo];
                $objCase->actualizar('mgsucursal', 'idsucursal', $valuesSuc);

                $mensaje = "Sucursal Actualizada de forma satisfactoria.";

				$cnx->commit();
				$resultado = array("idinstitucion"=>$_POST['idinstitucion'.$sufijo], "idsucursal"=>$idsucursal, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}catch(Exception $e){
				$cnx->rollBack();
				$mensaje = "*** Error al Actualizar. ". $e->getMessage();
				$resultado = array("idinstitucion"=>0, "idsucursal"=>0, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}
			break;

		case "CAMBIAR_ESTADO_SUCURSAL":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();
					
				$idsucursal=$_POST['idsucursal'];
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
                    
				$objCase->cambiarEstado('mgsucursal', $estado, 'idsucursal', $idsucursal);

		 		$cnx->commit();
				echo "Sucursal ".$arrayEstado[$estado]." de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar. ". $e->getMessage();
			}
			break;

		case "LISTA_SUCURSAL": 
			try{

				$idinstitucion = $_POST['idinstitucion'];
				$idsucursal = 0;
				if(isset($_POST['idsucursal'])){
					$idsucursal = $_POST['idsucursal'];
				}
				$data = $objCase->getListTableFiltroSimple('mgsucursal', 'estado', 'N', 'idinstitucion', $idinstitucion);
				
				$opciones = "<option value='0'>- Todos -</option>";
				if(isset($_POST['vista']) && $_POST['vista']=='MANT'){
					$opciones = "<option value='0'>- Seleccione -</option>";
				}
				while($fila=$data->fetch(PDO::FETCH_NAMED)){
					$selected = "";
					if($idsucursal==$fila['idsucursal']){
						$selected="selected";
					}
					$opciones .= "<option value='".$fila['idsucursal']."' ".$selected.">".$fila['nombre']."</option>";
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