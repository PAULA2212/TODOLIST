<?php
    namespace app\controllers;

    use app\models\viewsModel;

    class viewsController extends viewsModel{
        public function ObtenerVistasControlador($vista){
            if($vista != ""){
                $respuesta = $this->ObtenerVistasModelo($vista);
            }else{
                $respuesta = "login";
            }
            return $respuesta;
        }
    }