<?php
require_once("cado.php");
class clsReporte{
    function consultarDatosSensorPorDia($iddispositivo, $fechaini, $fechafin, $frecuencia){

        $columnas = " * ";
        $join = "";
        if($frecuencia=='H'){
            $columnas = " MAX(cli.temperatura) as tempmax,
                            MIN(cli.temperatura) as tempmin,
                            ROUND(AVG(cli.temperatura),2) as temppro,
                            MAX(cli.humedad_relativa) as hummax,
                            MIN(cli.humedad_relativa) as hummin,
                            ROUND(AVG(cli.humedad_relativa), 2) as humpro,
                            MAX(cli.velocidad_viento) as vvemax,
                            MIN(cli.velocidad_viento) as vvemin,
                            ROUND(AVG(cli.velocidad_viento),2) as vvepro,
                            SUM(cli.precipitacion) as precipitacion,
                            ROUND(AVG(cli.radiacion_solar),2) as 'radiacion_solar',
                            DATE(cli.fecha) as fecha,
                            HOUR(cli.fecha) as hora,
                            DATE_FORMAT(cli.fecha,'%Y-%m-%d %H:00:00') as 'fechahora',
                            DATE_FORMAT(cli.fecha,'%d/%m/%Y %H:00:00') as 'fecharepor',
                            cli.iddispositivo ";
        }else if($frecuencia=='D'){
            $columnas = " MAX(cli.temperatura) as tempmax,
                            MIN(cli.temperatura) as tempmin,
                            ROUND(AVG(cli.temperatura),2) as temppro,
                            MAX(cli.humedad_relativa) as hummax,
                            MIN(cli.humedad_relativa) as hummin,
                            ROUND(AVG(cli.humedad_relativa), 2) as humpro,
                            MAX(cli.velocidad_viento) as vvemax,
                            MIN(cli.velocidad_viento) as vvemin,
                            ROUND(AVG(cli.velocidad_viento),2) as vvepro,
                            SUM(cli.precipitacion) as precipitacion,
                            IFNULL(cal.radiacion_solar,ROUND(calcular_radiacion_diaria(DATE(cli.fecha), cli.iddispositivo),2)) as 'radiacion_solar',
                            DATE(cli.fecha) as fecha,
                            HOUR(cli.fecha) as hora,
                            DATE_FORMAT(cli.fecha,'%Y-%m-%d %H:00:00') as 'fechahora',
                            DATE_FORMAT(cli.fecha,'%d/%m/%Y') as 'fecharepor',
                            cli.iddispositivo ";
            $join = " LEFT JOIN clima_calculos cal ON cli.iddispositivo=cal.iddispositivo AND DATE(cli.fecha)=cal.fecha ";
        }

        $sql="SELECT
                $columnas 
            FROM
                clima cli $join
            WHERE
                cli.iddispositivo = :iddispositivo ";
        $parametros=array(':iddispositivo'=>$iddispositivo);

        if($fechaini!=""){
            $sql.=" AND DATE(cli.fecha)>=:fechaini ";
            $parametros[':fechaini'] = $fechaini;
        } 

        if($fechafin!=""){
            $sql.=" AND DATE(cli.fecha)<=:fechafin ";
            $parametros[':fechafin'] = $fechafin;
        }

        if($frecuencia=='D'){
            $sql.=" GROUP BY DATE(cli.fecha) ";
            $sql.=" ORDER BY DATE(cli.fecha) ASC ";
        }else if($frecuencia=='H'){
            $sql.=" GROUP BY DATE(cli.fecha), HOUR(cli.fecha) ";
            $sql.=" ORDER BY DATE(cli.fecha) ASC, HOUR(cli.fecha) ASC ";
        }else{
            $sql.=" ORDER BY cli.fecha ASC ";
        }


        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre; 
    }


