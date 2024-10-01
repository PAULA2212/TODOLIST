
<?php

spl_autoload_register(function($className) {
    // Cambia las barras invertidas por barras normales y añade .php al final
    $archivo = str_replace("\\", "/", $className) . ".php";

    // Combina la ruta del archivo con la ruta de trabajo actual
    $archivoCompleto = __DIR__ . "/" . $archivo;

    // Verifica si el archivo existe y lo carga
    if (is_file($archivoCompleto)) {
        require_once $archivoCompleto;
    } else {
        throw new Exception("El archivo para la clase {$className} no fue encontrado en {$archivoCompleto}");
    }
});
