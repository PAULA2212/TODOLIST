<div class="container is-fluid mb-6">
    <h1 class="title">Tareas</h1>
    <h2 class="subtitle">Actualizar tarea</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
        $id_tarea = $loginController->limpiarCadena($url[1]);
        $datos_tarea = $loginController->seleccionarDatos("Unico","tarea","tarea_id",$id_tarea);
        if($datos_tarea->rowCount() == 1){
            $datos_tarea = $datos_tarea->fetch();
    ?>
    <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/tareaAjax.php" method="POST" autocomplete="off">

        <input type="hidden" name="modulo_tarea" value="actualizar">
        <input type="hidden" name="tarea_id" value="<?php echo $id_tarea ?>">

        <div class="columns">
            <div class="column">
                <div class="control">
                    <label>Descripción</label>
                    <input class="input" type="text" name="tarea_descripcion" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,400}" value="<?php echo $datos_tarea['tarea_descripcion']?>" maxlength="400" required>
                </div>
            </div>
            <div class="column">
                <div class="control">
                    <label>Prioridad</label></br>
                    <div class="select">
                        <select name="tarea_prioridad" id="prioridad" required value="<?php echo $datos_tarea['tarea_prioridad']?>">
                        <option value="prioritario" <?php if($datos_tarea['tarea_prioridad'] == 'prioritario'){ echo 'selected'; } ?>>Prioritario</option>
                        <option value="no_prioritario" <?php if($datos_tarea['tarea_prioridad'] == 'no_prioritario'){ echo 'selected'; } ?>>No prioritario</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <p class="has-text-centered">
            <button type="reset" class="button is-link is-light is-rounded">Limpiar</button>
            <button type="submit" class="button is-info is-rounded">Guardar</button>
        </p>
    </form>
    <?php 
    }else{
        include "./app/views/inc/error_alert.php";
    }
    ?>

</div>