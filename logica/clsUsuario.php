<?php 
require_once("cado.php");
class clsUsuario{

	function consultarAcceso($login,$pass){
		$sql="SELECT u.idusuario,u.idpersona, u.idperfil, pf.descripcion as perfil, u.login, u.foto, TRIM(CONCAT(p.nombres,' ',p.apellidos)) as persona, p.nombres, p.nro_documento, u.clave FROM usuario u INNER JOIN persona p on p.idpersona=u.idpersona INNER JOIN perfil pf ON u.idperfil=pf.idperfil WHERE u.login= :login and u.password=SHA1( :pass ) and u.estado='N'";
		global $cnx;
		$pre=$cnx->prepare($sql);		
		$pre->execute(array(':login'=>$login, ':pass'=>$pass));
		return $pre;
	}
  
    function consultarAccesoInstitucionSucursal($idperfil){
        $sql="SELECT t1.idsucursal, t1.idinstitucion, t2.nombre sucursal, t3.nombre institucion, 
        		t3.parafact, t3.defectofact, t3.ruc 
                FROM mgperfilsucursal t1 
                INNER JOIN mgsucursal t2 on t1.idsucursal=t2.idsucursal
                INNER JOIN mginstitucion t3 on t1.idinstitucion=t3.idinstitucion
                WHERE idperfil=:idperfil and t1.estado='N' AND t2.estado='N' AND t3.estado='N' ";
        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute(array(':idperfil'=>$idperfil));
        return $pre;        
    }

    function actualizarVisitas($idusuario){
		$sql="UPDATE usuario SET visitas=ifnull(visitas,0)+1 WHERE idusuario=:idusuario";
		global $cnx;
		$pre=$cnx->prepare($sql);
		$pre->execute(array(':idusuario'=>$idusuario));
		return $pre;
	}

	function consultarOpciones($idperfil){
        $sql="SELECT op.title, op.descripcion, op.link, op2.descripcion as principal, op2.idopcion as idprincipal, op.idopcion, op2.icon as iconprincipal, op.icon, op.idinstitucion, op.idsucursal, op.accesodirecto, op.tituloaccesodirecto FROM acceso a INNER JOIN opcion op on a.idopcion=op.idopcion INNER JOIN opcion op2 on op.idopcion_ref=op2.idopcion WHERE a.idperfil= :idperfil and a.estado='N' and op.estado='N' and op2.estado='N' ORDER BY op2.orden asc, op.idopcion_ref asc, op.orden asc";
		global $cnx;
		$pre=$cnx->prepare($sql);		
		$pre->execute(array(':idperfil'=>$idperfil));
		return $pre;
	}

