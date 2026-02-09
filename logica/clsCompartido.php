<?php
include_once("clsCase.php");

function getConfig($idconfig, $idinstitucion=0, $idsucursal=0){
  
    if(isset($_SESSION['config'])){
        $valor='';
        $config = $_SESSION['config'];
        if(isset($config[$idconfig][0][0])){
            $valor = $config[$idconfig][0][0];
        }else{
            if(isset($config[$idconfig])){
                foreach ($config[$idconfig] as $k => $v) {
                    foreach ($v as $p => $q) {
                        $valor = $q;
                        break;
                    }
                    break;
                }
            }
        }

        if($idinstitucion>0){
            if(isset($config[$idconfig][$idinstitucion])){
                foreach($config[$idconfig][$idinstitucion] as $k=>$v){
                    $valor = $v;
                    break;
                }
            }
        }

        if($idsucursal>0){
            if(isset($config[$idconfig][$idinstitucion][$idsucursal])){
                $valor = $config[$idconfig][$idinstitucion][$idsucursal];
            }
        }

        return $valor;
    }else{
        $sql="SELECT * FROM mgconfig WHERE idconfig=:idconfig ";    
          $parametros=array(':idconfig'=>$idconfig);
          
          if($idinstitucion>0){
              $sql.=" AND (idinstitucion=0 OR idinstitucion=:idinstitucion) ";
              $parametros[':idinstitucion']=$idinstitucion;
          }
          
          if($idsucursal>0){
              $sql.=" AND (idsucursal=0 OR idsucursal=:idsucursal) ";
              $parametros[':idsucursal']=$idsucursal;
          }
          $sql.=" ORDER BY idinstitucion ASC, idsucursal ASC";
        global $cnx;
        $pre = $cnx -> prepare($sql);
        $pre->execute($parametros);
        $pre=$pre->fetch(PDO::FETCH_NAMED);
        $pre=$pre['valor'];
        return $pre;
    }
}

function setConfig($idconfig, $valor, $idinstitucion=0, $idsucursal=0){
	$sql="UPDATE mgconfig SET valor=:valor WHERE idconfig=:idconfig ";    
    $parametros=array(':idconfig'=>$idconfig, ':valor'=>$valor);
    
    if($idinstitucion>0){
        $sql.=" AND (idinstitucion=0 OR idinstitucion=:idinstitucion) ";
        $parametros[':idinstitucion']=$idinstitucion;
    }
    
    if($idsucursal>0){
        $sql.=" AND (idsucursal=0 OR idsucursal=:idsucursal) ";
        $parametros[':idsucursal']=$idsucursal;
    }

    //@323 - Inicio
    if(isset($_SESSION['config'])){
        $config = $_SESSION['config'];
        if(isset($_SESSION['config'][$idconfig][0][0])){
            $_SESSION['config'][$idconfig][0][0] = $valor;
        }else{
            if(isset($_SESSION['config'][$idconfig])){
                foreach ($_SESSION['config'][$idconfig] as $k => $v) {
                    foreach ($v as $p => $q) {
                        $_SESSION['config'][$k][$p] = $valor;
                        break;
                    }
                    break;
                }
            }
        }

        if($idinstitucion>0){
            if(isset($_SESSION['config'][$idconfig][$idinstitucion])){
                foreach($_SESSION['config'][$idconfig][$idinstitucion] as $k=>$v){
                    $_SESSION['config'][$idconfig][$idinstitucion][$k] = $valor;
                    break;
                }
            }
        }

        if($idsucursal>0){
            if(isset($_SESSION['config'][$idconfig][$idinstitucion][$idsucursal])){
                $_SESSION['config'][$idconfig][$idinstitucion][$idsucursal] = $valor;
            }
        }
    }    
    //@323 - Fin

    global $cnx;
	$pre = $cnx -> prepare($sql);
	$pre->execute($parametros);
	return $pre;
}

function getTipoCambio($moneda, $fecha, $pcompra=false, $fechaExacta=false){
	$comparacion="<=";
	$campo="pventa";
	if($fechaExacta){
		$comparacion="=";
	}
	if($pcompra){
		$campo="pcompra";
	}
	$sql="SELECT $campo FROM cgtipocambio WHERE fecha ".$comparacion." :fecha AND codmoneda=:moneda AND estado='N' ORDER BY fecha DESC LIMIT 1";
    global $cnx;
    $parametros = array(':moneda'=>$moneda, ':fecha'=>$fecha);
	$pre = $cnx -> prepare($sql);
	$pre->execute($parametros);
	if($pre->rowCount()>0){
		$pre = $pre->fetch(PDO::FETCH_NUM);
		$pre = $pre[0];
	}else{
		$pre=0.00;
	}
	return $pre;
}


