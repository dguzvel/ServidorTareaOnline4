<?php

    $parametros["titulo"] = "Mi Blog PHP-MVC";

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