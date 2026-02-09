<?php
require_once("cado.php");
class clsCultivo{

    function consultarCultivo($nombre, $variedad, $estado, $inicio, $cantidad, $total=false){
        $sql = "SELECT
                idcultivo,
                nombre,
                variedad,
                altura,
                raiz_maxima,
                raiz_minima,
                periodovegetativo,
                imagen,
                estado ";

        if($total){
            $sql = "SELECT COUNT(idcultivo) ";
        }

        $sql.="FROM
                cultivo
            WHERE
                estado <> 'E' ";
        $parametros = array();

        if($nombre != ""){
            $sql.= " AND nombre LIKE :nombre ";
            $parametros[':nombre'] = '%'.$nombre.'%';
        }

        if($variedad != ""){
            $sql.= " AND variedad LIKE :variedad ";
            $parametros[':variedad'] = '%'.$variedad.'%';
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

    function verificarCultivo($nombre, $variedad, $idcultivo=0){
        $sql="SELECT * FROM cultivo WHERE nombre=:nombre AND variedad=:variedad AND estado<>'E' AND idcultivo<>:idcultivo ";
        $parametros=array(':nombre'=>$nombre, ':variedad'=>$variedad, ':idcultivo'=>$idcultivo);

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre;    
    }

    function verificarCultivoFenologia($nombre, $idcultivo, $idfenologia=0){
        $sql="SELECT * FROM cultivo_fenologia WHERE nombre=:nombre AND idcultivo=:idcultivo AND estado<>'E' AND idfenologia<>:idfenologia ";
        $parametros=array(':nombre'=>$nombre, ':idcultivo'=>$idcultivo, ':idfenologia'=>$idfenologia);

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre;    
    }

    function consultarFenologia($idcultivo, $fenologia){
        $sql="SELECT *, IFNULL(kc,0) as 'valorkc' FROM cultivo_fenologia WHERE estado='N' AND idcultivo=:idcultivo ";
        $parametros=array(':idcultivo'=>$idcultivo);

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

    function getColumnTablaCultivo(){
        $values = array(
                        ':idcultivo'=>NULL,
                        ':nombre'=>'',
                        ':variedad'=>'',
                        ':altura'=>null,
                        ':raiz_maxima'=>null,
                        ':raiz_minima'=>null,
                        ':periodovegetativo'=>null,
                        ':imagen'=>'',
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

    function getColumnTablaCultivoFenologia(){
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