function getImpuestoIgv($idfacturar=0, $fecha=''){
    $sql="SELECT valor FROM mgimpuesto WHERE tipo='IGV' ";

    $parametros = array();

    if($idfacturar>0){
        $sql.=" AND idfacturar=:idfacturar ";
        $parametros[':idfacturar'] = $idfacturar;
    }

    if($fecha!=""){
        $sql.=" AND fechaini<=:fecha AND IF(IFNULL(fechafin,'')<>'',fechafin>=:fecha,1=1)";
        $parametros[':fecha'] = $fecha;
    }

    $sql.=" ORDER BY fechaini DESC LIMIT 1";

    global $cnx;
    $pre=$cnx->prepare($sql);
    $pre->execute($parametros);
    if($pre->rowCount()>0){
        $pre=$pre->fetch(PDO::FETCH_NUM);
        $pre=$pre[0];
    }else{
        $pre=0;
    }
    return $pre;
}

function rand_code($chars, $long){
	$code = "";
	for ($x=0; $x < $long; $x++){
			$rand = mt_rand(0, strlen($chars)-1);
			$code .= substr($chars, $rand, 1);
	}
	return $code;
}

function getCodigoPreInscripcion(){
	$caracteres = "23456789ABCDEFGHJKLMNPRSTUVWXYZ";
	$longitud = 6;
	$serie = rand_code($caracteres, $longitud);
	return $serie;
}

function formatoCortoFecha($fecha){
    if($fecha!='') {
        $fechita = explode("-", $fecha);
        $anio = $fechita[0];
        $mes = $fechita[1];
        $otros = explode(" ", $fechita[2]);
        $dia = $otros[0];
        $fecha = $dia . '/' . $mes . '/' . $anio;
    }
	return $fecha;
}

function formatoBDFecha($fecha){
    if($fecha!=''){
        $fechita = explode("/", $fecha);
        $anio = $fechita[2];
        $mes = $fechita[1];
        $dia = $fechita[0];
        $fecha = $anio . '-' . $mes . '-' . $dia;
    }
	return $fecha;
}
//@lucila cruz || 23-02-2021
function formatoCortoFechaHora($fecha)
{
  if ($fecha!='') {
      $fecha=date_create($fecha);
      $fecha=date_format($fecha, 'd/m/Y H:i:s');
  }
  return $fecha;
}
//@lucila cruz ||08-06-2021
function dianoconsiderar($stringdias,$fecha)
{
  //fecha='Y-m-d'
  //string dias separado por comas
  $arraydias=explode(',',$stringdias);
  for ($x=0; $x < count($arraydias); $x++) { 
    $dia_semana=date('w',strtotime($fecha));
    if ($arraydias[$x]== $dia_semana) {
      $fecha=agregarDiasFecha($fecha,1);
    }
  }
  return $fecha;
}

function generaPass(){
    $cadena = "abcdefghjkmnpqrstuvwxyz23456789";
    $longitudCadena=strlen($cadena);
    $pass = "";
    $longitudPass=5;
    for($i=1 ; $i<=$longitudPass ; $i++){
        $pos=rand(0,$longitudCadena-1);
        $pass .= substr($cadena,$pos,1);
    }
    return $pass;
}

function getScalar($result){
    $result = $result->fetch(PDO::FETCH_NUM);
    $result = $result[0];
    return $result;
}

function crearRangoFechas($strDateFrom,$strDateTo)
{
    $aryRange=array();
    $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),substr($strDateFrom,8,2),substr($strDateFrom,0,4));
    $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),substr($strDateTo,8,2),substr($strDateTo,0,4));

    if ($iDateTo>=$iDateFrom){
        array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
        while ($iDateFrom<$iDateTo){
            $iDateFrom+=86400; // add 24 hours
            array_push($aryRange,date('Y-m-d',$iDateFrom));
        }
    }
    return $aryRange;
}

