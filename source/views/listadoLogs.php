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
                    <th>USUARIO</th>
                    <th>FECHA</th>
                    <th>OPERACION</th>
                    <th>ELIMINAR</th>
                </tr>

                <?php foreach($parametros["datos"] as $dat){ { ?> 

                    <tr>
                        <td><?=$dat["usuario_id"]?></td>
                        <td><?=$dat["fecha"]?></td>
                        <td><?=$dat["operacion"]?></td>
                        <td><a href="../views/index.php?accion=eliminarLog&log_id=<?=$dat["log_id"]?>" onclick="return confirm('¿Seguro que desea eliminar este registro del Log?')">Eliminar</a></td>
                    </tr>

                <?php } } ?>
            
            </table>

        </main>  

        <?php require '../includes/footer.php'; ?>

    </body>
</html>