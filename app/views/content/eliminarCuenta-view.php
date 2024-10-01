<div class="container is-fluid mb-6">

	<h1 class="title">Mi cuenta</h1>
	<h2 class="subtitle">Eliminar cuenta</h2>

</div>
<div class="container pb-6 pt-6">
	

	
    <?php
    include"./app/views/inc/button_back.php";
    ?>

	<h2 class="title has-text-centered"><?php echo $_SESSION['nombre']." ".$_SESSION['apellido']?></h2>

	<p class="has-text-centered pb-6"><strong>Usuario creado:</strong><?php echo $_SESSION['creado']." "?><strong>Usuario actualizado:</strong> <?php echo $_SESSION['actualizado']?></p>

	<form class="FormularioAjax" action="<?php echo APP_URL?>app/ajax/usuarioAjax.php" method="POST" autocomplete="off" >

		<input type="hidden" name="modulo_usuario" value="eliminar">
		<input type="hidden" name="usuario_id" value="<?php echo $_SESSION['id']?>">

		<p class="has-text-centered">
			Para poder eliminar los datos de este usuario por favor ingrese su USUARIO y CLAVE con la que ha iniciado sesión
		</p>
		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Usuario</label>
				  	<input class="input" type="text" name="administrador_usuario" pattern="[a-zA-Z0-9]{4,20}" maxlength="20" required >
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label>Clave</label>
				  	<input class="input" type="password" name="administrador_clave" pattern="[a-zA-Z0-9$@.\-]{7,100}" maxlength="100" required >
				</div>
		  	</div>
		</div>
		<p class="has-text-centered">
			<button type="submit" class="button is-danger is-rounded">Eliminar</button>
		</p>
	</form>
  
</div>