<?php
require_once("cado.php");
class clsTurno{

    function consultarTurno($nombre, $idterreno, $idesquema, $estado, $inicio, $cantidad, $total=false){
        $sql = "SELECT
                tur.*,
                esq.descripcion as 'esquema',
                ter.descripcion as 'terreno' ";

        if($total){
            $sql = "SELECT COUNT(tur.idturno) ";
        }

        $sql.="FROM
                terreno_turno tur
                INNER JOIN terreno_esquema esq ON esq.idesquema = tur.idesquema
                INNER JOIN terreno ter ON ter.idterreno = tur.idterreno
            WHERE
                tur.estado <> 'E' ";
        $parametros = array();

        if($nombre != ""){
            $sql.= " AND tur.nombre LIKE :nombre ";
            $parametros[':nombre'] = '%'.$nombre.'%';
        }

        if($idterreno != "0"){
            $sql.= " AND tur.idterreno = :idterreno ";
            $parametros[':idterreno'] = $idterreno;
        }

        if($idesquema != "0"){
            $sql.= " AND tur.idesquema = :idesquema ";
            $parametros[':idesquema'] = $idesquema;
        }
        
        if($estado != "0"){
            $sql.=" AND tur.estado=:estado";
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

    function consultarCoordenadaPorEsquema($idterreno, $idesquema, $idturno){
        $sql="SELECT
                tc.*,
                tt.nombre AS 'turno',
                tt.area,
                tt.color 
            FROM
                terreno_turno_coordenada tc
                INNER JOIN terreno_turno tt ON tc.idturno = tt.idturno 
            WHERE
                tc.estado = 'N' 
                AND tt.estado = 'N' 
                AND tt.idterreno = :idterreno 
                AND tt.idesquema = :idesquema
                AND tt.idturno<>:idturno ";
        $parametros=array(':idterreno'=>$idterreno, ':idesquema'=>$idesquema, ':idturno'=>$idturno);

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre;   
    }

    function consultarTurnoPorTerrenoActivo($idterreno, $idesquema=0, $idturno=0, $verCoordenadas=0){
        $select = "";
        if($verCoordenadas==1){
            $select = " , tc.latitud, tc.longitud ";
        }

        $sql="SELECT
                tur.*, IFNULL(tx.idcampania,0) as 'idcampania', IFNULL(tx.fecha,'') as 'fecha', IFNULL(tx.cultivo,'') as 'cultivo' $select
            FROM
                terreno ter
                INNER JOIN terreno_esquema teq ON teq.idterreno = ter.idterreno
                INNER JOIN terreno_turno tur ON tur.idesquema = teq.idesquema 
                LEFT JOIN (SELECT cam.idcampania, cam.idturno, DATE_FORMAT(cam.fechaini, '%d/%m/%Y') as fecha, cul.nombre as cultivo FROM campania cam INNER JOIN cultivo cul ON cam.idcultivo=cul.idcultivo WHERE cam.estado='N' AND cam.finalizado=0 GROUP BY cam.idturno) tx ON tur.idturno=tx.idturno ";
        
        if($verCoordenadas==1){
            $sql.=" INNER JOIN terreno_turno_coordenada tc ON tc.idturno = tur.idturno AND tc.estado='N' ";
        }

        $sql.=" WHERE
                ter.estado = 'N' 
                AND teq.estado = 'N' 
                AND tur.estado = 'N' 
                AND tur.idterreno=:idterreno ";
        $parametros=array(':idterreno'=>$idterreno);

        if($verCoordenadas==0){
            $sql.=" AND (IFNULL(tx.idcampania,0)=0 OR tur.idturno=:idturno)";
            $parametros[':idturno'] = $idturno;
        }

        if($idesquema>0){
            $sql.=" AND tur.idesquema=:idesquema ";
            $parametros[':idesquema'] = $idesquema;
        }else{
            $sql.=" AND teq.activo =1 ";
        }

        if($verCoordenadas==1){
            $sql.=" ORDER BY tc.idcoordenada ASC ";
        }else{
            $sql.=" ORDER BY tur.nombre ASC ";
        }
        

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre;   
    }

    function getColumnTablaTerrenoTurno(){
        $values = array(
                        ':idturno'=>NULL,
                        ':nombre'=>'',
                        ':area'=>0,
                        ':color'=>'',
                        ':idesquema'=>NULL,
                        ':idterreno'=>NULL,
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

    function getColumnTablaTerrenoTurnoCoordenada(){
        $values = array(
                        ':idcoordenada'=>NULL,
                        ':latitud'=>null,
                        ':longitud'=>null,
                        ':idturno'=>null,
                        ':idesquema'=>NULL,
                        ':idterreno'=>NULL,
                        ':fhregistro'=>NULL,
                        ':idpersonaregistro'=>0,
                        ':estado'=>'N'
                        );
        return $values;
    }

}
?>