function crearRangoFechasSumaRestaDias($strDateFrom,$sumaresta)
{
    if(intval($sumaresta)>0){
        $strDateTo = strtotime ($sumaresta.' day',strtotime ($strDateFrom ));
        $strDateTo = date('Y-m-d',$strDateTo);
    }else{
        $strDateTo = $strDateFrom;
        $strDateFrom = strtotime ($sumaresta.' day',strtotime ($strDateFrom ));
        $strDateFrom = date('Y-m-d', $strDateFrom);
    }
    $aryRange=array();
    $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),substr($strDateFrom,8,2),substr($strDateFrom,0,4));
    $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),substr($strDateTo,8,2),substr($strDateTo,0,4));

    if ($iDateTo>=$iDateFrom){
        array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
        while ($iDateFrom<$iDateTo){
            $iDateFrom+=86400; // add 24 hours
            array_push($aryRange,date('Y-m-d',$iDateFrom));
        }
    }
    return $aryRange;
}

function ConsultaToArray($result,$key,$value){
	$result=$result->fetchAll();
	$NewResult= array();
	foreach($result as $k=>$v){
		$NewResult[$v[$key]]=$v[$value];
	}
	return $NewResult;
}

function ConsultaToArrayAll($result,$key){
	$result=$result->fetchAll();
	$NewResult= array();
	foreach($result as $k=>$v){
		$NewResult[$v[$key]]=$v;
	}
	return $NewResult;
}

function GetTiempoNotificacion($horaminuto, $haceminutos, $hacehoras, $hacedias, $dian, $mescorto){
	$mensaje="";
	
	if($hacedias==0 && $hacehoras==0 && $haceminutos<=3){
		$mensaje='Hace un instante';
	}elseif($hacedias==0 && $hacehoras==0 && $haceminutos<60){
		$mensaje='Hace '.$haceminutos.'min';
	}elseif($hacedias==0 && $hacehoras>0 && $hacehoras<5){
		$mensaje='Hace '.$hacehoras.'h';
		if($haceminutos>0){
			$mensaje.=' '.$haceminutos.'min';
		}
	}elseif($hacedias==0 && $hacehoras>=5 && date('d')==$dian){
		$mensaje='Hoy '.$horaminuto;
	}elseif($hacedias==0 && $hacehoras>=5 && date('d')!=$dian){
		$mensaje='Ayer '.$horaminuto;
	}elseif($hacedias<5){
		$mensaje='Hace '.$hacedias.'d';
		if($hacehoras>0){
			$mensaje.=' '.$hacehoras.'h';
		}
	}else{
		$mensaje=$dian.' '.$mescorto.' '.$horaminuto;
	}
	return $mensaje;
}
//INICIO NUMERO A LETRAS
function CantidadEnLetra($tyCantidad){  
		$enLetras = new EnLetras;
		return $enLetras->ValorEnLetras($tyCantidad,"SOLES"); 
}

class EnLetras 
{ 
      var $Void = ""; 
      var $SP = " "; 
      var $Dot = "."; 
      var $Zero = "0"; 
      var $Neg = "Menos"; 
       
    function ValorEnLetras($x, $Moneda )  
    { 
        $s=""; 
        $Ent=""; 
        $Frc=""; 
        $Signo=""; 
             
        if(floatVal($x) < 0) 
         $Signo = $this->Neg . " "; 
        else 
         $Signo = ""; 
         
        if(intval(number_format($x,2,'.','') )!=$x) //<- averiguar si tiene decimales 
          $s = number_format($x,2,'.',''); 
        else 
          $s = number_format($x,2,'.',''); 
            
        $Pto = strpos($s, $this->Dot); 
             
        if ($Pto === false) 
        { 
          $Ent = $s; 
          $Frc = $this->Void; 
        } 
        else 
        { 
          $Ent = substr($s, 0, $Pto ); 
          $Frc =  substr($s, $Pto+1); 
        } 

        if($Ent == $this->Zero || $Ent == $this->Void) 
           $s = "CERO "; 
        elseif( strlen($Ent) > 7) 
        { 
           $s = $this->SubValLetra(intval( substr($Ent, 0,  strlen($Ent) - 6))) .  
                 "MILLONES " . $this->SubValLetra(intval(substr($Ent,-6, 6))); 
        } 
        else 
        { 
          $s = $this->SubValLetra(intval($Ent)); 
        } 

        if (substr($s,-9, 9) == "MILLONES " || substr($s,-7, 7) == "MILLÓN ") 
           $s = $s . "DE "; 

        $s = $s ; 

        if($Frc != $this->Void) 
        { 
           $s = $s . " CON " . $Frc. "/100"; 
           //$s = $s . " " . $Frc . "/100"; 
        } 
        $letrass=$Signo . $s ." ".$Moneda; 
        return ($Signo . $s ." ".$Moneda); 
        
    } 


