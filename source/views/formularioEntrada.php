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

                <h3 id="titulo">Añadir Entrada</h3>
                <br>

                <form action="../views/index.php?accion=insertarEntrada" method="POST" enctype="multipart/form-data">

                    <label for="titulo">
                        Titulo:
                        <input type="text" name="titulo" class="form-control" 
                            <?php
                                if(isset($_POST["titulo"])){
                                    echo "value='{$_POST["titulo"]}'";
                                }
                            ?>
                        required />
                    </label>
                    <br>

                    <label for="imagen">
                        Imagen:
                        <input type="file" name="imagen" class="form-control" />
                    </label>
                    <br><br>
                    
                    Descripcion:
                    <textarea name="descripcion" id="descripcion"></textarea>

                    <br>

                    <input type="submit" value="Enviar" name="submit" class="btn btn-success" />

                </form>

                <script>
                    ClassicEditor
                        .create(document.querySelector('#descripcion'))
                        .catch(error => {
                            console.error(error);
                        });
                </script>
            </section>
        </main>        

        <?php require '../includes/footer.php'; ?>

    </body>
</html>