<?php

    require_once "../libraries/recaptchalib.php";

    //Se dan valores para un usuario y contraseña que sean válidos y puedan hacer login
    $usuarioValido = "Mortadelo";
    $passwordValido = "12345";

    //Se accede si se pulsa el botón para enviar información del formulario de login
    if(isset($_POST["submit"])){
        //Si ambos campos, usuario y conraseña, tienen información
        if((isset($_POST["usuario"]) && isset($_POST["password"])) && (!empty($_POST["usuario"]) && !empty($_POST["password"]))){
            //Se iniciará sesión si el usuario y contraseña son válidos
            if ($_POST["usuario"] == strcasecmp($_POST["usuario"], $usuarioValido) && $_POST["password"] == $passwordValido){

                session_start(); //Se activa el uso de sesiones
                $_SESSION["logueado"] = 1; //La sessión logueado se establece en 1, puede servir como contador de sesiones
                $_SESSION["usuario"] = $_POST["usuario"];//El valor usuario de la sesión será el introducido en el formulario

                //Si el checkbox de recordar está marcado, se crearán las cookies
                if(isset($_POST["recordar"]) && ($_POST["recordar"] == "on")){

                    setcookie ("usuario", $_POST["usuario"] , time() + (15 * 24 * 60 * 60));//Nombre, valor y vencimiento de la cookie
                    setcookie ("password", $_POST["password"], time() + (15 * 24 * 60 * 60));
                    setcookie ("recordar", $_POST["recordar"], time() + (15 * 24 * 60 * 60));

                }else {//En caso contrario, las cookies se eliminarán. Obtendrán un valor vacío
                    
                    if(isset($_COOKIE["usuario"])){
                        setcookie ("usuario","");
                    }
                    if(isset($_COOKIE["password"])) {
                        setcookie ("password","");
                    }
                    if(isset($_COOKIE["recordar"])) {
                        setcookie ("recordar","");
                    }

                }

                //Si el checkbox de mantener la sesión abierta está marcado, se crea una cookie para esta sesión exclusiva del usuario
                if(isset($_POST["abierta"]) && ($_POST["abierta"] == "on")){

                    setcookie ("abierta", $_POST["usuario"] , time() + (15 * 24 * 60 * 60));//Nombre, valor y vencimiento de la cookie

                }else {//En caso contrario, las cookies se eliminarán. Obtendrán un valor vacío
                    
                    if(isset($_COOKIE["abierta"])){
                        setcookie ("abierta","");
                    }

                }

                require_once "../includes/captcha.php";

            } else {
                //Si los valores de usuario y contraseña no son correctos volvemos a login y mostramos el error de datos no válidos
                header ("Location: ./login.php?error=incorrecto");

            }
        } else {
            //Si los campos han quedado vacíos volvemos a login y lo indicamos con un mensaje de error
            header ("Location: ./login.php?error=vacio");

        }

    }

?>

<!DOCTYPE html>
<html lang="es">
    <head>

        <?php require '../includes/head.php'; ?>

    </head>
    <body>

        <?php require '../includes/header.php'; ?>

        <main>
            <section class="container cuerpo text-center">

                <h3 id="titulo">Login de Usuario</h3>
                <br>
                <!-- Formulario HTML que realizará la acción de la ruta establecida -->
                <form action="" method="POST" enctype="multipart/form-data">

                    <label for="usuario">
                        Usuario:
                        <input type="text" name="usuario" class="form-control" 
                            <?php
                                if(isset($_COOKIE["usuario"])){
                                    echo "value='{$_COOKIE["usuario"]}'";
                                }
                            ?>
                        />
                    </label>
                    <br><br>

                    <label for="password">
                        Contraseña:
                        <input type="password" name="password" class="form-control"
                            <?php
                                if(isset($_COOKIE["password"])){
                                    echo "value='{$_COOKIE["password"]}'";
                                }
                            ?>
                        />
                    </label>
                    <br><br>

                    <label>
                        <input type="checkbox" name="recordar"
                            <?php
                                if(isset($_COOKIE["recordar"])){
                                    echo "checked";
                                }
                            ?>
                        />
                        Recuérdame
                    </label>
                    <br><br>
                    
                    <label>
                        <input type="checkbox" name="abierta"
                            <?php
                                if(isset($_COOKIE["abierta"])){
                                    echo "checked";
                                }
                            ?>
                        />
                        Mantener la sesión abierta
                    </label>
                    <br><br>

                    <div class="g-recaptcha row justify-content-center" data-sitekey="6Lf7ticeAAAAAA8zF-Lc3BI2qcP8ZSdo01LKzJc3"></div>

                    <?php

                        //Accede si hay valores en ?error= tras la ruta de la página
                        if(isset($_GET["error"])){

                            if ($_GET["error"] == "incorrecto") {

                                echo '<div class="alert alert-danger">'."Usuario o Contraseña incorrectos".'</div> <br>';

                            }elseif ($_GET["error"] == "fuera") {

                                echo '<div class="alert alert-danger">'."No se puede acceder directamente, debe hacer login".'</div> <br>';

                            }elseif ($_GET["error"] == "vacio") {

                                echo '<div class="alert alert-danger">'."Debe rellenar sus credenciales".'</div> <br>';
            
                            }elseif ($_GET["error"] == "captcha") {

                                echo '<div class="alert alert-danger">'."La validación no es correcta, rellene el Captcha".'</div> <br>';
            
                            }
                                
                        }

                    ?>

                    <input type="submit" value="Enviar" name="submit" class="btn btn-success" />

                </form>

            </section>
        </main>

        <?php require '../includes/footer.php'; ?>

    </body>
</html>