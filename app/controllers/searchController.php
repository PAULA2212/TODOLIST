<?php

    namespace app\controllers;

    use app\models\mainModel;

    class searchController extends mainModel{
        //controlador de los modulos de busqueda:
        public function modulosBusqueda($modulo){
            $listaModulos = ['buscarTarea'];

            if(in_array($modulo, $listaModulos)){
                return false;
            }else{
                return true;
            }
        }

        public function iniciarBuscador(){
            $url = $this->limpiarCadena($_POST['modulo_url']);
            $text = $this->limpiarCadena($_POST['txt_buscador']);

            if($this->modulosBusqueda($url)){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ha ocurrido un error",
                    "texto" => "No podemos procesar la peticion en este momento",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
            if($text == ""){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ha ocurrido un error",
                    "texto" => "Introduce un termino de busqueda",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
            if ($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}", $text)) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ha ocurrido un error",
                    "texto" => "El termino de busqueda no coincide con el formato solicitado",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }

            $_SESSION[$url] = $text;
            $alerta = [
                "tipo" => "redireccionar",
                "url" => APP_URL.$url."/"
            ];
            return json_encode($alerta);
        }

        public function eliminarBuscador(){
            $url = $this->limpiarCadena($_POST['modulo_url']);
            if($this->modulosBusqueda($url)){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ha ocurrido un error",
                    "texto" => "No podemos procesar la peticion en este momento",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
            unset($_SESSION[$url]);
            $alerta = [
                "tipo" => "redireccionar",
                "url" => APP_URL.$url."/"
            ];
            return json_encode($alerta);
        }
    }