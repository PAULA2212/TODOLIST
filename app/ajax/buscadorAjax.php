<?php

require_once "../../config/app.php";
require_once "../views/inc/sesion_start.php";
require_once "../../autoload.php";

use app\controllers\searchController;

if(isset($_POST['modulo_buscador'])){
    $insBuscador = new searchController();
    
    if($_POST["modulo_buscador"] == "buscar"){
        echo $insBuscador-> iniciarBuscador();
    }
    if($_POST["modulo_buscador"] == "eliminar"){
        echo $insBuscador-> eliminarBuscador();
    }
}else{
    session_destroy();
    header("Location: ".APP_URL."login/");
}