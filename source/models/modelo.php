<?php

    class modelo{

        private $conexion;
        
        private $host = "localhost";
        private $nombreBase = "bdblog";
        private $usuario = "root";
        private $password = "";

        public function __construct(){
            $this->conectar();
        }

        //Creación de Base de Datos y Tablas
        
        public function conectar(){

            try {

                //Creamos la Base de Datos
                $this->conexion = new PDO("mysql:host=$this->host", $this->usuario, $this->password);
                $sql = "CREATE DATABASE IF NOT EXISTS $this->nombreBase";
                $this->conexion->exec($sql);
                
                //Nos conectamos a la Base de Datos
                $this->conexion = new PDO("mysql:host=$this->host; dbname=$this->nombreBase", $this->usuario, $this->password);

                //Creamos las Tablas
                //Tabla Categorías, contiene información sobre los distintos niveles de acceso de los usuarios
                $sql = "CREATE TABLE IF NOT EXISTS categorias(

                    categoria_id int(255) auto_increment not null,
                    nombre  varchar(50) not null,
                    CONSTRAINT pk_categoria PRIMARY KEY (categoria_id),
                    CONSTRAINT uq_nombre UNIQUE (nombre)

                );";
                $this->conexion->exec($sql);

                //Tabla Usuarios, que contiene a todos los usuarios registrados
                $sql = "CREATE TABLE IF NOT EXISTS usuarios(

                    usuario_id int(255) auto_increment not null,
                    nick  varchar(50) not null,
                    nombre  varchar(50) not null,
                    apellidos   varchar(255) not null,
                    email   varchar(255) not null,
                    password    varchar(255) not null,
                    imagen  varchar(255) not null,
                    categoria_id int(255) not null,
                    CONSTRAINT pk_usuario PRIMARY KEY (usuario_id),
                    CONSTRAINT uq_nick UNIQUE (nick),
                    CONSTRAINT fk_cateusuario FOREIGN KEY (categoria_id) REFERENCES categorias (categoria_id)

                );";
                $this->conexion->exec($sql);

                //Tabla Entradas, que consiste en las entradas que se registran en el blog
                $sql = "CREATE TABLE IF NOT EXISTS entradas(

                    entrada_id int(255) auto_increment not null,
                    usuario_id int(255) not null,
                    categoria_id int(255) not null,
                    titulo  varchar(255) not null,
                    imagen  varchar(255) not null,
                    descripcion text not null,
                    fecha datetime(6) not null,
                    CONSTRAINT pk_entrada PRIMARY KEY (entrada_id),
                    CONSTRAINT fk_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (usuario_id),
                    CONSTRAINT fk_categoria FOREIGN KEY (categoria_id) REFERENCES categorias (categoria_id)

                );";
                $this->conexion->exec($sql);

                //Tabla Logs, que guarda los registros de las operaciones realizadas
                $sql = "CREATE TABLE IF NOT EXISTS logs(

                    log_id int(255) auto_increment not null,
                    usuario_id int(255) not null,
                    fecha datetime(6) not null,
                    operacion varchar(255),
                    CONSTRAINT pk_log PRIMARY KEY (log_id),
                    CONSTRAINT fk_usuariolog FOREIGN KEY (usuario_id) REFERENCES usuarios (usuario_id)

                );";
                $this->conexion->exec($sql);

                //Activamos el  modo de excepciones
                $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
            } catch (PDOException $e) {

                return $e->getMessage();

            }

        }

        //Login y roles

        /**
         * Función que devuelve la información de todo el contenido de la tabla usuarios. Posteriormente,
         * será utilizado para conocer los nicks y passwords de registro y, con ellos, permitir el login.
         * 
         * @return void
         */
        public function login(){

            $resultadoModelo = ["correcto" => FALSE, "datos" => NULL, "error" => NULL];
    
            try {               

                $sql = "SELECT * FROM usuarios;";

                $query = $this->conexion->query($sql);
                $query->setFetchMode(PDO::FETCH_ASSOC);
                $articulos = $query->fetchAll();
                
                if ($query){ 
                    
                    $resultadoModelo["correcto"] = TRUE;
                    $resultadoModelo["datos"] = $articulos;

                }              
    
            } catch(PDOException $e){
    
                $resultadoModelo["error"] = $e->getMessage();

            }
    
            return $resultadoModelo;

        }    
        
        /**
         * Mediante un nick, se selecciona a un usuario concreto, del cual querremos conocer su ID
         * y su Categoría (1 Administrador o 2 Usuario).
         *
         * @param string $nick
         * @return void
         */
        public function categoria($nick){

            $sql = "SELECT * FROM usuarios WHERE nick = :nick;";
    
            $query = $this->conexion->prepare($sql);

            $query->execute(['nick' => $nick]);
             
            if ($query) { 

                $resultadoModelo["correcto"] = TRUE;
                $resultadoModelo["datos"] = $query->fetch(PDO::FETCH_ASSOC);

            }

            return $resultadoModelo;

        }

        //Modelo LOG

        /**
         * Inserta los datos en la tabla logs que le son enviados mediante un array.
         *
         * @param array $datos
         * @return void
         */
        public function insertarLog($datos){

            try {
                
                //Iniciamos la transacción de Datos
                $this->conexion->beginTransaction();
                //Insertar valores en una tabla de la Base de Datos
                $sql = "INSERT INTO logs VALUES(

                    NULL,
                    :usuario_id,
                    :fecha,
                    :operacion

                );";
                $query = $this->conexion->prepare($sql);
                $query->execute([

                        'usuario_id' => $datos["usuario_id"],
                        'fecha' => $datos["fecha"],
                        'operacion' => $datos["operacion"]

                        ]);

                if($query){

                    $this->conexion->commit();

                }

            } catch (PDOException $e) {

                $this->conexion->rollback();

            }

        }

        /**
         * Devuelve toda la información almacenada en la tabla logs
         *
         * @return void
         */
        public function listarLogs(){

            $resultadoModelo = ["correcto" => FALSE, "datos" => NULL, "error" => NULL];
    
            try {               

                $sql = "SELECT * FROM logs;";

                $query = $this->conexion->query($sql);
                $query->setFetchMode(PDO::FETCH_ASSOC);
                $articulos = $query->fetchAll();
                
                if ($query){ 
                    
                    $resultadoModelo["correcto"] = TRUE;
                    $resultadoModelo["datos"] = $articulos;

                }             
    
            } catch(PDOException $e){
    
                $resultadoModelo["error"] = $e->getMessage();

            }
    
            return $resultadoModelo;

        }

        /**
         * Elimina un registro de la tabla logs cuya ID coincida con aquella que se le pasa como
         * parámetro a la función
         *
         * @param int $id
         * @return void
         */
        public function eliminarLog($id){

            $resultadoModelo = ["correcto" => FALSE, "error" => NULL];
    
            if ($id and is_numeric($id)){
    
                try{
    
                    $sql = "DELETE FROM logs WHERE log_id = :log_id;";
    
                    $query = $this->conexion->prepare($sql);
    
                    $query->execute(['log_id' => $id]);
            
                    if ($query){
                        
                       $resultadoModelo["correcto"] = TRUE;
            
                    }
            
                } catch (PDOException $e){ 
            
                    $resultadoModelo["error"] = $e->getMessage();

                }
            
            } else {
    
                $resultadoModelo["correcto"] = FALSE;

            }
    
            return $resultadoModelo;
            
        }

        //Modelo USUARIO

        /**
         * Realiza la inserción de una nueva fila en la tabla usuarios
         *
         * @param array $datos
         * @return void
         */
        public function insertarUsuario($datos){

            $resultadoModelo = [ "correcto" => FALSE, "error" => NULL ];

            try {
                
                //Iniciamos la transacción de Datos
                $this->conexion->beginTransaction();
                //Insertar valores en una tabla de la Base de Datos
                $sql = "INSERT INTO usuarios VALUES(

                    NULL,
                    :nick,
                    :nombre,
                    :apellidos,
                    :email,
                    :password,
                    :imagen,
                    :categoria_id

                );";
                $query = $this->conexion->prepare($sql);
                $query->execute([

                        'nick' => $datos["nick"],
                        'nombre' => $datos["nombre"],
                        'apellidos' => $datos["apellidos"],
                        'email' => $datos["email"],
                        'password' => $datos["password"],
                        'imagen' => $datos["imagen"],
                        'categoria_id' => 2

                        ]);

                if($query){

                    $this->conexion->commit();
                    $resultadoModelo ["correcto"] = TRUE;

                }

            } catch (PDOException $e) {

                $this->conexion->rollback();
                $resultadoModelo ["error"] = $e->getMessage();

            }

                return $resultadoModelo;

        }

        /**
         * Devuelve toda la información almacenada en la tabla usuarios y la divide por páginas
         *
         * @return void
         */
        public function listarUsuarios(){

            $resultadoModelo = ["correcto" => FALSE, "datos" => NULL, "pagina" => NULL, "numeroPagina" => NULL, "error" => NULL];
    
            try {               

                //La página por defecto será la 1 a menos que una sea seleccionada
                $pagina = isset($_GET["pagina"]) ? (int)$_GET["pagina"] : 1;
                $resultadoModelo["pagina"] = $pagina;

                $filasPorPagina = 2;

                //Cálculo de la primera fila que debe aparecer en cada página
                $inicio = ($pagina > 1) ? ($pagina * $filasPorPagina - $filasPorPagina) : 0;

                $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM usuarios LIMIT $inicio, $filasPorPagina;";

                $query = $this->conexion->query($sql);
                $query->setFetchMode(PDO::FETCH_ASSOC);
                $articulos = $query->fetchAll();
                
                if ($query){ 
                    
                    $resultadoModelo["correcto"] = TRUE;
                    $resultadoModelo["datos"] = $articulos;

                }

                $totalArticulos = $this->conexion->query("SELECT FOUND_ROWS() as total;");
                $totalArticulos = $totalArticulos->fetch()["total"];

                //Número total de páginas, resultado de la división de los artículos existentes en la tabla entre los que aparacen en cada página
                $numeroPagina = ceil($totalArticulos / $filasPorPagina);
                $resultadoModelo["numeroPagina"] = $numeroPagina;                
    
    
            } catch(PDOException $e){
    
                $resultadoModelo["error"] = $e->getMessage();

            }
    
            return $resultadoModelo;

        }

        /**
         * Devuelve la información de un usuario específico mediante su ID
         *
         * @param int $id
         * @return void
         */
        public function listarUnUsuario($id){

            $resultadoModelo = ["correcto" => FALSE, "datos" => NULL, "error" => NULL];
    
            if ($id && is_numeric($id)){
    
                try {
    
                    $sql = "SELECT * FROM usuarios WHERE usuario_id = :usuario_id;";
    
                    $query = $this->conexion->prepare($sql);
    
                    $query->execute(['usuario_id' => $id]);
                     
                    if ($query) { 
    
                        $resultadoModelo["correcto"] = TRUE;
                        $resultadoModelo["datos"] = $query->fetch(PDO::FETCH_ASSOC);
    
                    }
    
                } catch (PDOException $e) {
    
                    $resultadoModelo["error"] = $e->getMessage();
    
                }
    
            }
          
            return $resultadoModelo;
    
        }

        /**
         * Mediante un array con datos se modifican unos valores determinados del usuario seleccionado mediante su ID
         *
         * @param array $datos
         * @return void
         */
        public function editarUsuario($datos){

            $resultadoModelo = ["correcto" => FALSE, "error" => NULL];
    
            try{
    
                $this->conexion->beginTransaction();
    
                $sql = "UPDATE usuarios SET nombre = :nombre, apellidos = :apellidos, email = :email, 
                imagen = :imagen WHERE usuario_id = :usuario_id;";
    
                $query = $this->conexion->prepare($sql);
    
                $query->execute(['usuario_id' => $datos["usuario_id"], 'nombre' => $datos["nombre"],
                'apellidos' => $datos["apellidos"], 'email' => $datos["email"], 'imagen' => $datos["imagen"]]);
    
                if ($query){ 
    
                    $this->conexion->commit();
                    $resultadoModelo["correcto"] = TRUE;

                } 
    
            } catch (PDOException $e){
    
                $this->conexion->rollback();
                $resultadoModelo["error"] = $e->getMessage();
    
            }
    
            return $resultadoModelo;
    
        }      

        /**
         * Se elimina a un usuario indentificado por su ID
         *
         * @param int $id
         * @return void
         */
        public function eliminarUsuario($id){

            $resultadoModelo = ["correcto" => FALSE, "error" => NULL];
    
            if ($id and is_numeric($id)){
    
                try{
    
                    $sql = "DELETE FROM usuarios WHERE usuario_id = :usuario_id;";
    
                    $query = $this->conexion->prepare($sql);
    
                    $query->execute(['usuario_id' => $id]);
            
                    if ($query){
                        
                       $resultadoModelo["correcto"] = TRUE;
            
                    }
            
                } catch (PDOException $e){ 
            
                    $resultadoModelo["error"] = $e->getMessage();

                }
            
            } else {
    
                $resultadoModelo["correcto"] = FALSE;

            }
    
            return $resultadoModelo;
            
        }

        /**
         * Devuelve una consulta a la tabla usuarios para poder exportarla como un excel
         *
         * @return void
         */
        public function listarEXCEL(){         

            $sql = "SELECT * FROM usuarios;";

            $query = $this->conexion->query($sql);           
    
            return $query;

        }

        //Modelo ENTRADA

        /**
         * Realiza la inserción de una nueva fila en la tabla entradas
         *
         * @param array $datos
         * @return void
         */
        public function insertarEntrada($datos){

            $resultadoModelo = [ "correcto" => FALSE, "error" => NULL ];

            try {
                
                //Iniciamos la transacción de Datos
                $this->conexion->beginTransaction();
                //Insertar valores en una tabla de la Base de Datos
                $sql = "INSERT INTO entradas VALUES(

                    NULL,
                    :usuario_id,
                    :categoria_id,
                    :titulo,
                    :imagen,
                    :descripcion,
                    :fecha

                );";
                $query = $this->conexion->prepare($sql);
                $query->execute([

                        'usuario_id' => $datos["usuario_id"],
                        'categoria_id' => $datos["categoria_id"],
                        'titulo' => $datos["titulo"],
                        'imagen' => $datos["imagen"],
                        'descripcion' => $datos["descripcion"],
                        'fecha' => $datos["fecha"]

                        ]);

                if($query){

                    $this->conexion->commit();
                    $resultadoModelo ["correcto"] = TRUE;

                }

            } catch (PDOException $e) {

                $this->conexion->rollback();
                $resultadoModelo ["error"] = $e->getMessage();

            }

                return $resultadoModelo;

        }

        /**
         * Devuelve toda la información almacenada en la tabla entradas y la divide por páginas
         *
         * @return void
         */
        public function listarEntradas(){

            $resultadoModelo = ["correcto" => FALSE, "datos" => NULL, "pagina" => NULL, "numeroPagina" => NULL, "error" => NULL, "direccion" => NULL];
    
            try {               

                $pagina = isset($_GET["pagina"]) ? (int)$_GET["pagina"] : 1;
                $resultadoModelo["pagina"] = $pagina;

                $direccion = "";
                $direccion = ($direccion == "ASC") ? "DESC" : "ASC";
                $resultadoModelo["direccion"] = $direccion;

                $filasPorPagina = 2;

                $inicio = ($pagina > 1) ? ($pagina * $filasPorPagina - $filasPorPagina) : 0;

                //Las filas de la tabla de entradas se pueden ordenar de manera ascendente o descendente a través de la FECHA
                $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM entradas ORDER BY fecha $direccion LIMIT $inicio, $filasPorPagina;";

                $query = $this->conexion->query($sql);
                $query->setFetchMode(PDO::FETCH_ASSOC);
                $articulos = $query->fetchAll();
                
                if ($query){ 
                    
                    $resultadoModelo["correcto"] = TRUE;
                    $resultadoModelo["datos"] = $articulos;

                }

                $totalArticulos = $this->conexion->query("SELECT FOUND_ROWS() as total;");
                $totalArticulos = $totalArticulos->fetch()["total"];

                $numeroPagina = ceil($totalArticulos / $filasPorPagina);
                $resultadoModelo["numeroPagina"] = $numeroPagina;                
    
    
            } catch(PDOException $e){
    
                $resultadoModelo["error"] = $e->getMessage();

            }
    
            return $resultadoModelo;

        }

        /**
         * Devuelve la información de una entrada específica mediante su ID
         *
         * @param int $id
         * @return void
         */
        public function listarUnaEntrada($id){

            $resultadoModelo = ["correcto" => FALSE, "datos" => NULL, "error" => NULL];
    
            if ($id && is_numeric($id)){
    
                try {
    
                    $sql = "SELECT * FROM entradas WHERE entrada_id = :entrada_id;";
    
                    $query = $this->conexion->prepare($sql);
    
                    $query->execute(['entrada_id' => $id]);
                     
                    if ($query) { 
    
                        $resultadoModelo["correcto"] = TRUE;
                        $resultadoModelo["datos"] = $query->fetch(PDO::FETCH_ASSOC);
    
                    }
    
                } catch (PDOException $e) {
    
                    $resultadoModelo["error"] = $e->getMessage();
    
                }
    
            }
          
            return $resultadoModelo;
    
        }

        /**
         * Mediante un array con datos se modifican unos valores determinados de la entrada seleccionada mediante su ID
         *
         * @param array $datos
         * @return void
         */
        public function editarEntrada($datos){

            $resultadoModelo = ["correcto" => FALSE, "error" => NULL];
    
            try{
    
                $this->conexion->beginTransaction();
    
                $sql = "UPDATE entradas SET titulo = :titulo, imagen = :imagen, descripcion = :descripcion WHERE entrada_id = :entrada_id;";
    
                $query = $this->conexion->prepare($sql);
    
                $query->execute(['entrada_id' => $datos["entrada_id"], 'titulo' => $datos["titulo"],
                'imagen' => $datos["imagen"], 'descripcion' => $datos["descripcion"]]);
    
                if ($query){ 
    
                    $this->conexion->commit();
                    $resultadoModelo["correcto"] = TRUE;

                } 
    
            } catch (PDOException $e){
    
                $this->conexion->rollback();
                $resultadoModelo["error"] = $e->getMessage();
    
            }
    
            return $resultadoModelo;
    
        }      

        /**
         * Se elimina una entrada identificada por su ID
         *
         * @param int $id
         * @return void
         */
        public function eliminarEntrada($id){

            $resultadoModelo = ["correcto" => FALSE, "error" => NULL];
    
            if ($id and is_numeric($id)){
    
                try{
    
                    $sql = "DELETE FROM entradas WHERE entrada_id = :entrada_id;";
    
                    $query = $this->conexion->prepare($sql);
    
                    $query->execute(['entrada_id' => $id]);
            
                    if ($query){
                        
                       $resultadoModelo["correcto"] = TRUE;
            
                    }
            
                } catch (PDOException $e){ 
            
                    $resultadoModelo["error"] = $e->getMessage();

                }
            
            } else {
    
                $resultadoModelo["correcto"] = FALSE;

            }
    
            return $resultadoModelo;
            
        }

        /**
         * Mediante parte del contenido de la descripción, en una barra de búsqueda, se listarán las entradas que contengan
         * esa palabra o palabras.
         *
         * @param string $descripcion
         * @return void
         */
        public function buscarEntrada($descripcion){

            $resultadoModelo = ["correcto" => FALSE, "datos" => NULL, "error" => NULL];
    
            if ($descripcion){
    
                try {
                    
                    //Con LIKE el valor no tiene que ser idéntico, bastante con que contenga el valor que se almacene en descripcion.
                    $sql = "SELECT * FROM entradas WHERE descripcion LIKE :descripcion;";
    
                    $query = $this->conexion->prepare($sql);
    
                    $query->execute(['descripcion' => "%".$descripcion."%"]);
                     
                    if ($query) { 
    
                        $resultadoModelo["correcto"] = TRUE;
                        $resultadoModelo["datos"] = $query->fetch(PDO::FETCH_ASSOC);
    
                    }
    
                } catch (PDOException $e) {
    
                    $resultadoModelo["error"] = $e->getMessage();
    
                }
    
            }
          
            return $resultadoModelo;
    
        }

        /**
         * Devuelve toda la información de la tabla entradas sin paginar, para mostrarla en una vista que se podrá
         * descargar en formato pdf.
         *
         * @return void
         */
        public function listarPDF(){

            $resultadoModelo = ["correcto" => FALSE, "datos" => NULL, "error" => NULL];             

            $sql = "SELECT * FROM entradas;";

            $query = $this->conexion->query($sql);
            $query->setFetchMode(PDO::FETCH_ASSOC);
            $articulos = $query->fetchAll();
            
            if ($query){ 
                
                $resultadoModelo["correcto"] = TRUE;
                $resultadoModelo["datos"] = $articulos;

            }             
    
            return $resultadoModelo;

        }

    }

?>