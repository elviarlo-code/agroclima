<?php 
require_once("cado.php");
class clsCase{

	function insertar($table, $values, $parametros=NULL){
		foreach ($values as $k => $v) {
			//@Antonio Fuentes Alcantara
			if($v==null){
				$values[$k]=$v;
			}else{
				$values[$k]=mb_strtoupper($v);
			}
		}
		if(!isset($parametros)){
			$parametros = array_keys($values);
		}
		$sql="INSERT INTO $table VALUES (".implode(",", $parametros).")";
		global $cnx;
		$pre = $cnx->prepare($sql);
		return $pre->execute($values);
	}

	function insertarWithoutUpper($table, $values, $parametros=NULL){
		if(!isset($parametros)){
			$parametros = array_keys($values);
		}
		$sql="INSERT INTO $table VALUES (".implode(",", $parametros).")";
		global $cnx;
		$pre = $cnx->prepare($sql);
		return $pre->execute($values);
	}

	function getLastIdInsert($table, $pk){
		$sql="SELECT $pk FROM $table ORDER BY $pk DESC LIMIT 1";
		global $cnx;
		$res = $cnx->query($sql);
		$res = $res->fetch(PDO::FETCH_NUM);
		return $res[0];
	}

	function getLastRegiter($table, $pk, $cantidad){
		$sql="SELECT * FROM $table ORDER BY $pk DESC LIMIT $cantidad";
		global $cnx;
		$res = $cnx->query($sql);
		return $res;
	}

	function getPkFromTable($table){
		$sql="SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'";
		global $cnx;
		$res = $cnx->query($sql);
		if($res->rowCount()>0){
			$res = $res->fetch(PDO::FETCH_NAMED);
			$res = $res['Column_name'];
		}else{
			$res = '';
		}
		return $res;
	}

	function actualizar($table, $pk, $values, $parametros=NULL){
		foreach ($values as $k => $v) {
			//@Antonio Fuentes Alcantara
			if($v==null){
				$values[$k]=$v;
			}else{
				$values[$k]=mb_strtoupper($v);
			}
		}		
		if(!isset($parametros)){
			$parametros = array_keys($values);
			foreach ($parametros as $k => $v) {
				$parametros[$k]=str_replace(':', '', $v).'='.$v;
			}
		}

		$sql="UPDATE $table SET ".implode(",", $parametros)." WHERE $pk=:$pk ";
		global $cnx;
		$pre = $cnx->prepare($sql);
		return $pre->execute($values);
	}

	function actualizarWithoutUpper($table, $pk, $values, $parametros=NULL){	
		if(!isset($parametros)){
			$parametros = array_keys($values);
			foreach ($parametros as $k => $v){
				$parametros[$k]=str_replace(':', '', $v).'='.$v;
			}
		}
		$sql="UPDATE $table SET ".implode(",", $parametros)." WHERE $pk=:$pk ";
		
		global $cnx;
		$pre = $cnx->prepare($sql);
		return $pre->execute($values);
	}	

	function actualizarDatoSimple($table, $campo, $valor, $filtro, $valorFiltro, $filtroExtra="", $valorExtra=""){
		$sql="UPDATE $table SET $campo=:valor WHERE $filtro=:filtro ";
		$values=array(':valor' => $valor, ':filtro' =>$valorFiltro);
		if($filtroExtra!=""){
			$sql.=" AND $filtroExtra=:filtroExtra ";
			$values[":filtroExtra"]=$valorExtra;
		}
		global $cnx;
		$pre = $cnx->prepare($sql);
		return $pre->execute($values);
	}

	function cambiarEstado($table, $estado, $pk, $pkvalue, $where="", $valorWhere=""){
		$sql="UPDATE $table SET estado=:estado WHERE $pk=:$pk ";
		$values=array(':estado' => $estado, ':'.$pk =>$pkvalue);

		if($where!=""){
			$sql.=" AND $where=:x$where ";
			$values[":x".$where] = $valorWhere;
		}

		global $cnx;
		$pre = $cnx->prepare($sql);
		return $pre->execute($values);
	}