    function consultarDatosSensorPorMes($iddispositivo, $year, $mes){

        $columnas = " MAX(cli.temperatura) as tempmax,
                        MIN(cli.temperatura) as tempmin,
                        ROUND(AVG(cli.temperatura),2) as temppro,
                        MAX(cli.humedad_relativa) as hummax,
                        MIN(cli.humedad_relativa) as hummin,
                        ROUND(AVG(cli.humedad_relativa), 2) as humpro,
                        MAX(cli.velocidad_viento) as vvemax,
                        MIN(cli.velocidad_viento) as vvemin,
                        ROUND(AVG(cli.velocidad_viento),2) as vvepro,
                        SUM(cli.precipitacion) as precipitacion,
                        IFNULL(cal.radiacion_solar,ROUND(calcular_radiacion_diaria(DATE(cli.fecha), cli.iddispositivo),2)) as 'radiacion_solar',
                        DATE(cli.fecha) as fecha,
                        HOUR(cli.fecha) as hora,
                        DATE_FORMAT(cli.fecha,'%Y-%m-%d %H:00:00') as 'fechahora',
                        cli.iddispositivo ";
        

        $sql="SELECT
                $columnas 
            FROM
                clima cli 
                LEFT JOIN clima_calculos cal ON cli.iddispositivo=cal.iddispositivo AND DATE(cli.fecha)=cal.fecha
            WHERE
                cli.iddispositivo = :iddispositivo ";
        $parametros=array(':iddispositivo'=>$iddispositivo);

        if($year!=""){
            $sql.=" AND YEAR(cli.fecha)=:year ";
            $parametros[':year'] = $year;
        } 

        if($mes!=""){
            $sql.=" AND MONTH(cli.fecha)=:mes ";
            $parametros[':mes'] = $mes;
        }

        
        $sql.=" GROUP BY DATE(cli.fecha) ";
        $sql.=" ORDER BY DATE(cli.fecha) ASC";

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre; 
    }


    function consultarDatosSensorPorAnio($iddispositivo, $year){

        $columnas = " MAX( cli.temperatura ) AS tempmax,
                        MIN( cli.temperatura ) AS tempmin,
                        ROUND( AVG( cli.temperatura ), 2 ) AS temppro,
                        MAX( cli.humedad_relativa ) AS hummax,
                        MIN( cli.humedad_relativa ) AS hummin,
                        ROUND( AVG( cli.humedad_relativa ), 2 ) AS humpro,
                        MAX( cli.velocidad_viento ) AS vvemax,
                        MIN( cli.velocidad_viento ) AS vvemin,
                        ROUND( AVG( cli.velocidad_viento ), 2 ) AS vvepro,
                        SUM( cli.precipitacion ) AS precipitacion,
                        IFNULL(tbx.radiacion_solar,0) as radiacion_solar,
                        YEAR ( cli.fecha ) AS anio,
                        MONTH ( cli.fecha ) AS mes,
                        cli.iddispositivo ";
        

        $sql="SELECT
                $columnas 
            FROM
                clima cli 
                LEFT JOIN (SELECT YEAR(fecha) as anio, MONTH(fecha) as mes, SUM(radiacion_solar) as radiacion_solar, iddispositivo FROM clima_calculos WHERE YEAR(fecha)=:year GROUP BY YEAR(fecha), MONTH(fecha) ORDER BY YEAR(fecha) ASC, MONTH(fecha) ASC) tbx ON cli.iddispositivo=tbx.iddispositivo AND YEAR ( cli.fecha ) = tbx.anio AND MONTH ( cli.fecha ) = tbx.mes
            WHERE
                cli.iddispositivo = :iddispositivo AND YEAR(cli.fecha)=:year ";
        $parametros=array(':iddispositivo'=>$iddispositivo, ':year'=>$year); 
        
        $sql.=" GROUP BY YEAR ( cli.fecha ), MONTH ( cli.fecha )  ";
        $sql.=" ORDER BY YEAR ( cli.fecha ) ASC, MONTH ( cli.fecha ) ASC ";

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre; 
    }

    function consultarDatosSensorUltimoEnvio($iddispositivo){
        $sql="SELECT *, DATE_FORMAT(fecha, '%d/%m/%y') as 'dia', DATE_FORMAT(fecha, '%H:%i:%s') as 'hora'/*, ROUND(calcular_radiacion_diaria(DATE(fecha), iddispositivo),2) as 'radiacion_acumulada'*/ FROM clima WHERE iddispositivo=:iddispositivo ORDER BY fecha DESC LIMIT 1";
        $parametros = array(':iddispositivo'=>$iddispositivo);

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre; 
    }

