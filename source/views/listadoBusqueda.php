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
                    <th>TITULO</th>
                    <th>IMAGEN</th>
                    <th>DESCRIPCION</th>
                    <th>OPERACIONES</th>
                </tr>

                    <tr>
                        <td><?=$parametros["datos"]["usuario_id"]?></td>
                        <td><?=$parametros["datos"]["titulo"]?></td>
                        <td><img src="fotos/<?=$parametros["datos"]["imagen"]?>" height="100" width="100"/></td>
                        <td><?=$parametros["datos"]["descripcion"]?></td>
                        <td>
                            <?php if($_COOKIE["categoria_id"] == 1 || $_COOKIE["usuario_id"] == $parametros["datos"]["usuario_id"]){ ?>

                                <a href="../views/index.php?accion=editarEntrada&entrada_id=<?=$parametros["datos"]["entrada_id"]?>">Editar</a>
                                &nbsp;&nbsp;&nbsp;&nbsp;

                            <?php } ?>

                            <?php if($_COOKIE["categoria_id"] == 1 || $_COOKIE["usuario_id"] == $parametros["datos"]["usuario_id"]){ ?>

                                <a href="../views/index.php?accion=eliminarEntrada&entrada_id=<?=$parametros["datos"]["entrada_id"]?>" onclick="return confirm('¿Seguro que desea eliminar esta entrada?')">Eliminar</a>

                            <?php } ?>
                        </td>
                    </tr>
            
            </table>            

        </main>  

        <?php require '../includes/footer.php'; ?>

    </body>
</html>