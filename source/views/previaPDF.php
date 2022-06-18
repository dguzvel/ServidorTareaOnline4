<?php

    ob_start();

?>

<!DOCTYPE html>
<html lang="es">
    <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

        <style type="text/css">

            .encabezado{

            color:white;
            text-align: center;
            background-color: black;
            padding: 10px; 

            }

            .cuerpo{

            width: 100%;
            margin-top: 25px;
            margin-bottom: 25px;
            min-width: 100%;
            background-color: white;

            }

            table {

            width: 100%;
            border: 1px solid;

            }

            th {

            width: 25%;
            text-align: center;
            vertical-align: top;
            border: 1px solid;
            background-color: black;
            color: white;

            }

            td {

            width: 25%;
            text-align: center;
            vertical-align: top;
            border: 1px solid;
            background-color: lightgrey;
            color: black;

            }


            .pie{

            color:white;
            text-align: center;
            background-color: black;
            min-width: 100%;
            padding: 10px;
            margin-top: 25px;

            }

        </style>

    </head>
    <body>

        <?php require '../includes/sesion.php'; ?>

            <header class="encabezado text-center">
            <!-- Encabezado de la página -->
                <h1>DWES Tarea Online 4 - Mi Blog</h1>
            </header>

            <main class="cuerpo text-center">

                <div class="text-center">           
                
                <table class="table table-dark table-striped table-hover text-center align-middle" style="font-size: 20px;">

                    <tr>
                        <th>USUARIO</th>
                        <th>TITULO</th>
                        <th>IMAGEN</th>
                        <th>DESCRIPCION</th>
                    </tr>

                    <?php foreach($parametros["datos"] as $dat){ { ?> 

                        <tr>
                            <td><?=$dat["usuario_id"]?></td>
                            <td><?=$dat["titulo"]?></td>
                            
                            <td><img class="img-thumbnail rounded" src="http://<?php echo $_SERVER['HTTP_HOST']?>/servidortareaonline4 copy/source/views/fotos/<?=$dat["imagen"]?>" height="150" width="150"/></td>
                            <td><?=$dat["descripcion"]?></td>
                        </tr>

                    <?php } } ?>
                
                </table>

        </main>  

        <footer class="pie text-center">
        <!-- Pie de la página -->
            <p>Domingo Guzmán Vélez</p>
        </footer>

    </body>
</html>

<?php

    $html = ob_get_clean();

    require_once '../libraries/dompdf/autoload.inc.php';
    use Dompdf\Dompdf;

    $dompdf = new Dompdf();

    $opciones = $dompdf->getOptions();
    $opciones->set(array('isRemoteEnabled' => true));
    $dompdf->setOptions($opciones);


    $dompdf->loadHtml($html);
    $dompdf->setPaper('letter');
    $dompdf->render();
    $dompdf->stream("entradas.pdf", array("Attachment" => false));

?>