<?php
require_once("cado.php");
class clsPerfil{

    function consultarPerfil($descripcion, $estado, $inicio, $cantidad, $total=false){
        $sql = "SELECT
                idperfil,
                descripcion,
                estado ";

        if($total){
            $sql = "SELECT COUNT(idperfil) ";
        }

        $sql.="FROM
                perfil
            WHERE
                estado <> 'E' ";
        $parametros = array();

        if($descripcion != ""){
            $sql.= " AND descripcion LIKE :descripcion ";
            $parametros[':descripcion'] = '%'.$descripcion.'%';
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

    function verificarPerfil($descripcion, $idperfil=0){
        $sql="SELECT * FROM perfil WHERE descripcion=:descripcion AND estado<>'E' AND idperfil<>:idperfil ";
        $parametros=array(':descripcion'=>$descripcion, ':idperfil'=>$idperfil);

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre;    
    }

    function getColumnTablaPerfil(){
        $values = array(
                        ':idperfil'=>NULL,
                        ':descripcion'=>'',
                        ':estado'=>'N'
                        );
        return $values;
    }

}
?>