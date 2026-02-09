<?php 
require_once("cado.php");

class clsSesion{

	function insertarSesion($idusuario, $idperfil){
		$sql="INSERT INTO sissesion(idsesion,idusuario,idperfil,fechahora) VALUES(NULL,:idusuario, :idperfil, NOW()) ";				
		global $cnx;
		$pre = $cnx -> prepare($sql);
		$pre->execute(array(':idusuario'=>$idusuario,':idperfil'=>$idperfil));
		return $pre;
	}

	function listaSesion($idusuario, $idperfil){
		$sql="SELECT idsesion, idusuario, idperfil, fechahora FROM sissesion WHERE DATE(fechahora)=CURDATE() AND idusuario=:idusuario AND idperfil=:idperfil ";
		global $cnx;
		$pre = $cnx -> prepare($sql);
		$pre->execute(array(':idusuario'=>$idusuario,':idperfil'=>$idperfil));
		return $pre;
	}
}
?>