    function SubValLetra($numero)  
    { 
        $Ptr=""; 
        $n=0; 
        $i=0; 
        $x =""; 
        $Rtn =""; 
        $Tem =""; 

        $x = trim("$numero"); 
        $n = strlen($x); 

        $Tem = $this->Void; 
        $i = $n; 
         
        while( $i > 0) 
        { 
           $Tem = $this->Parte(intval(substr($x, $n - $i, 1).  
                               str_repeat($this->Zero, $i - 1 ))); 
           If( $Tem != "CERO" ) 
              $Rtn .= $Tem . $this->SP; 
           $i = $i - 1; 
        } 

         
        //--------------------- GoSub FiltroMil ------------------------------ 
        $Rtn=str_replace(" MIL MIL", " UN MIL", $Rtn ); 
        while(1) 
        { 
           $Ptr = strpos($Rtn, "MIL ");        
           If(!($Ptr===false)) 
           { 
              If(! (strpos($Rtn, "MIL ",$Ptr + 1) === false )) 
                $this->ReplaceStringFrom($Rtn, "MIL ", "", $Ptr); 
              Else 
               break; 
           } 
           else break; 
        } 

        //--------------------- GoSub FiltroCiento ------------------------------ 
        $Ptr = -1; 
        do{ 
           $Ptr = strpos($Rtn, "CIEN ", $Ptr+1); 
           if(!($Ptr===false)) 
           { 
              $Tem = substr($Rtn, $Ptr + 5 ,1); 
              if( $Tem == "M" || $Tem == $this->Void) 
                 ; 
              else           
                 $this->ReplaceStringFrom($Rtn, "CIEN", "CIENTO", $Ptr); 
           } 
        }while(!($Ptr === false)); 

        //--------------------- FiltroEspeciales ------------------------------ 
        $Rtn=str_replace("DIEZ UN", "ONCE", $Rtn ); 
        $Rtn=str_replace("DIEZ UNO", "ONCE", $Rtn ); 
        $Rtn=str_replace("DIEZ DOS", "DOCE", $Rtn ); 
        $Rtn=str_replace("DIEZ TRES", "TRECE", $Rtn ); 
        $Rtn=str_replace("DIEZ CUATRO", "CATORCE", $Rtn ); 
        $Rtn=str_replace("DIEZ CINCO", "QINCE", $Rtn ); 
        $Rtn=str_replace("DIEZ SEIS", "DIECISEIS", $Rtn ); 
        $Rtn=str_replace("DIEZ SIETE", "DIECISIETE", $Rtn ); 
        $Rtn=str_replace("DIEZ OCHO", "DIECIOCHO", $Rtn ); 
        $Rtn=str_replace("DIEZ NUEVE", "DIECINUEVE", $Rtn ); 
        $Rtn=str_replace("VEINTE UN", "VEINTIUN", $Rtn ); 
        $Rtn=str_replace("VEINTE DOS", "VEINTIDOS", $Rtn ); 
        $Rtn=str_replace("VEINTE TRES", "VEINTITRES", $Rtn ); 
        $Rtn=str_replace("VEINTE CUATRO", "VEINTICUATRO", $Rtn ); 
        $Rtn=str_replace("VEINTE CINCO", "VEINTICINCO", $Rtn ); 
        $Rtn=str_replace("VEINTE SEIS", "VEINTISEIS", $Rtn ); 
        $Rtn=str_replace("VEINTE SIETE", "VEINTISIETE", $Rtn ); 
        $Rtn=str_replace("VEINTE OCHO", "VEINTIOCHO", $Rtn ); 
        $Rtn=str_replace("VEINTE NUEVE", "VEINTINUEVE", $Rtn ); 

        //--------------------- FiltroUn ------------------------------ 
        If(substr($Rtn,0,1) == "M") $Rtn = " " . $Rtn; 
        //--------------------- Adicionar Y ------------------------------ 
        for($i=65; $i<=88; $i++) 
        { 
          If($i != 77) 
             $Rtn=str_replace("A " . Chr($i), "* Y " . Chr($i), $Rtn); 
        } 
        $Rtn=str_replace("*", "A" , $Rtn); 
        return($Rtn); 
    } 


