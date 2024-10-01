<?php

    namespace app\controllers;

    use app\models\mainModel;


    class userController extends mainModel{

        //controlador para registrar un usuario
        public function registrarUsuario(){
                //almacenando datos:
                $nombre = $this->limpiarCadena($_POST['usuario_nombre']);
                $apellido = $this->limpiarCadena($_POST['usuario_apellido']);
                $usuario = $this->limpiarCadena($_POST['usuario_usuario']);
                $email = $this->limpiarCadena($_POST['usuario_email']);
                $clave1 = $this->limpiarCadena($_POST['usuario_clave_1']);
                $clave2 = $this->limpiarCadena($_POST['usuario_clave_2']);

                //verificando campos obligarios:
                if($nombre == "" OR $apellido == "" OR $usuario =="" OR $clave1 == "" OR $clave2 == ""){
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ha ocurrido un error",
                        "texto" => "No se han rellenado todos los campos obligatorios",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    exit();
                }

                //verificando la integridad de los datos:
                if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}",$nombre)){
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ha ocurrido un error",
                        "texto" => "El nombre no coincide con el formato solicitado",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    exit();
                }
                if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}",$apellido)){
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ha ocurrido un error",
                        "texto" => "El apellido no coincide con el formato solicitado",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    exit();
                }
                if($this->verificarDatos("[a-zA-Z0-9]{4,20}",$usuario)){
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ha ocurrido un error",
                        "texto" => "El usuario no coincide con el formato solicitado",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    exit();
                }
                if($this->verificarDatos("[a-zA-Z0-9$@.\-]{7,100}",$clave1)){
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ha ocurrido un error",
                        "texto" => "La primera clave no coincide con el formato solicitado",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    exit();
                }
                if($this->verificarDatos("[a-zA-Z0-9$@.\-]{7,100}",$clave2)){
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ha ocurrido un error",
                        "texto" => "La segunda clave no coincide con el formato solicitado",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    exit();
                }
                //verificando correo:
                if($email != ""){
                    if(filter_var($email,FILTER_VALIDATE_EMAIL)){
                        //verificar que el correo no exista ya en la base de datos:
                            $check_email = $this->ejecutarConsulta("SELECT usuario_email FROM usuario WHERE usuario_email='$email'");
                            if($check_email->rowCount()>0){
                                $alerta = [
                                    "tipo" => "simple",
                                    "titulo" => "Ha ocurrido un error",
                                    "texto" => "El email que intentas ingresar ya existe en la base de datos",
                                    "icono" => "error"
                                ];
                                return json_encode($alerta);
                                exit();
                            }
                    }else{
                        $alerta = [
                            "tipo" => "simple",
                            "titulo" => "Ha ocurrido un error",
                            "texto" => "El email no coincide con el formato solicitado",
                            "icono" => "error"
                        ];
                        return json_encode($alerta);
                        exit();
                    }
                }

                //verificando las claves:

                if($clave1 != $clave2){
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ha ocurrido un error",
                        "texto" => "Las claves que acaba de ingresar no coinciden",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    exit();
                }else{
                    $clave = password_hash($clave1, PASSWORD_BCRYPT, ["cost"=>10]);
                }

                //verificando que el usuario sea unico:
                $check_usuario = $this->ejecutarConsulta("SELECT usuario_usuario FROM usuario WHERE usuario_usuario='$usuario'");
                if($check_usuario->rowCount()>0){
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ha ocurrido un error",
                        "texto" => "El usuario que intentas ingresar ya existe en la base de datos",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    exit();
                }

                //directorio de imagenes:
                $img_dir = "../views/fotos/";
                //comprobar si se ha selecionado una imagen:
                if($_FILES['usuario_foto']['name'] != "" && $_FILES['usuario_foto']['size'] != 0){
                    if(!file_exists($img_dir)){
                        if(!mkdir($img_dir,0777)){
                            $alerta = [
                                "tipo" => "simple",
                                "titulo" => "Ha ocurrido un error",
                                "texto" => "Error al crear el directorio de imagenes",
                                "icono" => "error"
                            ];
                            return json_encode($alerta);
                            exit();
                        }
                    }
                    //verificando el formato de imagenes:
                    if(mime_content_type($_FILES['usuario_foto']['tmp_name']) !="image/jpeg" && mime_content_type($_FILES['usuario_foto']['tmp_name']) !="image/png"){
                        $alerta = [
                            "tipo" => "simple",
                            "titulo" => "Ha ocurrido un error",
                            "texto" => "La imagen seleccionada es de un formato no permitido",
                            "icono" => "error"
                        ];
                        return json_encode($alerta);
                        exit();
                    }
                    //verificando el peso de las imagenes:
                    if(($_FILES['usuario_foto']['size']/1024)>5120){
                        $alerta = [
                            "tipo" => "simple",
                            "titulo" => "Ha ocurrido un error",
                            "texto" => "La imagen seleccionada supera el peso permitido permitido",
                            "icono" => "error"
                        ];
                        return json_encode($alerta);
                        exit();
                    }

                    //nombrando la foto:
                    $foto = str_ireplace(" ", "_", $nombre);
                    $foto .= "-".rand(0,100);

                    switch(mime_content_type($_FILES['usuario_foto']['tmp_name'])){
                        case "image/jpeg":
                        $foto .= ".jpg";
                        break;
                        case "image/png":
                        $foto .= ".png";                      
                    }

                    chmod($img_dir,0777);

                    if(!move_uploaded_file($_FILES['usuario_foto']['tmp_name'],$img_dir.$foto)){
                        $alerta = [
                            "tipo" => "simple",
                            "titulo" => "Ha ocurrido un error",
                            "texto" => "No se ha podido enviar la foto al sistema",
                            "icono" => "error"
                        ];
                        return json_encode($alerta);
                        exit();
                    }
                    
                }else{
                    $foto = "";
                }

                //preparamos los datos para la consulta de insercion:

                $usuario_datos_reg = [
                    [
                        "campo_nombre"=>"usuario_nombre",
                        "campo_marcador"=>":Nombre",
                        "campo_valor"=> $nombre
                    ],
                    [
                        "campo_nombre"=>"usuario_apellido",
                        "campo_marcador"=>":Apellido",
                        "campo_valor"=>$apellido
                    ],
                    [
                        "campo_nombre"=>"usuario_email",
                        "campo_marcador"=>":Email",
                        "campo_valor"=>$email
                    ],
                    [
                        "campo_nombre"=>"usuario_usuario",
                        "campo_marcador"=>":Usuario",
                        "campo_valor"=>$usuario
                    ],
                    [
                        "campo_nombre"=>"usuario_clave",
                        "campo_marcador"=>":Clave",
                        "campo_valor"=>$clave
                    ],
                    [
                        "campo_nombre"=>"usuario_foto",
                        "campo_marcador"=>":Foto",
                        "campo_valor"=>$foto
                    ],
                    [
                        "campo_nombre"=>"usuario_creado",
                        "campo_marcador"=>":Creado",
                        "campo_valor"=>date("Y-m-d H:i:s") 
                    ],
                    [
                        "campo_nombre"=>"usuario_actualizado",
                        "campo_marcador"=>":Actualizado",
                        "campo_valor"=>date("Y-m-d H:i:s") 
                    ]
                ];
                //usados el metodo heredado para guardar los datos:
                $registrar_usuario = $this->guardarDatos("usuario", $usuario_datos_reg);

                if($registrar_usuario->rowCount() == 1){
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Usuario registrado",
                        "texto" => "Usuario registrado correctamente, accede a tu cuenta desde login",
                        "icono" => "success",
                        "url" => APP_URL."login/" 
                    ];
                    return json_encode($alerta);

                }else{
                    if(is_file($img_dir.$foto)){
                        chmod($img_dir.$foto, 0777);
                        unlink($img_dir.$foto);
                    }
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ha ocurrido un error",
                        "texto" => "No se ha podido registrar al usuario",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    
                }

        }
        public function actualizarUsuario(){

            $admin_usuario = $this->limpiarCadena($_POST['administrador_usuario']);
            $admin_clave = $this->limpiarCadena($_POST['administrador_clave']);
            //verificando que se rellenen los campos de usuario y clave para poder actualizar
            if($admin_usuario == "" || $admin_clave == ""){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ha ocurrido un error",
                    "texto" => "No se han rellenado todos los campos obligatorios de USUARIO Y CLAVE",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
            //verificando la integridad de los datos usuario y clave:
            if($this->verificarDatos("[a-zA-Z0-9]{4,20}",$admin_usuario)){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ha ocurrido un error",
                    "texto" => "El usuario no coincide con el formato solicitado",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
            if($this->verificarDatos("[a-zA-Z0-9$@.\-]{7,100}",$admin_clave)){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ha ocurrido un error",
                    "texto" => "La primera clave no coincide con el formato solicitado",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
            $check_admin = $this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_usuario = '".$admin_usuario."'");
            if($check_admin->rowCount() == 1){
                $check_admin = $check_admin->fetch();
                if($_SESSION['id'] == $check_admin['usuario_id']){
                    if(!password_verify($admin_clave,$check_admin['usuario_clave'])){
                        $alerta = [
                            "tipo" => "simple",
                            "titulo" => "Ha ocurrido un error",
                            "texto" => "La contraseña no coincide con el usuario",
                            "icono" => "error"
                        ];
                        return json_encode($alerta);
                        exit();
                    }else{
                        $nombre = $this->limpiarCadena($_POST['usuario_nombre']);
                        $apellido = $this->limpiarCadena($_POST['usuario_apellido']);
                        $usuario = $this->limpiarCadena($_POST['usuario_usuario']);
                        $email = $this->limpiarCadena($_POST['usuario_email']);

                        $clave1 = $this->limpiarCadena($_POST['usuario_clave_1']);
                        $clave2 = $this->limpiarCadena($_POST['usuario_clave_2']);
                        //verificando campos obligarios:
                        if($nombre == "" OR $apellido == "" OR $usuario ==""){
                            $alerta = [
                                "tipo" => "simple",
                                "titulo" => "Ha ocurrido un error",
                                "texto" => "No se han rellenado todos los campos obligatorios",
                                "icono" => "error"
                            ];
                            return json_encode($alerta);
                            exit();
                        }

                        //verificando la integridad de los datos:
                        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}",$nombre)){
                            $alerta = [
                                "tipo" => "simple",
                                "titulo" => "Ha ocurrido un error",
                                "texto" => "El nombre no coincide con el formato solicitado",
                                "icono" => "error"
                            ];
                            return json_encode($alerta);
                            exit();
                        }
                        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}",$apellido)){
                            $alerta = [
                                "tipo" => "simple",
                                "titulo" => "Ha ocurrido un error",
                                "texto" => "El apellido no coincide con el formato solicitado",
                                "icono" => "error"
                            ];
                            return json_encode($alerta);
                            exit();
                        }
                        if($this->verificarDatos("[a-zA-Z0-9]{4,20}",$usuario)){
                            $alerta = [
                                "tipo" => "simple",
                                "titulo" => "Ha ocurrido un error",
                                "texto" => "El usuario no coincide con el formato solicitado",
                                "icono" => "error"
                            ];
                            return json_encode($alerta);
                            exit();
                        }
                        //verificando correo:
                        if($email != "" && $email != $check_admin['usuario_email']){
                            if(filter_var($email,FILTER_VALIDATE_EMAIL)){
                                //verificar que el correo no exista ya en la base de datos:
                                    $check_email = $this->ejecutarConsulta("SELECT usuario_email FROM usuario WHERE usuario_email='$email'");
                                    if($check_email->rowCount()>0){
                                        $alerta = [
                                            "tipo" => "simple",
                                            "titulo" => "Ha ocurrido un error",
                                            "texto" => "El email que intentas ingresar ya existe en la base de datos",
                                            "icono" => "error"
                                        ];
                                        return json_encode($alerta);
                                        exit();
                                    }
                            }else{
                                $alerta = [
                                    "tipo" => "simple",
                                    "titulo" => "Ha ocurrido un error",
                                    "texto" => "El email no coincide con el formato solicitado",
                                    "icono" => "error"
                                ];
                                return json_encode($alerta);
                                exit();
                            }
                        }

                        //verificando las claves:

                        if($clave1 != "" || $clave2 != ""){
                            if($this->verificarDatos("[a-zA-Z0-9$@.\-]{7,100}",$clave1) || $this->verificarDatos("[a-zA-Z0-9$@.\-]{7,100}",$clave2)){
                                $alerta = [
                                    "tipo" => "simple",
                                    "titulo" => "Ha ocurrido un error",
                                    "texto" => "Las claves clave no coincide con el formato solicitado",
                                    "icono" => "error"
                                ];
                                return json_encode($alerta);
                                exit();
                            }else{
                                if($clave1 != $clave2){
                                    $alerta = [
                                        "tipo" => "simple",
                                        "titulo" => "Ha ocurrido un error",
                                        "texto" => "Las claves que acaba de ingresar no coinciden",
                                        "icono" => "error"
                                    ];
                                    return json_encode($alerta);
                                    exit();
                                }else{
                                    $clave = password_hash($clave1, PASSWORD_BCRYPT, ["cost" => 10]);
                                }
                            }
                        }else{
                            $clave = $check_admin['usuario_clave'];
                        }    

                        //verificando que el usuario sea unico:
                        if($check_admin['usuario_usuario'] != $usuario){
                            $check_usuario = $this->ejecutarConsulta("SELECT usuario_usuario FROM usuario WHERE usuario_usuario='$usuario'");
                            if($check_usuario->rowCount()>0){
                                $alerta = [
                                    "tipo" => "simple",
                                    "titulo" => "Ha ocurrido un error",
                                    "texto" => "El usuario que intentas ingresar ya existe en la base de datos",
                                    "icono" => "error"
                                ];
                                return json_encode($alerta);
                                exit();
                            }
                        }
                        $usuario_datos_up = [
                            [
                                "campo_nombre"=>"usuario_nombre",
                                "campo_marcador"=>":Nombre",
                                "campo_valor"=> $nombre
                            ],
                            [
                                "campo_nombre"=>"usuario_apellido",
                                "campo_marcador"=>":Apellido",
                                "campo_valor"=>$apellido
                            ],
                            [
                                "campo_nombre"=>"usuario_email",
                                "campo_marcador"=>":Email",
                                "campo_valor"=>$email
                            ],
                            [
                                "campo_nombre"=>"usuario_usuario",
                                "campo_marcador"=>":Usuario",
                                "campo_valor"=>$usuario
                            ],
                            [
                                "campo_nombre"=>"usuario_clave",
                                "campo_marcador"=>":Clave",
                                "campo_valor"=>$clave
                            ],
                            [
                                "campo_nombre"=>"usuario_actualizado",
                                "campo_marcador"=>":Actualizado",
                                "campo_valor"=>date("Y-m-d H:i:s") 
                            ]
                        ];
                        $condicion = [
                            "condicion_campo" => "usuario_id",
                            "condicion_marcador"=> ":Id",
                            "condicion_valor"=>$check_admin['usuario_id']
                        ];
        
                        if($actualizar_usuario = $this->actualizarDatos("usuario",$usuario_datos_up,$condicion)){
                            $_SESSION['nombre'] = $nombre;
                            $_SESSION['apellido'] = $apellido;
                            $_SESSION['usuario'] = $usuario;
                            $_SESSION['actualizado'] = date("Y-m-d H:i:s");
                            $alerta = [
                                "tipo" => "recargar",
                                "titulo" => "Usuario actualizado",
                                "texto" => "Usuario actualizado correctamente",
                                "icono" => "success"
                            ];
                            return json_encode($alerta);
        
                        }else{
                            $alerta = [
                                "tipo" => "simple",
                                "titulo" => "Ha ocurrido un error",
                                "texto" => "No se ha podido actualizar al usuario",
                                "icono" => "error"
                            ];
                            return json_encode($alerta);
                            
                        }

                    }
                }else{
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ha ocurrido un error",
                        "texto" => "El usuario introducido no coincide con el usuario con sesion iniciada, no puede borrar los datos de otro usuario",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    exit();
                }
            }else{
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ha ocurrido un error",
                    "texto" => "El usuario que intenta actualizar no existe en la base de datos",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }

        }

        public function eliminarUsuario(){
            $admin_usuario = $this->limpiarCadena($_POST['administrador_usuario']);
            $admin_clave = $this->limpiarCadena($_POST['administrador_clave']);
            //verificando que se rellenen los campos de usuario y clave para poder actualizar
            if($admin_usuario == "" || $admin_clave == ""){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ha ocurrido un error",
                    "texto" => "No se han rellenado todos los campos obligatorios de USUARIO Y CLAVE",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
            //verificando la integridad de los datos usuario y clave:
            if($this->verificarDatos("[a-zA-Z0-9]{4,20}",$admin_usuario)){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ha ocurrido un error",
                    "texto" => "El usuario no coincide con el formato solicitado",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
            if($this->verificarDatos("[a-zA-Z0-9$@.\-]{7,100}",$admin_clave)){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ha ocurrido un error",
                    "texto" => "La primera clave no coincide con el formato solicitado",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }

            //trayendo los datos del usuario segun su nombre de usuario:
            $check_usuario = $this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_usuario='$admin_usuario'");
            if($check_usuario->rowCount()==1){
                $check_usuario = $check_usuario->fetch();
                if($admin_usuario == $check_usuario['usuario_usuario'] && $admin_usuario ==  $_SESSION['usuario'] && password_verify($admin_clave, $check_usuario['usuario_clave'])){
                    $check_tareas = $this->ejecutarConsulta("SELECT * FROM tarea WHERE usuario_id='" . $check_usuario['usuario_id'] . "'");
                    if($check_tareas->rowCount() >= 1){
                        $alerta = [
                            "tipo" => "simple",
                            "titulo" => "Ha ocurrido un error",
                            "texto" => "No se ha podido eliminar el usuario porque tiene tareas, elimine las tareas.",
                            "icono" => "error"
                        ];
                        return json_encode($alerta);
                        exit();
                    }else{
                        $borrar_usuario = $this->eliminarRegistro("usuario", "usuario_id", $check_usuario['usuario_id']);
                    if($borrar_usuario->rowCount() == 1){
                        if(is_file("../views/fotos/".$check_usuario['usuario_foto'])){
                            chmod("../views/fotos/".$check_usuario['usuario_foto'],0777);
                            unlink("../views/fotos/".$check_usuario['usuario_foto']);
                        }
                        $alerta = [
                            "tipo" => "simple",
                            "titulo" => "Usuario eliminado",
                            "texto" => "El usuario se ha eliminado del sistema",
                            "icono" => "success",
                            "url" => APP_URL."login/" 
                        ];
                        return json_encode($alerta);
                        exit();

                    }else{
                        $alerta = [
                            "tipo" => "simple",
                            "titulo" => "Ha ocurrido un error",
                            "texto" => "No se ha podido eliminar el usuario del sistema",
                            "icono" => "error"
                        ];
                        return json_encode($alerta);
                        exit();
                    }
                    }
                }else{
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ha ocurrido un error",
                        "texto" => "La clave introducida no coincide con la clave del usuario en el sistema",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    exit();
                }
            }else{
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ha ocurrido un error",
                    "texto" => "El usuario que intenta borrar no existe en el sistema",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
        }
        public function actualizarFotoUsuario(){

			$id=$this->limpiarCadena($_POST['usuario_id']);

			# Verificando usuario #
		    $datos=$this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_id='$id'");
		    if($datos->rowCount()<=0){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos encontrado el usuario en el sistema",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }else{
		    	$datos=$datos->fetch();
		    }

		    # Directorio de imagenes #
    		$img_dir="../views/fotos/";

    		# Comprobar si se selecciono una imagen #
    		if($_FILES['usuario_foto']['name']=="" && $_FILES['usuario_foto']['size']<=0){
    			$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No ha seleccionado una foto para el usuario",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
    		}

    		# Creando directorio #
	        if(!file_exists($img_dir)){
	            if(!mkdir($img_dir,0777)){
	                $alerta=[
						"tipo"=>"simple",
						"titulo"=>"Ocurrió un error inesperado",
						"texto"=>"Error al crear el directorio",
						"icono"=>"error"
					];
					return json_encode($alerta);
	                exit();
	            } 
	        }

	        # Verificando formato de imagenes #
	        if(mime_content_type($_FILES['usuario_foto']['tmp_name'])!="image/jpeg" && mime_content_type($_FILES['usuario_foto']['tmp_name'])!="image/png"){
	            $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"La imagen que ha seleccionado es de un formato no permitido",
					"icono"=>"error"
				];
				return json_encode($alerta);
	            exit();
	        }

	        # Verificando peso de imagen #
	        if(($_FILES['usuario_foto']['size']/1024)>5120){
	            $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"La imagen que ha seleccionado supera el peso permitido",
					"icono"=>"error"
				];
				return json_encode($alerta);
	            exit();
	        }

	        # Nombre de la foto #
	        if($datos['usuario_foto']!=""){
		        $foto=explode(".", $datos['usuario_foto']);
		        $foto=$foto[0];
	        }else{
	        	$foto=str_ireplace(" ","_",$datos['usuario_nombre']);
	        	$foto=$foto."_".rand(0,100);
	        }
	        

	        # Extension de la imagen #
	        switch(mime_content_type($_FILES['usuario_foto']['tmp_name'])){
	            case 'image/jpeg':
	                $foto=$foto.".jpg";
	            break;
	            case 'image/png':
	                $foto=$foto.".png";
	            break;
	        }

	        chmod($img_dir,0777);

	        # Moviendo imagen al directorio #
	        if(!move_uploaded_file($_FILES['usuario_foto']['tmp_name'],$img_dir.$foto)){
	            $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No podemos subir la imagen al sistema en este momento",
					"icono"=>"error"
				];
				return json_encode($alerta);
	            exit();
	        }

	        # Eliminando imagen anterior #
	        if(is_file($img_dir.$datos['usuario_foto']) && $datos['usuario_foto']!=$foto){
		        chmod($img_dir.$datos['usuario_foto'], 0777);
		        unlink($img_dir.$datos['usuario_foto']);
		    }

		    $usuario_datos_up=[
				[
					"campo_nombre"=>"usuario_foto",
					"campo_marcador"=>":Foto",
					"campo_valor"=>$foto
				],
				[
					"campo_nombre"=>"usuario_actualizado",
					"campo_marcador"=>":Actualizado",
					"campo_valor"=>date("Y-m-d H:i:s")
				]
			];

			$condicion=[
				"condicion_campo"=>"usuario_id",
				"condicion_marcador"=>":ID",
				"condicion_valor"=>$id
			];

			if($this->actualizarDatos("usuario",$usuario_datos_up,$condicion)){

				if($id==$_SESSION['id']){
					$_SESSION['foto']=$foto;
				}

				$alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Foto actualizada",
					"texto"=>"La foto del usuario ".$datos['usuario_nombre']." ".$datos['usuario_apellido']." se actualizo correctamente",
					"icono"=>"success"
				];
			}else{

				$alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Foto actualizada",
					"texto"=>"No hemos podido actualizar algunos datos del usuario ".$datos['usuario_nombre']." ".$datos['usuario_apellido']." , sin embargo la foto ha sido actualizada",
					"icono"=>"warning"
				];
			}

			return json_encode($alerta);
		}
        
        public function eliminarFotoUsuario(){
            $id=$this->limpiarCadena($_POST['usuario_id']);

			# Verificando usuario #
		    $datos=$this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_id='$id'");
		    if($datos->rowCount()<=0){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos encontrado el usuario en el sistema",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }else{
		    	$datos=$datos->fetch();
		    }

            # Directorio de imagenes #
    		$img_dir="../views/fotos/";

            chmod($img_dir,0777);

            if(is_file($img_dir.$datos['usuario_foto'])){
                //eliminando la foto
                chmod($img_dir.$datos['usuario_foto'],0777);
                if(!unlink($img_dir.$datos['usuario_foto'])){
                    $alerta=[
                        "tipo"=>"simple",
                        "titulo"=>"Ocurrió un error inesperado",
                        "texto"=>"No hemos podido eliminar la foto del usuario, intentelo de nuevo",
                        "icono"=>"error"
                    ];
                    return json_encode($alerta);
                    exit();
                }
            }else{
                $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos encontrado el foto del usuario en el sistema",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
            }
            //actualizando la base de datos
            $usuario_datos_up=[
				[
					"campo_nombre"=>"usuario_foto",
					"campo_marcador"=>":Foto",
					"campo_valor"=> ""
				],
				[
					"campo_nombre"=>"usuario_actualizado",
					"campo_marcador"=>":Actualizado",
					"campo_valor"=>date("Y-m-d H:i:s")
				]
			];

			$condicion=[
				"condicion_campo"=>"usuario_id",
				"condicion_marcador"=>":ID",
				"condicion_valor"=>$id
			];

			if($this->actualizarDatos("usuario",$usuario_datos_up,$condicion)){

				if($id==$_SESSION['id']){
					$_SESSION['foto']= "";
				}

				$alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Foto eliminada",
					"texto"=>"La foto del usuario ".$datos['usuario_nombre']." ".$datos['usuario_apellido']." se elimino correctamente",
					"icono"=>"success"
				];
			}else{

				$alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Foto actualizada",
					"texto"=>"No hemos podido actualizar algunos datos del usuario ".$datos['usuario_nombre']." ".$datos['usuario_apellido']." , sin embargo la foto ha sido eliminada",
					"icono"=>"warning"
				];
			}
            return json_encode($alerta);
        }
        
    }

    
