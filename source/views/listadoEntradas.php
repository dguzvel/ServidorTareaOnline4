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
                    <th>TITULO</th>
                    <th>IMAGEN</th>
                    <th><a href="../views/index.php?accion=listarEntradas&direccion=<?=$parametros["direccion"]?>">FECHA<i id="icono" class="fa fa-sort"></i></a></th>
                    <th>OPERACIONES</th>
                </tr>

                <?php foreach($parametros["datos"] as $dat){ { ?> 

                    <tr>
                        <td><?=$dat["titulo"]?></td>
                        <td><img src="fotos/<?=$dat["imagen"]?>" height="100" width="100"/></td>
                        <td><?=$dat["fecha"]?></td>
                        <td>
                            <?php if($_COOKIE["categoria_id"] == 1 || $_COOKIE["usuario_id"] == $dat["usuario_id"]){ ?>

                                <a href="../views/index.php?accion=editarEntrada&entrada_id=<?=$dat["entrada_id"]?>">Editar</a>
                                &nbsp;&nbsp;&nbsp;&nbsp;

                            <?php } ?>
                        
                            <?php if($_COOKIE["categoria_id"] == 1 || $_COOKIE["usuario_id"] == $dat["usuario_id"]){ ?>

                                <a href="../views/index.php?accion=eliminarEntrada&entrada_id=<?=$dat["entrada_id"]?>" onclick="return confirm('??Seguro que desea eliminar esta entrada?')">Eliminar</a>
                                &nbsp;&nbsp;&nbsp;&nbsp;

                            <?php } ?>
                        
                            <a href="../views/index.php?accion=listarUnaEntrada&entrada_id=<?=$dat["entrada_id"]?>">Detallar</a>
                        </td>
                    </tr>

                <?php } } ?>
            
            </table>

            &nbsp;&nbsp;&nbsp;&nbsp;
            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-success" onclick="location.href='../views/index.php?accion=listarPDF'">Previsualizar PDF</button>
            </div>
            &nbsp;&nbsp;&nbsp;&nbsp;
            
            <!-- Navegaci??n de la p??gina -->
            <nav aria-label="Page navigation">
            <ul class="pagination">
                <!-- Si la pagina es la 1 se desactiva el bot??n de p??gina anterior, en caso contrario permite retroceder una p??gina -->
                <?php if($parametros["pagina"] == 1){ ?>

                <li class="page-item disabled">
                <a class="page-link" href="" aria-label="Previous">
                    <span aria-hidden="true">??</span>
                    <span class="sr-only">Anterior</span>
                </a>
                </li>
                <?php }else{ ?>

                <li class="page-item">
                <a class="page-link" href="../views/index.php?accion=listarEntradas&pagina=<?php echo $parametros["pagina"] - 1 ?>" aria-label="Previous">
                    <span aria-hidden="true">??</span>
                    <span class="sr-only">Anterior</span>
                </a>
                </li>
                <?php } ?>

                <?php
                //Comenzando en 1 y hasta el n??mero de paginas se a??adir??n botones num??ricos, activo si coincide con la p??gina en la que estamos
                for($i = 1; $i <= $parametros["numeroPagina"]; $i++){

                    if($parametros["pagina"] == $i){

                    echo "<li class='page-item active'>
                            <a class='page-link' href='../views/index.php?accion=listarEntradas&pagina=$i'>$i</a>
                            </li>";

                    }else{

                    echo "<li class='page-item'>
                            <a class='page-link' href='../views/index.php?accion=listarEntradas&pagina=$i'>$i</a>
                            </li>";

                    }  

                }

                ?>
                
                <!-- Si la pagina es la ??ltima se desactiva el bot??n de p??gina siguiente, en caso contrario permite avanzar una p??gina -->
                <?php if($parametros["pagina"] == $parametros["numeroPagina"]){ ?>

                <li class="page-item disabled">
                <a class="page-link" href="" aria-label="Previous">
                    <span aria-hidden="true">??</span>
                    <span class="sr-only">Siguiente</span>
                </a>
                </li>

                <?php }else{ ?>

                <li class="page-item">
                <a class="page-link" href="../views/index.php?accion=listarEntradas&pagina=<?php echo $parametros["pagina"] + 1 ?>" aria-label="Previous">
                    <span aria-hidden="true">??</span>
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