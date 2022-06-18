<?php

    session_start(); //Se activa el uso de sesiones
    session_unset(); //Deshace las variables de sexión previamente registradas
    session_destroy(); //Se destruye la sesión
    header("Location: ../views/index.php"); //Volvemos a la página de login

?>