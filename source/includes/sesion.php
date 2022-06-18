<?php

    session_start(); //Se activa el uso de sesiones
    if(!isset($_SESSION["logueado"]) && !isset($_COOKIE["abierta"])){

    //Volvemos al login con el error=fuera, es decir, se ha intentado acceder sin pasar por el login desde la URL o sin una cookie de sesión abierta
    header ("Location: login.php?error=fuera");


    }

?>