    function ReplaceStringFrom(&$x, $OldWrd, $NewWrd, $Ptr) 
    { 
      $x = substr($x, 0, $Ptr)  . $NewWrd . substr($x, strlen($OldWrd) + $Ptr); 
    } 


    function Parte($x) 
    { 
        $Rtn=''; 
        $t=''; 
        $i=0; 
        Do 
        { 
          switch($x) 
          { 
             Case 0:  $t = "CERO";break; 
             Case 1:  $t = "UNO";break; 
             Case 2:  $t = "DOS";break; 
             Case 3:  $t = "TRES";break; 
             Case 4:  $t = "CUATRO";break; 
             Case 5:  $t = "CINCO";break; 
             Case 6:  $t = "SEIS";break; 
             Case 7:  $t = "SIETE";break; 
             Case 8:  $t = "OCHO";break; 
             Case 9:  $t = "NUEVE";break; 
             Case 10: $t = "DIEZ";break; 
             Case 20: $t = "VEINTE";break; 
             Case 30: $t = "TREINTA";break; 
             Case 40: $t = "CUARENTA";break; 
             Case 50: $t = "CINCUENTA";break; 
             Case 60: $t = "SESENTA";break; 
             Case 70: $t = "SETENTA";break; 
             Case 80: $t = "OCHENTA";break; 
             Case 90: $t = "NOVENTA";break; 
             Case 100: $t = "CIEN";break; 
             Case 200: $t = "DOSCIENTOS";break; 
             Case 300: $t = "TRESCIENTOS";break; 
             Case 400: $t = "CUATROCIENTOS";break; 
             Case 500: $t = "QUINIENTOS";break; 
             Case 600: $t = "SEISCIENTOS";break; 
             Case 700: $t = "SETECIENTOS";break; 
             Case 800: $t = "OCHOCIENTOS";break; 
             Case 900: $t = "NOVECIENTOS";break; 
             Case 1000: $t = "MIL";break; 
             Case 1000000: $t = "MILLÓN";break; 
          } 

          If($t == $this->Void) 
          { 
            $i = $i + 1; 
            $x = $x / 1000; 
            If($x== 0) $i = 0; 
          } 
          else 
             break; 
                
        }while($i != 0); 
        
        $Rtn = $t; 
        Switch($i) 
        { 
           Case 0: $t = $this->Void;break; 
           Case 1: $t = " MIL";break; 
           Case 2: $t = " MILLONES";break; 
           Case 3: $t = " BILLONES";break; 
        } 
        return($Rtn . $t); 
    } 

} 

//FIN NUMERO A LETRAS

function formatNumber(&$var, $decimalfijo=false, $decimals=2)
{
    if($decimalfijo){
        $var = number_format($var,$decimals,'.','');
    }else{
        if($decimals>=0){
            $var = (float) number_format($var,$decimals,'.','');
        }else{
            $var = (float) $var;
        }
    }
    return $var;
}

//VALIDAR SI ESTOY DENTRO DEL HORARIO
function estasDentroDelHorario($horaActual,$inicioHorario, $finHorario){
  //$currentTime = (new DateTime($horaActual))->modify('+1 day');
  $currentTime = (new DateTime($horaActual));
  $startTime = new DateTime($inicioHorario);
  $endTime = (new DateTime($finHorario));

  if ($currentTime >= $startTime && $currentTime <= $endTime) {
      return true;
  }
  return false;
}

function diasEntreFechas($fecha1, $fecha2){
  $fechaIni = new DateTime($fecha1);
  $fechaFin = new DateTime($fecha2);
  $diff = $fechaIni->diff($fechaFin); 
 
  return $diff->days;
}
function minutosEntreFechas($fecha1, $fecha2){
  $minutos=0;
  $fechaIni = new DateTime($fecha1);
  $fechaFin = new DateTime($fecha2);
  $diff = $fechaIni->diff($fechaFin); 

  $minutos = $diff->days * 24 * 60;
  $minutos += $diff->h * 60;
  $minutos += $diff->i;
 
  return $minutos;
}

