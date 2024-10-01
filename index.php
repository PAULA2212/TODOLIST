<?php
    require_once "./autoload.php";
    require_once "./config/app.php";
    require_once "./app/views/inc/sesion_start.php";

    //obtenemos la get views:

    if(isset($_GET['views'])){
        $url = explode("/",$_GET['views']);
    }else{
        $url = ["login"];
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once "./app/views/inc/head.php"?>
</head>
<body>
    <?php 

    use app\controllers\viewsController;
    use app\controllers\loginController;


    $loginController = new loginController();
    $viewsController = new viewsController();
    $vista = $viewsController->ObtenerVistasControlador($url[0]);

    if($vista == "login" OR $vista == "404" OR $vista == "registroUsuario"){
        require_once "./app/views/content/".$vista."-view.php";
    }else{
        if(!isset($_SESSION['id']) || !isset($_SESSION['nombre']) || !isset($_SESSION['usuario']) 
        || $_SESSION['id'] == "" || $_SESSION['nombre'] == "" || $_SESSION['usuario'] == ""){
            $loginController->cerrarSesion();
            exit();
        }
        require_once "./app/views/inc/navbar.php";
        require_once $vista;
    }
    
    require_once "./app/views/inc/script.php";
    ?>
</body>
</html>