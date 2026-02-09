<?php 
require_once("../logica/clsUsuario.php");
require_once("../logica/clsSesion.php");
require_once("../logica/clsCase.php");
require_once("../logica/clsPersona.php");
require_once("../logica/clsCompartido.php");
controlador($_POST['accion']);

function controlador($accion){
	$objUsu = new clsUsuario();
	$objSesion = new clsSesion();
    $objCase = new clsCase();
    $objPer = new clsPersona();
	switch ($accion){
		
		case "INGRESAR": 
			try{
				$rst=$objUsu->consultarAcceso($_POST['txtuser'],$_POST['txtclave']);
				if($rst->rowCount()>0){
					$user=$rst->fetch();

					$_SESSION['idusuario']=$user['idusuario'];
					$_SESSION['idpersona']=$user['idpersona'];
					$_SESSION['idperfil']=$user['idperfil'];
					$_SESSION['perfil']=$user['perfil'];
					$_SESSION['persona']=$user['persona'];
					$_SESSION['nombre']=$user['nombres'];
					$_SESSION['login']=$user['login'];
                    $_SESSION['nrodoc']=$user['nro_documento'];
                    $_SESSION['sesionid']=session_id();
                    $_SESSION['foto'] = "assets/media/users/blank.png";

                    if($user['foto']!=""){
						if(file_exists("../files/imagenes/".$user['foto'])){
							$_SESSION['foto'] = "files/imagenes/".$user['foto'];
						}
					}

                    $idempresas=array();
                    $acceso = $objUsu->consultarAccesoInstitucionSucursal($user['idperfil']);

					$idsucursal=array();
                    while($acc= $acceso->fetch(PDO::FETCH_NAMED)){
                        $idempresas[]=$acc;
                        $idsucursal[]=$acc["idsucursal"];
                    }
                    
                    $_SESSION['acceso']=$idempresas;
                                        

					$objUsu->actualizarVisitas($user['idusuario']);
					
					$objSesion->insertarSesion($user['idusuario'],$user['idperfil']);

					$configuraciones = $objCase->getListTableFiltroSimple("mgconfig","1",1);
					$config=array();
					while($fila = $configuraciones->fetch(PDO::FETCH_NAMED)){
						$config[intval($fila['idconfig'])][intval($fila['idinstitucion'])][intval($fila['idsucursal'])] = $fila['valor'];
					}
					$_SESSION['config'] = $config;
					
					echo 'admin.php';
				}else{
					echo '*** Usuario o contraseña no válido ***';
				}
			}catch(Exception $e){
				echo "*** ERROR AL ACCEDER<br/><br/>".$e->getMessage();
			}
			break;

		case 'CONSULTAR_DOCUMENTO_WS':
			try {

				$respuesta = array();

				$documento = $_POST['doc'];
				$tipodocumento = $_POST['tipodoc'];

				$parametros = array('dni'=>$documento);
				$url = "https://apiperu.dev/api/dni";
				if($tipodocumento==6){
					$parametros = array('ruc'=>$documento);
					$url = "https://apiperu.dev/api/ruc";
				}

				$params = json_encode($parametros);
			    $curl = curl_init();
			    curl_setopt_array($curl, array(
			        CURLOPT_URL => $url,
			        CURLOPT_RETURNTRANSFER => true,
			        CURLOPT_CUSTOMREQUEST => "POST",
			        CURLOPT_SSL_VERIFYPEER => false,
			        CURLOPT_POSTFIELDS => $params,        
			        CURLOPT_HTTPHEADER => [
			            'Accept: application/json',
			            'Content-Type: application/json',
			            'Authorization: Bearer 9357eb9c98612b5e568b26ec0b3952091e32f2b088e6ec1f436bffbf5076ef17'
			        ],        
			    ));
			    $response = curl_exec($curl);
			    $datos = json_decode($response,true);

			    if(isset($datos['data']) && count($datos['data'])>0){
				    if($tipodocumento==6){
				    	$respuesta = array(
											'numero'=>$documento,
											'nombre_completo'=>$datos['data']['nombre_o_razon_social'],
											'nombre'=>$datos['data']['nombre_o_razon_social'],
											'apellido_paterno'=>'',
											'apellido_materno'=>'',
											'direccion'=>$datos['data']['direccion_completa']
						);
				    }else if($tipodocumento==1){
				    	$respuesta = array(
											'numero'=>$datos['data']['numero'],
											'nombre_completo'=>$datos['data']['nombre_completo'],
											'nombre'=>$datos['data']['nombres'],
											'apellido_paterno'=>$datos['data']['apellido_paterno'],
											'apellido_materno'=>$datos['data']['apellido_materno'],
											'direccion'=>$datos['data']['direccion']
						);
				    }
				}

			    $response = json_encode($respuesta);
			    $err = curl_error($curl);
			    curl_close($curl);
			    if ($err) {
			        echo "[]";
			    } else {
			        echo $response;
			    } 
			} catch (Exception $e) {
			 	echo "Los sentimos, datos no pudieron ser obtenidos";
			}
			break;

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
				
				$registroPersona = $objCase->getRowTableFiltroSimple("persona","nro_documento", $_POST['nrodoc'.$sufijo], 'estado', 'N');
				if($registroPersona!=NULL){
    				$idpersona = $registroPersona['idpersona'];
    				$objCase->actualizarDatoSimple('persona', 'estrabajador', 1, 'idpersona', $idpersona);
				}else{
					$tipodocumento= 0;
					if(strlen($_POST['nrodoc'.$sufijo])==8){
						$tipodocumento = 1;
					}else if(strlen($_POST['nrodoc'.$sufijo])==11){
						$tipodocumento = 6;
					}

					$valuesPersona = $objPer->getColumnTablaPersona();
					$valuesPersona[':apellidos'] = $_POST['apellidousuario'.$sufijo];
                    $valuesPersona[':nombres'] = $_POST['nombreusuario'.$sufijo];
                    $valuesPersona[':razon_social'] = $_POST['nombre'.$sufijo];
                    $valuesPersona[':tipo_documento'] = $tipodocumento;
                    $valuesPersona[':nro_documento'] = $_POST['nrodoc'.$sufijo];
                    $valuesPersona[':direccion'] = $_POST['direccion'.$sufijo];
                    $valuesPersona[':estrabajador'] = 1;
                    $valuesPersona[':idregistrador'] = $_SESSION['idpersona'];
                    $valuesPersona[':fhregistro'] = date('Y-m-d H:i:s');
                    $objCase->insertar('persona', $valuesPersona);

                    $idpersona = $objCase->getLastIdInsert('persona', 'idpersona');
				}

				$existeUsuario = $objUsu->verificarUsuario($_POST['login'.$sufijo]);
				if($existeUsuario->rowCount()>=1){
					throw new Exception("Login ingresado ya existe", 123);	
				}

				$foto="";
				//guardar file
				if ($_FILES['profile_avatar']['name']!='') {
					
                   	$urlbase="../files/imagenes";
                   	if (!file_exists($urlbase) && !is_dir($urlbase)){
                    	mkdir($urlbase);
                    	mkdir($urlbase."/usuarios",0777);
                    }else{
               	    	if (!file_exists($urlbase."/usuarios") && !is_dir($urlbase."/usuarios")) {
               		    	mkdir($urlbase."/usuarios",0777);
               	    	}
                   }
                   $archivos = $_FILES['profile_avatar']; 
                   $ruta = $urlbase."/usuarios/IMG_".$_POST['nrodoc'.$sufijo]."_".$archivos["name"];
                   move_uploaded_file($archivos["tmp_name"], $ruta);

                   $foto="/usuarios/IMG_".$_POST['nrodoc'.$sufijo]."_".$archivos["name"];
				}
				//fin guardar file
				
				$objUsu->registrarUsuario($_POST['nombre'.$sufijo], $_POST['login'.$sufijo], $_POST['primeraclave'.$sufijo], $idpersona, $_POST['idperfil'.$sufijo],$foto);

				$cnx->commit();
				echo "Usuario Registrado de forma satisfactoria.";
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
				$idusuario = $_POST['idusuario'.$sufijo];
				$idopcion = $_POST['idopcion'];
				$permiso = validarPermisoPorPerfil($idopcion,"puedeeditar");

				if(!$permiso){
					throw new Exception("No tienes permiso para realizar esta operacion.", 123);
				}
				
				$registroPersona = $objCase->getRowTableFiltroSimple("persona","nro_documento", $_POST['nrodoc'.$sufijo], 'estado', 'N');
				if($registroPersona!=NULL){
    				$idpersona = $registroPersona['idpersona'];
    				$objCase->actualizarDatoSimple('persona', 'estrabajador', 1, 'idpersona', $idpersona);
				}else{
					$tipodocumento= 0;
					if(strlen($_POST['nrodoc'.$sufijo])==8){
						$tipodocumento = 1;
					}else if(strlen($_POST['nrodoc'.$sufijo])==11){
						$tipodocumento = 6;
					}

					$valuesPersona = $objPer->getColumnTablaPersona();
					$valuesPersona[':apellidos'] = $_POST['apellidousuario'.$sufijo];
                    $valuesPersona[':nombres'] = $_POST['nombreusuario'.$sufijo];
                    $valuesPersona[':razon_social'] = $_POST['nombre'.$sufijo];
                    $valuesPersona[':tipo_documento'] = $tipodocumento;
                    $valuesPersona[':nro_documento'] = $_POST['nrodoc'.$sufijo];
                    $valuesPersona[':direccion'] = $_POST['direccion'.$sufijo];
                    $valuesPersona[':estrabajador'] = 1;
                    $valuesPersona[':idregistrador'] = $_SESSION['idpersona'];
                    $valuesPersona[':fhregistro'] = date('Y-m-d H:i:s');
                    $objCase->insertar('persona', $valuesPersona);

                    $idpersona = $objCase->getLastIdInsert('persona', 'idpersona');
				}

				$existeUsuario = $objUsu->verificarUsuario($_POST['login'.$sufijo],$idusuario);
				if($existeUsuario->rowCount()>1){
					throw new Exception("Login ingresado ya existe", 123);	
				}

				$rowUsuario = $objCase->getRowTableFiltroSimple("usuario","idusuario", $idusuario);
				$foto = "";
				//guardar file
				if ($_FILES['profile_avatar']['name']!='') {
					
					$urlbase="../files/imagenes";
                   	if (!file_exists($urlbase) && !is_dir($urlbase)){
                    	mkdir($urlbase);
                    	mkdir($urlbase."/usuarios",0777);
                    }else{
               	    	if (!file_exists($urlbase."/usuarios") && !is_dir($urlbase."/usuarios")) {
               		    	mkdir($urlbase."/usuarios",0777);
               	    	}
                   	}

                   	if(file_exists($urlbase.$rowUsuario['foto'])){
					    @unlink($urlbase.$rowUsuario['foto']);
				    }

                   	$archivos = $_FILES['profile_avatar']; 
                   	$ruta = $urlbase."/usuarios/IMG_".$_POST['nrodoc'.$sufijo]."_".$archivos["name"];
                   	move_uploaded_file($archivos["tmp_name"], $ruta);

                   	$foto="/usuarios/IMG_".$_POST['nrodoc'.$sufijo]."_".$archivos["name"];
                    
				}
				//fin guardar file
				
				$objUsu->actualizarUsuario($idusuario, $_POST['nombre'.$sufijo], $idpersona, $_POST['idperfil'.$sufijo], $_POST['login'.$sufijo], $_POST['primeraclave'.$sufijo], $foto);

				$cnx->commit();
				echo "Usuario Actualizado de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al Actualizar. ". $e->getMessage();
			}
			break;

		case "CAMBIAR_ESTADO_USUARIO":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();
					
				$idusuario=$_POST['idusuario'];
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
                    
				$objCase->cambiarEstado('usuario', $estado, 'idusuario', $idusuario);

		 		$cnx->commit();
				echo "Usuario ".$arrayEstado[$estado]." de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar. ". $e->getMessage();
			}
			break;

		case "CAMBIAR_ESTADO_ACCESO": 
			try{
				$idperfil = $_POST['idperfil'];
				$idopcion = $_POST['idopcion'];
				$idacceso = $_POST['idacceso'];

				$permiso = $_POST['permiso'];
				
				if ($permiso == 1){
					$objUsu->registrarAcceso($_POST['idperfil'],$_POST['idopcion'],'N');
					echo "Permiso registrado ";
				}else{
					$objUsu->quitarAcceso($_POST['idperfil'],$_POST['idopcion']); 
					echo "Permiso Eliminado ";
				}
			}catch(Exception $e){
				echo "*** Error al actualizar permiso.";
			}
			break;

		case "COPIAR_PERMISOS_PERFIL": 
			try{
			    $idperfil=$_POST['idperfil'];
			    $idperfilDestino=$_POST['idperfilDestino'];
			    
				$permisos=$objCase->getListTableFiltroSimple("acceso","estado","N","idperfil",$idperfil);
				$permisos=$permisos->fetchAll(PDO::FETCH_NAMED);

				$permisosD=$objCase->getListTableFiltroSimple("acceso","estado","N","idperfil",$idperfilDestino);

				if ($permisosD->rowCount()>0) {
					$objCase->eliminarBD('acceso','idperfil',$idperfilDestino);
				}

				foreach ($permisos as $k => $v) {
                    
					$valores[':idacceso']=null;
					$valores[':idperfil']=$idperfilDestino;
					$valores[':idopcion']=$v['idopcion'];
					$valores[':estado']='N';
					$objCase->insertar('acceso',$valores);
											
				}
				echo "Permisos copiados satisfactoriamente";

			}catch(Exception $e){
				echo "*** Error al Copiar Permisos.";
			}
			break;

		case "ACTUALIZAR_PERFIL_INFORMACION":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$idusuario = $_POST['idusuario'];
				$idpersona = $_POST['idpersona'];
				$rowUsuario = $objCase->getRowTableFiltroSimple("usuario","idusuario", $idusuario);
				$rowPersona = $objCase->getRowTableFiltroSimple("persona","idpersona", $idpersona);
				$foto = "";
				//guardar file
				if ($_FILES['profile_avatar']['name']!='') {
					
					$urlbase="../files/imagenes";
                   	if (!file_exists($urlbase) && !is_dir($urlbase)){
                    	mkdir($urlbase);
                    	mkdir($urlbase."/usuarios",0777);
                    }else{
               	    	if (!file_exists($urlbase."/usuarios") && !is_dir($urlbase."/usuarios")) {
               		    	mkdir($urlbase."/usuarios",0777);
               	    	}
                   	}

                   	if(file_exists($urlbase.$rowUsuario['foto'])){
					    @unlink($urlbase.$rowUsuario['foto']);
				    }

                   	$archivos = $_FILES['profile_avatar']; 
                   	$ruta = $urlbase."/usuarios/IMG_".$rowPersona['nro_documento']."_".$archivos["name"];
                   	move_uploaded_file($archivos["tmp_name"], $ruta);

                   	$foto="/usuarios/IMG_".$rowPersona['nro_documento']."_".$archivos["name"];

                   	$objCase->actualizarDatoSimple('usuario', 'foto', $foto, 'idusuario', $idusuario);

				    $foto = 'files/imagenes/'.$foto;
				    $_SESSION['foto'] = $foto;
                    
				}
				//fin guardar file
				
				$razon_social = $_POST['nombres'].' '.$_POST['apellidos'];	
				$values = array(
					':idpersona'=>$idpersona,
					':nombres'=>$_POST['nombres'],
					':apellidos'=>$_POST['apellidos'],
					':razon_social'=>$razon_social,
					':telcelular'=>$_POST['telcelular'],
					':email' => $_POST['email'],
					':facebook' => $_POST['facebook'],
					':idpersonaeditar' =>$_SESSION['idpersona'],
					':fheditar' => date('Y-m-d H:i:s')
				);
				$objCase->actualizar('persona','idpersona', $values);

				$_SESSION['persona']=$razon_social;
				$_SESSION['nombre']=$_POST['nombres'];

		 		$cnx->commit();
				$mensaje = "Usuario actualizado de forma satisfactoria.";
				$email = substr($_POST['email'],0,18).(strlen($_POST['email'])>18?'...':'');
				$celular = $_POST['telcelular'];
				$facebook = substr($_POST['facebook'],0,15).(strlen($_POST['facebook'])>15?'...':'');
				$resultado = array("foto"=>$foto, "nombre"=>$_POST['nombres'], 'email'=>$email, 'celular'=>$celular, 'facebook'=>$facebook, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}catch(Exception $e){
				$cnx->rollBack();
				$mensaje = "*** Error al actualizar. ". $e->getMessage();
				$resultado = array("foto"=>'', "nombre"=>'', 'email'=>'', 'celular'=>'', 'facebook'=>'', "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}
			break;

		case "ACTUALIZAR_PERFIL_USUARIO":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();
					
				$idusuario=$_POST['idusuario'];
				$idpersona=$_POST['idpersona'];
				$existe=$objUsu->verificarUsuarioClave($idusuario, $idpersona, $_POST['password_actual']);
				$mensaje = "";
				if($existe->rowCount()==1){
					$objUsu->actualizarClaveUsuario($idusuario, $idpersona, $_POST['password_nuevo']);
					$mensaje = "Contraseña actualizada satisfactoriamente";
					
				}else{
					$mensaje = "*** Contraseña ingresada es incorrecta.";	
				}

		 		$cnx->commit();
				echo $mensaje;
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar. ". $e->getMessage();
			}
			break;

		case "CAMBIAR_ESTADO_SUCURSAL": 
			try{
				$idperfil = $_POST['idperfil'];
				$idinstitucion = $_POST['idinstitucion'];
				$idsucursal = $_POST['idsucursal'];
				$idconfiguracion = $_POST['idconfiguracion'];

				$permiso = $_POST['permiso'];
				
				if ($permiso == 1){
					$objUsu->registrarAccesoSucursal($_POST['idperfil'],$idinstitucion,$idsucursal,'N');
					echo "Permiso registrado ";
				}else{
					$objUsu->quitarAccesoSucursal($_POST['idperfil'],$idinstitucion,$idsucursal); 
					echo "Permiso Eliminado ";
				}
			}catch(Exception $e){
				echo "*** Error al actualizar permiso.";
			}
			break;

		default: 
			echo "Debe especificar alguna accion"; 
			break;
	}
	
}


?>