    function consultarUsuario($filtro, $idperfil, $estado, $inicio, $cantidad, $total=false, $idusuario=0){
        $sql = "SELECT
                u.idusuario,
                u.login,
                CONCAT_WS( ' ', p.nombres, p.apellidos ) AS persona,
                pf.descripcion AS perfil,
                u.estado,
                u.clave ";

        if($total){
            $sql = "SELECT COUNT(u.idusuario) ";
        }

        $sql.="FROM
                usuario u
                INNER JOIN persona p ON u.idpersona = p.idpersona
                LEFT JOIN perfil pf ON u.idperfil = pf.idperfil AND pf.estado <> 'E'
            WHERE
                u.estado <> 'E' 
                AND p.estado <> 'E' 
                AND ( login LIKE :descripcion OR CONCAT_WS( ' ', p.nombres, p.apellidos ) LIKE :descripcion)";
        $parametros[':descripcion']='%'.$filtro.'%';
        if($idperfil != "0"){
            $sql.=" AND u.idperfil=:idperfil"; 
            $parametros[':idperfil']=$idperfil;
        }

        if($estado != "0"){
            $sql.=" AND u.estado=:estado";
            $parametros[':estado']=$estado;
        }
        
        if($idusuario!=1){
            $sql.=" AND u.idusuario<>1 ";
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

    function verificarUsuario($login, $idusuario=0){
        $sql="SELECT * FROM usuario WHERE UPPER(login)=UPPER(:login) AND estado<>'E' AND idusuario<>:idusuario ";
        $parametros=array(':login'=>$login, ':idusuario'=>$idusuario);

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre;    
    }

    function registrarUsuario($nombre, $login, $password, $idpersona, $idperfil, $foto){
        $sql="INSERT INTO usuario VALUES (NULL, :nombre, :login, SHA1(:password), :idpersona, :idperfil, :password, 0, :foto, 'N')";
        $parametros=array(':login'=>$login, ':password'=>$password, ':idpersona'=>$idpersona, ':idperfil'=>$idperfil, ':nombre'=>$nombre, ':foto'=>$foto);

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre;
    }

    function actualizarUsuario($idusuario, $nombre, $idpersona, $idperfil, $login, $password, $foto){
        $sql=" UPDATE usuario SET login=:login, idpersona=:idpersona, idperfil=:idperfil, nombre=:nombre ";
        $parametros=array(':idusuario'=>$idusuario,':login'=>$login, ':nombre'=>$nombre, ':idpersona'=>$idpersona,':idperfil'=>$idperfil);
        if(trim($password)!=""){
            $sql.=" ,password = SHA1(:password), clave= :password ";
            $parametros[':password']=$password;
        }
        if(trim($foto)!=""){
            $sql.=" ,foto = :foto ";
            $parametros[':foto']=$foto;
        }
        $sql.=" WHERE idusuario=:idusuario";

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre;
    }

    function consultarUsuarioById($idusuario){
        $sql="SELECT us.idusuario, pe.idpersona, pe.apellidos, pe.nombres, pe.tipo_documento, pe.nro_documento, pe.direccion, us.login, us.idperfil, us.foto, pe.telcelular, pe.email, pe.facebook, pe.escliente, pe.estrabajador, pe.esproveedor FROM usuario us INNER JOIN persona pe ON us.idpersona = pe.idpersona WHERE us.idusuario=:idusuario ";
        $parametros = array(':idusuario'=>$idusuario);

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre;
    }

    function consultarOpcionesPorPerfil($idperfil){
        $sql="SELECT DISTINCT ifnull( a.idacceso,0) as idacceso,op.title, op.descripcion, op.link, op2.descripcion as principal, op2.idopcion as idprincipal, op.idopcion,a.estado FROM opcion op INNER JOIN opcion op2 on op.idopcion_ref=op2.idopcion LEFT JOIN acceso a ON a.idopcion=op.idopcion AND a.idperfil=:idperfil WHERE op.estado<>'E' ";
           if ($_SESSION['idperfil'] != 1) {
              $sql.=" AND op.idopcion IN (SELECT DISTINCT idopcion FROM acceso WHERE idperfil<>1) ";
           }
           $sql .=" ORDER BY principal ASC, op.orden ASC";
        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute(array(':idperfil'=>$idperfil));
        return $pre;
    }

    function registrarAcceso($idperfil, $idopcion, $estado){
        $sql="INSERT INTO acceso VALUES (NULL, :idperfil, :idopcion, :estado)";    
        $parametros=array(':idperfil'=>$idperfil,':idopcion'=>$idopcion,':estado'=>$estado);

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre;
    }

    function quitarAcceso($idperfil, $idopcion){
        $sql="DELETE FROM acceso WHERE idperfil=:idperfil AND  idopcion=:idopcion";    
        $parametros=array(':idperfil'=>$idperfil,':idopcion'=>$idopcion);

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre;
    }

    function verificarUsuarioClave($iduser, $idpersona, $password){
        $sql="SELECT * FROM usuario WHERE estado='N' and idusuario=:idusuario and idpersona=:idpersona and 
              password=SHA1(:password)";

        global $cnx;
        $pre=$cnx->prepare($sql);       
        $pre->execute(array(':idusuario'=>$iduser,':idpersona'=>$idpersona,':password'=>$password));
        return $pre;
    }
        
    function actualizarClaveUsuario($iduser, $idpersona, $password){
        $sql="UPDATE usuario SET password=SHA1(:password), clave=:password WHERE estado='N' and idusuario=:idusuario and idpersona=:idpersona";

        global $cnx;
        $pre=$cnx->prepare($sql);       
        $pre->execute(array(':idusuario'=>$iduser,':idpersona'=>$idpersona,':password'=>$password));
        return $pre;
    }

    function consultaPerfilSucursal($idperfil){
        $sql="SELECT IFNULL( ps.idconfiguracion,0) idconfiguracion , ps.estado,ps.idperfil, s.idsucursal, s.nombre sucursal, s.idinstitucion, i.nombre institucion 
        FROM mgsucursal s
        INNER JOIN mginstitucion i on s.idinstitucion=i.idinstitucion
        LEFT JOIN  mgperfilsucursal ps on s.idsucursal=ps.idsucursal AND ps.idperfil=?  
        WHERE s.estado = 'N'";
           
       global $cnx;
       $pre=$cnx->prepare($sql);        
       $pre->execute(array($idperfil));
       return $pre;
    }

    function registrarAccesoSucursal($idperfil, $idinstitucion, $idsucursal, $estado){
        $sql="INSERT INTO mgperfilsucursal VALUES (NULL, :idperfil, :idinstitucion, :idsucursal, :estado)";    
        $parametros=array(':idperfil'=>$idperfil,':idinstitucion'=>$idinstitucion, ':idsucursal'=>$idsucursal,':estado'=>$estado);

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre;
    }

    function quitarAccesoSucursal($idperfil, $idinstitucion, $idsucursal){
        $sql="DELETE FROM mgperfilsucursal WHERE idperfil=:idperfil AND  idinstitucion=:idinstitucion AND idsucursal=:idsucursal";    
        $parametros=array(':idperfil'=>$idperfil,':idinstitucion'=>$idinstitucion, ':idsucursal'=>$idsucursal);

        global $cnx;
        $pre=$cnx->prepare($sql);
        $pre->execute($parametros);
        return $pre;
    }
}
?>