	function eliminarBD($table, $pk, $pkvalue){
		$sql="DELETE FROM $table WHERE $pk=:$pk ";
		$values=array(':'.$pk =>$pkvalue);
		global $cnx;
		$pre = $cnx->prepare($sql);
		return $pre->execute($values);
	}


	function getCampos($table) {
		$sql = "DESCRIBE $table;";
		global $cnx;
		$pre = $cnx->query($sql) ;
		return $pre;
	}

	function getTablaGeneral($codtabla, $descripcion='descripcion', $orderby='', $order=''){
		$sql = "SELECT codelemento, $descripcion FROM mgtabgend WHERE codtabla=$codtabla ";
		if($orderby!=''){
			$sql.=$sql." ORDER BY ".$orderby." ".$order;
		}
		global $cnx;
		$pre = $cnx->query($sql) ;
		return $pre;
	}


	function getRowTableById($table, $id, $pk=''){
		if($pk==''){
			$pk='id'.$table;
		}
		$sql = "SELECT * FROM $table WHERE ".$pk."=:id ";
		global $cnx;
		$parametros=array(':id'=>$id);
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		if($pre->rowCount()>0){
			$pre = $pre->fetch(PDO::FETCH_NAMED);
		}else{
			return NULL;
		}
		return $pre;
	}

	function getRowTableFiltroSimple($table, $campo, $valor, $campo1='', $valor1='', $campo2='', $valor2='', $campo3='', $valor3=''){		
		$sql = "SELECT * FROM $table WHERE ".$campo."=:valor ";
		$parametros=array(':valor'=>$valor);
		if($campo1!=''){
			$sql.=" AND ".$campo1."=:valor1";
			$parametros[':valor1'] = $valor1;
		}
		if($campo2!=''){
			$sql.=" AND ".$campo2."=:valor2";
			$parametros[':valor2'] = $valor2;
		}		
		if($campo3!=''){
			$sql.=" AND ".$campo3."=:valor3";
			$parametros[':valor3'] = $valor3;
		}	
		global $cnx;		
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		if($pre->rowCount()>0){
			$pre = $pre->fetch(PDO::FETCH_NAMED);
		}else{
			$pre = NULL;
		}
		return $pre;
	}

	function getLastRowTableFiltroSimple($table, $campo, $valor, $campo1='', $valor1='', $campo2='', $valor2='', $campo3='', $valor3='', $orderby='', $order=''){		
		$sql = "SELECT * FROM $table WHERE ".$campo."=:valor ";
		$parametros=array(':valor'=>$valor);
		if($campo1!=''){
			$sql.=" AND ".$campo1."=:valor1";
			$parametros[':valor1'] = $valor1;
		}
		if($campo2!=''){
			$sql.=" AND ".$campo2."=:valor2";
			$parametros[':valor2'] = $valor2;
		}		
		if($campo3!=''){
			$sql.=" AND ".$campo3."=:valor3";
			$parametros[':valor3'] = $valor3;
		}		

		if($orderby!=''){
			$sql.=" ORDER BY $orderby $order ";
		}

		$sql.=" LIMIT 1 ";

		global $cnx;		
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		if($pre->rowCount()>0){
			$pre = $pre->fetch(PDO::FETCH_NAMED);
		}else{
			$pre = NULL;
		}
		return $pre;
	}	
    
