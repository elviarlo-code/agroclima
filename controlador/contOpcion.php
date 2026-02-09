<?php 
require_once("../logica/clsCase.php");
require_once("../logica/clsOpcion.php");
require_once("../logica/clsCompartido.php");
controlador($_POST['accion']);

function controlador($accion){
    $objCase = new clsCase();
    $objOpcion = new clsOpcion();
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

				$values = $objOpcion->getColumnTablaOpcion();
				$values[':descripcion'] = $_POST['descripcion'.$sufijo];
				$values[':link'] = $_POST['link'.$sufijo];
                $values[':idopcion_ref'] = $_POST['idopcion_ref'.$sufijo];
                $values[':orden'] = ($_POST['orden'.$sufijo]>=0)?$_POST['orden'.$sufijo]:null;
                $values[':nro_registro'] = (is_numeric($_POST['nro_registro'.$sufijo]))?$_POST['nro_registro'.$sufijo]:null;
                $values[':title'] = $_POST['title'.$sufijo];
                $values[':icon'] = $_POST['icon'.$sufijo];
                if($_POST['puederegistrar']!=""){
                	$values[':puederegistrar'] = $_POST['puederegistrar'];
            	}
            	if($_POST['puedeeditar']!=""){
                	$values[':puedeeditar'] = $_POST['puedeeditar'];
            	}
            	if($_POST['puedeanular']!=""){
                	$values[':puedeanular'] = $_POST['puedeanular'];
            	}
            	if($_POST['puedeeliminar']!=""){
                	$values[':puedeeliminar'] = $_POST['puedeeliminar'];
            	}
            	if($_POST['puedeimprimir']!=""){
                	$values[':puedeimprimir'] = $_POST['puedeimprimir'];
            	}
            	if($_POST['opcion_especial']!=""){
                	$values[':opcion_especial'] = $_POST['opcion_especial'];
            	}
            	if($_POST['opcion_especial1']!=""){
                	$values[':opcion_especial1'] = $_POST['opcion_especial1'];
            	}
            	if($_POST['opcion_especial2']!=""){
                	$values[':opcion_especial2'] = $_POST['opcion_especial2'];
            	}
                $values[':accesodirecto'] = $_POST['accesodirecto'.$sufijo];
                $values[':tituloaccesodirecto'] = $_POST['tituloaccesodirecto'.$sufijo];
                $objCase->insertarWithoutUpper('opcion', $values);

				$cnx->commit();
				echo "Opcion Registrada de forma satisfactoria.";
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
				$id = $_POST['idopcion'.$sufijo];
				$idopcion = $_POST['idopcion'];
				$permiso = validarPermisoPorPerfil($idopcion,"puedeeditar");

				if(!$permiso){
					throw new Exception("No tienes permiso para realizar esta operacion.", 123);
				}

				$rowOpcion = $objCase->getRowTableFiltroSimple('opcion', 'idopcion', $id);
				
				$values = $objOpcion->getColumnTablaOpcion();
				$values[':idopcion'] = $id;
				$values[':descripcion'] = $_POST['descripcion'.$sufijo];
				$values[':link'] = $_POST['link'.$sufijo];
                $values[':idopcion_ref'] = $_POST['idopcion_ref'.$sufijo];
                $values[':orden'] = ($_POST['orden'.$sufijo]>=0)?$_POST['orden'.$sufijo]:null;
                $values[':nro_registro'] = (is_numeric($_POST['nro_registro'.$sufijo]))?$_POST['nro_registro'.$sufijo]:null;
                $values[':title'] = $_POST['title'.$sufijo];
                $values[':icon'] = $_POST['icon'.$sufijo];
                if($_POST['puederegistrar']!=""){
                	$values[':puederegistrar'] = $_POST['puederegistrar'];
            	}
            	if($_POST['puedeeditar']!=""){
                	$values[':puedeeditar'] = $_POST['puedeeditar'];
            	}
            	if($_POST['puedeanular']!=""){
                	$values[':puedeanular'] = $_POST['puedeanular'];
            	}
            	if($_POST['puedeeliminar']!=""){
                	$values[':puedeeliminar'] = $_POST['puedeeliminar'];
            	}
            	if($_POST['puedeimprimir']!=""){
                	$values[':puedeimprimir'] = $_POST['puedeimprimir'];
            	}
            	if($_POST['opcion_especial']!=""){
                	$values[':opcion_especial'] = $_POST['opcion_especial'];
            	}
            	if($_POST['opcion_especial1']!=""){
                	$values[':opcion_especial1'] = $_POST['opcion_especial1'];
            	}
            	if($_POST['opcion_especial2']!=""){
                	$values[':opcion_especial2'] = $_POST['opcion_especial2'];
            	}
                $values[':accesodirecto'] = $_POST['accesodirecto'.$sufijo];
                $values[':tituloaccesodirecto'] = $_POST['tituloaccesodirecto'.$sufijo];
                $values[':estado'] = $rowOpcion['estado'];
                $objCase->actualizarWithoutUpper('opcion', 'idopcion', $values);

				$cnx->commit();
				echo "Opción Actualizado de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al Actualizar. ". $e->getMessage();
			}
			break;

		case "CAMBIAR_ESTADO_OPCION":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();
					
				$id=$_POST['idop'];
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
                    
				$objCase->cambiarEstado('opcion', $estado, 'idopcion', $id);

		 		$cnx->commit();
				echo "Opción ".$arrayEstado[$estado]." de forma satisfactoria.";
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