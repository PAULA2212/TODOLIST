<div class="container is-fluid mb-6">
	<h1 class="title">Tareas</h1>
	<h2 class="subtitle">Lista de tareas</h2>
</div>
<div class="container pb-6 pt-6">
    <?php
        use app\controllers\taskController;

        $tabla_tareas = new taskController();

        echo $tabla_tareas->listarTareas($url[1],15,$url[0],"");

    ?>
    
</div>
