<?php

namespace app\controllers;

use app\models\mainModel;

class loginController extends mainModel
{
    public function iniciarSesion()
    {
        //almacenando datos:
        $usuario = $this->limpiarCadena($_POST['login_usuario']);
        $clave = $this->limpiarCadena($_POST['login_clave']);

        //verficando campos obligatorios:
        if ($usuario == "" || $clave == "") {
            echo "
            <script>
                Swal.fire({
                        icon: 'error',
                        title: 'Ha ocurrido un error',
                        text: 'No se han rellenado todos los campos obligatorios',
                        confirmButtonText: 'Aceptar'
                    });
            </script>";
        } else {
            //verificando la integridad de los datos
            if ($this->verificarDatos("[a-zA-Z0-9]{4,20}", $usuario)) {
                echo "
                <script>
                    Swal.fire({
                            icon: 'error',
                            title: 'Ha ocurrido un error',
                            text: 'El usuario no coincide con el formato esperado',
                            confirmButtonText: 'Aceptar'
                        });
                </script>";
            } else {
                if ($this->verificarDatos("[a-zA-Z0-9$@.\-]{7,100}", $clave)) {
                    echo "
                    <script>
                        Swal.fire({
                                icon: 'error',
                                title: 'Ha ocurrido un error',
                                text: 'La clave no coincide con el formato esperado',
                                confirmButtonText: 'Aceptar'
                            });
                    </script>";
                } else {
                    //verificando que el usuario exista:
                    $check_usuario = $this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_usuario = '$usuario'");
                    if ($check_usuario->rowCount() == 1) {
                        $datos_usuario = $check_usuario->fetch();

                        if($datos_usuario['usuario_usuario'] == $usuario && password_verify($clave, $datos_usuario['usuario_clave'])){

                            //creando las variables de sesion:
                            $_SESSION['id'] = $datos_usuario['usuario_id'];
                            $_SESSION['nombre'] = $datos_usuario['usuario_nombre'];
                            $_SESSION['apellido'] = $datos_usuario['usuario_apellido'];
                            $_SESSION['usuario'] = $datos_usuario['usuario_usuario'];
                            $_SESSION['foto'] = $datos_usuario['usuario_foto'];
                            $_SESSION['creado'] = $datos_usuario['usuario_creado'];
                            $_SESSION['actualizado'] = $datos_usuario['usuario_actualizado'];
                            $_SESSION['email'] = $datos_usuario['usuario_email'];


                            if(headers_sent()){
                                echo "<script> window.location.href='".APP_URL."dashboard/'; </script>";
                            }else{
                                header("Location: ".APP_URL."dashboard/");
                            }
                        }else{
                            echo "
                            <script>
                                Swal.fire({
                                        icon: 'error',
                                        title: 'Ha ocurrido un error',
                                        text: 'La contraseña no coincide con el usuario',
                                        confirmButtonText: 'Aceptar'
                                    });
                            </script>";
                        }
                    } else {
                        echo "
                        <script>
                            Swal.fire({
                                    icon: 'error',
                                    title: 'Ha ocurrido un error',
                                    text: 'El usuario no existe en el sistema',
                                    confirmButtonText: 'Aceptar'
                                });
                        </script>";
                    }
                }
            }
        }
    }
    public function cerrarSesion(){

        session_destroy();
        if(headers_sent()){
            echo "<script> window.location.href='".APP_URL."dashboard/'; </script>";
        }else{
            header("Location: ".APP_URL."login/");
        }

    }
}
