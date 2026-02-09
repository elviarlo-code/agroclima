<?php
require_once("cado.php");
class clsConfiguracion{

    function consultarConfiguracion($idconfig, $descripcion, $estado, $inicio, $cantidad, $total=false){
        $sql = "SELECT
                * ";

        if($total){
            $sql = "SELECT COUNT(codigo) ";
        }

        $sql.="FROM
                mgconfig
            WHERE
                estado <> 'E' ";
        $parametros = array();

        if($idconfig != ""){
            $sql.= " AND idconfig = :idconfig ";
            $parametros[':idconfig'] = $idconfig;
        }

        if($descripcion != ""){
            $sql.= " AND descripcion LIKE :descripcion ";
            $parametros[':descripcion'] = '%'.$descripcion.'%';
        }
        
        if($estado != "0"){
            $sql.=" AND estado=:estado";
            $parametros[':estado']=$estado;
        }

        if(!$total){
            $sql.=" ORDER BY codigo ASC ";
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

    function verificarConfig($idconfig, $codigo=0){
        $sql="SELECT * FROM mgconfig WHERE idconfig=:idconfig AND estado<>'E' AND codigo<>:codigo ";
        $parametros=array(':idconfig'=>$idconfig, ':codigo'=>$codigo);

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre;    
    }

    function getColumnTablaConfiguracion(){
        $values = array(
                        ':codigo'=>NULL,
                        ':idconfig'=>NULL,
                        ':descripcion'=>'',
                        ':modulo'=>'',
                        ':tipdat'=>'',
                        ':longitud'=>'',
                        ':valor'=>'',
                        ':observacion'=>'',
                        ':idinstitucion'=>0,
                        ':idsucursal'=>0,
                        ':estado'=>'N'
                        );
        return $values;
    }

}
?>