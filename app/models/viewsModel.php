<?php
    namespace app\models;

    class viewsModel{
        protected function ObtenerVistasModelo($vista){
            $listaBlanca = ["dashboard", "nuevaTarea", "listaTareas", "buscarTarea","cuenta","foto","actualizarTarea","cerrarSesion", "eliminarCuenta"];

            if(in_array($vista, $listaBlanca)){
                if(is_file("./app/views/content/".$vista."-view.php")){
                    $contenido = "./app/views/content/".$vista."-view.php";
                }else{
                    $contenido = "404";
                }
            }elseif($vista == "login" OR $vista=="index"){
                $contenido= "login";
            }elseif($vista == "registroUsuario"){
                $contenido = "registroUsuario";
            }else{
                $contenido = "404";
            }
            return $contenido;
        }
    }