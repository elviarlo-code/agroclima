<?php 
require_once("../logica/clsCase.php");
require_once("../logica/clsDispositivo.php");
require_once("../logica/clsCompartido.php");
controlador($_POST['accion']);

function controlador($accion){
    $objCase = new clsCase();
    $objDispositivo = new clsDispositivo();
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

				$values = $objDispositivo->getColumnTablaDispositivo();
				$values[':codigo'] = $_POST['codigo'.$sufijo];
                $values[':nombre'] = $_POST['nombre'.$sufijo];
                $values[':tipo'] = $_POST['tipo'.$sufijo];
                $values[':latitud'] = $_POST['latitud'.$sufijo];
                $values[':longitud'] = $_POST['longitud'.$sufijo];
                $values[':altitud'] = $_POST['altitud'.$sufijo];
                $values[':ubigeo'] = $_POST['ubigeo'.$sufijo];
                $values[':ubigeo_texto'] = $_POST['ubigeo_texto'.$sufijo];
                $values[':fhregistro'] = date('Y-m-d H:i:s');
				$values[':idpersonaregistro'] = $_SESSION['idpersona'];
                $objCase->insertar('dispositivo', $values);

                $iddispositivo = $objCase->getLastIdInsert('dispositivo', 'iddispositivo');
                $mensaje = "Dispositivo Registrado de forma satisfactoria.";

				$cnx->commit();
				$resultado = array("iddispositivo"=>$iddispositivo, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}catch(Exception $e){
				$cnx->rollBack();
				$mensaje = "*** Error al Registrar. ". $e->getMessage();
				$resultado = array("iddispositivo"=>0, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}
			break;

		case "MODIFICAR": 
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$sufijo = $_POST['sufijo'];
				$iddispositivo = $_POST['iddispositivo'.$sufijo];
				$idopcion = $_POST['idopcion'];
				$permiso = validarPermisoPorPerfil($idopcion,"puedeeditar");

				if(!$permiso){
					throw new Exception("No tienes permiso para realizar esta operacion.", 123);
				}

				$rowDispositivo = $objCase->getRowTableFiltroSimple('dispositivo', 'iddispositivo', $iddispositivo);
				
				$values = $objDispositivo->getColumnTablaDispositivo();
				$values[':iddispositivo'] = $iddispositivo;
				$values[':codigo'] = $_POST['codigo'.$sufijo];
                $values[':nombre'] = $_POST['nombre'.$sufijo];
                $values[':tipo'] = $_POST['tipo'.$sufijo];
                $values[':latitud'] = $_POST['latitud'.$sufijo];
                $values[':longitud'] = $_POST['longitud'.$sufijo];
                $values[':altitud'] = $_POST['altitud'.$sufijo];
                $values[':ubigeo'] = $_POST['ubigeo'.$sufijo];
                $values[':ubigeo_texto'] = $_POST['ubigeo_texto'.$sufijo];
                $values[':fhregistro'] = $rowDispositivo['fhregistro'];
				$values[':idpersonaregistro'] = $rowDispositivo['idpersonaregistro'];
				$values[':fheditar'] = date('Y-m-d H:i:s');
				$values[':idpersonaeditar'] = $_SESSION['idpersona'];
                $objCase->actualizar('dispositivo', 'iddispositivo', $values);

				$mensaje = "Dispositivo Actualizado de forma satisfactoria.";

				$cnx->commit();
				$resultado = array("iddispositivo"=>$iddispositivo, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}catch(Exception $e){
				$cnx->rollBack();
				$mensaje = "*** Error al Actualizar. ". $e->getMessage();
				$resultado = array("iddispositivo"=>0, "mensaje"=>$mensaje);
				echo json_encode($resultado);
			}
			break;

		case "CAMBIAR_ESTADO_DISPOSITIVO":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();
					
				$iddispositivo=$_POST['iddispositivo'];
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
                    
				$objCase->cambiarEstado('dispositivo', $estado, 'iddispositivo', $iddispositivo);

		 		$cnx->commit();
				echo "Dispositivo ".$arrayEstado[$estado]." de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al actualizar. ". $e->getMessage();
			}
			break;

		case "GUARDAR_IMAGENES_SERVIDOR": 
		    try {
		        global $cnx;
		        $cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		        $cnx->beginTransaction();

		        if (!isset($_POST['imagenes']) || !is_array($_POST['imagenes'])) {
		            throw new Exception("No se recibieron imágenes.", 123);
		        }

		        $imagenes = $_POST['imagenes'];
		        $id = $_POST['id']; // Usar ID si está disponible, opcional
		        $sufijo = $_POST['sufijo']; // Usar sufijo si está disponible, opcional

		        // Directorio base donde se guardarán las imágenes
		        $urlbase = "../files/imagenes";
		        if (!file_exists($urlbase) && !is_dir($urlbase)) {
		            mkdir($urlbase, 0777, true);
		        }

		        // Procesar cada imagen recibida
		        foreach ($imagenes as $nombreGrafico => $pngBase64) {
		            // Validar y decodificar la imagen
		            if (empty($pngBase64)) {
		                throw new Exception("La imagen del gráfico '{$nombreGrafico}' está vacía.");
		            }

		            // Eliminar el encabezado de tipo MIME
		            $base64_str = preg_replace('#^data:image/\w+;base64,#i', '', $pngBase64);
		            $grafico = base64_decode($base64_str);

		            if ($grafico === false) {
		                throw new Exception("Error al decodificar la imagen del gráfico '{$nombreGrafico}'.");
		            }

		            // Crear un nombre único para cada archivo
		            $nombreArchivo = 'grafico_' . $nombreGrafico . '_' . $sufijo . $id . '.png';
		            $rutaArchivo = $urlbase . '/' . $nombreArchivo;

		            // Si ya existe un archivo con el mismo nombre, eliminarlo
		            if (file_exists($rutaArchivo)) {
		                @unlink($rutaArchivo);
		            }

		            // Guardar la imagen en el servidor
		            file_put_contents($rutaArchivo, $grafico);
		        }

		        $cnx->commit();
		        echo "Todas las imágenes fueron subidas con éxito.";
		    } catch (Exception $e) {
		        $cnx->rollBack();
		        echo "*** Error. " . $e->getMessage();
		    }
		    break;

		case "IMPORTAR_DATA_DAVIS": 
		    try {
		        global $cnx;
		        $cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		        $cnx->beginTransaction();

		        $iddispositivo = $_POST['iddispositivo'];

		        if (!isset($_FILES['file'])) {
		            throw new Exception("No se selecciono ningun archivo.", 123);
		        }

		        $file = $_FILES['file'];
		        $filePath = $file['tmp_name'];

		        $handle = fopen($filePath, "r");
		        if (!$handle) {
		            throw new Exception("No se pudo abrir el archivo.", 123);
		        }

		        $climaData = []; // Array donde almacenaremos los datos procesados
		        $lineNumber = 0;

		        // Omitir las dos primeras líneas de encabezado
		        fgets($handle); // Primera línea
		        fgets($handle); // Segunda línea

		        while (($line = fgets($handle)) !== false) {
		            $lineNumber++;

		            // Separar los datos por espacios/tabulaciones
		            $datos = preg_split('/\s+/', trim($line));

		            if (count($datos) < 38) {
		                continue; // Si no tiene los datos esperados, saltarlo
		            }

		            // list($fecha, $hora, $temp_salida, $temp_maxima, $temp_minima, $humedad) = $datos;

		            list($fecha, $hora, $temp_out, $temp_hi, $temp_low, $hum_out, $dew_pt, $wind_speed, $wind_dir, $wind_run, $hi_speed, $hi_dir, $wind_chill, $heat_index, $thw_index, $thsw_index, $bar, $rain, $rain_rate, $solar_rad, $solar_energy, $hi_solar_rad, $uv_index, $uv_dose, $hi_uv, $heat_dd, $cool_dd, $in_temp, $in_hum, $in_dew, $in_heat, $in_emc, $in_air_density, $et, $wind_samp, $wind_tx, $iss_recept, $arc_int) = $datos;


		            // Convertir la fecha de "DD/MM/YYYY" a "YYYY-MM-DD"
		            $fechaSQL = DateTime::createFromFormat('d/m/y', $fecha);
		            if (!$fechaSQL) {
		                continue; // Si la conversión falla, omitir la fila
		            }
		            $fechaSQL = $fechaSQL->format('Y-m-d');

		            // Agregar los datos en el array
		            $climaData[] = [
		                "fecha"        		=> $fechaSQL." ".$hora,
		                "temp_out"        	=> $temp_out,
						"temp_hi"        	=> $temp_hi,
						"temp_low"        	=> $temp_low,
						"hum_out"        	=> $hum_out,
						"dew_pt"        	=> $dew_pt,
						"wind_speed"       	=> $wind_speed,
						"wind_dir"        	=> $wind_dir,
						"wind_run"        	=> $wind_run,
						"hi_speed"        	=> $hi_speed,
						"hi_dir"        	=> $hi_dir,
						"wind_chill"        => $wind_chill,
						"heat_index"        => $heat_index,
						"thw_index"        	=> $thw_index,
						"thsw_index"        => $thsw_index,
						"bar"        		=> $bar,
						"rain"        		=> $rain,
						"rain_rate"        	=> $rain_rate,
						"solar_rad"        	=> $solar_rad,
						"solar_energy"      => $solar_energy,
						"hi_solar_rad"      => $hi_solar_rad,
						"uv_index"        	=> $uv_index,
						"uv_dose"        	=> $uv_dose,
						"hi_uv"        		=> $hi_uv,
						"heat_dd"        	=> $heat_dd,
						"cool_dd"        	=> $cool_dd,
						"in_temp"        	=> $in_temp,
						"in_hum"        	=> $in_hum,
						"in_dew"        	=> $in_dew,
						"in_heat"        	=> $in_heat,
						"in_emc"        	=> $in_emc,
						"in_air_density"    => $in_air_density,
						"et"        		=> $et,
						"wind_samp"			=> $wind_samp,
						"wind_tx"			=> $wind_tx,
						"iss_recept"		=> $iss_recept,
						"arc_int"			=> $arc_int
		            ];
		        }

		        fclose($handle);
		        $objDispositivo->insertarClimaDavis($climaData,$iddispositivo);

		        $cnx->commit();
		        echo "Datos Importados de forma correcta.";
		    } catch (Exception $e) {
		        $cnx->rollBack();
		        echo "*** Error. " . $e->getMessage();
		    }
		    break;


		default: 
			echo "Debe especificar alguna accion"; 
			break;
	}
	
}


?>