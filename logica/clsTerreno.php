<?php
require_once("cado.php");
class clsTerreno{

    function consultarTerreno($nombre, $estado, $inicio, $cantidad, $total=false){
        $sql = "SELECT
                idterreno,
                descripcion,
                direccion,
                latitud,
                longitud,
                altitud,
                area,
                ubigeo,
                ubigeo_texto,
                estado ";

        if($total){
            $sql = "SELECT COUNT(idterreno) ";
        }

        $sql.="FROM
                terreno
            WHERE
                estado <> 'E' ";
        $parametros = array();

        if($nombre != ""){
            $sql.= " AND descripcion LIKE :nombre ";
            $parametros[':nombre'] = '%'.$nombre.'%';
        }
        
        if($estado != "0"){
            $sql.=" AND estado=:estado";
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

    function consultarEsquema($idterreno, $esquema){
        $sql="SELECT tq.*, te.latitud, te.longitud FROM terreno_esquema tq INNER JOIN terreno te ON tq.idterreno=te.idterreno WHERE tq.estado='N' AND tq.idterreno=:idterreno ";
        $parametros=array(':idterreno'=>$idterreno);

        if($esquema != ""){
            $sql.= " AND descripcion LIKE :esquema ";
            $parametros[':esquema'] = '%'.$esquema.'%';
        }

        $sql .= " ORDER BY fhregistro ASC ";

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre;   
    }

    function verificarEsquemaActivo($idterreno, $idesquema){
        $sql="SELECT * FROM terreno_esquema WHERE idterreno=:idterreno AND estado='N' AND idesquema<>:idesquema AND activo=1";
        $parametros=array(':idterreno'=>$idterreno, ':idesquema'=>$idesquema);

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre;   
    }

    function getColumnTablaTerreno(){
        $values = array(
                        ':idterreno'=>NULL,
                        ':descripcion'=>'',
                        ':direccion'=>'',
                        ':latitud'=>null,
                        ':longitud'=>null,
                        ':altitud'=>null,
                        ':area'=>null,
                        ':ubigeo'=>'',
                        ':ubigeo_texto'=>'',
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


    function getColumnTablaTerrenoEsquema(){
        $values = array(
                        ':idesquema'=>NULL,
                        ':descripcion'=>null,
                        ':activo'=>0,
                        ':idterreno'=>null,
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