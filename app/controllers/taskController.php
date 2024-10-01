<?php

namespace app\controllers;

use app\models\mainModel;

class taskController extends mainModel
{
    //controlador para registrar una tarea
    public function registrarTarea()
    {

        //guardando los datos de inputs:
        $descripcion = $this->limpiarCadena($_POST['tarea_descripcion']);
        $prioridad = $this->limpiarCadena($_POST['tarea_prioridad']);

        //verificando que se han introducido los datos obligatorios:
        if ($descripcion == "" || $prioridad == "") {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ha ocurrido un error",
                "texto" => "No se han rellenado todos los campos obligatorios",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        //verificando la integridad de los datos de entrada:
        if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,400}", $descripcion)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ha ocurrido un error",
                "texto" => "La descripcion no coincide con el formato esperado",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }
        $valores_permitidos = ["prioritario", "no_prioritario"];

        if (!in_array($prioridad, $valores_permitidos)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ha ocurrido un error",
                "texto" => "Se ha introducido un valor no válido en el campo prioridad",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        //preparando datos para su envio:
        $tarea_datos_reg = [
            [
                "campo_nombre" => "tarea_descripcion",
                "campo_marcador" => ":Descripcion",
                "campo_valor" => $descripcion
            ],
            [
                "campo_nombre" => "tarea_prioridad",
                "campo_marcador" => ":Prioridad",
                "campo_valor" => $prioridad,
            ],
            [
                "campo_nombre" => "tarea_creacion",
                "campo_marcador" => ":Creacion",
                "campo_valor" => date("Y-m-d H:i:s")
            ],
            [
                "campo_nombre" => "tarea_actualizacion",
                "campo_marcador" => ":Actualizacion",
                "campo_valor" => date("Y-m-d H:i:s")
            ],
            [
                "campo_nombre" => "tarea_finalizacion",
                "campo_marcador" => ":Finalizacion",
                "campo_valor" => null
            ],
            [
                "campo_nombre" => "usuario_id",
                "campo_marcador" => ":Usuario",
                "campo_valor" => $_SESSION['id']
            ]
        ];

        $registrar_tarea = $this->guardarDatos("tarea", $tarea_datos_reg);

        if($registrar_tarea->rowCount() == 1){
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Nueva tarea registrada",
                "texto" => "La nueva tarea ya esta en tu registro de tareas",
                "icono" => "success"
            ];
            return json_encode($alerta);
        }else{
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ha ocurrido un error",
                "texto" => "La nueva tarea no se pudo asignar a tu registro, intentalo de nuevo",
                "icono" => "error"
            ];
            return json_encode($alerta);
        }
    }
    
    //controlador para listar las tareas:
    public function listarTareas($pagina,$registros,$url,$busqueda){
        $pagina = $this->limpiarCadena($pagina);
        $registros = $this->limpiarCadena($registros);
        $busqueda = $this->limpiarCadena($busqueda);
        $url = $this->limpiarCadena($url);
        $url = APP_URL.$url."/";


        $tabla = "";

        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1 ;

        $inicio = ($pagina > 0) ? (($pagina*$registros) - $registros) : 0 ;

        if(isset($busqueda) && $busqueda != ""){
            $consulta_total = "SELECT COUNT(tarea_id) FROM tarea WHERE ((usuario_id='".$_SESSION['id']."') AND 
            ( tarea_descripcion LIKE '%$busqueda%' OR tarea_descripcion LIKE '%$busqueda%' OR tarea_creacion LIKE '%$busqueda%' OR tarea_actualizacion LIKE '%$busqueda%'
             OR tarea_finalizacion LIKE '%$busqueda%')) 
             ORDER BY tarea_id DESC" ;
            $consulta_datos = "SELECT * FROM tarea WHERE ((usuario_id='".$_SESSION['id']."') AND 
            ( tarea_descripcion LIKE '%$busqueda%' OR tarea_descripcion LIKE '%$busqueda%' OR tarea_creacion LIKE '%$busqueda%' OR tarea_actualizacion LIKE '%$busqueda%'
             OR tarea_finalizacion LIKE '%$busqueda%')) 
             ORDER BY tarea_id DESC LIMIT $inicio, $registros" ;
        }else{
            $consulta_total = "SELECT COUNT(tarea_id) FROM tarea WHERE usuario_id='".$_SESSION['id']."' ORDER BY tarea_id DESC LIMIT $inicio, $registros" ;
            $consulta_datos = "SELECT * FROM tarea WHERE usuario_id='".$_SESSION['id']."' ORDER BY tarea_id DESC LIMIT $inicio, $registros";
        }

        $datos = $this->ejecutarConsulta($consulta_datos);
        $datos = $datos->fetchAll();

        $total = $this->ejecutarConsulta($consulta_total);
        $total = (int) $total->fetchColumn();

        $numeroPaginas = ceil($total/$registros);

        $tabla .= '
        <div class="table-container">
            <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                <thead>
                    <tr>
                        <th class="has-text-centered">#</th>
                        <th class="has-text-centered">Descripcion</th>
                        <th class="has-text-centered">Prioridad</th>
                        <th class="has-text-centered">Creado</th>
                        <th class="has-text-centered">Actualizado</th>
                        <th class="has-text-centered">Finalizado</th>
                        <th class="has-text-centered" colspan="3">Opciones</th>
                    </tr>
                </thead>
                <tbody>
        ';
        if($total >= 1 && $pagina <= $numeroPaginas){
            $contador = $inicio+1;
            $pag_inicio = $inicio+1;

            foreach($datos as $dato){
                $tabla .= '
                    <tr class="has-text-centered">
                        <td>'.$contador.'</td>
                        <td>'.$dato['tarea_descripcion'].'</td>
                        <td>'.$dato['tarea_prioridad'].'</td>
                        <td>'.date("d-m-Y h:i:s A",strtotime($dato['tarea_creacion'])).'</td>
                        <td>'.date("d-m-Y h:i:s A",strtotime($dato['tarea_actualizacion'])).'</td>
                        <td>'.($dato['tarea_finalizacion'] ? date("d-m-Y h:i:s A", strtotime($dato['tarea_finalizacion'])) : 'No finalizado').'</td>
                    
                        <td>
                            <a href="'.APP_URL.'actualizarTarea/'.$dato['tarea_id'].'" class="button is-info is-rounded is-small">Actualizar</a>
                        </td>
                        <td>
                            <form class="FormularioAjax" action="'.APP_URL.'/app/ajax/tareaAjax.php" method="POST" autocomplete="off">

                                <input type="hidden" name="modulo_tarea" value="finalizar">
                                <input type="hidden" name="tarea_id_fin" value="'.$dato['tarea_id'].'">

                                <button type="submit" class="button is-success is-rounded is-small">Finalizar</button>
                            </form>
                        </td>
                        <td>
                            <form class="FormularioAjax" action="'.APP_URL.'/app/ajax/tareaAjax.php" method="POST" autocomplete="off">

                                <input type="hidden" name="modulo_tarea" value="eliminar">
                                <input type="hidden" name="tarea_id" value="'.$dato['tarea_id'].'">

                                <button type="submit" class="button is-danger is-rounded is-small">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                ';
                $contador++;

            }
            $pag_final = $contador - 1;

        }else{
            if($total >= 1){
                $tabla .= '
                    <tr class="has-text-centered" >
                        <td colspan="7">
                            <a href="'.$url.'1/" class="button is-link is-rounded is-small mt-4 mb-4">
                                Haga clic aqui para recargar el listado
                            </a>
                        </td>
                    </tr>
                ';
            }else{
                $tabla .= '
                    <tr class="has-text-centered" >
                        <td colspan="7">
                            No hay registros en el sistema
                        </td>
                    </tr>
                ';
            }
        }
        $tabla .='
                        </tbody>
            </table>
        </div>
        ';
        if($total >= 1 && $pagina <= $numeroPaginas){
            $tabla .= '
            <p class="has-text-right">Mostrando usuarios <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>
            ';

            $tabla .= $this->paginadorTablas($pagina,$numeroPaginas,$url,10);
        }
        return $tabla;
    }

    public function eliminarTarea(){
        $id_tarea = $this->limpiarCadena($_POST['tarea_id']);
        //verificando que exista la tarea
        $datos_tarea = $this->ejecutarConsulta("SELECT * FROM tarea WHERE tarea_id = '$id_tarea'");

        if($datos_tarea->rowCount() <= 0){
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ha ocurrido un error",
                "texto" => "La tarea que intenta eliminar no existe en el sistema",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }else{
            $datos_tarea = $datos_tarea->fetch();
        }

        $eliminarTarea = $this->eliminarRegistro("tarea","tarea_id",$id_tarea);

        if($eliminarTarea->rowCount() == 1){
            $alerta = [
                "tipo" => "recargar",
                "titulo" => "Tarea eliminada",
                "texto" => "La tarea se ha eliminado con existo del sistema",
                "icono" => "success"
            ];
        }else{
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Tarea NO eliminada",
                "texto" => "La tarea no se ha eliminado del sistema, porfavor, intentelo de nuevo",
                "icono" => "error"
            ];
        }
        return json_encode($alerta);
    }

    public function finalizarTarea(){
        $id_tarea = $this->limpiarCadena($_POST['tarea_id_fin']);
        //verificando que exista la tarea
        $datos_tarea = $this->ejecutarConsulta("SELECT * FROM tarea WHERE tarea_id = '$id_tarea'");

        if($datos_tarea->rowCount() <= 0){
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ha ocurrido un error",
                "texto" => "La tarea que intenta finalizar no existe en el sistema",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }else{
            $datos_tarea = $datos_tarea->fetch();
        }

        $datos = [
            [
                "campo_nombre" => "tarea_finalizacion",
                "campo_marcador" => ":Finalizar",
                "campo_valor" => date("Y-m-d H:i:s")
            ]
        ];
        $condicion = [
                "condicion_campo" => "tarea_id",
                "condicion_marcador" => ":Id",
                "condicion_valor" => $id_tarea
        ];

        $actualizarTarea = $this->actualizarDatos("tarea", $datos,$condicion);

        if($actualizarTarea->rowCount() == 1){
            $alerta = [
                "tipo" => "recargar",
                "titulo" => "Tarea finalizada",
                "texto" => "La tarea se ha finalizado con existo del sistema",
                "icono" => "success"
            ];
        }else{
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Tarea NO finalizada",
                "texto" => "La tarea no se ha finalizado en el sistema, porfavor, intentelo de nuevo",
                "icono" => "error"
            ];
        }
        return json_encode($alerta);
    }

    public function actualizarTarea(){
        $tarea_id = $this->limpiarCadena($_POST['tarea_id']);
        $descripcion = $this->limpiarCadena($_POST['tarea_descripcion']);
        $prioridad = $this->limpiarCadena($_POST['tarea_prioridad']);

        $datos_tarea = $this->ejecutarConsulta("SELECT * FROM tarea WHERE tarea_id = '$tarea_id'");

        if ($descripcion == "" || $prioridad == "") {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ha ocurrido un error",
                "texto" => "No se han rellenado todos los campos obligatorios",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        //verificando la integridad de los datos de entrada:
        if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,400}", $descripcion)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ha ocurrido un error",
                "texto" => "La descripcion no coincide con el formato esperado",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }
        $valores_permitidos = ["prioritario", "no_prioritario"];

        if (!in_array($prioridad, $valores_permitidos)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ha ocurrido un error",
                "texto" => "Se ha introducido un valor no válido en el campo prioridad",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        $tarea_datos_upd = [
            [
                "campo_nombre" => "tarea_descripcion",
                "campo_marcador" => ":Descripcion",
                "campo_valor" => $descripcion
            ],
            [
                "campo_nombre" => "tarea_prioridad",
                "campo_marcador" => ":Prioridad",
                "campo_valor" => $prioridad,
            ],
            [
                "campo_nombre" => "tarea_actualizacion",
                "campo_marcador" => ":Actualizacion",
                "campo_valor" => date("Y-m-d H:i:s")
            ]
        ];

        $condicion = [
            "condicion_campo" => "tarea_id",
            "condicion_marcador" => ":Id",
            "condicion_valor" => $tarea_id
        ];
        
        $actualizar_tarea = $this->actualizarDatos("tarea",$tarea_datos_upd,$condicion);

        if($actualizar_tarea->rowCount() == 1){
            $alerta = [
                "tipo" => "recargar",
                "titulo" => "Tarea actualizada",
                "texto" => "La tarea ha sido actualizada en el sistema",
                "icono" => "success"
            ];
            return json_encode($alerta);
            exit();
        }else{
            $alerta = [
                "tipo" => "recargar",
                "titulo" => "Ha ocurrido un error",
                "texto" => "La tareano ha sido actualizada en el sistem, intentelo de nuevo",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }
    }
}