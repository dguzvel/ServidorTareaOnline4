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

                //Activamos el  modo de excepciones
                $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
            } catch (PDOException $e) {

                return $e->getMessage();

            }

        }

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

        public function listarUsuarios(){

            $resultadoModelo = ["correcto" => FALSE, "datos" => NULL, "pagina" => NULL, "numeroPagina" => NULL, "error" => NULL];
    
            try {               

                $pagina = isset($_GET["pagina"]) ? (int)$_GET["pagina"] : 1;
                $resultadoModelo["pagina"] = $pagina;

                $filasPorPagina = 2;

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

                $numeroPagina = ceil($totalArticulos / $filasPorPagina);
                $resultadoModelo["numeroPagina"] = $numeroPagina;                
    
    
            } catch(PDOException $e){
    
                $resultadoModelo["error"] = $e->getMessage();

            }
    
            return $resultadoModelo;

        }

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

    }

?>