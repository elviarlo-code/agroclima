<?php
require_once("cado.php");
class clsSucursal{

    function consultarSucursal($nombre, $idinstitucion, $estado, $inicio, $cantidad, $total=false){
        $sql = "SELECT
                mgs.idsucursal,
                mgs.nombre,
                mgs.direccion,
                mgs.idinstitucion,
                mgi.nombre as 'institucion',
                mgs.estado ";

        if($total){
            $sql = "SELECT COUNT(mgs.idsucursal) ";
        }

        $sql.="FROM
                mgsucursal mgs
                INNER JOIN mginstitucion mgi ON mgs.idinstitucion = mgi.idinstitucion
            WHERE
                mgs.estado <> 'E' AND mgi.estado='N' ";
        $parametros = array();

        if($nombre != ""){
            $sql.= " AND mgs.nombre LIKE :nombre ";
            $parametros[':nombre'] = '%'.$nombre.'%';
        }

        if($idinstitucion != "0"){
            $sql.= " AND mgs.idinstitucion = :idinstitucion ";
            $parametros[':idinstitucion'] = $idinstitucion;
        }
        
        if($estado != "0"){
            $sql.=" AND mgs.estado=:estado";
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

    function getColumnTablaSucursal(){
        $values = array(
                        ':idsucursal'=>NULL,
                        ':nombre'=>'',
                        ':direccion'=>'',
                        ':idinstitucion'=>NULL,
                        ':estado'=>'N'
                        );
        return $values;
    }

}
?>