	function getListTableFiltroSimple($table, $campo='', $valor='', $campo1='', $valor1='', $campo2='', $valor2='', $campo3='', $valor3='', $orderby='', $order='',$campoDiferente='',$valorDiferente='',$campoDiferente1='',$valorDiferente1=''){	

		if($campo==''){	$campo='estado'; $valor='N'; }

		$sql = "SELECT * FROM $table WHERE ".$campo."=:valor ";
		$parametros=array(':valor'=>$valor);

		if($campo1!=''){
			$sql.=" AND ".$campo1."=:valor1";
			$parametros[':valor1'] = $valor1;
		}
		if($campo2!=''){
			$sql.=" AND ".$campo2."=:valor2";
			$parametros[':valor2'] = $valor2;
		}		
		if($campo3!=''){
			$sql.=" AND ".$campo3."=:valor3";
			$parametros[':valor3'] = $valor3;
		}	
		if ($campoDiferente !='') {
			$sql.=" AND ".$campoDiferente."<> :valorDiferente";
			$parametros[':valorDiferente'] = $valorDiferente;
		}
		if ($campoDiferente1 !='') {
			$sql.=" AND ".$campoDiferente1."<> :valorDiferente1";
			$parametros[':valorDiferente1'] = $valorDiferente1;
		}

		if($orderby!=''){
			$sql.=" ORDER BY $orderby $order";
		}		

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}


