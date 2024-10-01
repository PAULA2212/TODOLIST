<?php

require_once "../../config/app.php";
require_once "../views/inc/sesion_start.php";
require_once "../../autoload.php";

use app\controllers\userController;

if(isset($_POST['modulo_usuario'])){
    $insUsuario = new userController();

		if($_POST['modulo_usuario']=="registrar"){
			echo $insUsuario->registrarUsuario();
		}
        if($_POST['modulo_usuario']=="actualizar"){
			echo $insUsuario->actualizarUsuario();
		}
        if($_POST['modulo_usuario']=="eliminar"){
			echo $insUsuario->eliminarUsuario();
		}
        if($_POST['modulo_usuario']=="eliminarFoto"){
			echo $insUsuario->eliminarFotoUsuario();
		} 
        if($_POST['modulo_usuario']=="actualizarFoto"){
			echo $insUsuario->actualizarFotoUsuario();
		}
}else{
    session_destroy();
    header("Location: ".APP_URL."login/");
}