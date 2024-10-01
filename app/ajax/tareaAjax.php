<?php

    require_once "../../config/app.php";
    require_once "../views/inc/sesion_start.php";
    require_once "../../autoload.php";

    use app\controllers\taskController;

    if(isset($_POST['modulo_tarea'])){
        $insTarea = new taskController();
    
        if($_POST['modulo_tarea'] == "registrar"){
            echo $insTarea->registrarTarea();
        }
        if($_POST['modulo_tarea'] == "eliminar"){
            echo $insTarea->eliminarTarea();
        }
        if($_POST['modulo_tarea'] == "finalizar"){
            echo $insTarea->finalizarTarea();
        }
        if($_POST['modulo_tarea'] == "actualizar"){
            echo $insTarea->actualizarTarea();
        }

    }else{
        session_destroy();
        header("Location: ".APP_URL."login/");
    }