    function consultarDatosSensorPorDiaDavis($iddispositivo, $fechaini, $fechafin, $frecuencia){

        $columnas = " * ";
        $join = "";
        if($frecuencia=='H'){
            $columnas = " MAX(cli.temp_out) as tempmax,
                            MIN(cli.temp_out) as tempmin,
                            ROUND(AVG(cli.temp_out),2) as temppro,
                            MAX(cli.hum_out) as hummax,
                            MIN(cli.hum_out) as hummin,
                            ROUND(AVG(cli.hum_out), 2) as humpro,
                            MAX(cli.wind_speed) as vvemax,
                            MIN(cli.wind_speed) as vvemin,
                            ROUND(AVG(cli.wind_speed),2) as vvepro,
                            SUM(cli.rain) as precipitacion,
                            ROUND(AVG(cli.solar_rad),2) as 'radiacion_solar',
                            DATE(cli.fecha) as fecha,
                            HOUR(cli.fecha) as hora,
                            DATE_FORMAT(cli.fecha,'%Y-%m-%d %H:00:00') as 'fechahora',
                            DATE_FORMAT(cli.fecha,'%d/%m/%Y %H:00:00') as 'fecharepor',
                            cli.iddispositivo ";
        }else if($frecuencia=='D'){
            $columnas = " MAX(cli.temp_out) as tempmax,
                            MIN(cli.temp_out) as tempmin,
                            ROUND(AVG(cli.temp_out),2) as temppro,
                            MAX(cli.hum_out) as hummax,
                            MIN(cli.hum_out) as hummin,
                            ROUND(AVG(cli.hum_out), 2) as humpro,
                            MAX(cli.wind_speed) as vvemax,
                            MIN(cli.wind_speed) as vvemin,
                            ROUND(AVG(cli.wind_speed),2) as vvepro,
                            SUM(cli.rain) as precipitacion,
                            IFNULL(cal.radiacion_solar,ROUND(calcular_radiacion_diaria_davis(DATE(cli.fecha), cli.iddispositivo),2)) as 'radiacion_solar',
                            DATE(cli.fecha) as fecha,
                            HOUR(cli.fecha) as hora,
                            DATE_FORMAT(cli.fecha,'%Y-%m-%d %H:00:00') as 'fechahora',
                            DATE_FORMAT(cli.fecha,'%d/%m/%Y') as 'fecharepor',
                            cli.iddispositivo ";
            $join = " LEFT JOIN clima_calculos cal ON cli.iddispositivo=cal.iddispositivo AND DATE(cli.fecha)=cal.fecha ";
        }

        $sql="SELECT
                $columnas 
            FROM
                clima_davis cli $join
            WHERE
                cli.iddispositivo = :iddispositivo ";
        $parametros=array(':iddispositivo'=>$iddispositivo);

        if($fechaini!=""){
            $sql.=" AND DATE(cli.fecha)>=:fechaini ";
            $parametros[':fechaini'] = $fechaini;
        } 

        if($fechafin!=""){
            $sql.=" AND DATE(cli.fecha)<=:fechafin ";
            $parametros[':fechafin'] = $fechafin;
        }

        if($frecuencia=='D'){
            $sql.=" GROUP BY DATE(cli.fecha) ";
            $sql.=" ORDER BY DATE(cli.fecha) ASC ";
        }else if($frecuencia=='H'){
            $sql.=" GROUP BY DATE(cli.fecha), HOUR(cli.fecha) ";
            $sql.=" ORDER BY DATE(cli.fecha) ASC, HOUR(cli.fecha) ASC ";
        }else{
            $sql.=" ORDER BY cli.fecha ASC ";
        }


        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre; 
    }

