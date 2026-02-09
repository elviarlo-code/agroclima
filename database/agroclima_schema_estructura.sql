-- phpMyAdmin SQL Dump
-- version 4.0.10.14
-- http://www.phpmyadmin.net
--
-- Servidor: localhost:3306
-- Tiempo de generación: 09-02-2026 a las 00:42:57
-- Versión del servidor: 5.5.52-cll
-- Versión de PHP: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `riteccom_agroinversionesolmos`
--

DELIMITER $$
--
-- Procedimientos
--
$$

--
-- Funciones
--
$$

$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acceso`
--

CREATE TABLE IF NOT EXISTS `acceso` (
  `idacceso` int(11) NOT NULL AUTO_INCREMENT,
  `idperfil` int(11) NOT NULL,
  `idopcion` int(11) NOT NULL,
  `estado` char(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (`idacceso`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=47 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alarticulo`
--

CREATE TABLE IF NOT EXISTS `alarticulo` (
  `idarticulo` int(255) NOT NULL AUTO_INCREMENT,
  `codigobarra` varchar(50) DEFAULT NULL,
  `nro_orden` decimal(10,2) DEFAULT NULL,
  `idcategoria` int(11) DEFAULT NULL,
  `idsubcategoria` int(11) DEFAULT NULL,
  `idmarca` int(11) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `tipo` char(1) DEFAULT NULL,
  `unidad` varchar(50) DEFAULT NULL,
  `solocantidadentera` smallint(6) DEFAULT NULL,
  `stock` char(1) DEFAULT NULL,
  `stockmin` decimal(10,2) DEFAULT NULL,
  `totalstock` decimal(15,2) DEFAULT NULL,
  `series` char(1) DEFAULT NULL,
  `lotes` char(1) DEFAULT NULL,
  `cantidad_serie` int(4) DEFAULT NULL,
  `nombre_serie` varchar(200) DEFAULT NULL,
  `codigointerno` varchar(50) DEFAULT NULL,
  `pcompra` decimal(15,5) DEFAULT NULL,
  `pcosto` decimal(15,5) DEFAULT NULL,
  `pventa` decimal(15,5) DEFAULT NULL,
  `pminimo` decimal(15,5) DEFAULT NULL,
  `pmaximo` decimal(15,5) DEFAULT NULL,
  `afectoigv` char(1) DEFAULT NULL,
  `afecto_detraccion` smallint(6) DEFAULT NULL,
  `percepcion` char(1) DEFAULT NULL,
  `condocumento` char(1) DEFAULT NULL,
  `pesokg` decimal(10,5) DEFAULT NULL,
  `sevendeme` char(1) DEFAULT NULL,
  `pcomprame` decimal(10,5) DEFAULT NULL,
  `pcostome` decimal(10,5) DEFAULT NULL,
  `pventame` decimal(10,5) DEFAULT NULL,
  `pminimome` decimal(10,5) DEFAULT NULL,
  `pmaximome` decimal(10,5) DEFAULT NULL,
  `codsunatsegmento` int(11) DEFAULT NULL,
  `codsunatfamilia` int(11) DEFAULT NULL,
  `codsunatclase` int(11) DEFAULT NULL,
  `codsunatproducto` int(11) DEFAULT NULL,
  `multipleunidad` char(1) DEFAULT NULL,
  `icbper_afecto` smallint(1) DEFAULT NULL,
  `tiposunat` varchar(10) DEFAULT NULL,
  `tiposunat_otro` varchar(255) DEFAULT NULL,
  `idcategoria_contable` int(11) DEFAULT '0',
  `fhregistro` datetime DEFAULT NULL,
  `idpersonaregistro` int(11) DEFAULT NULL,
  `fheditar` datetime DEFAULT NULL,
  `idpersonaeditar` int(11) DEFAULT NULL,
  `fheliminar` datetime DEFAULT NULL,
  `idpersonaeliminar` int(11) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`idarticulo`) USING BTREE,
  KEY `index_alarticulo_codigobarra` (`codigobarra`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alarticulo_imagen`
--

CREATE TABLE IF NOT EXISTS `alarticulo_imagen` (
  `idarticuloimagen` int(11) NOT NULL AUTO_INCREMENT,
  `idarticulo` int(11) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `orden` int(11) DEFAULT NULL,
  `pordefecto` smallint(6) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`idarticuloimagen`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alarticulo_propiedad`
--

CREATE TABLE IF NOT EXISTS `alarticulo_propiedad` (
  `idarticulopropiedad` int(11) NOT NULL AUTO_INCREMENT,
  `idarticulo` int(11) DEFAULT NULL,
  `idpropiedad` int(11) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`idarticulopropiedad`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alarticulo_unidad`
--

CREATE TABLE IF NOT EXISTS `alarticulo_unidad` (
  `idarticulounidad` int(11) NOT NULL AUTO_INCREMENT,
  `idarticulo` int(11) DEFAULT NULL,
  `unidad` varchar(50) DEFAULT NULL,
  `factor` decimal(10,2) DEFAULT NULL,
  `pventa` decimal(15,5) DEFAULT NULL,
  `pminimo` decimal(15,5) DEFAULT NULL,
  `pventame` decimal(15,5) DEFAULT NULL,
  `pminimome` decimal(15,5) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`idarticulounidad`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alcategoria`
--

CREATE TABLE IF NOT EXISTS `alcategoria` (
  `idcategoria` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(200) NOT NULL,
  `observacion` text,
  `nro_orden` decimal(10,2) DEFAULT NULL,
  `estado` char(1) NOT NULL,
  PRIMARY KEY (`idcategoria`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alcombo`
--

CREATE TABLE IF NOT EXISTS `alcombo` (
  `idcombo` int(11) NOT NULL AUTO_INCREMENT,
  `idarticulo` int(11) NOT NULL,
  `idcomponente` int(11) DEFAULT NULL,
  `cantidad` decimal(10,2) DEFAULT NULL,
  `alterno` char(2) DEFAULT NULL,
  `mostrar` char(2) DEFAULT NULL,
  `tipo` char(1) DEFAULT NULL,
  `fhregistro` datetime DEFAULT NULL,
  `idpersonaregistro` int(11) DEFAULT NULL,
  `fheditar` datetime DEFAULT NULL,
  `idpersonaeditar` int(11) DEFAULT NULL,
  `fheliminar` datetime DEFAULT NULL,
  `idpersonaeliminar` int(11) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`idcombo`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `almarca`
--

CREATE TABLE IF NOT EXISTS `almarca` (
  `idmarca` int(255) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(200) NOT NULL,
  `estado` char(1) NOT NULL,
  PRIMARY KEY (`idmarca`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alpropiedad`
--

CREATE TABLE IF NOT EXISTS `alpropiedad` (
  `idpropiedad` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(4) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `observacion` varchar(255) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`idpropiedad`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alsubcategoria`
--

CREATE TABLE IF NOT EXISTS `alsubcategoria` (
  `idsubcategoria` int(11) NOT NULL AUTO_INCREMENT,
  `idcategoria` int(11) DEFAULT NULL,
  `descripcion` varchar(200) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`idsubcategoria`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campania`
--

CREATE TABLE IF NOT EXISTS `campania` (
  `idcampania` int(11) NOT NULL AUTO_INCREMENT,
  `fechaini` date DEFAULT NULL,
  `fechasiembra` date DEFAULT NULL,
  `fechafin` date DEFAULT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  `finalizado` smallint(6) DEFAULT NULL,
  `idcultivo` int(11) DEFAULT NULL,
  `idturno` int(11) DEFAULT NULL,
  `idesquema` int(11) DEFAULT NULL,
  `idterreno` int(11) DEFAULT NULL,
  `iddispositivo` int(11) DEFAULT NULL,
  `fhregistro` datetime DEFAULT NULL,
  `idpersonaregistro` int(11) DEFAULT NULL,
  `fheditar` datetime DEFAULT NULL,
  `idpersonaeditar` int(11) DEFAULT NULL,
  `fheliminar` datetime DEFAULT NULL,
  `idpersonaeliminar` int(11) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`idcampania`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campania_fenologia`
--

CREATE TABLE IF NOT EXISTS `campania_fenologia` (
  `idfenologia` int(11) NOT NULL AUTO_INCREMENT,
  `orden` int(11) DEFAULT NULL,
  `nombre` varchar(200) DEFAULT NULL,
  `duracion` int(11) DEFAULT NULL,
  `kc` decimal(10,3) DEFAULT NULL,
  `raiz` decimal(10,2) DEFAULT NULL,
  `cobertura` decimal(10,2) DEFAULT NULL,
  `umbral` decimal(10,2) DEFAULT NULL,
  `temp_min` decimal(10,2) DEFAULT NULL,
  `temp_max` decimal(10,2) DEFAULT NULL,
  `humd_min` decimal(10,2) DEFAULT NULL,
  `humd_max` decimal(10,2) DEFAULT NULL,
  `idcultivo` int(11) DEFAULT NULL,
  `idcampania` int(11) DEFAULT NULL,
  `fhregistro` datetime DEFAULT NULL,
  `idpersonaregistro` int(11) DEFAULT NULL,
  `fheditar` datetime DEFAULT NULL,
  `idpersonaeditar` int(11) DEFAULT NULL,
  `fheliminar` datetime DEFAULT NULL,
  `idpersonaeliminar` int(11) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`idfenologia`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clima`
--

CREATE TABLE IF NOT EXISTS `clima` (
  `idclima` int(11) NOT NULL AUTO_INCREMENT,
  `temperatura` decimal(10,2) NOT NULL,
  `humedad_relativa` decimal(10,2) NOT NULL,
  `direccion_viento` int(10) DEFAULT NULL,
  `puntos_cardinales` varchar(10) CHARACTER SET utf8 COLLATE utf8_spanish2_ci DEFAULT NULL,
  `velocidad_viento` decimal(10,2) DEFAULT NULL,
  `radiacion_solar` decimal(10,2) DEFAULT NULL,
  `precipitacion` decimal(10,2) DEFAULT NULL,
  `uv` decimal(10,2) DEFAULT NULL,
  `altitud` decimal(10,2) DEFAULT NULL,
  `nivel` decimal(10,2) DEFAULT NULL,
  `bateria` int(4) DEFAULT NULL COMMENT 'Nivel de bateria (0 a 1024)',
  `cobertura` int(1) DEFAULT NULL COMMENT 'Cobertura GSM (1 a 4)',
  `fecha` datetime NOT NULL,
  `iddispositivo` int(11) NOT NULL,
  PRIMARY KEY (`idclima`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='Datos de los sensores que se recibiran cada 24hrs' AUTO_INCREMENT=238411 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clima_calculos`
--

CREATE TABLE IF NOT EXISTS `clima_calculos` (
  `idcalculos` int(11) NOT NULL AUTO_INCREMENT,
  `radiacion_solar` double DEFAULT NULL,
  `eto` double DEFAULT NULL,
  `epan` double DEFAULT '0',
  `eto_epan` double DEFAULT '0',
  `fecha` date NOT NULL,
  `iddispositivo` int(11) NOT NULL,
  PRIMARY KEY (`idcalculos`) USING BTREE,
  UNIQUE KEY `fecha` (`fecha`,`iddispositivo`) USING BTREE,
  KEY `fk_calculos_dispositivo1_idx` (`iddispositivo`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci ROW_FORMAT=COMPACT COMMENT='Datos calculados de los sensores que se recibiran cada 24hrs' AUTO_INCREMENT=1948 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clima_davis`
--

CREATE TABLE IF NOT EXISTS `clima_davis` (
  `idclima` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` datetime DEFAULT NULL,
  `temp_out` decimal(10,2) DEFAULT NULL,
  `temp_hi` decimal(10,2) DEFAULT NULL,
  `temp_low` decimal(10,2) DEFAULT NULL,
  `hum_out` decimal(10,2) DEFAULT NULL,
  `dew_pt` decimal(10,2) DEFAULT NULL,
  `wind_speed` decimal(10,2) DEFAULT NULL,
  `wind_dir` varchar(10) DEFAULT NULL,
  `wind_run` decimal(10,2) DEFAULT NULL,
  `hi_speed` decimal(10,2) DEFAULT NULL,
  `hi_dir` varchar(10) DEFAULT NULL,
  `wind_chill` decimal(10,2) DEFAULT NULL,
  `heat_index` decimal(10,2) DEFAULT NULL,
  `thw_index` decimal(10,2) DEFAULT NULL,
  `thsw_index` decimal(10,2) DEFAULT NULL,
  `bar` decimal(10,2) DEFAULT NULL,
  `rain` decimal(10,2) DEFAULT NULL,
  `rain_rate` decimal(10,2) DEFAULT NULL,
  `solar_rad` decimal(10,2) DEFAULT NULL,
  `solar_energy` decimal(10,2) DEFAULT NULL,
  `hi_solar_rad` decimal(10,2) DEFAULT NULL,
  `uv_index` decimal(10,2) DEFAULT NULL,
  `uv_dose` decimal(10,2) DEFAULT NULL,
  `hi_uv` decimal(10,2) DEFAULT NULL,
  `heat_dd` decimal(10,4) DEFAULT NULL,
  `cool_dd` decimal(10,4) DEFAULT NULL,
  `in_temp` decimal(10,2) DEFAULT NULL,
  `in_hum` decimal(10,2) DEFAULT NULL,
  `in_dew` decimal(10,2) DEFAULT NULL,
  `in_heat` decimal(10,2) DEFAULT NULL,
  `in_emc` varchar(10) DEFAULT NULL,
  `in_air_density` decimal(10,4) DEFAULT NULL,
  `et` decimal(10,2) DEFAULT NULL,
  `wind_samp` decimal(10,2) DEFAULT NULL,
  `wind_tx` decimal(10,2) DEFAULT NULL,
  `iss_recept` decimal(10,2) DEFAULT NULL,
  `arc_int` decimal(10,2) DEFAULT NULL,
  `iddispositivo` int(11) DEFAULT NULL,
  `fhregistro` datetime DEFAULT NULL,
  `idpersonaregistro` int(11) DEFAULT NULL,
  PRIMARY KEY (`idclima`) USING BTREE,
  UNIQUE KEY `fecha_id` (`fecha`,`iddispositivo`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=2018 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cultivo`
--

CREATE TABLE IF NOT EXISTS `cultivo` (
  `idcultivo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `variedad` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `altura` decimal(10,2) DEFAULT NULL,
  `raiz_maxima` decimal(10,2) DEFAULT NULL,
  `raiz_minima` decimal(10,2) DEFAULT NULL,
  `periodovegetativo` int(11) DEFAULT NULL,
  `imagen` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `fhregistro` datetime DEFAULT NULL,
  `idpersonaregistro` int(11) DEFAULT NULL,
  `fheditar` datetime DEFAULT NULL,
  `idpersonaeditar` int(11) DEFAULT NULL,
  `fheliminar` datetime DEFAULT NULL,
  `idpersonaeliminar` int(11) DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`idcultivo`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cultivo_fenologia`
--

CREATE TABLE IF NOT EXISTS `cultivo_fenologia` (
  `idfenologia` int(11) NOT NULL AUTO_INCREMENT,
  `orden` int(11) DEFAULT NULL,
  `nombre` varchar(200) DEFAULT NULL,
  `duracion` int(11) DEFAULT NULL,
  `kc` decimal(10,3) DEFAULT NULL,
  `raiz` decimal(10,2) DEFAULT NULL,
  `cobertura` decimal(10,2) DEFAULT NULL,
  `umbral` decimal(10,2) DEFAULT NULL,
  `temp_min` decimal(10,2) DEFAULT NULL,
  `temp_max` decimal(10,2) DEFAULT NULL,
  `humd_min` decimal(10,2) DEFAULT NULL,
  `humd_max` decimal(10,2) DEFAULT NULL,
  `idcultivo` int(11) DEFAULT NULL,
  `fhregistro` datetime DEFAULT NULL,
  `idpersonaregistro` int(11) DEFAULT NULL,
  `fheditar` datetime DEFAULT NULL,
  `idpersonaeditar` int(11) DEFAULT NULL,
  `fheliminar` datetime DEFAULT NULL,
  `idpersonaeliminar` int(11) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`idfenologia`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dashboard`
--

CREATE TABLE IF NOT EXISTS `dashboard` (
  `iddashboard` int(11) NOT NULL AUTO_INCREMENT,
  `link` varchar(255) DEFAULT NULL,
  `script` varchar(255) DEFAULT NULL,
  `contenedorgrafico` varchar(50) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `orden` smallint(6) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`iddashboard`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamento`
--

CREATE TABLE IF NOT EXISTS `departamento` (
  `iddepartamento` char(6) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`iddepartamento`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dispositivo`
--

CREATE TABLE IF NOT EXISTS `dispositivo` (
  `iddispositivo` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(200) DEFAULT NULL,
  `nombre` varchar(200) DEFAULT NULL,
  `tipo` char(10) DEFAULT NULL,
  `latitud` double DEFAULT NULL,
  `longitud` double DEFAULT NULL,
  `altitud` double DEFAULT NULL,
  `ubigeo` varchar(10) DEFAULT NULL,
  `ubigeo_texto` varchar(200) DEFAULT NULL,
  `fhregistro` datetime DEFAULT NULL,
  `idpersonaregistro` int(11) DEFAULT NULL,
  `fheditar` datetime DEFAULT NULL,
  `idpersonaeditar` int(11) DEFAULT NULL,
  `fheliminar` datetime DEFAULT NULL,
  `idpersonaeliminar` int(11) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`iddispositivo`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `distrito`
--

CREATE TABLE IF NOT EXISTS `distrito` (
  `iddistrito` char(6) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `idprovincia` char(6) NOT NULL,
  `nro_orden` decimal(10,2) DEFAULT NULL,
  `idruta` int(11) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`iddistrito`) USING BTREE,
  KEY `idprovincia` (`idprovincia`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `kanban`
--

CREATE TABLE IF NOT EXISTS `kanban` (
  `idkanban` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) DEFAULT NULL,
  `clase` varchar(50) DEFAULT NULL,
  `grid` varchar(25) DEFAULT NULL,
  `orden` int(11) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`idkanban`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `kanban_dashboard`
--

CREATE TABLE IF NOT EXISTS `kanban_dashboard` (
  `idtarjeta` int(11) NOT NULL AUTO_INCREMENT,
  `idkanban` int(11) DEFAULT NULL,
  `iddashboard` int(11) DEFAULT NULL,
  `titulo` varchar(50) DEFAULT NULL,
  `subtitulo` varchar(50) DEFAULT NULL,
  `body` varchar(50) DEFAULT NULL,
  `grafico` varchar(50) DEFAULT NULL,
  `orden` int(11) DEFAULT NULL,
  `idusuario` int(11) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`idtarjeta`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mgalmacen`
--

CREATE TABLE IF NOT EXISTS `mgalmacen` (
  `idalmacen` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(200) DEFAULT NULL,
  `ubigeo` varchar(10) DEFAULT NULL,
  `ubigeo_texto` varchar(200) DEFAULT NULL,
  `direccion` varchar(250) DEFAULT NULL,
  `idinstitucion` int(11) DEFAULT NULL,
  `idsucursal` int(11) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`idalmacen`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mgconfig`
--

CREATE TABLE IF NOT EXISTS `mgconfig` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `idconfig` int(11) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `modulo` char(3) NOT NULL,
  `tipdat` char(1) NOT NULL,
  `longitud` int(11) NOT NULL,
  `valor` varchar(50) NOT NULL,
  `observacion` varchar(100) NOT NULL,
  `idinstitucion` smallint(6) DEFAULT NULL,
  `idsucursal` smallint(6) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`codigo`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mginstitucion`
--

CREATE TABLE IF NOT EXISTS `mginstitucion` (
  `idinstitucion` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `tipodoc` char(1) DEFAULT NULL,
  `ruc` varchar(11) DEFAULT NULL,
  `direccion` text,
  `parafact` smallint(1) DEFAULT NULL,
  `defectofact` smallint(1) DEFAULT NULL,
  `nrodocrepresentante` varchar(8) DEFAULT NULL,
  `nombrerepresentante` varchar(255) DEFAULT NULL,
  `codigo_ubigeo_departamento` char(6) CHARACTER SET latin1 DEFAULT NULL,
  `codigo_ubigeo_provincia` char(6) CHARACTER SET latin1 DEFAULT NULL,
  `codigo_ubigeo_distrito` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `direccion_departamento` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `direccion_provincia` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `direccion_distrito` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `direccion_codigopais` varchar(10) CHARACTER SET latin1 DEFAULT NULL,
  `estado` char(1) NOT NULL,
  PRIMARY KEY (`idinstitucion`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mgperfilsucursal`
--

CREATE TABLE IF NOT EXISTS `mgperfilsucursal` (
  `idconfiguracion` int(11) NOT NULL AUTO_INCREMENT,
  `idperfil` int(11) NOT NULL,
  `idinstitucion` int(11) DEFAULT NULL,
  `idsucursal` int(11) NOT NULL,
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`idconfiguracion`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mgsucursal`
--

CREATE TABLE IF NOT EXISTS `mgsucursal` (
  `idsucursal` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `idinstitucion` int(11) NOT NULL,
  `estado` varchar(100) NOT NULL,
  PRIMARY KEY (`idsucursal`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mgtablageneralc`
--

CREATE TABLE IF NOT EXISTS `mgtablageneralc` (
  `idtablageneral` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) DEFAULT NULL,
  `nro_registro` int(11) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`idtablageneral`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mgtablagenerald`
--

CREATE TABLE IF NOT EXISTS `mgtablagenerald` (
  `iddetalle` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` char(10) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `descripcion1` varchar(255) DEFAULT NULL,
  `descripcion2` varchar(255) DEFAULT NULL,
  `valor1` smallint(6) DEFAULT NULL,
  `valor2` int(11) DEFAULT NULL,
  `factora` varchar(10) DEFAULT NULL,
  `factorb` varchar(10) DEFAULT NULL,
  `factorc` varchar(10) DEFAULT NULL,
  `idtablageneral` int(11) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`iddetalle`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=27 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opcion`
--

CREATE TABLE IF NOT EXISTS `opcion` (
  `idopcion` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) NOT NULL,
  `link` varchar(100) NOT NULL,
  `idopcion_ref` int(11) DEFAULT NULL,
  `orden` int(11) DEFAULT NULL,
  `nro_registro` int(11) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `icon` text,
  `idinstitucion` int(11) DEFAULT NULL,
  `idsucursal` int(11) DEFAULT NULL,
  `tabla` varchar(100) DEFAULT NULL,
  `tabladetalle` varchar(100) DEFAULT NULL,
  `puederegistrar` varchar(100) DEFAULT NULL,
  `puedeeditar` varchar(100) DEFAULT NULL,
  `puedeanular` varchar(100) DEFAULT NULL,
  `puedeeliminar` varchar(100) DEFAULT NULL,
  `puedeimprimir` varchar(100) DEFAULT NULL,
  `opcion_especial` varchar(100) DEFAULT NULL,
  `opcion_especial1` varchar(100) DEFAULT NULL,
  `opcion_especial2` varchar(100) DEFAULT NULL,
  `accesodirecto` smallint(1) DEFAULT NULL,
  `accesodashboard` char(2) DEFAULT NULL COMMENT ' columna para las cajitas del dashboard ',
  `tituloaccesodirecto` varchar(50) DEFAULT NULL,
  `estado` char(1) NOT NULL,
  PRIMARY KEY (`idopcion`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=30 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perfil`
--

CREATE TABLE IF NOT EXISTS `perfil` (
  `idperfil` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) NOT NULL,
  `estado` char(1) NOT NULL,
  PRIMARY KEY (`idperfil`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona`
--

CREATE TABLE IF NOT EXISTS `persona` (
  `idpersona` int(11) NOT NULL AUTO_INCREMENT,
  `apellidos` varchar(100) DEFAULT NULL,
  `nombres` varchar(100) DEFAULT NULL,
  `razon_social` varchar(255) DEFAULT NULL,
  `tipo_documento` char(1) DEFAULT NULL,
  `nro_documento` varchar(20) DEFAULT NULL,
  `email` char(200) DEFAULT NULL,
  `facebook` varchar(200) DEFAULT NULL,
  `medio_comunicacion` varchar(5) DEFAULT NULL,
  `ocupacion` varchar(300) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `sexo` char(1) DEFAULT NULL,
  `ubigeo` varchar(200) DEFAULT NULL,
  `ubigeo_dir_dep` char(6) DEFAULT NULL,
  `ubigeo_dir_pro` char(6) DEFAULT NULL,
  `ubigeo_dir_dis` char(6) DEFAULT NULL,
  `telcelular` varchar(45) DEFAULT NULL,
  `telotro` varchar(45) DEFAULT NULL,
  `fnacimiento` date DEFAULT NULL,
  `credito` decimal(13,2) DEFAULT NULL,
  `escliente` smallint(6) DEFAULT NULL,
  `esproveedor` smallint(6) DEFAULT NULL,
  `estrabajador` smallint(6) DEFAULT NULL,
  `observacion` text,
  `agente_retencion` smallint(6) DEFAULT NULL,
  `idregistrador` int(11) DEFAULT NULL,
  `fhregistro` datetime DEFAULT NULL,
  `idpersonaeditar` int(11) DEFAULT NULL,
  `fheditar` datetime DEFAULT NULL,
  `idpersonaeliminar` int(11) DEFAULT NULL,
  `fheliminar` datetime DEFAULT NULL,
  `estado` char(1) NOT NULL,
  PRIMARY KEY (`idpersona`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `provincia`
--

CREATE TABLE IF NOT EXISTS `provincia` (
  `idprovincia` char(6) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `iddepartamento` char(6) NOT NULL,
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`idprovincia`) USING BTREE,
  KEY `iddepartamento` (`iddepartamento`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sissesion`
--

CREATE TABLE IF NOT EXISTS `sissesion` (
  `idsesion` int(11) NOT NULL AUTO_INCREMENT,
  `idusuario` int(11) NOT NULL,
  `idperfil` int(11) NOT NULL,
  `fechahora` datetime NOT NULL,
  PRIMARY KEY (`idsesion`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=301 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `terreno`
--

CREATE TABLE IF NOT EXISTS `terreno` (
  `idterreno` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(200) COLLATE utf8_spanish2_ci NOT NULL,
  `direccion` varchar(200) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `latitud` double DEFAULT NULL,
  `longitud` double DEFAULT NULL,
  `altitud` double DEFAULT NULL,
  `area` decimal(10,2) DEFAULT NULL,
  `ubigeo` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `ubigeo_texto` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `fhregistro` datetime DEFAULT NULL,
  `idpersonaregistro` int(11) DEFAULT NULL,
  `fheditar` datetime DEFAULT NULL,
  `idpersonaeditar` int(11) DEFAULT NULL,
  `fheliminar` datetime DEFAULT NULL,
  `idpersonaeliminar` int(11) DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`idterreno`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci ROW_FORMAT=COMPACT AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `terreno_esquema`
--

CREATE TABLE IF NOT EXISTS `terreno_esquema` (
  `idesquema` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(125) DEFAULT NULL,
  `activo` smallint(6) DEFAULT NULL,
  `idterreno` int(11) DEFAULT NULL,
  `fhregistro` datetime DEFAULT NULL,
  `idpersonaregistro` int(11) DEFAULT NULL,
  `fheditar` datetime DEFAULT NULL,
  `idpersonaeditar` int(11) DEFAULT NULL,
  `fheliminar` datetime DEFAULT NULL,
  `idpersonaeliminar` int(11) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`idesquema`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `terreno_turno`
--

CREATE TABLE IF NOT EXISTS `terreno_turno` (
  `idturno` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) DEFAULT NULL,
  `area` decimal(10,2) DEFAULT NULL,
  `color` varchar(25) DEFAULT NULL,
  `idesquema` int(11) DEFAULT NULL,
  `idterreno` int(11) DEFAULT NULL,
  `fhregistro` datetime DEFAULT NULL,
  `idpersonaregistro` int(11) DEFAULT NULL,
  `fheditar` datetime DEFAULT NULL,
  `idpersonaeditar` int(11) DEFAULT NULL,
  `fheliminar` datetime DEFAULT NULL,
  `idpersonaeliminar` int(11) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`idturno`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=27 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `terreno_turno_coordenada`
--

CREATE TABLE IF NOT EXISTS `terreno_turno_coordenada` (
  `idcoordenada` int(11) NOT NULL AUTO_INCREMENT,
  `latitud` double DEFAULT NULL,
  `longitud` double DEFAULT NULL,
  `idturno` int(11) DEFAULT NULL,
  `idesquema` int(11) DEFAULT NULL,
  `idterreno` int(11) DEFAULT NULL,
  `fhregistro` datetime DEFAULT NULL,
  `idpersonaregistro` int(11) DEFAULT NULL,
  `estado` char(1) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`idcoordenada`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=245 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
  `idusuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(250) DEFAULT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `idpersona` int(11) NOT NULL,
  `idperfil` int(11) NOT NULL,
  `clave` varchar(50) DEFAULT NULL,
  `visitas` int(11) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`idusuario`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=11 ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `distrito`
--
ALTER TABLE `distrito`
  ADD CONSTRAINT `distrito_ibfk_1` FOREIGN KEY (`idprovincia`) REFERENCES `provincia` (`idprovincia`);

--
-- Filtros para la tabla `provincia`
--
ALTER TABLE `provincia`
  ADD CONSTRAINT `provincia_ibfk_1` FOREIGN KEY (`iddepartamento`) REFERENCES `departamento` (`iddepartamento`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
