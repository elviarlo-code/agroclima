<?php
require_once("cado.php");
class clsDashboard{

    function consultarkanban($idpersona){
        $sql = "SELECT
                    kn.idkanban,
                    kn.titulo,
                    kn.grid,
                    IFNULL( kd.idtarjeta, 0 ) AS 'idtarjeta',
                    IFNULL( kd.titulo, '' ) AS 'titulo_dash',
                    IFNULL( kd.subtitulo, '' ) AS 'subtitulo_dash',
                    IFNULL( kd.body, '' ) AS 'body_dash',
                    IFNULL( kd.grafico, null ) AS 'grafico_dash',
                    IFNULL( da.link, '' ) AS 'link_dash',
                    IFNULL( da.script, '' ) AS 'script_dash' 
                FROM
                    kanban kn
                    LEFT JOIN kanban_dashboard kd ON kn.idkanban = kd.idkanban 
                    AND kd.estado = 'N'
                    INNER JOIN dashboard da ON kd.iddashboard = da.iddashboard 
                WHERE
                    kn.estado = 'N' 
                    AND (IFNULL(kd.idusuario,0)=0 OR IFNULL(kd.idusuario,0)=:idpersona)
                ORDER BY
                    kn.orden ASC,
                    IFNULL( kd.orden, 0 ) ASC";
        $parametros = array(':idpersona'=>$idpersona);

        global $cnx;
        $pre = $cnx -> prepare($sql);
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