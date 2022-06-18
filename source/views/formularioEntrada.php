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

            <?php echo (new DateTime())->format('Y-m-d H:i:s'); ?>

                <form action="" method="POST">

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

                    <textarea name="editor" id="editor"></textarea>

                    <br>

                    <input type="submit" value="Enviar" name="submit" class="btn btn-success" />

                </form>

                <script>
                    ClassicEditor
                        .create(document.querySelector('#editor'))
                        .catch(error => {
                            console.error(error);
                        });
                </script>
            </section>
        </main>        

        <?php require '../includes/footer.php'; ?>

    </body>
</html>