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
        <?php require '../includes/sesion.php'; ?>

            <section class="container cuerpo text-center">

                <h1 id="titulo">Mi Blog PHP/MVC - Inicio</h1>
                <br>
                <h2>
                    Bienvenido <?php
                                    if(isset($_COOKIE["abierta"])){
                                        echo $_COOKIE["abierta"];
                                    }
                                ?>
                </h2>

            </section>
        </main>

        <?php require '../includes/footer.php'; ?>

    </body>
</html>