function restarDiasFecha($fecha,$dias)
{
  $fechaLimite= date("Y-m-d",strtotime($fecha.'- '.$dias.' days')); 
  return $fechaLimite;
}

function agregarDiasFecha($fecha,$dias)
{
  $fechaLimite= date("Y-m-d",strtotime($fecha.'+ '.$dias.' days')); 
  return $fechaLimite;
}

function getFactorUnidadProducto($articulo, $unidad){  
  $factorunidad = 1;
  for($x=1;$x<=4; $x++){
    if($articulo['unidad_alterno'.$x]==$unidad){
      $factorunidad = $articulo['factor_alterno'.$x];
      break;
    }
  } 
  return $factorunidad;
}

function anything_to_utf8($var,$deep=TRUE){
    if(is_array($var)){
        foreach($var as $key => $value){
            if($deep){
                $var[$key] = anything_to_utf8($value,$deep);
            }elseif(!is_array($value) && !is_object($value) && !mb_detect_encoding($value,'utf-8',true)){
                 $var[$key] = utf8_encode($var);
            }
        }
        return $var;
    }elseif(is_object($var)){
        foreach($var as $key => $value){
            if($deep){
                $var->$key = anything_to_utf8($value,$deep);
            }elseif(!is_array($value) && !is_object($value) && !mb_detect_encoding($value,'utf-8',true)){
                 $var->$key = utf8_encode($var);
            }
        }
        return $var;
    }else{
        return (!mb_detect_encoding($var,'utf-8',true))?utf8_encode($var):$var;
    }
}
function calcularancho($total,$porcentaje){
  $medida=$porcentaje*($total/100);
  return  $medida;

}
function getsaludo($hora=0)
{
  // Formato 24 horas (de 1 a 24) date('G')
  if (($hora >= 0) && ($hora < 6)) { 
    $mensaje = "Buena madrugada"; 
  }else if (($hora >= 6) && ($hora < 12)) { 
    $mensaje = "Buenos días"; 
  } else if (($hora >= 12) && ($hora < 18)) { 
    $mensaje = "Buenas tardes"; 
  } else { 
    $mensaje = "Buenas noches"; 
  } 
  return $mensaje;
}
function scanear_string($string)
{
  $string = trim($string);
 
    $string = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $string
    );
 
    $string = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $string
    );
 
    $string = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $string
    );
 
    $string = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $string
    );
 
    $string = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $string
    );
 
    $string = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C',),
        $string
    );
 
    //Esta parte se encarga de eliminar cualquier caracter extraño
    $string = str_replace(
        array('\'', '¨', 'º', '-', '~',
             '#', '@', '|', '!', '"',
             '·', '$', '%', '&', '/',
             '(', ')', '?', "'", '¡',
             '¿', '[', '^', '<code>', ']',
             '+', '}', '{', '¨', '´',
             '>', '<', ';', ',', ':',
             '.', ' '),
        '',
        $string
    );
 
 
    return $string;
}

function calcular_edad($fecha)
{
    //fecha= 15-08-1998 ejemplo 
  $fecha_nacimiento = new DateTime($fecha);
  $hoy = new DateTime();
  $edad = $hoy->diff($fecha_nacimiento)->y;
  return $edad;
}

function quitarSaltoLinea($texto){
    $texto = isset($texto)? $texto:'';
    $texto = preg_replace("/[\r\n|\n|\r]+/", " ", $texto);
    return $texto;
}

function asignar_color_fecha($fecha,$arrayColor)
{
    //07-07-2022
    //fecha vencimiento
    $fechaVencimiento = new DateTime($fecha);
    //fecha para considerar pronto a vencer
    foreach ($arrayColor as $m => $n) {
        $fechaColor = new DateTime($n['fecha']);
        if ($n['condicion'] == 'menorigual' ) {
            if ($fechaVencimiento <= $fechaColor ) {
                return $n['color'];
            }
        }else if($n['condicion'] == 'mayor'){
            if ($fechaVencimiento > $fechaColor ) {
                return $n['color'];
            }
        }
    }
    
}
function crearRangoHoras($hinicio,$hfin,$rango=60)
{
    $aryRange=array();
    //explode hora inicio
    $hinicio=explode(' ',$hinicio);
    $fechaini = $hinicio[0];
    $fechaini = explode('-',$fechaini);
    $horaini=$hinicio[1];
    $horaini= explode(':',$horaini);

    //explode hora fin
    $hfin=explode(' ',$hfin);
    $fechafin=$hfin[0];
    $fechafin = explode('-',$fechafin);
    $horafin=$hfin[1];
    $horafin = explode(':',$horafin);


    $finicio=mktime(intval($horaini[0]),intval($horaini[1]),0,$fechaini[1],$fechaini[2],$fechaini[0]);
    $ffin=mktime(intval($horafin[0]),intval($horafin[1]),0,$fechafin[1],$fechafin[2],$fechafin[0]);
    //$aryRange = array($finicio,$ffin);
    array_push($aryRange,date('H:i',$finicio));
    while ($finicio<$ffin){
        $finicio += ($rango * 60); // add 24 hours
        array_push($aryRange,date('H:i',$finicio));
    }
    
    return $aryRange;
}

