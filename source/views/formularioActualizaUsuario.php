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

                <h3 id="titulo">Editar Usuario</h3>
                <br>
                <!-- Formulario HTML que realizará la acción de la ruta establecida, recoger.php -->
                <form action="../views/index.php?accion=editarUsuario" method="POST" enctype="multipart/form-data">

                    <label for="nombre">
                        Nombre:
                        <input type="text" name="nombre" class="form-control" 
                            <?php
                                if(isset($_POST["nombre"])){
                                    echo "value='{$_POST["nombre"]}'";
                                }else{
                                    echo "value='{$parametros["datos"]["nombre"]}'";
                                }
                            ?>
                        />
                        <?php echo mostrarError($errores, "nombre"); ?>
                    </label>
                    <br><br>

                    <label for="apellidos">
                        Apellidos:
                        <input type="text" name="apellidos" class="form-control"
                            <?php
                                if(isset($_POST["apellidos"])){
                                    echo "value='{$_POST["apellidos"]}'";
                                }else{
                                    echo "value='{$parametros["datos"]["apellidos"]}'";
                                }
                            ?>
                        />
                        <?php echo mostrarError($errores, "apellidos"); ?>
                    </label>
                    <br><br>

                    <label for="email">
                        E-mail:
                        <input type="email" name="email" class="form-control"
                            <?php
                                if(isset($_POST["email"])){
                                    echo "value='{$_POST["email"]}'";
                                }else{
                                    echo "value='{$parametros["datos"]["email"]}'";
                                }
                            ?>
                        />
                        <?php echo mostrarError($errores, "email"); ?>
                    </label>
                    <br><br>
                    
                    <?php 
                    
                    if ($parametros["datos"]["imagen"] != null && $parametros["datos"]["imagen"] != ""){ ?>
                    <img src="fotos/<?= $parametros["datos"]["imagen"] ?>" height="100" width="100"/></br>
                    <?php } 
                    
                    ?>
                    <label for="imagen">
                        Imagen:
                        <input type="file" name="imagen" class="form-control" />
                        <?php echo mostrarError($errores, "imagen"); ?>
                    </label>
                    <br><br>

                    <input type="hidden" name="usuario_id" value="<?php echo $id;?>">

                    <input type="submit" value="Enviar" name="submit" class="btn btn-success" />

                </form>

            </section>
        </main>

        <?php require '../includes/footer.php'; ?>

    </body>
</html>