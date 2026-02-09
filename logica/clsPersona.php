<?php
require_once("cado.php");
class clsPersona{

    function consultarPersona($nombre, $documento, $tipo, $estado, $inicio, $cantidad, $total=false){
        $sql = "SELECT
                idpersona,
                apellidos,
                nombres,
                razon_social,
                tipo_documento,
                nro_documento,
                email,
                telcelular,
                telotro,
                direccion,
                observacion,
                estado ";

        if($total){
            $sql = "SELECT COUNT(idpersona) ";
        }

        $sql.="FROM
                persona
            WHERE
                estado <> 'E' ";
        $parametros = array();

        if($nombre != ""){
            $sql.= " AND CONCAT_WS( ' ', nombres, apellidos ) LIKE :nombre ";
            $parametros[':nombre'] = '%'.$nombre.'%';
        }

        if($documento != ""){
            $sql.= " AND nro_documento LIKE :nro_documento ";
            $parametros[':nro_documento'] = '%'.$documento.'%';
        }

        if ($tipo != '0') {
            if ($tipo == 'T') {
                $sql.=" AND estrabajador = 1 ";
            }
            if ($tipo == 'P') {
                $sql.=" AND esproveedor = 1 ";
            }
            if ($tipo == 'C') {
                $sql.=" AND escliente = 1 ";
            }
        }
        
        if($estado != "0"){
            $sql.=" AND estado=:estado";
            $parametros[':estado']=$estado;
        }

        $sql.=" ORDER BY CONCAT_WS( ' ', nombres, apellidos ) ASC ";

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

    function verificarPersona($nro_documento, $idpersona=0){
        $sql="SELECT * FROM persona WHERE nro_documento=:nro_documento AND estado<>'E' AND idpersona<>:idpersona ";
        $parametros=array(':nro_documento'=>$nro_documento, ':idpersona'=>$idpersona);

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre;    
    }

    function listarUbigeo(){
        $sql="SELECT t1.iddistrito, t2.idprovincia, t3.iddepartamento, t1.nombre distrito,t2.nombre provincia, t3.nombre departamento
                FROM distrito t1 
                INNER JOIN provincia t2 on t1.idprovincia=t2.idprovincia
                INNER JOIN departamento t3 on t3.iddepartamento=t2.iddepartamento";
        global $cnx;
        $pre=$cnx->query($sql);     
        return $pre;            
    }

    function getColumnTablaPersona(){
        $values = array(
                        ':idpersona'=>NULL,
                        ':apellidos'=>'',
                        ':nombres'=>'',
                        ':razon_social'=>'',
                        ':tipo_documento'=>'',
                        ':nro_documento'=>'',
                        ':email'=>'',
                        ':facebook'=>'',
                        ':medio_comunicacion'=>'',
                        ':ocupacion'=>'',
                        ':direccion'=>'',
                        ':sexo'=>'',
                        ':ubigeo'=>'',
                        ':ubigeo_dir_dep'=>'',
                        ':ubigeo_dir_pro'=>'',
                        ':ubigeo_dir_dis'=>'',
                        ':telcelular'=>'',
                        ':telotro'=>'',
                        ':fnacimiento'=>NULL,
                        ':credito'=>0,
                        ':escliente'=>0,
                        ':esproveedor'=>0,
                        ':estrabajador'=>0,
                        ':observacion'=>'',
                        ':agente_retencion'=>0,
                        ':idregistrador'=>0,
                        ':fhregistro'=>NULL,
                        ':idpersonaeditar'=>0,
                        ':fheditar'=>NULL,
                        ':idpersonaeliminar'=>0,
                        ':fheliminar'=>NULL,
                        ':estado'=>'N'
                        );
        return $values;
    }

}
?>