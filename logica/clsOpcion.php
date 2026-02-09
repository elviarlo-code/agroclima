<?php
require_once("cado.php");
class clsOpcion{

    function consultarOpcion($modulo,$descripcion, $link, $estado, $inicio, $cantidad, $total=false){
        $sql = "SELECT
                op1.*,
                op2.descripcion AS 'modulo' ";

        if($total){
            $sql = "SELECT COUNT(op1.idopcion) ";
        }

        $sql.="FROM
                    opcion op1
                    LEFT JOIN opcion op2 ON op1.idopcion_ref = op2.idopcion 
                WHERE
                    op1.estado <> 'E'";
        $parametros = array();

        if($descripcion != ""){
            $sql.= " AND op1.descripcion LIKE :descripcion ";
            $parametros[':descripcion'] = '%'.$descripcion.'%';
        }

        if($link != ""){
            $sql.= " AND op1.link LIKE :link ";
            $parametros[':link'] = '%'.$link.'%';
        }

        if($modulo > 0){
            $sql.= " AND op1.idopcion_ref = :modulo ";
            $parametros[':modulo'] = $modulo;
        }
        
        if($estado != "0"){
            $sql.=" AND op1.estado=:estado";
            $parametros[':estado']=$estado;
        }

        $sql.=" ORDER BY op2.descripcion ASC, op1.orden ASC ";

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

    function getColumnTablaOpcion(){
        $values = array(
                        ':idopcion'=>NULL,
                        ':descripcion'=>'',
                        ':link'=>'',
                        ':idopcion_ref'=>'',
                        ':orden'=>0,
                        ':nro_registro'=>0,
                        ':title'=>'',
                        ':icon'=>'',
                        ':idinstitucion'=>null,
                        ':idsucursal'=>null,
                        ':tabla'=>'',
                        ':tabladetalle'=>'',
                        ':puederegistrar'=>'',
                        ':puedeeditar'=>'',
                        ':puedeanular'=>'',
                        ':puedeeliminar'=>'',
                        ':puedeimprimir'=>'',
                        ':opcion_especial'=>'',
                        ':opcion_especial1'=>'',
                        ':opcion_especial2'=>'',
                        ':accesodirecto'=>0,
                        ':accesodashboard'=>'',
                        ':tituloaccesodirecto'=>'',
                        ':estado'=>'N'
                        );
        return $values;
    }

}
?>