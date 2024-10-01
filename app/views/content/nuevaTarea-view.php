<div class="container is-fluid mb-6">
    <h1 class="title">Tareas</h1>
    <h2 class="subtitle">Nueva tarea</h2>
</div>

<div class="container pb-6 pt-6">

    <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/tareaAjax.php" method="POST" autocomplete="off">

        <input type="hidden" name="modulo_tarea" value="registrar">

        <div class="columns">
            <div class="column">
                <div class="control">
                    <label>Descripción</label>
                    <input class="input" type="text" name="tarea_descripcion" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,400}" maxlength="400" required>
                </div>
            </div>
            <div class="column">
                <div class="control">
                    <label>Prioridad</label></br>
                    <div class="select">
                        <select name="tarea_prioridad" id="prioridad" required>
                            <option value="prioritario">Prioritario</option>
                            <option value="no_prioritario">No prioritario</option>
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
</div>