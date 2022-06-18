<header class="encabezado text-center">
<!-- Encabezado de la página -->
    <h1>
        <img class="textoImagen" src="../images/php.png" />
            <a href="../views/inicio.php">DWES Tarea Online 4 - Mi Blog</a>
        <img class="textoImagen" src="../images/php.png" />
    </h1>
</header>

<main>
    <nav class="navbar navbar-dark bg-dark">
    <!-- Incluimos un nav con un botón toggler, tres líneas verticales, que podrá desplegarse y mostrar un menú -->
    <div class="navbar navbar-toggleable-md navbar-light bg-faded">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
    </div>
    <a class="navbar-brand" href="../includes/logout.php">Cerrar sesión</a>
    </nav>
    <div class="collapse" id="navbarToggleExternalContent">
        <div class="bg-dark p-4">
            <?php if($_COOKIE["categoria_id"] == 1){ ?>

                <button type="button" class="btn btn-light" onclick="location.href='formularioUsuario.php'">Registro de Usuarios</button>
                &nbsp;&nbsp;&nbsp;&nbsp;

            <?php } ?>
                <button type="button" class="btn btn-light" onclick="location.href='../views/index.php?accion=listarUsuarios'">Listado de Usuarios</button>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <button type="button" class="btn btn-light" onclick="location.href='formularioEntrada.php'">Dejar una Entrada</button>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <button type="button" class="btn btn-light" onclick="location.href='../views/index.php?accion=listarEntradas'">Listado de Entradas</button>
                &nbsp;&nbsp;&nbsp;&nbsp;
            <?php if($_COOKIE["categoria_id"] == 1){ ?>

                <button type="button" class="btn btn-light" onclick="location.href='../views/index.php?accion=listarLogs'">Tabla de Logs</button>

            <?php } ?>
        </div>

            <form action="" method="GET">

            <div class="input-group">  
            <input class="form-control" name="" placeholder="¿Busca algo en concreto? ... Busque aquí el contenido de las entradas que desee visualizar">
            <div class="input-group-append">
                <button class="btn btn-outline-success" type="submit"><i id="icono" class="fa fa-search"></i></button>
            </div>

            </form>

        </div>

    </div>