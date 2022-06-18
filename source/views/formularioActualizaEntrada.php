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
        <?php require '../includes/validarUsuario.php'; ?>

            <section class="container cuerpo text-center">

            <?php

            if (!empty($parametros["mensajes"])){

                foreach ($parametros["mensajes"] as $mensaje) :

            ?> 

                <div class="alert alert-<?= $mensaje["tipo"] ?>"><?= $mensaje["mensaje"] ?></div>

            <?php 

                    endforeach;

                }
            
            ?>

                <h3 id="titulo">Editar Entrada</h3>
                <br>
                <!-- Formulario HTML que realizará la acción de la ruta establecida, recoger.php -->
                <form action="../views/index.php?accion=editarEntrada" method="POST" enctype="multipart/form-data">

                    <label for="titulo">
                        Titulo:
                        <input type="text" name="titulo" class="form-control" 
                            <?php
                                if(isset($_POST["titulo"])){
                                    echo "value='{$_POST["titulo"]}'";
                                }else{
                                    echo "value='{$parametros["datos"]["titulo"]}'";
                                }
                            ?>
                        />
                    </label>
                    <br><br>
                    
                    <?php if ($parametros["datos"]["imagen"] != null && $parametros["datos"]["imagen"] != ""){ ?>
                        <img src="fotos/<?= $parametros["datos"]["imagen"] ?>" height="100" width="100"/></br>
                    <?php } ?>

                    <label for="imagen">
                        Imagen:
                        <input type="file" name="imagen" class="form-control" />
                    </label>
                    <br><br>

                    Descripcion:
                    <textarea name="descripcion" id="descripcion"></textarea>
                    <br><br>

                    <input type="hidden" name="entrada_id" value="<?php echo $id;?>">

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