	function getListTableFiltroLike($table, $campo='', $valor='', $campo1='', $valor1='', $campo2='', $valor2='', $campolike1='', $valorlike1='', $campolike2='', $valorlike2='', $orderby='', $order=''){	

		if($campo==''){	$campo='estado'; $valor='N'; }

		$sql = "SELECT * FROM $table WHERE ".$campo."=:valor ";
		$parametros=array(':valor'=>$valor);

		if($campo1!=''){
			$sql.=" AND ".$campo1."=:valor1";
			$parametros[':valor1'] = $valor1;
		}
		if($campo2!=''){
			$sql.=" AND ".$campo2."=:valor2";
			$parametros[':valor2'] = $valor2;
		}		

		if($campolike1!=''){
			$sql.=" AND ".$campolike1." LIKE :valorlike1";
			$parametros[':valorlike1'] = '%'.$valorlike1.'%';
		}

		if($campolike2!=''){
			$sql.=" AND ".$campolike2." LIKE :valorlike2";
			$parametros[':valorlike2'] = '%'.$valorlike2.'%';
		}

		if($orderby!=''){
			$sql.=" ORDER BY $orderby $order";
		}		
		
		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function getListTableFiltroFecha($table, $campoFecha, $fechaDesde, $fechaHasta, $campo1='', $valor1='', $campo2='', $valor2='', $campo3='', $valor3='', $orderby='', $ordertype=''){
		$sql = "SELECT * FROM $table WHERE estado='N' ";
		$parametros=array();

		if($fechaDesde!=''){
			$sql.=" AND DATE(".$campoFecha.")>=:fechaDesde";
			$parametros[':fechaDesde'] = $fechaDesde;			
		}

		if($fechaHasta!=''){
			$sql.=" AND DATE(".$campoFecha.")<=:fechaHasta";
			$parametros[':fechaHasta'] = $fechaHasta;			
		}		

		if($campo1!=''){
			$sql.=" AND ".$campo1."=:valor1";
			$parametros[':valor1'] = $valor1;
		}
		if($campo2!=''){
			$sql.=" AND ".$campo2."=:valor2";
			$parametros[':valor2'] = $valor2;
		}		
		if($campo3!=''){
			$sql.=" AND ".$campo3."=:valor3";
			$parametros[':valor3'] = $valor3;
		}	

		if($orderby!=''){
			$sql.=" ORDER BY $orderby ";
			if($ordertype != ''){
				$sql.=" ".$ordertype;
			}
		}		
		global $cnx;		
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}

	function getSimpleList($table){
		$sql = "SELECT * FROM $table WHERE estado='N' ";
		global $cnx;
		return $cnx->query($sql);
	}

	function getList($table, $columnas, $filtro, $inicio=0, $cantidad=12, $total=false, $listartodo=false, $vertodocampos=true, $filtroestado=true){
		$values=array();
		$where=array();
		$orderby=array();

		$select = "";
		$i_tables=2;
		//$tables=array($table=>1);
		$tables=array();
		$join="";
		if($total){
			$select=" COUNT(*) ";
		}else{
			$select=array();
			foreach($columnas as $k=>$v){
				//para cuando sea directo
				if(!is_array($v)){
					$v=array('Field'=>$v, 'Type'=>'');
				}else{
					if(!isset($v['Type'])){
						$v['Type']='';
					}
				}
				$numtable=1;
				$field='t'.$numtable.".".$v['Field'];
				//@ts327 - Inicio
				$tipos_especiales_excluidos = array('dato_multiple_coma');
				//if(isset($v['tabla_origen']) || isset($v['codtabla'])){
				if((isset($v['tabla_origen']) || isset($v['codtabla'])) && ($v['tipo_especial']==null || !in_array($v['tipo_especial'], $tipos_especiales_excluidos))){
				//@ts327 - Fin
					if(isset($v['codtabla'])){
						$v['tabla_origen'] = "mgtabgend";
					}

					if(!isset($v['join'])){
						$v['join']="LEFT";
					}
					$i_tables++;
					$numtable=$i_tables;
					$tables[$v['tabla_origen']]=$numtable;
					$fk=$v['Field'];
					if(isset($v['fk'])){
						$fk=$v['fk'];
					}
					if(isset($v['codtabla'])){
						$fk="codelemento";
					}

					$join .= " ".$v['join']." JOIN ".$v['tabla_origen']." t".$numtable." ON ";
					if(isset($v['codtabla'])){
						$join.=" t".$numtable.".codtabla = ".$v['codtabla']." AND ";
					}
					$join.=" t1.".$v['Field']." = t".$numtable.".".$fk;
									
					if(isset($v['campo_origen'])){
						$field=" t".$numtable.".".$v['campo_origen']." ";
					}
				}
				
				if(isset($v['order'])){
					$orderby[]=$field;
				}
				if(isset($v['Type'])){
					if($v['Type']=='date'){
						$field = " DATE_FORMAT(".$field.",'%d/%m/%Y') ";
					}
					//@Antonio Fuentes Alcantara
					if($v['Type']=='datetime'){
						$field = " DATE_FORMAT(".$field.",'%d/%m/%Y %H:%i') ";
					}
				}	

				$col = $field;

				if(isset($v['alias'])){
					$col.=" as '".$v['alias']."' ";
				}else{
					$col.=" as '".$v['Field']."' ";
				}
				$select[]=$col;

			}
			$select = implode(" , ",$select);
		}
		if($filtroestado && $vertodocampos){
			//$sql = " SELECT $select, t".$tables[$table].".* FROM $table t".$tables[$table]." ".$join." WHERE t".$tables[$table].".estado <> 'E' ";
			$sql = " SELECT $select, t1.* FROM $table t1 ".$join." WHERE t1.estado <> 'E' ";
		}else{
			if($filtroestado){
			$sql = " SELECT $select FROM $table t1 ".$join." WHERE t1.estado <> 'E' ";				
			}else if($vertodocampos){
				$sql = " SELECT $select, t1.* FROM $table t1 ".$join." WHERE 1=1 ";
			}else{
				$sql = " SELECT $select FROM $table t1 ".$join." WHERE 1=1 ";
			}

		}

		if(count($filtro)>0){
			foreach ($filtro as $k => $v) {
				if(!isset($v['Type'])){
					$v['Type']='';
				}
				$pos_par = strrpos($v['Type'], "(");
				if($pos_par>0){
					$v['Type'] = substr($v['Type'], 0, $pos_par);
				}
				if(in_array($v['Type'], array('varchar','text'))){
					if(trim($v['Value'])!='' && trim($v['Value'])!='0' && trim($v['Value'])!=-1){
						$where[]='t1.'.$v['Field']." LIKE :".$v['Field'];
						$values[':'.$v['Field']]="%".str_replace(' ','%',$v['Value']).'%';
					}
				}else if(in_array($v['Type'], array('date','datetime'))){
					if(trim($v['Value'])!='' && trim($v['Value'])!='0' && trim($v['Value'])!=-1){
						$where[]='DATE(t1.'.$v['Field'].") >= :".$v['Field'];
						$values[':'.$v['Field']]=$v['Value'];
					}
					if(!isset($v['ValueHasta'])){
						if($v['Type']=='date'){
							$v['ValueHasta']=$v['Value'];	
						}
					}
					if(trim($v['ValueHasta'])!='' && trim($v['ValueHasta'])!='0' && trim($v['ValueHasta'])!=-1){
						$where[]='DATE(t1.'.$v['Field'].") <= :".$v['Field']."Hasta";
						$values[':'.$v['Field'].'Hasta']=$v['ValueHasta'];
					}					
				}else{
					if(trim($v['Value'])!='' && trim($v['Value'])!='0' && trim($v['Value'])!=-1){
						$where[]='t1.'.$v['Field']." = :".$v['Field'];
						$values[':'.$v['Field']]=$v['Value'];
					}
				}
				
			}

			if(count($where)>0){
				$sql.=" AND ";
			}

			$sql.= implode(" AND ",$where);
		}

		if(!$total){
			if(count($orderby)>0){
				$sql.=" ORDER BY ".implode(",", $orderby);
			}
		}

		if(!$total && !$listartodo){
			$sql.=" LIMIT $inicio, $cantidad";
		}

		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($values);
		if($total){
			$pre=$pre->fetch(PDO::FETCH_NUM);
			$pre=$pre[0];
		}

		return $pre;
	}


	function getResultadoCalculado($table, $calculo, $campo, $filtro1='', $valor1='', $filtro2='', $valor2='', $filtro3='', $valor3=''){
		$sql = "SELECT $calculo($campo) FROM $table ";

		if($filtro1==''){	$filtro1='estado'; $valor1='N'; }
		
		$sql.=" WHERE ".$filtro1."=:valor1";
		$parametros[':valor1'] = $valor1;

		$parametros=array(':valor1'=>$valor1);

		if($filtro2!=''){
			$sql.=" AND ".$filtro2."=:valor2";
			$parametros[':valor2'] = $valor2;
		}		

		if($filtro3!=''){
			$sql.=" AND ".$filtro3."=:valor3";
			$parametros[':valor3'] = $valor3;
		}	
		
		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		if($pre->rowCount()>0){
			$pre=$pre->fetch(PDO::FETCH_NUM);
			$pre=$pre[0];
		}else{
			$pre = 0;
		}

		return $pre;
	}

	function ejecucionSimpleSQL($sql){
		global $cnx;
		$pre = $cnx->query($sql);
		return $pre;
	}

	function ejecucionSimpleParametrosSQL($sql, $values){
		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($values);
		return $pre;
	}

	/* GERSON (22-09-21) */
	function getRowTableFiltroCompleto($table, $campo, $valor, $campo1='', $valor1='', $campo2='', $valor2='', $campo3='', $valor3='', $campo4='', $valor4='', $campo5='', $valor5='', $campo6='', $valor6='', $campo7='', $valor7=''){		
		$sql = "SELECT * FROM $table WHERE ".$campo."=:valor ";
		$parametros=array(':valor'=>$valor);
		if($campo1!=''){
			$sql.=" AND ".$campo1."=:valor1";
			$parametros[':valor1'] = $valor1;
		}
		if($campo2!=''){
			$sql.=" AND ".$campo2."=:valor2";
			$parametros[':valor2'] = $valor2;
		}		
		if($campo3!=''){
			$sql.=" AND ".$campo3."=:valor3";
			$parametros[':valor3'] = $valor3;
		}			
		if($campo4!=''){
			$sql.=" AND ".$campo4."=:valor4";
			$parametros[':valor4'] = $valor4;
		}	
		if($campo5!=''){
			$sql.=" AND ".$campo5."=:valor5";
			$parametros[':valor5'] = $valor5;
		}	
		if($campo6!=''){
			$sql.=" AND ".$campo6."=:valor6";
			$parametros[':valor6'] = $valor6;
		}	
		if($campo7!=''){
			$sql.=" AND ".$campo7."=:valor7";
			$parametros[':valor7'] = $valor7;
		}		
		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		if($pre->rowCount()>0){
			$pre = $pre->fetch(PDO::FETCH_NAMED);
		}else{
			$pre = NULL;
		}
		return $pre;
	}

	function getRowTableFiltroSimpleCentralizacion($table, $campo, $valor, $campo1='', $valor1='', $campo2='', $valor2='', $campo3='', $valor3='', $campo4='', $valor4='', $campo5='', $valor5='', $orderby='', $order=''){		
		$sql = "SELECT * FROM $table WHERE ".$campo."=:valor ";
		$parametros=array(':valor'=>$valor);
		if($campo1!=''){
			$sql.=" AND ".$campo1."=:valor1";
			$parametros[':valor1'] = $valor1;
		}
		if($campo2!=''){
			$sql.=" AND ".$campo2."=:valor2";
			$parametros[':valor2'] = $valor2;
		}		
		if($campo3!=''){
			$sql.=" AND ".$campo3."=:valor3";
			$parametros[':valor3'] = $valor3;
		}	
		if($campo4!=''){
			$sql.=" AND ".$campo4."=:valor4";
			$parametros[':valor4'] = $valor4;
		}	
		if($campo5!=''){
			$sql.=" AND ".$campo5."=:valor5";
			$parametros[':valor5'] = $valor5;
		}	

		global $cnx;		
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		if($pre->rowCount()>0){
			$pre = $pre->fetch(PDO::FETCH_NAMED);
		}else{
			$pre = NULL;
		}
		return $pre;
	}	

	/* function getRowTableFiltroAsientoPredeterminado($table, $campo, $valor, $campo1='', $valor1='', $campo2='', $valor2='', $campo3='', $valor3='', $campo4='', $valor4='', $campo5='', $valor5=''){		
		$sql = "SELECT * FROM $table WHERE ".$campo."=:valor ";
		$parametros=array(':valor'=>$valor);
		if($campo1!=''){
			$sql.=" AND ".$campo1."=:valor1";
			$parametros[':valor1'] = $valor1;
		}
		if($campo2!=''){
			$sql.=" AND ".$campo2."=:valor2";
			$parametros[':valor2'] = $valor2;
		}		
		if($campo3!=''){
			$sql.=" AND ".$campo3."=:valor3";
			$parametros[':valor3'] = $valor3;
		}	
		if($campo4!=''){
			$sql.=" AND ".$campo4."=:valor4";
			$parametros[':valor4'] = $valor4;
		}	
		if($campo5!=''){
			$sql.=" AND ".$campo5."=:valor5";
			$parametros[':valor5'] = $valor5;
		}			
		global $cnx;		
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		if($pre->rowCount()>0){
			$pre = $pre->fetch(PDO::FETCH_NAMED);
		}else{
			$pre = NULL;
		}
		return $pre;
	} */

	function getListTableFiltroAsientoPredeterminado($table, $campo, $valor, $campo1='', $valor1='', $campo2='', $valor2='', $campo3='', $valor3='', $campo4='', $valor4='', $campo5='', $valor5='', $orderby='', $order=''){	

		$sql = "SELECT * FROM $table WHERE ".$campo."=:valor AND estado = 'N' ";
		$parametros=array(':valor'=>$valor);

		if($campo1!=''){
			$sql.=" AND ".$campo1."=:valor1";
			$parametros[':valor1'] = $valor1;
		}
		if($campo2!=''){
			$sql.=" AND ".$campo2."=:valor2";
			$parametros[':valor2'] = $valor2;
		}		
		if($campo3!=''){
			$sql.=" AND ".$campo3."=:valor3";
			$parametros[':valor3'] = $valor3;
		}	
		if($campo4!=''){
			$sql.=" AND ".$campo4."=:valor4";
			$parametros[':valor4'] = $valor4;
		}	
		if($campo5!=''){
			$sql.=" AND ".$campo5."=:valor5";
			$parametros[':valor5'] = $valor5;
		}

		if($orderby!=''){
			$sql.=" ORDER BY $orderby $order";
		}		
		
		global $cnx;
		$pre = $cnx->prepare($sql);
		$pre->execute($parametros);
		return $pre;
	}
	/*  */

}
?>