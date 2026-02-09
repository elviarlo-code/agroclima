<?php
require_once("cado.php");
class clsAlmacen{

    function consultarAlmacen($nombre, $idinstitucion, $idsucursal, $estado, $inicio, $cantidad, $total=false){
        $sql = "SELECT
                mga.*,
                mgs.nombre as 'sucursal',
                mgi.nombre as 'institucion' ";

        if($total){
            $sql = "SELECT COUNT(mga.idalmacen) ";
        }

        $sql.="FROM
                mgalmacen mga
                INNER JOIN mgsucursal mgs ON mga.idsucursal = mgs.idsucursal
                INNER JOIN mginstitucion mgi ON mgs.idinstitucion = mgi.idinstitucion
            WHERE
                mga.estado <> 'E' AND mgi.estado='N' AND mgs.estado='N' ";
        $parametros = array();

        if($nombre != ""){
            $sql.= " AND mga.descripcion LIKE :nombre ";
            $parametros[':nombre'] = '%'.$nombre.'%';
        }

        if($idinstitucion != "0"){
            $sql.= " AND mga.idinstitucion = :idinstitucion ";
            $parametros[':idinstitucion'] = $idinstitucion;
        }

        if($idsucursal != "0"){
            $sql.= " AND mga.idsucursal = :idsucursal ";
            $parametros[':idsucursal'] = $idsucursal;
        }
        
        if($estado != "0"){
            $sql.=" AND mga.estado=:estado";
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

    function getColumnTablaAlmacen(){
        $values = array(
                        ':idalmacen'=>NULL,
                        ':descripcion'=>'',
                        ':ubigeo'=>'',
                        ':ubigeo_texto'=>'',
                        ':direccion'=>'',
                        ':idinstitucion'=>NULL,
                        ':idsucursal'=>NULL,
                        ':estado'=>'N'
                        );
        return $values;
    }

}
?>