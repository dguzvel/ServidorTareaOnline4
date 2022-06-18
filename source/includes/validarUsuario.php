<?php
    //Array que almacenará los errores durante la validación de los campos del formulario
    $errores = [];

    //Función que muestra con un alert el error de un campo determinado, el array errores y el campo se pasan por parámetro
    function mostrarError($errores, $campo){

        $alerta = "";

        if(isset($errores[$campo]) && !empty($campo)){

            $alerta = '<div class="alert alert-danger">'.$errores[$campo].'</div>';

        }

        return $alerta;

    }

    //Función que filtra/sanitiza el valor que se le pase por parámetro
    function filtrado($datos){

        $datos = trim($datos);//Elimina los espacios en blanco
        $datos = stripslashes($datos);//Quita las barras de un string
        $datos = htmlspecialchars($datos);//Convierte caracteres especiales en entidades HTML

        return $datos;

    }
    
    /*
     *Cuando en el formulario se pulse el botón submit se validará cada campo con sus condiciones y
     *se dará ese valor a cada variable o se mostrará un mensaje de error
     */
    if(isset($_POST["submit"])){
        
        if(!empty($_POST["nick"]) && strlen($_POST["nick"]) <= 20){

            $nombre = filtrado($_POST["nick"]);
            $nombre = filter_var($nombre, FILTER_SANITIZE_STRING);

        }else{

            $errores["nick"] = "Un nick no puede estar vacío ni tener más de 20 caracteres";

        }

        if(!empty($_POST["nombre"]) && strlen($_POST["nombre"]) <= 20 && !preg_match("/[\d]/", $_POST["nombre"])){

            $nombre = filtrado($_POST["nombre"]);
            $nombre = filter_var($nombre, FILTER_SANITIZE_STRING);

        }else{

            $errores["nombre"] = "Un nombre no puede estar vacío, tener más de 20 caracteres ni contener números";

        }
        
        if(!empty($_POST["apellidos"]) && !preg_match("/[\d]/", $_POST["apellidos"])){

            $apellidos = filtrado($_POST["apellidos"]);
            $apellidos = filter_var($apellidos, FILTER_SANITIZE_STRING);

        }else{

            $errores["apellidos"] = "Un apellido no puede estar vacío ni contener números";

        }
        
        if(!empty($_POST["email"]) && filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){

            //preg_match("/[a-z]{7}[\d]{3}@g.educaand.es/", $_POST["email"])

            $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);

        }else{

            $errores["email"] = "El e-mail no es válido";

        }
        
        if(!empty($_POST["password"]) && preg_match("/(?=.+[a-z])(?=.+[A-Z])(?=.+\d)(?=.+\W)[a-zA-Z\d\W]{6,}/", $_POST["password"])){

            $password = sha1($_POST["password"]);

        }else{

            $errores["password"] = "La contraseña debe contener al menos 6 caracteres";

        }

    }

?>