<?php
require_once("cado.php");
class clsDispositivo{

    function consultarDispositivo($nombre, $estado, $inicio, $cantidad, $total=false){
        $sql = "SELECT
                dis.iddispositivo,
                dis.codigo,
                dis.nombre,
                dis.tipo,
                mgd.descripcion as tipodispositivo,
                dis.latitud,
                dis.longitud,
                dis.altitud,
                dis.ubigeo,
                dis.ubigeo_texto,
                dis.estado ";

        if($total){
            $sql = "SELECT COUNT(iddispositivo) ";
        }

        $sql.="FROM
                dispositivo dis
            INNER JOIN mgtablagenerald mgd ON dis.tipo = mgd.codigo AND mgd.idtablageneral=7
            WHERE
                dis.estado <> 'E' ";
        $parametros = array();

        if($nombre != ""){
            $sql.= " AND dis.nombre LIKE :nombre ";
            $parametros[':nombre'] = '%'.$nombre.'%';
        }
        
        if($estado != "0"){
            $sql.=" AND dis.estado=:estado";
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

    function insertarClimaDavis($clima,$iddispositivo){
        $sql = "INSERT INTO clima_davis(idclima, fecha, temp_out, temp_hi, temp_low, hum_out, dew_pt, wind_speed, wind_dir, wind_run, hi_speed, hi_dir, wind_chill, heat_index, thw_index, thsw_index, bar, rain, rain_rate, solar_rad, solar_energy, hi_solar_rad, uv_index, uv_dose, hi_uv, heat_dd, cool_dd, in_temp, in_hum, in_dew, in_heat, in_emc, in_air_density, et, wind_samp, wind_tx, iss_recept, arc_int, iddispositivo, fhregistro, idpersonaregistro) VALUES (NULL, :fecha, :temp_out, :temp_hi, :temp_low, :hum_out, :dew_pt, :wind_speed, :wind_dir, :wind_run, :hi_speed, :hi_dir, :wind_chill, :heat_index, :thw_index, :thsw_index, :bar, :rain, :rain_rate, :solar_rad, :solar_energy, :hi_solar_rad, :uv_index, :uv_dose, :hi_uv, :heat_dd, :cool_dd, :in_temp, :in_hum, :in_dew, :in_heat, :in_emc, :in_air_density, :et, :wind_samp, :wind_tx, :iss_recept, :arc_int, :iddispositivo, :fhregistro, :idpersonaregistro)";
        global $cnx;
        $pre = $cnx->prepare($sql);
        
        foreach($clima as $k=>$v){
            $parametros = array(
                ":fecha"            => $v['fecha'],
                ":temp_out"         => $v['temp_out'], 
                ":temp_hi"          => $v['temp_hi'], 
                ":temp_low"         => $v['temp_low'], 
                ":hum_out"          => $v['hum_out'], 
                ":dew_pt"           => $v['dew_pt'], 
                ":wind_speed"       => $v['wind_speed'], 
                ":wind_dir"         => $v['wind_dir'], 
                ":wind_run"         => $v['wind_run'], 
                ":hi_speed"         => $v['hi_speed'], 
                ":hi_dir"           => $v['hi_dir'], 
                ":wind_chill"       => $v['wind_chill'], 
                ":heat_index"       => $v['heat_index'], 
                ":thw_index"        => $v['thw_index'], 
                ":thsw_index"       => $v['thsw_index'], 
                ":bar"              => $v['bar'], 
                ":rain"             => $v['rain'], 
                ":rain_rate"        => $v['rain_rate'], 
                ":solar_rad"        => $v['solar_rad'], 
                ":solar_energy"     => $v['solar_energy'], 
                ":hi_solar_rad"     => $v['hi_solar_rad'], 
                ":uv_index"         => $v['uv_index'], 
                ":uv_dose"          => $v['uv_dose'], 
                ":hi_uv"            => $v['hi_uv'], 
                ":heat_dd"          => $v['heat_dd'], 
                ":cool_dd"          => $v['cool_dd'], 
                ":in_temp"          => $v['in_temp'], 
                ":in_hum"           => $v['in_hum'], 
                ":in_dew"           => $v['in_dew'], 
                ":in_heat"          => $v['in_heat'], 
                ":in_emc"           => $v['in_emc'], 
                ":in_air_density"   => $v['in_air_density'], 
                ":et"               => $v['et'], 
                ":wind_samp"        => $v['wind_samp'], 
                ":wind_tx"          => $v['wind_tx'], 
                ":iss_recept"       => $v['iss_recept'], 
                ":arc_int"          => $v['arc_int'], 
                ":iddispositivo"    => $iddispositivo, 
                ":fhregistro"       => date('Y-m-d H:i:s'), 
                ":idpersonaregistro"=> $_SESSION['idpersona']
            );
            $pre->execute($parametros);  
        }
    }

    function getColumnTablaDispositivo(){
        $values = array(
                        ':iddispositivo'=>NULL,
                        ':codigo'=>'',
                        ':nombre'=>'',
                        ':tipo'=>'',
                        ':latitud'=>null,
                        ':longitud'=>null,
                        ':altitud'=>null,
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

}
?>