<?php 
require_once("../logica/clsCase.php");
require_once("../logica/clsPersona.php");
require_once("../logica/clsCompartido.php");
controlador($_POST['accion']);

function controlador($accion){
    $objCase = new clsCase();
    $objPersona = new clsPersona();
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
				
				$existePersona = $objPersona->verificarPersona($_POST['nro_documento'.$sufijo]);

				if($existePersona->rowCount()>=1){
					throw new Exception("Numero de Documento ya existe", 123);	
				}

				$escliente=0;
				$estrabajador=0;
				$esproveedor=0;

				$tipo = explode(",", $_POST['tipo_persona']);
				if(in_array('C', $tipo)){
					$escliente=1;
				}

				if(in_array('T', $tipo)){
					$estrabajador=1;
				}

				if(in_array('P', $tipo)){
					$esproveedor=1;
				}

				if($_POST['razon_social'.$sufijo]==""){
					$_POST['razon_social'.$sufijo] = $_POST['nombres'.$sufijo].' '.$_POST['apellidos'.$sufijo];
					$_POST['razon_social'.$sufijo] = trim($_POST['razon_social'.$sufijo]);
				}

				$valuesPersona = $objPersona->getColumnTablaPersona();
				$valuesPersona[':apellidos'] = $_POST['apellidos'.$sufijo];
                $valuesPersona[':nombres'] = $_POST['nombres'.$sufijo];
                $valuesPersona[':razon_social'] = $_POST['razon_social'.$sufijo];
                $valuesPersona[':tipo_documento'] = $_POST['tipo_documento'.$sufijo];
                $valuesPersona[':nro_documento'] = $_POST['nro_documento'.$sufijo];
                $valuesPersona[':email'] = $_POST['email'.$sufijo];
                $valuesPersona[':facebook'] = $_POST['facebook'.$sufijo];
                $valuesPersona[':medio_comunicacion'] = $_POST['medio_comunicacion'.$sufijo];
                $valuesPersona[':direccion'] = $_POST['direccion'.$sufijo];
                $valuesPersona[':sexo'] = $_POST['sexo'.$sufijo];
                $valuesPersona[':ubigeo'] = $_POST['ubigeo'.$sufijo];
                $valuesPersona[':ubigeo_dir_dep'] = $_POST['ubigeo_dir_dep'.$sufijo];
                $valuesPersona[':ubigeo_dir_pro'] = $_POST['ubigeo_dir_pro'.$sufijo];
                $valuesPersona[':ubigeo_dir_dis'] = $_POST['ubigeo_dir_dis'.$sufijo];
                $valuesPersona[':telcelular'] = $_POST['telcelular'.$sufijo];
                if($_POST['fnacimiento'.$sufijo]!=""){
                	$valuesPersona[':fnacimiento'] = $_POST['fnacimiento'.$sufijo];
            	}
            	$valuesPersona[':observacion'] = $_POST['observacion'.$sufijo];
                $valuesPersona[':estrabajador'] = $estrabajador;
                $valuesPersona[':escliente'] = $escliente;
                $valuesPersona[':esproveedor'] = $esproveedor;
                $valuesPersona[':idregistrador'] = $_SESSION['idpersona'];
                $valuesPersona[':fhregistro'] = date('Y-m-d H:i:s');
                $objCase->insertar('persona', $valuesPersona);

                $idpersona = $objCase->getLastIdInsert('persona', 'idpersona');

				$cnx->commit();
				echo "Persona Registrada de forma satisfactoria.";
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
				$idpersona = $_POST['idpersona'.$sufijo];
				$idopcion = $_POST['idopcion'];
				$permiso = validarPermisoPorPerfil($idopcion,"puedeeditar");

				if(!$permiso){
					throw new Exception("No tienes permiso para realizar esta operacion.", 123);
				}
				
				$existePersona = $objPersona->verificarPersona($_POST['nro_documento'.$sufijo],$idpersona);

				if($existePersona->rowCount()>=1){
					throw new Exception("Numero de Documento ya existe", 123);	
				}

				$rowPersona = $objCase->getRowTableFiltroSimple('persona', 'idpersona', $idpersona);

				$escliente=0;
				$estrabajador=0;
				$esproveedor=0;

				$tipo = explode(",", $_POST['tipo_persona']);
				if(in_array('C', $tipo)){
					$escliente=1;
				}

				if(in_array('T', $tipo)){
					$estrabajador=1;
				}

				if(in_array('P', $tipo)){
					$esproveedor=1;
				}

				if($_POST['razon_social'.$sufijo]==""){
					$_POST['razon_social'.$sufijo] = $_POST['nombres'.$sufijo].' '.$_POST['apellidos'.$sufijo];
					$_POST['razon_social'.$sufijo] = trim($_POST['razon_social'.$sufijo]);
				}
				
				$valuesPersona = $objPersona->getColumnTablaPersona();
				$valuesPersona[':idpersona'] = $idpersona;
				$valuesPersona[':apellidos'] = $_POST['apellidos'.$sufijo];
                $valuesPersona[':nombres'] = $_POST['nombres'.$sufijo];
                $valuesPersona[':razon_social'] = $_POST['razon_social'.$sufijo];
                $valuesPersona[':tipo_documento'] = $_POST['tipo_documento'.$sufijo];
                $valuesPersona[':nro_documento'] = $_POST['nro_documento'.$sufijo];
                $valuesPersona[':email'] = $_POST['email'.$sufijo];
                $valuesPersona[':facebook'] = $_POST['facebook'.$sufijo];
                $valuesPersona[':medio_comunicacion'] = $_POST['medio_comunicacion'.$sufijo];
                $valuesPersona[':direccion'] = $_POST['direccion'.$sufijo];
                $valuesPersona[':sexo'] = $_POST['sexo'.$sufijo];
                $valuesPersona[':ubigeo'] = $_POST['ubigeo'.$sufijo];
                $valuesPersona[':ubigeo_dir_dep'] = $_POST['ubigeo_dir_dep'.$sufijo];
                $valuesPersona[':ubigeo_dir_pro'] = $_POST['ubigeo_dir_pro'.$sufijo];
                $valuesPersona[':ubigeo_dir_dis'] = $_POST['ubigeo_dir_dis'.$sufijo];
                $valuesPersona[':telcelular'] = $_POST['telcelular'.$sufijo];
                if($_POST['fnacimiento'.$sufijo]!=""){
                	$valuesPersona[':fnacimiento'] = $_POST['fnacimiento'.$sufijo];
            	}
            	$valuesPersona[':observacion'] = $_POST['observacion'.$sufijo];
                $valuesPersona[':estrabajador'] = $estrabajador;
                $valuesPersona[':escliente'] = $escliente;
                $valuesPersona[':esproveedor'] = $esproveedor;
                $valuesPersona[':idregistrador'] = $rowPersona['idregistrador'];
                $valuesPersona[':fhregistro'] = $rowPersona['fhregistro'];
                $valuesPersona[':idpersonaeditar'] = $_SESSION['idpersona'];
                $valuesPersona[':fheditar'] = date('Y-m-d H:i:s');
                $valuesPersona[':estado'] = $rowPersona['estado'];
                $objCase->actualizar('persona', 'idpersona', $valuesPersona);

				$cnx->commit();
				echo "Persona Actualizada de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al Actualizar. ". $e->getMessage();
			}
			break;

		case "CAMBIAR_ESTADO_PERSONA":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();
					
				$idpersona=$_POST['idpersona'];
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
                    
				$objCase->cambiarEstado('persona', $estado, 'idpersona', $idpersona);
				$objCase->actualizarDatoSimple('persona', 'idpersonaeliminar', $_SESSION['idpersona'], 'idpersona', $idpersona);
				$objCase->actualizarDatoSimple('persona', 'fheliminar', date('Y-m-d H:i:s'), 'idpersona', $idpersona);

		 		$cnx->commit();
				echo "Usuario ".$arrayEstado[$estado]." de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar. ". $e->getMessage();
			}
			break;

		case "GET_NRO_DOCUMENTO":
			try {
				$data = $objCase->getRowTableFiltroSimple("persona","nro_documento", $_POST['nro_documento'], 'estado', 'N');
				if($data!=NULL){
    				$data['fnacimiento']=formatoCortoFecha($data['fnacimiento']);
				}else{
					$data=[];
				}

				echo json_encode($data);
			} catch (Exception $e) {
				echo [];
			}
			break;

		default: 
			echo "Debe especificar alguna accion"; 
			break;
	}
	
}


?>