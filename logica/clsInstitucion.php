<?php
require_once("cado.php");
class clsInstitucion{

    function consultarInstitucion($nombre, $ruc, $estado, $inicio, $cantidad, $total=false){
        $sql = "SELECT
                idinstitucion,
                nombre,
                tipodoc,
                ruc,
                direccion,
                parafact,
                defectofact,
                nrodocrepresentante,
                nombrerepresentante,
                estado ";

        if($total){
            $sql = "SELECT COUNT(idinstitucion) ";
        }

        $sql.="FROM
                mginstitucion
            WHERE
                estado <> 'E' ";
        $parametros = array();

        if($nombre != ""){
            $sql.= " AND nombre LIKE :nombre ";
            $parametros[':nombre'] = '%'.$nombre.'%';
        }

        if($ruc != ""){
            $sql.= " AND ruc LIKE :ruc ";
            $parametros[':ruc'] = '%'.$ruc.'%';
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

    function verificarInstitucion($ruc, $idinstitucion=0){
        $sql="SELECT * FROM mginstitucion WHERE ruc=:ruc AND estado<>'E' AND idinstitucion<>:idinstitucion ";
        $parametros=array(':ruc'=>$ruc, ':idinstitucion'=>$idinstitucion);

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre;    
    }

    function getColumnTablaInstitucion(){
        $values = array(
                        ':idinstitucion'=>NULL,
                        ':nombre'=>'',
                        ':tipodoc'=>'',
                        ':ruc'=>'',
                        ':direccion'=>'',
                        ':parafact'=>0,
                        ':defectofact'=>0,
                        ':nrodocrepresentante'=>'',
                        ':nombrerepresentante'=>'',
                        ':codigo_ubigeo_departamento'=>'',
                        ':codigo_ubigeo_provincia'=>'',
                        ':codigo_ubigeo_distrito'=>'',
                        ':direccion_departamento'=>'',
                        ':direccion_provincia'=>'',
                        ':direccion_distrito'=>'',
                        ':direccion_codigopais'=>'PE',
                        ':estado'=>'N'
                        );
        return $values;
    }

}
?>