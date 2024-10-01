<nav class="navbar">
    <div class="navbar-brand">
        <a class="navbar-item" href="<?php echo APP_URL; ?>dashboard">
            <img src="<?php echo APP_URL; ?>app/views/img/icon.svg" alt="todolist" width="50" height="28">
            <h1><strong> TO DO LIST</strong></h1>
        </a>
        <div class="navbar-burger" data-target="navbarExampleTransparentExample">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>

    <div id="navbarExampleTransparentExample" class="navbar-menu">

        <div class="navbar-start">

            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link" href="#">
                    Tareas
                </a>
                <div class="navbar-dropdown is-boxed">

                    <a class="navbar-item" href="<?php echo APP_URL; ?>nuevaTarea/">
                        Nueva
                    </a>
                    <a class="navbar-item" href="<?php echo APP_URL."listaTareas/" ?>">
                        Lista
                    </a>
                    <a class="navbar-item" href="<?php echo APP_URL; ?>buscarTarea/">
                        Buscar
                    </a>

                </div>
            </div>
        </div>

        <div class="navbar-end">
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link">
                <?php echo $_SESSION['usuario']; ?>
                </a>
                <div class="navbar-dropdown is-boxed">

                    <a class="navbar-item" href="<?php echo APP_URL."cuenta/".$_SESSION['id']."/" ?>">
                        Mi cuenta
                    </a>
                    <a class="navbar-item" href="<?php echo APP_URL."foto/".$_SESSION['id']."/" ?>">
                        Mi foto
                    </a>
                    <hr class="navbar-divider">
                    <a class="navbar-item" href="<?php echo APP_URL; ?>cerrarSesion/" id="btn_exit" >
                        Salir
                    </a>
                </div>
            </div>
        </div>

    </div>
</nav>