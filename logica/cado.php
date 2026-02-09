<?php
ini_set('memory_limit', '1024M'); //Raise to 512 MB
ini_set('max_execution_time', '60000'); //Raise to 512 MB
ini_set('max_input_vars', '60000'); //Raise to 512 MB
@session_start();

date_default_timezone_set("America/Lima");
$verificar = true;
if(isset($_POST['accion'])){
	if($_POST['accion']=="INGRESAR"){
            if(!isset($_SESSION['idusuario'])){
                $_SESSION['idusuario']=0;
            }
		$verificar=false;
	}
}

if($verificar){
	if(!isset($_SESSION['idusuario'])){
		if(isset($_POST['accion']) || isset($_GET['ajax'])){
			echo "%\$u\$V\$%+cLA SESION HA EXPIRADO, INGRESE NUEVAMENTE AL SISTEMA.";	
		}else{
			echo "<META HTTP-EQUIV=Refresh CONTENT='0;URL= index.php'>";
		}		
		exit;
	}
}

try{

if(!isset($_SESSION['config_taq'])){
	$config = file_get_contents("../config/.taq");
	$config = json_decode($config);
	$_SESSION['config_taq'] = $config;
}

ini_set("display_errors", $_SESSION['config_taq']->local->display_errors);
$manejador = "mysql";
$servidor = $_SESSION['config_taq']->local->server;
$usuario = $_SESSION['config_taq']->local->user;
$pass = $_SESSION['config_taq']->local->pass;
$base = $_SESSION['config_taq']->central->bd;
$cadena = "$manejador:host=$servidor;dbname=$base";

global $cnx;
$cnx = new PDO($cadena, $usuario, $pass, array(PDO::ATTR_PERSISTENT => "true", PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
// mail
$mail_config = array(
					'SMTPAuth'   => $_SESSION['config_taq']->mail->SMTPAuth,
					'SMTPSecure' => $_SESSION['config_taq']->mail->SMTPSecure,
					'Host' => $_SESSION['config_taq']->mail->Host,
					'Port' => $_SESSION['config_taq']->mail->Port,
					'Username' => $_SESSION['config_taq']->mail->Username,
					'Password' => $_SESSION['config_taq']->mail->Password,
					'From'=>$_SESSION['config_taq']->mail->From,
					'From2'=>$_SESSION['config_taq']->mail->From2,
					'Subject' => $_SESSION['config_taq']->mail->Subject
);
// fin mail
}catch(Exception $ex){
	echo "Error de acceso, inf&oacute;rmelo a la brevedad.";
	exit;
}
?>