function fechatexto($fecha)
{
   //$fecha = "2023-02-15"; // fecha en formato Y-m-d
    $timestamp = strtotime($fecha); // convierte la fecha a un timestamp

    // mapea el número del mes a su nombre en español
    $meses = array(
        '01' => "ENERO",
        '02' => "FEBRERO",
        '03' => "MARZO",
        '04' => "ABRIL",
        '05' => "MAYO",
        '06' => "JUNIO",
        '07' => "JULIO",
        '08' => "AGOSTO",
        '09' => "SEPTIEMBRE",
        '10' => "OCTUBRE",
        '11' => "NOVIEMBRE",
        '12' => "DICIEMBRE"
    );

    $mes = $meses[date("m", $timestamp)]; // obtiene el nombre del mes en español
    $fecha_texto = date("d", $timestamp) . " DE " . $mes . " DEL " . date("Y", $timestamp); // formatea la fecha como texto en español

    return $fecha_texto; // imprime la fecha en texto (ej: "15 febrero 2023")
}

function reemplazarComillas($cadena) {
    $comillas = array('"', "'");
    $reemplazos = array('&quot;', '&apos;');

    return str_replace($comillas, $reemplazos, $cadena);
}

//@ts341 - Inicio
function toMilimetros($px){
    $mm = (25.4*$px)/96;
    $mmint = round($mm);
    return $mmint;
}
//@ts341 - Fin

function verificarPermiso($opcionMenu){
    $permiso = 1;
    if($_SESSION['idperfil']>1){
        if($opcionMenu!=""){
            $puedeimprimir = explode(",",$opcionMenu);
            if(!in_array($_SESSION['idperfil'],$puedeimprimir)){
                $permiso=0;
            }
        }
    }
  return $permiso;
}

function validarPermisoPorPerfil($idopcion, $permiso){
    $objCase = new clsCase();
    $permiso = true;
    if($idopcion>0){

        $opcionMenu = $objCase->getRowTableById("opcion",$idopcion);
        if($_SESSION['idperfil']>1){
            if(isset($opcionMenu[$permiso]) && $opcionMenu[$permiso]!=""){
                $permitir = explode(",",$opcionMenu[$permiso]);
                if(!in_array($_SESSION['idperfil'],$permitir)){
                    $permiso = false;
                }
            }
        }
    }

    return $permiso;
}

function highchartsColor(){
    $color = array("#FFC0CB", "#AEC6CF", "#77DD77", "#FDFD96", "#CBAACB", "#FFDAB9", "#B2F2BB", "#D4A5A5", "#B0E0E6", "#FFB347", "#FFB6C1", "#96DED1", "#DDA0DD", "#D3D3D3", "#A0D6B4", "#F4C2C2", "#FFFACD", "#98FF98", "#B3E5FC", "#FFE5B4", "#E6E6FA", "#E3F9A6", "#C1E1C1", "#AEC6CF", "#FFFFF0", "#D2B48C", "#FFD1C1", "#FFDAB9", "#AFEEEE", "#B5B35C", "#BEF574", "#AFEEEE", "#FFF5BA", "#7FFFD4", "#A8E6CF");

    return $color;
}

function generarArrayAnios($anioInicial) {
    $arrayAnio=array();
    $anioActual = (int)date('Y');
    for ($i=$anioActual; $i >= $anioInicial; $i--) { 
        $arrayAnio[]=$i;
    }
    return $arrayAnio;
}

function boolval($val) {
        return (bool) $val;
    }

?>