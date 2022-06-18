<!DOCTYPE html>
<html lang="es">
    <head>

        <?php require '../includes/head.php'; ?>

    </head>
    <body>

        <?php require '../includes/header.php'; ?>
        <?php require '../includes/sesion.php'; ?>

            <div class="text-center">
                <?php

                if (!empty($parametros["mensajes"])){

                    foreach ($parametros["mensajes"] as $mensaje) :

                ?> 

                    <div class="alert alert-<?= $mensaje["tipo"] ?>"><?= $mensaje["mensaje"] ?></div>

                <?php 

                        endforeach;

                    }
                
                ?>
            </div>            
            
            <table class="table table-dark table-striped table-hover text-center align-middle" style="font-size: 20px;">

                <tr>
                    <th>NICK</th>
                    <th>NOMBRE</th>
                    <th>APELLIDOS</th>
                    <th>E-MAIL</th>
                    <th>CONTRASEÑA</th>
                    <th>IMAGEN</th>
                    <th>OPERACIONES</th>
                </tr>

                    <tr>
                        <td><?=$parametros["datos"]["nick"]?></td>
                        <td><?=$parametros["datos"]["nombre"]?></td>
                        <td><?=$parametros["datos"]["apellidos"]?></td>
                        <td><?=$parametros["datos"]["email"]?></td>
                        <td><?=$parametros["datos"]["password"]?></td>
                        <td><img src="fotos/<?=$parametros["datos"]["imagen"]?>" height="100" width="100"/></td>
                        <td><a href="../views/index.php?accion=editarUsuario&usuario_id=<?=$parametros["datos"]["usuario_id"]?>">Editar</a>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <a href="../views/index.php?accion=eliminarUsuario&usuario_id=<?=$parametros["datos"]["usuario_id"]?>">Eliminar</a>
                        </td>
                    </tr>
            
            </table>            

        </main>  

        <?php require '../includes/footer.php'; ?>

    </body>
</html>