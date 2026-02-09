<?php
require_once("cado.php");
class clsCampania{

    function consultarCampania($desde, $hasta ,$turno, $idterreno, $idesquema, $estado, $inicio, $cantidad, $total=false){
        $sql = "SELECT
                cam.*,
                cul.nombre as 'cultivo',
                tur.nombre as 'turno',
                tur.area,
                esq.descripcion as 'esquema',
                ter.descripcion as 'terreno' ";

        if($total){
            $sql = "SELECT COUNT(cam.idcampania) ";
        }

        $sql.="FROM
                campania cam
                INNER JOIN terreno_turno tur ON cam.idturno = tur.idturno
                INNER JOIN terreno_esquema esq ON esq.idesquema = cam.idesquema
                INNER JOIN terreno ter ON ter.idterreno = cam.idterreno
                INNER JOIN cultivo cul ON cam.idcultivo = cul.idcultivo
            WHERE
                cam.estado <> 'E' ";
        $parametros = array();

        if($desde != ""){
            $sql.= " AND cam.fechaini >= :desde ";
            $parametros[':desde'] = $desde;
        }

        if($hasta != ""){
            $sql.= " AND cam.fechaini <= :hasta ";
            $parametros[':hasta'] = $hasta;
        }

        if($turno != ""){
            $sql.= " AND tur.nombre LIKE :turno ";
            $parametros[':turno'] = '%'.$turno.'%';
        }

        if($idterreno != "0"){
            $sql.= " AND cam.idterreno = :idterreno ";
            $parametros[':idterreno'] = $idterreno;
        }

        if($idesquema != "0"){
            $sql.= " AND cam.idesquema = :idesquema ";
            $parametros[':idesquema'] = $idesquema;
        }
        
        if($estado != "0"){
            $sql.=" AND cam.estado=:estado";
            $parametros[':estado']=$estado;
        }

        if(!$total){
            $sql.= " LIMIT $inicio, $cantidad ";
        }
        
        global $cnx;
        $pre = $cnx -> prepare($sql);
        $pre->execute($parametros);

        if($total){
            $pre = $pre->fetch(PDO::FETCH_NUM);
            $pre = $pre[0];
        }
        return $pre;
    }

    function seleccionarCampaniaPorID($idcampania){
        $sql="SELECT
                cam.*,
                cul.nombre AS 'cultivo',
                cul.variedad,
                cul.imagen,
                tur.nombre AS 'turno',
                tur.area,
                esq.descripcion AS 'esquema',
                ter.descripcion AS 'terreno',
                ter.latitud,
                ter.longitud,
                IFNULL(dis.tipo,0) as tipodis,
                IFNULL(dis.nombre,'') as dispositivo,
                per.nombres as 'registrador',
                DATE_FORMAT(cam.fhregistro,'%d/%m/%Y %h:%i:%s') as 'fecha_registro' 
            FROM
                campania cam
                INNER JOIN terreno_turno tur ON cam.idturno = tur.idturno
                INNER JOIN terreno_esquema esq ON esq.idesquema = cam.idesquema
                INNER JOIN terreno ter ON ter.idterreno = cam.idterreno
                INNER JOIN cultivo cul ON cam.idcultivo = cul.idcultivo 
                INNER JOIN persona per ON per.idpersona = cam.idpersonaregistro
                LEFT JOIN dispositivo dis ON cam.iddispositivo=dis.iddispositivo
            WHERE
                cam.estado <> 'E'
                AND cam.idcampania = :idcampania ";
        $parametros=array(':idcampania'=>$idcampania);

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre; 
    }

    function consultarFenologiaCampania($idcampania, $fenologia=''){
        $sql="SELECT *, IFNULL(kc,0) as 'valorkc' FROM campania_fenologia WHERE estado='N' AND idcampania=:idcampania ";
        $parametros=array(':idcampania'=>$idcampania);

        if($fenologia != ""){
            $sql.= " AND nombre LIKE :fenologia ";
            $parametros[':fenologia'] = '%'.$fenologia.'%';
        }

        $sql .= " ORDER BY IF(IFNULL(orden,0)=0 ,100000,orden) ASC, nombre ASC ";

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre;   
    }

    function verificarCultivoFenologiaCampania($nombre, $idcampania, $idfenologia=0){
        $sql="SELECT * FROM campania_fenologia WHERE nombre=:nombre AND idcampania=:idcampania AND estado<>'E' AND idfenologia<>:idfenologia ";
        $parametros=array(':nombre'=>$nombre, ':idcampania'=>$idcampania, ':idfenologia'=>$idfenologia);

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre;    
    }

    function consultarDatoSensorCampania($iddispositivo, $fechaini, $fechafin){
        $sql="SELECT MAX(temperatura) as tempmax, MIN(temperatura) as tempmin, ROUND(AVG(temperatura),2) as temppro, MAX(humedad_relativa) as hummax, MIN(humedad_relativa) as hummin, ROUND(AVG(humedad_relativa),2) as humpro, MAX(velocidad_viento) as vvemax, MIN(velocidad_viento) as vvemin, AVG(velocidad_viento) as vvepro, SUM(precipitacion), DATE(fecha) as fecha, iddispositivo FROM clima WHERE iddispositivo=:iddispositivo AND DATE(fecha)>=:fechaini ";
        $parametros=array(':iddispositivo'=>$iddispositivo, ':fechaini'=>$fechaini);

        if($fechafin!=""){
            $sql.=" AND DATE(fecha)<=:fechafin ";
            $parametros[':fechafin'] = $fechafin;
        } 

        $sql.=" GROUP BY DATE(fecha) ";
        $sql.=" ORDER BY DATE(fecha) ASC ";

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre; 
    }

    function getColumnTablaCampania(){
        $values = array(
                        ':idcampania'=>NULL,
                        ':fechaini'=>NULL,
                        ':fechasiembra'=>NULL,
                        ':fechafin'=>NULL,
                        ':descripcion'=>'',
                        ':finalizado'=>0,
                        ':idcultivo'=>null,
                        ':idturno'=>null,
                        ':idesquema'=>null,
                        ':idterreno'=>null,
                        ':iddispositivo'=>null,
                        ':fhregistro'=>NULL,
                        ':idpersonaregistro'=>0,
                        ':fheditar'=>NULL,
                        ':idpersonaeditar'=>0,
                        ':fheliminar'=>NULL,
                        ':idpersonaeliminar'=>0,
                        ':estado'=>'N'
                        );
        return $values;
    }

    function getColumnTablaCampaniaFenologia(){
        $values = array(
                        ':idfenologia'=>NULL,
                        ':orden'=>null,
                        ':nombre'=>'',
                        ':duracion'=>null,
                        ':kc'=>null,
                        ':raiz'=>null,
                        ':cobertura'=>null,
                        ':umbral'=>null,
                        ':temp_min'=>null,
                        ':temp_max'=>null,
                        ':humd_min'=>null,
                        ':humd_max'=>null,
                        ':idcultivo'=>null,
                        ':idcampania'=>NULL,
                        ':fhregistro'=>NULL,
                        ':idpersonaregistro'=>0,
                        ':fheditar'=>NULL,
                        ':idpersonaeditar'=>0,
                        ':fheliminar'=>NULL,
                        ':idpersonaeliminar'=>0,
                        ':estado'=>'N'
                        );
        return $values;
    }

}
?>