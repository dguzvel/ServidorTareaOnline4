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

            $resultadoModelo = [ "correcto" => FALSE, "datos" => NULL, "error" => NULL ];

            try {

                //Creamos la Base de Datos
                $this->conexion = new PDO("mysql:host=$this->host", $this->usuario, $this->password);
                $sql = "CREATE DATABASE IF NOT EXISTS $this->nombreBase";
                $this->conexion->exec($sql);
                
                //Nos conectamos a la Base de Datos
                $this->conexion = new PDO("mysql:host=$this->host; dbname=$this->nombreBase", $this->usuario, $this->password);

                //Creamos las Tablas
                //Tabla Usuarios, que contiene a todos los usuarios registrados
                $sql = "CREATE TABLE IF NOT EXISTS usuarios(

                    usuario_id int(255) auto_increment not null,
                    nick  varchar(50),
                    nombre  varchar(50),
                    apellidos   varchar(255),
                    email   varchar(255),
                    password    varchar(255),
                    imagen  varchar(255),
                    CONSTRAINT pk_usuario PRIMARY KEY (usuario_id)

                );";
                $this->conexion->exec($sql);

                //Tabla Categorías, contiene información sobre los distintos niveles de acceso de los usuarios
                $sql = "CREATE TABLE IF NOT EXISTS categorias(

                    categoria_id int(255) auto_increment not null,
                    nombre  varchar(50),
                    CONSTRAINT pk_categoria PRIMARY KEY (categoria_id)

                );";
                $this->conexion->exec($sql);

                //Tabla Entradas, que consiste en las entradas que se registran en el blog
                $sql = "CREATE TABLE IF NOT EXISTS entradas(

                    entrada_id int(255) auto_increment not null,
                    usuario_id int(255),
                    categoria_id int(255),
                    titulo  varchar(255),
                    imagen  varchar(255),
                    descripcion text,
                    fecha datetime(6),
                    CONSTRAINT pk_entrada PRIMARY KEY (entrada_id),
                    CONSTRAINT fk_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (usuario_id),
                    CONSTRAINT fk_categoria FOREIGN KEY (categoria_id) REFERENCES categorias (categoria_id)

                );";
                $this->conexion->exec($sql);

                //Activamos el  modo de excepciones
                $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $resultadoModelo ["correcto"] = TRUE;
                echo '<div class="alert alert-success text-center">La conexión a la Base de Datos se ha realizado correctamente :)</div>';

            } catch (PDOException $e) {

                //$resultadoModelo ["error"] =
                echo '<div class="alert alert-danger text-center"> No se ha podido conectar a la Base de Datos :( <br>'.$e->getMessage().'</div>';

            }

            return $resultadoModelo;
            $this->conexion = null;

        }

    }

?>