    function consultarDatosSensorPorMesDavis($iddispositivo, $year, $mes){

        $columnas = " MAX(cli.temp_out) as tempmax,
                        MIN(cli.temp_out) as tempmin,
                        ROUND(AVG(cli.temp_out),2) as temppro,
                        MAX(cli.hum_out) as hummax,
                        MIN(cli.hum_out) as hummin,
                        ROUND(AVG(cli.hum_out), 2) as humpro,
                        MAX(cli.wind_speed) as vvemax,
                        MIN(cli.wind_speed) as vvemin,
                        ROUND(AVG(cli.wind_speed),2) as vvepro,
                        SUM(cli.rain) as precipitacion,
                        IFNULL(cal.radiacion_solar,ROUND(calcular_radiacion_diaria_davis(DATE(cli.fecha), cli.iddispositivo),2)) as 'radiacion_solar',
                        DATE(cli.fecha) as fecha,
                        HOUR(cli.fecha) as hora,
                        DATE_FORMAT(cli.fecha,'%Y-%m-%d %H:00:00') as 'fechahora',
                        cli.iddispositivo ";
        

        $sql="SELECT
                $columnas 
            FROM
                clima_davis cli 
                LEFT JOIN clima_calculos cal ON cli.iddispositivo=cal.iddispositivo AND DATE(cli.fecha)=cal.fecha
            WHERE
                cli.iddispositivo = :iddispositivo ";
        $parametros=array(':iddispositivo'=>$iddispositivo);

        if($year!=""){
            $sql.=" AND YEAR(cli.fecha)=:year ";
            $parametros[':year'] = $year;
        } 

        if($mes!=""){
            $sql.=" AND MONTH(cli.fecha)=:mes ";
            $parametros[':mes'] = $mes;
        }

        
        $sql.=" GROUP BY DATE(cli.fecha) ";
        $sql.=" ORDER BY DATE(cli.fecha) ASC";

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre; 
    }

    function consultarDatosSensorPorAnioDavis($iddispositivo, $year){

        $columnas = " MAX( cli.temp_out ) AS tempmax,
                        MIN( cli.temp_out ) AS tempmin,
                        ROUND( AVG( cli.temp_out ), 2 ) AS temppro,
                        MAX( cli.hum_out ) AS hummax,
                        MIN( cli.hum_out ) AS hummin,
                        ROUND( AVG( cli.hum_out ), 2 ) AS humpro,
                        MAX( cli.wind_speed ) AS vvemax,
                        MIN( cli.wind_speed ) AS vvemin,
                        ROUND( AVG( cli.wind_speed ), 2 ) AS vvepro,
                        SUM( cli.rain ) AS precipitacion,
                        IFNULL(tbx.radiacion_solar,0) as radiacion_solar,
                        YEAR ( cli.fecha ) AS anio,
                        MONTH ( cli.fecha ) AS mes,
                        cli.iddispositivo ";
        

        $sql="SELECT
                $columnas 
            FROM
                clima_davis cli 
                LEFT JOIN (SELECT YEAR(fecha) as anio, MONTH(fecha) as mes, SUM(radiacion_solar) as radiacion_solar, iddispositivo FROM clima_calculos WHERE YEAR(fecha)=:year GROUP BY YEAR(fecha), MONTH(fecha) ORDER BY YEAR(fecha) ASC, MONTH(fecha) ASC) tbx ON cli.iddispositivo=tbx.iddispositivo AND YEAR ( cli.fecha ) = tbx.anio AND MONTH ( cli.fecha ) = tbx.mes
            WHERE
                cli.iddispositivo = :iddispositivo AND YEAR(cli.fecha)=:year ";
        $parametros=array(':iddispositivo'=>$iddispositivo, ':year'=>$year); 
        
        $sql.=" GROUP BY YEAR ( cli.fecha ), MONTH ( cli.fecha )  ";
        $sql.=" ORDER BY YEAR ( cli.fecha ) ASC, MONTH ( cli.fecha ) ASC ";

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre; 
    }

    function consultarDatosSensorUltimoEnvioDavis($iddispositivo){
        $sql="SELECT temp_out as temperatura, hum_out as humedad_relativa, wind_speed as velocidad_viento, wind_dir as direccion_viento, rain as precipitacion, solar_rad as radiacion_solar, DATE_FORMAT(fecha, '%d/%m/%y') as 'dia', DATE_FORMAT(fecha, '%H:%i:%s') as 'hora'/*, ROUND(calcular_radiacion_diaria_davis(DATE(fecha), iddispositivo),2) as 'radiacion_acumulada'*/ FROM clima_davis WHERE iddispositivo=:iddispositivo ORDER BY fecha DESC LIMIT 1";
        $parametros = array(':iddispositivo'=>$iddispositivo);

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre; 
    }
}
?>