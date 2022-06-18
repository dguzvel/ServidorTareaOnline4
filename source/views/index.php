<?php

    require_once '../controllers/controlador.php';

    $controlador = new controlador();

    if ($_GET && $_GET["accion"]){
    
        $accion = filter_input(INPUT_GET, "accion", FILTER_SANITIZE_STRING);
        
        if (method_exists($controlador, $accion)){
            $controlador->$accion(); //Ejecutamos la operación indicada en $accion
        }else{
            $controlador->index();   //Redirigimos a la página de inicio 
        }

    }else{

        $controlador->index();

    }

?>