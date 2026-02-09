<?php 
require_once("../logica/clsCase.php");
require_once("../logica/clsAlmacen.php");
require_once("../logica/clsCompartido.php");
controlador($_POST['accion']);

function controlador($accion){
    $objCase = new clsCase();
    $objAlmacen = new clsAlmacen();
	switch ($accion){

		case "GUARDAR_ORDEN_BOARD": 
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$boardOrder = json_decode($_POST['boardOrder'], true); // Decodificar el JSON
				foreach ($boardOrder as $board) {
                    $idkanban = intval(str_replace('board_', '', $board['id'])); // Extraer el ID numérico
                    $order = intval($board['order']);

                    // Actualizar el orden en la base de datos
                    $objCase->actualizarDatoSimple('kanban', 'orden', $order, 'idkanban', $idkanban);
                }

				$cnx->commit();
				echo "Orden Actualizado de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al Actualizar. ". $e->getMessage();
			}
			break;

		case "GUARDAR_ORDEN_ITEM": 
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$itemOrder = json_decode($_POST['itemOrder'], true); // Decodificar el JSON
				foreach ($itemOrder as $item) {
                    $idtarjeta = intval(str_replace('item_', '', $item['id'])); // Extraer el ID numérico
                    $idkanban = intval(str_replace('board_', '', $item['boardId'])); // Extraer el ID del board
                    $order = intval($item['order']);

                    // Actualizar el orden y el board en la base de datos
                    $objCase->actualizarDatoSimple('kanban_dashboard', 'orden', $order, 'idtarjeta', $idtarjeta);
                    $objCase->actualizarDatoSimple('kanban_dashboard', 'idkanban', $idkanban, 'idtarjeta', $idtarjeta);
                }

				$cnx->commit();
				echo "Orden Actualizado de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al Actualizar. ". $e->getMessage();
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