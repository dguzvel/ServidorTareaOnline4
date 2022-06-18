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
                    <th>E-MAIL</th>
                    <th>IMAGEN</th>
                    <th>OPERACIONES</th>
                </tr>

                <?php foreach($parametros["datos"] as $dat){ { ?> 

                    <tr>
                        <td><?=$dat["nick"]?></td>
                        <td><?=$dat["email"]?></td>
                        <td><img src="fotos/<?=$dat["imagen"]?>" height="100" width="100"/></td>
                        <td>
                            <?php if($_COOKIE["categoria_id"] == 1 || $_COOKIE["usuario_id"] == $dat["usuario_id"]){ ?>

                                <a href="../views/index.php?accion=editarUsuario&usuario_id=<?=$dat["usuario_id"]?>">Editar</a>
                                &nbsp;&nbsp;&nbsp;&nbsp;

                            <?php } ?>
                        
                            <?php if($_COOKIE["categoria_id"] == 1 || $_COOKIE["usuario_id"] == $dat["usuario_id"]){ ?>

                                <a href="../views/index.php?accion=eliminarUsuario&usuario_id=<?=$dat["usuario_id"]?>" onclick="return confirm('¿Seguro que desea eliminar a este usuario?')">Eliminar</a>
                                &nbsp;&nbsp;&nbsp;&nbsp;

                            <?php } ?>
                        
                            <a href="../views/index.php?accion=listarUnUsuario&usuario_id=<?=$dat["usuario_id"]?>">Detallar</a>
                        </td>
                    </tr>

                <?php } } ?>
            
            </table>
            
            <!-- Navegación de la página -->
            <nav aria-label="Page navigation">
            <ul class="pagination">
                <!-- Si la pagina es la 1 se desactiva el botón de página anterior, en caso contrario permite retroceder una página -->
                <?php if($parametros["pagina"] == 1){ ?>

                <li class="page-item disabled">
                <a class="page-link" href="" aria-label="Previous">
                    <span aria-hidden="true">«</span>
                    <span class="sr-only">Anterior</span>
                </a>
                </li>
                <?php }else{ ?>

                <li class="page-item">
                <a class="page-link" href="../views/index.php?accion=listarUsuarios&pagina=<?php echo $parametros["pagina"] - 1 ?>" aria-label="Previous">
                    <span aria-hidden="true">«</span>
                    <span class="sr-only">Anterior</span>
                </a>
                </li>
                <?php } ?>

                <?php
                //Comenzando en 1 y hasta el número de paginas se añadirán botones numéricos, activo si coincide con la página en la que estamos
                for($i = 1; $i <= $parametros["numeroPagina"]; $i++){

                    if($parametros["pagina"] == $i){

                    echo "<li class='page-item active'>
                            <a class='page-link' href='../views/index.php?accion=listarUsuarios&pagina=$i'>$i</a>
                            </li>";

                    }else{

                    echo "<li class='page-item'>
                            <a class='page-link' href='../views/index.php?accion=listarUsuarios&pagina=$i'>$i</a>
                            </li>";

                    }  

                }

                ?>
                
                <!-- Si la pagina es la última se desactiva el botón de página siguiente, en caso contrario permite avanzar una página -->
                <?php if($parametros["pagina"] == $parametros["numeroPagina"]){ ?>

                <li class="page-item disabled">
                <a class="page-link" href="" aria-label="Previous">
                    <span aria-hidden="true">»</span>
                    <span class="sr-only">Siguiente</span>
                </a>
                </li>

                <?php }else{ ?>

                <li class="page-item">
                <a class="page-link" href="../views/index.php?accion=listarUsuarios&pagina=<?php echo $parametros["pagina"] + 1 ?>" aria-label="Previous">
                    <span aria-hidden="true">»</span>
                    <span class="sr-only">Siguiente</span>
                </a>
                </li>

                <?php } ?>

            </ul>
            </nav>            

        </main>  

        <?php require '../includes/footer.php'; ?>

    </body>
</html>