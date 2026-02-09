<?php 
require_once("../logica/clsCase.php");
require_once("../logica/clsAlmacen.php");
require_once("../logica/clsCompartido.php");
controlador($_POST['accion']);

function controlador($accion){
    $objCase = new clsCase();
    $objAlmacen = new clsAlmacen();
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

				$valuesAlm = $objAlmacen->getColumnTablaAlmacen();
				$valuesAlm[':descripcion'] = $_POST['descripcion'.$sufijo];
                $valuesAlm[':direccion'] = $_POST['direccion'.$sufijo];
                $valuesAlm[':ubigeo'] = $_POST['ubigeo'.$sufijo];
                $valuesAlm[':ubigeo_texto'] = $_POST['ubigeo_texto'.$sufijo];
                $valuesAlm[':idinstitucion'] = $_POST['idinstitucion'.$sufijo];
                $valuesAlm[':idsucursal'] = $_POST['idsucursal'.$sufijo];
                $objCase->insertar('mgalmacen', $valuesAlm);

                $idalmacen = $objCase->getLastIdInsert('mgalmacen', 'idalmacen');
                $mensaje = "Almacén Registrado de forma satisfactoria.";

				$cnx->commit();
				$resultado = array("idalmacen"=>$idalmacen, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}catch(Exception $e){
				$cnx->rollBack();
				$mensaje = "*** Error al Registrar. ". $e->getMessage();
				$resultado = array("idalmacen"=>0, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}
			break;

		case "MODIFICAR": 
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$sufijo = $_POST['sufijo'];
				$idalmacen = $_POST['idalmacen'.$sufijo];
				$idopcion = $_POST['idopcion'];
				$permiso = validarPermisoPorPerfil($idopcion,"puedeeditar");

				if(!$permiso){
					throw new Exception("No tienes permiso para realizar esta operacion.", 123);
				}

				$rowAlmacen = $objCase->getRowTableFiltroSimple('mgalmacen', 'idalmacen', $idalmacen);
				
				$valuesAlm = $objAlmacen->getColumnTablaAlmacen();
				$valuesAlm[':idalmacen'] = $idalmacen;
				$valuesAlm[':descripcion'] = $_POST['descripcion'.$sufijo];
                $valuesAlm[':direccion'] = $_POST['direccion'.$sufijo];
                $valuesAlm[':ubigeo'] = $_POST['ubigeo'.$sufijo];
                $valuesAlm[':ubigeo_texto'] = $_POST['ubigeo_texto'.$sufijo];
                $valuesAlm[':idinstitucion'] = $_POST['idinstitucion'.$sufijo];
                $valuesAlm[':idsucursal'] = $_POST['idsucursal'.$sufijo];
                $objCase->actualizar('mgalmacen', 'idalmacen', $valuesAlm);

                $mensaje = "Almacén Actualizado de forma satisfactoria.";

				$cnx->commit();
				$resultado = array("idalmacen"=>$idalmacen, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}catch(Exception $e){
				$cnx->rollBack();
				$mensaje = "*** Error al Actualizar. ". $e->getMessage();
				$resultado = array("idalmacen"=>0, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}
			break;

		case "CAMBIAR_ESTADO_ALMACEN":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();
					
				$idalmacen=$_POST['idalmacen'];
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
                    
				$objCase->cambiarEstado('mgalmacen', $estado, 'idalmacen', $idalmacen);

		 		$cnx->commit();
				echo "Almacén ".$arrayEstado[$estado]." de forma satisfactoria.";
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