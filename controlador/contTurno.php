<?php 
require_once("../logica/clsCase.php");
require_once("../logica/clsTurno.php");
require_once("../logica/clsCompartido.php");
controlador($_POST['accion']);

function controlador($accion){
    $objCase = new clsCase();
    $objTurno = new clsTurno();
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

				$coordenadas = json_decode($_POST['coordenadas'], true)['coordenadas'];

				$values = $objTurno->getColumnTablaTerrenoTurno();
				$values[':nombre'] = $_POST['nombre'.$sufijo];
                $values[':area'] = $_POST['area'.$sufijo];
                $values[':color'] = $_POST['color'.$sufijo];
                $values[':idterreno'] = $_POST['idterreno'.$sufijo];
                $values[':idesquema'] = $_POST['idesquema'.$sufijo];
                $values[':fhregistro'] = date('Y-m-d H:i:s');
				$values[':idpersonaregistro'] = $_SESSION['idpersona'];
                $objCase->insertar('terreno_turno', $values);

                $idturno = $objCase->getLastIdInsert('terreno_turno', 'idturno');
				if(is_array($coordenadas) && count($coordenadas)>0){
					foreach($coordenadas as $k=>$v){
						$valuesCoordenada = $objTurno->getColumnTablaTerrenoTurnoCoordenada();
						$valuesCoordenada[':latitud'] = $v['lat'];
						$valuesCoordenada[':longitud'] = $v['lng'];
						$valuesCoordenada[':idturno'] = $idturno;
						$valuesCoordenada[':idterreno'] = $_POST['idterreno'.$sufijo];
						$valuesCoordenada[':idesquema'] = $_POST['idesquema'.$sufijo];
						$valuesCoordenada[':fhregistro'] = date('Y-m-d H:i:s');
						$valuesCoordenada[':idpersonaregistro'] = $_SESSION['idpersona'];
						$objCase->insertar('terreno_turno_coordenada', $valuesCoordenada);
					}
				}


                $mensaje = "Turno Registrado de forma satisfactoria.";

				$cnx->commit();
				$resultado = array("idturno"=>$idturno, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}catch(Exception $e){
				$cnx->rollBack();
				$mensaje = "*** Error al Registrar. ". $e->getMessage();
				$resultado = array("idturno"=>0, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}
			break;

		case "MODIFICAR": 
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$sufijo = $_POST['sufijo'];
				$idturno = $_POST['idturno'.$sufijo];
				$idopcion = $_POST['idopcion'];
				$permiso = validarPermisoPorPerfil($idopcion,"puedeeditar");

				if(!$permiso){
					throw new Exception("No tienes permiso para realizar esta operacion.", 123);
				}

				$objCase->actualizarDatoSimple('terreno_turno_coordenada', 'estado', 'E', 'idturno', $idturno);


				$coordenadas = json_decode($_POST['coordenadas'], true)['coordenadas'];
				$rowTurno = $objCase->getRowTableFiltroSimple('terreno_turno', 'idturno', $idturno);
				
				$values = $objTurno->getColumnTablaTerrenoTurno();
				$values[':idturno'] = $idturno;
				$values[':nombre'] = $_POST['nombre'.$sufijo];
                $values[':area'] = $_POST['area'.$sufijo];
                $values[':color'] = $_POST['color'.$sufijo];
                $values[':idterreno'] = $_POST['idterreno'.$sufijo];
                $values[':idesquema'] = $_POST['idesquema'.$sufijo];
                $values[':fhregistro'] = $rowTurno['fhregistro'];
				$values[':idpersonaregistro'] = $rowTurno['idpersonaregistro'];
				$values[':fheditar'] = date('Y-m-d H:i:s');
				$values[':idpersonaeditar'] = $_SESSION['idpersona'];
                $objCase->actualizar('terreno_turno', 'idturno', $values);

                if(is_array($coordenadas) && count($coordenadas)>0){
					foreach($coordenadas as $k=>$v){
						$valuesCoordenada = $objTurno->getColumnTablaTerrenoTurnoCoordenada();
						$valuesCoordenada[':latitud'] = $v['lat'];
						$valuesCoordenada[':longitud'] = $v['lng'];
						$valuesCoordenada[':idturno'] = $idturno;
						$valuesCoordenada[':idterreno'] = $_POST['idterreno'.$sufijo];
						$valuesCoordenada[':idesquema'] = $_POST['idesquema'.$sufijo];
						$valuesCoordenada[':fhregistro'] = date('Y-m-d H:i:s');
						$valuesCoordenada[':idpersonaregistro'] = $_SESSION['idpersona'];
						$objCase->insertar('terreno_turno_coordenada', $valuesCoordenada);
					}
				}

                $mensaje = "Turno Actualizado de forma satisfactoria.";

				$cnx->commit();
				$resultado = array("idturno"=>$idturno, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}catch(Exception $e){
				$cnx->rollBack();
				$mensaje = "*** Error al Actualizar. ". $e->getMessage();
				$resultado = array("idturno"=>0, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}
			break;

		case "CAMBIAR_ESTADO_TURNO":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();
					
				$idturno=$_POST['idturno'];
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
                    
				$objCase->cambiarEstado('terreno_turno', $estado, 'idturno', $idturno);

		 		$cnx->commit();
				echo "Turno ".$arrayEstado[$estado]." de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar. ". $e->getMessage();
			}
			break;

		case "LISTA_ESQUEMA": 
			try{

				$idterreno = $_POST['idterreno'];
				$idesquema = 0;
				if(isset($_POST['idesquema'])){
					$idesquema = $_POST['idesquema'];
				}

				$solouno = 0;
				if(isset($_POST['solouno'])){
					$solouno = $_POST['solouno'];
				}

				/*if(isset($_POST['vista']) && $_POST['vista']=='MANT'){
					$data = $objCase->getListTableFiltroSimple('terreno_esquema', 'activo', 1, 'estado', 'N', 'idterreno', $idterreno);
				}else{*/
					if($solouno==1 && $idesquema>0){
						$data = $objCase->getListTableFiltroSimple('terreno_esquema', 'estado', 'N', 'idterreno', $idterreno, 'idesquema', $idesquema);
					}else{
						$data = $objCase->getListTableFiltroSimple('terreno_esquema', 'estado', 'N', 'idterreno', $idterreno);
					}
				/*}*/
				
				$opciones = "<option value='0'>- Todos -</option>";
				if(isset($_POST['vista']) && $_POST['vista']=='MANT'){
					$opciones = "<option value='0'>- Seleccione -</option>";
				}
				if($solouno==1 && isset($_POST['vista']) && $_POST['vista']!='MANT'){
					$opciones = "";
				}
				while($fila=$data->fetch(PDO::FETCH_NAMED)){
					$selected = "";
					if($idesquema==$fila['idesquema'] && isset($_POST['vista']) && $_POST['vista']!='MANT'){
						$selected="selected";
					}
					$opciones .= "<option value='".$fila['idesquema']."' ".$selected.">".$fila['descripcion']."</option>";
				}

				echo $opciones;
			}catch(Exception $e){
				echo "***Lo sentimos, datos no pudieron ser obtenidos";
			}
			break;

		case "LISTA_TERRENO": 
			try{

				$idterreno = $_POST['idterreno'];
				$solouno = 0;
				if(isset($_POST['solouno'])){
					$solouno = $_POST['solouno'];
				}

				if($solouno==0){
					$data = $objCase->getListTableFiltroSimple('terreno', 'estado', 'N');
				}else if($solouno==1 && $idterreno>0){
					$data = $objCase->getListTableFiltroSimple('terreno', 'estado', 'N', 'idterreno', $idterreno);
				}
				
				if($solouno==0){
					echo "<option value='0' latitud='' longitud=''>- Seleccione -</option>";
				}
				while($fila=$data->fetch(PDO::FETCH_NAMED)){
					$selected = "";
					if($idterreno==$fila['idterreno']){
						$selected = "selected";
					}
					echo "<option value='".$fila['idterreno']."' ".$selected." latitud='".$fila['latitud']."' longitud='".$fila['longitud']."'>".$fila['descripcion']."</option>";
				}
			}catch(Exception $e){
				echo "***Lo sentimos, datos no pudieron ser obtenidos";
			}
			break;

		case "LISTA_TURNO": 
			try{

				$idterreno = $_POST['idterreno'];
				$idesquema = 0;
				if(isset($_POST['idesquema'])){
					$idesquema = $_POST['idesquema'];
				}

				$idturno = 0;
				if(isset($_POST['idturno'])){
					$idturno = $_POST['idturno'];
				}
				
				$data = $objTurno->consultarTurnoPorTerrenoActivo($idterreno, $idesquema, $idturno);
				
				$opciones = "<option value='0'>- Todos -</option>";
				if(isset($_POST['vista']) && $_POST['vista']=='MANT'){
					$opciones = "<option value='0'>- Seleccione -</option>";
				}

				while($fila=$data->fetch(PDO::FETCH_NAMED)){
					$selected = "";
					$opciones .= "<option value='".$fila['idturno']."' ".$selected.">".$fila['nombre']."</option>";
				}

				echo $opciones;
			}catch(Exception $e){
				echo "***Lo sentimos, datos no pudieron ser obtenidos";
			}
			break;

		case "CONSULTAR_COORDENADAS_TURNOS": 
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$idterreno = $_POST['idterreno'];
				$idesquema = $_POST['idesquema'];
				$idturno = 0;
				if(isset($_POST['idturno'])){
					$idturno = $_POST['idturno'];
				}
				$data = $objTurno->consultarCoordenadaPorEsquema($idterreno, $idesquema, $idturno);
				$data = $data->fetchAll(PDO::FETCH_NAMED);

				$turnos = array();
				foreach($data as $kx=>$vx){
					$existe = false;
					$posicion = -1;
					foreach($turnos as $ky=>$vy){
						if($vx['idturno'] == $vy['idturno']){
							$existe = true;
							$posicion = $ky;
							break;
						}
					}

					if($existe){
						$turnos[$posicion]['coordenadas'][] = array('lat'=>$vx['latitud'], 'lng'=>$vx['longitud']);
					}else{
						$turnos[] = array('idturno'=>$vx['idturno'], 'turno'=>$vx['turno'], 'area'=>$vx['area'], 'color'=>$vx['color'], 'coordenadas'=>array(array('lat'=>$vx['latitud'], 'lng'=>$vx['longitud'])));
					}
				}
				
				
				$mensaje = "Datos Obtenidos";

				$cnx->commit();
				$resultado = array("data"=>$turnos, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}catch(Exception $e){
				$cnx->rollBack();
				$mensaje = "*** Error, los datos no se pudieron obtener. ". $e->getMessage();
				$resultado = array("idturno"=>0, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}
			break;

		default: 
			echo "Debe especificar alguna accion"; 
			break;
	}
	
}


?>