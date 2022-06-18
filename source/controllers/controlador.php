<?php

    require_once '../models/modelo.php';

    class controlador {
        
        private $modelo;
        private $mensajes;
        
        public function __construct() {

            $this->modelo = new modelo();
            $this->mensajes = [ ];

        }

        //Inicio y Login

        public function index(){

            error_reporting(E_ERROR | E_WARNING | E_PARSE);
            
            $nickAdmin = "Admin";
            $passwordAdmin = "12345";

            $nicksValidos = NULL;
            $passwordsValidos = NULL;
    
            $resultadoModelo = $this->modelo->login();
            
            if ($resultadoModelo["correcto"]){
                
                foreach ($resultadoModelo["datos"] as $res) :

                    $nicksValidos[] = $res["nick"];
                    $passwordsValidos[] = $res["password"];
                
                endforeach;
    
            } 

            require_once "../libraries/recaptchalib.php";
        
            //Se accede si se pulsa el botón para enviar información del formulario de login
            if(isset($_POST["submit"])){
                //Si ambos campos, usuario y conraseña, tienen información
                if((isset($_POST["usuario"]) && isset($_POST["password"])) && (!empty($_POST["usuario"]) && !empty($_POST["password"]))){
                    //Se iniciará sesión si el usuario y contraseña son válidos
        
                    //if ($_POST["usuario"] == strcasecmp($_POST["usuario"], $nicksValidos[0]) && $_POST["password"] == $passwordsValidos[0]){
                    if ((in_array($_POST["usuario"], $nicksValidos) && in_array(sha1($_POST["password"]), $passwordsValidos)) ||
                        ($_POST["usuario"] == strcasecmp($_POST["usuario"], $nickAdmin) && $_POST["password"] == $passwordAdmin)){
                            
                        session_start(); //Se activa el uso de sesiones
                        $_SESSION["logueado"] = 1; //La sessión logueado se establece en 1, puede servir como contador de sesiones
                        $_SESSION["usuario"] = $_POST["usuario"];//El valor usuario de la sesión será el introducido en el formulario
                        
                        $resultadoCategoria = $this->modelo->categoria($_POST["usuario"]);    
                        $categoria_id = $resultadoCategoria["datos"]["categoria_id"];
                        $usuario_id = $resultadoCategoria["datos"]["usuario_id"];

                        setcookie ("categoria_id", $categoria_id, time() + (15 * 24 * 60 * 60));
                        setcookie ("usuario_id", $usuario_id, time() + (15 * 24 * 60 * 60));

                        //Si el checkbox de recordar está marcado, se crearán las cookies
                        if(isset($_POST["recordar"]) && ($_POST["recordar"] == "on")){
        
                            setcookie ("usuario", $_POST["usuario"] , time() + (15 * 24 * 60 * 60));//Nombre, valor y vencimiento de la cookie
                            setcookie ("password", $_POST["password"], time() + (15 * 24 * 60 * 60));
                            setcookie ("recordar", $_POST["recordar"], time() + (15 * 24 * 60 * 60));
        
                        }else {//En caso contrario, las cookies se eliminarán. Obtendrán un valor vacío
                            
                            if(isset($_COOKIE["usuario"])){
                                setcookie ("usuario","");
                            }
                            if(isset($_COOKIE["password"])) {
                                setcookie ("password","");
                            }
                            if(isset($_COOKIE["recordar"])) {
                                setcookie ("recordar","");
                            }
        
                        }
        
                        //Si el checkbox de mantener la sesión abierta está marcado, se crea una cookie para esta sesión exclusiva del usuario
                        if(isset($_POST["abierta"]) && ($_POST["abierta"] == "on")){
        
                            setcookie ("abierta", $_POST["usuario"] , time() + (15 * 24 * 60 * 60));//Nombre, valor y vencimiento de la cookie
        
                        }else {//En caso contrario, las cookies se eliminarán. Obtendrán un valor vacío
                            
                            if(isset($_COOKIE["abierta"])){
                                setcookie ("abierta","");
                            }
        
                        }
                        
                        require_once "../includes/captcha.php";
        
                    } else {
                        //Si los valores de usuario y contraseña no son correctos volvemos a login y mostramos el error de datos no válidos
                        header ("Location: ./login.php?error=incorrecto");
        
                    }
                } else {
                    //Si los campos han quedado vacíos volvemos a login y lo indicamos con un mensaje de error
                    header ("Location: ./login.php?error=vacio");
        
                }
        
            }

            include_once '../views/login.php';

        }

        //Funciones para Logs

        public function listarLogs(){

            $parametros = ["titulo" => "Mi Blog PHP-MVC", "datos" => NULL, "mensajes" => []];
    
            $resultadoModelo = $this->modelo->listarLogs();
    
            if ($resultadoModelo["correcto"]){
    
                $parametros["datos"] = $resultadoModelo["datos"];
    
                $this->mensajes[] = ["tipo" => "success",
                "mensaje" => "Los registros Log se han listado correctamente <br/>"];
    
            } else {
    
                $this->mensajes[] = ["tipo" => "danger", "mensaje" => 
                "No se han podido listar los registros Log <br/>"];

            }
    
            $parametros["mensajes"] = $this->mensajes; 
    
            include_once '../views/listadoLogs.php';
    
        }

        public function eliminarLog(){

            $id = $_GET["log_id"];
    
            if (isset($_GET["log_id"]) and is_numeric($_GET["log_id"])){
    
                $resultadoModelo = $this->modelo->eliminarLog($id);
    
                if ($resultadoModelo["correcto"] == TRUE){
    
                    $this->mensajes[] = ["tipo" => "success", "mensaje" => "El registro Log ha sido eliminado correctamente <br>"];
    
                } else {
    
                    $this->mensajes[] = ["tipo" => "danger", "mensaje" => "No se ha podido eliminar el registro Log debido a un error"];
                }
    
            } else {
    
                $this->mensajes[] = ["tipo" => "danger","mensaje" => 
                "El registro Log que se ha intentado eliminar no existe o no se ha podido acceder a él"];

            }
    
            $this->listarLogs();

        }   

        //Funciones para Usuarios

        public function insertarUsuario(){

            $errores = array();
    
            //si se ha pulsado el botón del formulario y ha recibido datos
            if (isset($_POST) && !empty($_POST) && isset($_POST["submit"])){
        
                if(!empty($_POST["nick"]) && strlen($_POST["nick"]) <= 20){
        
                    $nick = trim($_POST["nick"]);
                    $nick = stripslashes($_POST["nick"]);
                    $nick = htmlspecialchars($_POST["nick"]);
                    $nick = filter_var($nick, FILTER_SANITIZE_STRING);
        
                }else{
        
                    $errores["nick"] = "Un nick no puede estar vacío ni tener más de 20 caracteres";
        
                }
        
                if(!empty($_POST["nombre"]) && strlen($_POST["nombre"]) <= 20 && !preg_match("/[\d]/", $_POST["nombre"])){
        
                    $nombre = trim($_POST["nombre"]);
                    $nombre = stripslashes($_POST["nombre"]);
                    $nombre = htmlspecialchars($_POST["nombre"]);
                    $nombre = filter_var($nombre, FILTER_SANITIZE_STRING);
        
                }else{
        
                    $errores["nombre"] = "Un nombre no puede estar vacío, tener más de 20 caracteres ni contener números";
        
                }
                
                if(!empty($_POST["apellidos"]) && !preg_match("/[\d]/", $_POST["apellidos"])){
        
                    $apellidos = trim($_POST["apellidos"]);
                    $apellidos = stripslashes($_POST["apellidos"]);
                    $apellidos = htmlspecialchars($_POST["apellidos"]);
                    $apellidos = filter_var($apellidos, FILTER_SANITIZE_STRING);
        
                }else{
        
                    $errores["apellidos"] = "Un apellido no puede estar vacío ni contener números";
        
                }
                
                if(!empty($_POST["email"]) && filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
        
                    //preg_match("/[a-z]{7}[\d]{3}@g.educaand.es/", $_POST["email"])
        
                    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
        
                }else{
        
                    $errores["email"] = "El e-mail no es válido";
        
                }
                
                if(!empty($_POST["password"]) && preg_match("/(?=.+[a-z])(?=.+[A-Z])(?=.+\d)(?=.+\W)[a-zA-Z\d\W]{6,}/", $_POST["password"])){
        
                    $password = ($_POST["password"]);
        
                }else{
        
                    $errores["password"] = "La contraseña debe contener al menos 6 caracteres y ser segura
                        (contiene mayúsculas, minúsculas, números y símbolos";
        
                }

                //si existe un archivo tipo imagen y no está vacío el campo
                if (isset($_FILES["imagen"]) and (!empty($_FILES["imagen"]["tmp_name"]))){
    
                    if (!is_dir("fotos")){ //si no existe un directorio llamado 'fotos', lo creará
    
                        $carpeta = mkdir("fotos", 0777, true);
    
                    } else {
    
                        $carpeta = true;
                    }
    
                    if ($carpeta){ //si está el directorio fotos, la imagen se moverá a dicho directorio
    
                        $nombreImagen= time()."-".$_FILES["imagen"]["name"];
    
                        $moverImagen = move_uploaded_file($_FILES["imagen"]["tmp_name"], "fotos/".$nombreImagen);
    
                        $imagen = $nombreImagen;
    
                        if ($moverImagen){
    
                            $imgCargada = true;
    
                        } else {
    
                            $imgCargada = false;
    
                            $errores["imagen"] = "La imagen no se cargó correctamente";
                            $this->mensajes[] = ["tipo" => "danger",
                                "mensaje" => "La imagen no se cargó correctamente"];

                        }
    
                    }
                }
    
                if (count($errores) == 0) {
                    $resultadoModelo = $this->modelo->insertarUsuario(['nick' => $nick,
                                                                 'nombre' => $nombre, 
                                                                 'apellidos' => $apellidos,
                                                                 'email' => $email,
                                                                 'password' => sha1($password),
                                                                 'imagen' => $imagen]);                                                                
    
                    if ($resultadoModelo["correcto"]){
    
                        $this->mensajes[] = ["tipo" => "success",
                        "mensaje" => "Usuario registrado correctamente"];
    
                    } else {
    
                        $this->mensajes[] = [
                            "tipo" => "danger",
                            "mensaje" => "No se ha podido registrar al usuario <br>({$resultadoModelo["error"]})"
                        ];
    
                    }

                    //Registro Log                                             
                    $ahora = date('Y-m-d H:i:s');
                    $this->modelo->insertarLog(['usuario_id' => $_COOKIE["usuario_id"],
                                                'fecha' => $ahora,
                                                'operacion' => "Se ha insertado un nuevo usuario"]); 
    
                  } else {
    
                    $this->mensajes[] = [
                        "tipo" => "danger",
                        "mensaje" => "Los datos de registro del usuario no son válidos"
                    ];
                    
                  }
            }
            
            $parametros = [
                "titulo" => "Mi Blog PHP-MVC",
                "datos" => [
                    "nick" => isset($nick) ? $nick : "",
                    "nombre" => isset($nombre) ? $nombre : "",
                    "apellidos" => isset($apellidos) ? $apellidos : "",
                    "email" => isset($email) ? $email : "",
                    "password" => isset($password) ? $password : "",
                    "imagen" => isset($imagen) ? $imagen : ""
                ],
                "mensajes" => $this->mensajes
            ];

            include_once '../views/formularioUsuario.php';
    
        }

        public function listarUsuarios(){

            $parametros = ["titulo" => "Mi Blog PHP-MVC", "datos" => NULL, "pagina" => NULL, "numeroPagina" => NULL, "mensajes" => []];
    
            $resultadoModelo = $this->modelo->listarUsuarios();
    
            if ($resultadoModelo["correcto"]){
    
                $parametros["datos"] = $resultadoModelo["datos"];
                $parametros["pagina"] = $resultadoModelo["pagina"];
                $parametros["numeroPagina"] = $resultadoModelo["numeroPagina"];
    
                $this->mensajes[] = ["tipo" => "success",
                "mensaje" => "Los usuarios se han listado correctamente <br/>"];
    
            } else {
    
                $this->mensajes[] = ["tipo" => "danger", "mensaje" => 
                "No se han podido listar los usuarios correctamente <br/>"];

            }
    
            $parametros["mensajes"] = $this->mensajes; 
    
            include_once '../views/listadoUsuarios.php';
    
        }

        public function eliminarUsuario(){

            $id = $_GET["usuario_id"];
    
            if (isset($_GET["usuario_id"]) and is_numeric($_GET["usuario_id"])){
    
                $resultadoModelo = $this->modelo->eliminarUsuario($id);

                //Registro Log                                             
                $ahora = date('Y-m-d H:i:s');
                $this->modelo->insertarLog(['usuario_id' => $_COOKIE["usuario_id"],
                                            'fecha' => $ahora,
                                            'operacion' => "Se ha eliminado un usuario"]);
    
                if ($resultadoModelo["correcto"] == TRUE){
    
                    $this->mensajes[] = ["tipo" => "success", "mensaje" => "El usuario ha sido eliminado correctamente <br>"];
    
                } else {
    
                    $this->mensajes[] = ["tipo" => "danger", "mensaje" => "No se ha podido eliminar al usuario debido a un error"];
                }
    
            } else {
    
                $this->mensajes[] = ["tipo" => "danger","mensaje" => 
                "El usuario que se ha intentado eliminar no existe o no se ha podido acceder a él"];

            }
    
            $this->listarUsuarios();

        }        
        
        public function listarUnUsuario(){

            $id = $_GET["usuario_id"];

            $parametros = ["titulo" => "Mi Blog PHP-MVC", "datos" => NULL, "mensajes" => []];
    
            if (isset($_GET["usuario_id"]) and is_numeric($_GET["usuario_id"])){
    
                $resultadoModelo = $this->modelo->listarUnUsuario($id);
    
                if ($resultadoModelo["correcto"]){

                    $parametros["datos"] = $resultadoModelo["datos"];

                    //Registro Log                                             
                    $ahora = date('Y-m-d H:i:s');
                    $this->modelo->insertarLog(['usuario_id' => $_COOKIE["usuario_id"],
                                                'fecha' => $ahora,
                                                'operacion' => "Se ha visto en detalle al usuario ".$parametros["datos"]["nick"]]);
    
                    $this->mensajes[] = ["tipo" => "success", "mensaje" => "Aquí se muestra con más detalle el usuario especificado <br>"];
    
                } else {
    
                    $this->mensajes[] = ["tipo" => "danger", "mensaje" => "No se ha podido detallar al usuario debido a un error"];
                }
    
            } else {
    
                $this->mensajes[] = ["tipo" => "danger","mensaje" => 
                "El usuario que desea detallar no existe o no se ha podido acceder a él"];

            }

            $parametros["mensajes"] = $this->mensajes;

            include_once '../views/listadoUnUsuario.php';

        }

        public function editarUsuario(){

            $errores = array();
    
            $presenteNombre = "";
            $presenteApellidos = "";
            $presenteEmail = "";
            $presenteImagen = "";
    
            if (isset($_POST["submit"])) {
                 
                $id = $_POST["usuario_id"];
    
                $nuevoNombre = $_POST["nombre"];
                $nuevoApellidos = $_POST["apellidos"];
                $nuevoEmail  = $_POST["email"];
                $nuevaImagen = "";
                
                $imagen = NULL;
          
                if (isset($_FILES["imagen"]) and (!empty($_FILES["imagen"]["tmp_name"]))){ 
    
                    if (!is_dir("fotos")){
    
                        $carpeta = mkdir("fotos", 0777, true);
    
                    } else {
    
                        $carpeta = true;
                    }
    
                    if ($carpeta){
    
                        $nombreImagen= time()."-".$_FILES["imagen"]["name"];
    
                        $moverImagen = move_uploaded_file($_FILES["imagen"]["tmp_name"], "fotos/".$nombreImagen);
    
                        $imagen = $nombreImagen;
    
                        if ($moverImagen){
    
                            $imgCargada = true;
    
                        } else {
    
                            $imgCargada = false;
    
                            $errores["imagen"] = "La imagen no se cargó correctamente";
                            $this->mensajes[] = ["tipo" => "danger",
                                "mensaje" => "La imagen no se cargó correctamente"];
    
                        }
    
                    }
                }
    
                $nuevaImagen = $imagen;
    
                if (count($errores) == 0){
                  
                    $resultadoModelo  = $this->modelo->editarUsuario([
                                                                    'usuario_id' => $id,
                                                                    'nombre' => $nuevoNombre, 
                                                                    'apellidos' => $nuevoApellidos, 
                                                                    'email' => $nuevoEmail, 
                                                                    'imagen' => $nuevaImagen
                                                                    ]);
                    
                    if ($resultadoModelo ["correcto"]){

                    $this->mensajes[] = ["tipo" => "success", "mensaje" => "El usuario se ha actualizado correctamente"];

                    }else{

                    $this->mensajes[] = ["tipo" => "danger", "mensaje" => "El usuario no ha podido actualizar sus datos <br>"];

                    }

                    //Registro Log                                             
                    $ahora = date('Y-m-d H:i:s');
                    $this->modelo->insertarLog(['usuario_id' => $_COOKIE["usuario_id"],
                                                'fecha' => $ahora,
                                                'operacion' => "Se ha actualizado un usuario"]);
    
                } else {
    
                    $this->mensajes[] = ["tipo" => "danger","mensaje" => "Los datos son erroneos"];
    
                }
          
                $presenteNombre = $nuevoNombre;
                $presenteApellidos = $nuevoApellidos;
                $presenteEmail  = $nuevoEmail;
                $presenteImagen = $nuevaImagen;
    
            } else {
    
                    if (isset($_GET['usuario_id']) && (is_numeric($_GET['usuario_id']))) {
    
                        $id = $_GET['usuario_id'];
    
                        $resultadoModelo = $this->modelo->listarUnUsuario($id);
                        
                        if ($resultadoModelo ["correcto"]){
    
                            $this->mensajes[] = ["tipo" => "success", "mensaje" => "Se han recuperado correctamente los datos del usuario"];
    
                            $presenteNombre = $resultadoModelo ["datos"]["nombre"];
                            $presenteApellidos = $resultadoModelo ["datos"]["apellidos"];
                            $presenteEmail  = $resultadoModelo ["datos"]["email"];
                            $presenteImagen = $resultadoModelo ["datos"]["imagen"];
    
                        } else{
    
                            $this->mensajes[] = ["tipo" => "danger",
                            "mensaje" => "No se han podido recuperar los datos del usuario <br>"];

                        }
                    }
              }

              $parametros = [
                "titulo" => "Mi Blog PHP-MVC",
                "datos" => [
                    "nombre" => $presenteNombre,
                    "apellidos" => $presenteApellidos,
                    "email" => $presenteEmail,
                    "imagen" => $presenteImagen
                ],
                "mensajes" => $this->mensajes
              ];                  
                
              include_once '../views/formularioActualizaUsuario.php'; 
    
        }

        //Funciones para Entradas

        public function insertarEntrada(){

            $errores = array();
    
            //si se ha pulsado el botón del formulario y ha recibido datos
            if (isset($_POST) && !empty($_POST) && isset($_POST["submit"])){

                //si existe un archivo tipo imagen y no está vacío el campo
                if (isset($_FILES["imagen"]) and (!empty($_FILES["imagen"]["tmp_name"]))){
    
                    if (!is_dir("fotos")){ //si no existe un directorio llamado 'fotos', lo creará
    
                        $carpeta = mkdir("fotos", 0777, true);
    
                    } else {
    
                        $carpeta = true;
                    }
    
                    if ($carpeta){ //si está el directorio fotos, la imagen se moverá a dicho directorio
    
                        $nombreImagen= time()."-".$_FILES["imagen"]["name"];
    
                        move_uploaded_file($_FILES["imagen"]["tmp_name"], "fotos/".$nombreImagen);
    
                        $imagen = $nombreImagen;
    
                    }
                }
                
                $ahora = date('Y-m-d H:i:s');

                $resultadoModelo = $this->modelo->insertarEntrada(['usuario_id' => $_COOKIE["usuario_id"],
                                                                'categoria_id' => $_COOKIE["categoria_id"], 
                                                                'titulo' => $_POST["titulo"],
                                                                'imagen' => $imagen,
                                                                'descripcion' => $_POST["descripcion"],
                                                                'fecha' => $ahora]);

                if ($resultadoModelo["correcto"]){

                    $this->mensajes[] = ["tipo" => "success",
                    "mensaje" => "Entrada registrada correctamente"];

                    //Registro Log                                             
                    $ahora = date('Y-m-d H:i:s');
                    $this->modelo->insertarLog(['usuario_id' => $_COOKIE["usuario_id"],
                                                'fecha' => $ahora,
                                                'operacion' => "Se ha insertado una nueva entrada"]); 

                } else {

                    $this->mensajes[] = [
                        "tipo" => "danger",
                        "mensaje" => "No se ha podido registrar la entrada <br>({$resultadoModelo["error"]})"
                    ];

                }
    
            }
            
            $parametros = [
                "titulo" => "Mi Blog PHP-MVC",
                "datos" => [
                    "usuario_id" => isset($usuario_id) ? $usuario_id : "",
                    "categria_id" => isset($categria_id) ? $categria_id : "",
                    "titulo" => isset($titulo) ? $titulo : "",
                    "imagen" => isset($imagen) ? $imagen : "",
                    "descripcion" => isset($descripcion) ? $descripcion : "",
                    "fecha" => isset($fecha) ? $fecha : ""
                ],
                "mensajes" => $this->mensajes
            ];

            include_once '../views/formularioEntrada.php';
    
        }

        public function listarEntradas(){

            $parametros = ["titulo" => "Mi Blog PHP-MVC", "datos" => NULL, "pagina" => NULL, "numeroPagina" => NULL, "mensajes" => [], "direccion" => NULL];
    
            $resultadoModelo = $this->modelo->listarEntradas();
    
            if ($resultadoModelo["correcto"]){
    
                $parametros["datos"] = $resultadoModelo["datos"];
                $parametros["pagina"] = $resultadoModelo["pagina"];
                $parametros["numeroPagina"] = $resultadoModelo["numeroPagina"];
                $parametros["direccion"] = $resultadoModelo["direccion"];
    
                $this->mensajes[] = ["tipo" => "success",
                "mensaje" => "Las entradas se han listado correctamente <br/>"];
    
            } else {
    
                $this->mensajes[] = ["tipo" => "danger", "mensaje" => 
                "No se han podido listar las entradas correctamente <br/>"];

            }
    
            $parametros["mensajes"] = $this->mensajes; 
    
            include_once '../views/listadoEntradas.php';
    
        }

        public function eliminarEntrada(){

            $id = $_GET["entrada_id"];
    
            if (isset($_GET["entrada_id"]) and is_numeric($_GET["entrada_id"])){
    
                $resultadoModelo = $this->modelo->eliminarEntrada($id);

                //Registro Log                                             
                $ahora = date('Y-m-d H:i:s');
                $this->modelo->insertarLog(['usuario_id' => $_COOKIE["usuario_id"],
                                            'fecha' => $ahora,
                                            'operacion' => "Se ha eliminado una entrada"]);
    
                if ($resultadoModelo["correcto"] == TRUE){
    
                    $this->mensajes[] = ["tipo" => "success", "mensaje" => "La entrada ha sido eliminado correctamente <br>"];
    
                } else {
    
                    $this->mensajes[] = ["tipo" => "danger", "mensaje" => "No se ha podido eliminar La entrada debido a un error"];
                }
    
            } else {
    
                $this->mensajes[] = ["tipo" => "danger","mensaje" => 
                "La entrada que se ha intentado eliminar no existe o no se ha podido acceder a ella"];

            }
    
            $this->listarEntradas();

        }        
        
        public function listarUnaEntrada(){

            $id = $_GET["entrada_id"];

            $parametros = ["titulo" => "Mi Blog PHP-MVC", "datos" => NULL, "mensajes" => []];
    
            if (isset($_GET["entrada_id"]) and is_numeric($_GET["entrada_id"])){
    
                $resultadoModelo = $this->modelo->listarUnaEntrada($id);
    
                if ($resultadoModelo["correcto"]){

                    $parametros["datos"] = $resultadoModelo["datos"];

                    //Registro Log                                             
                    $ahora = date('Y-m-d H:i:s');
                    $this->modelo->insertarLog(['usuario_id' => $_COOKIE["usuario_id"],
                                                'fecha' => $ahora,
                                                'operacion' => "Se ha visto en detalle la entrada de titulo ".$parametros["datos"]["titulo"]]);
    
                    $this->mensajes[] = ["tipo" => "success", "mensaje" => "Aquí se muestra con más detalle la entrada especificada <br>"];
    
                } else {
    
                    $this->mensajes[] = ["tipo" => "danger", "mensaje" => "No se ha podido detallar la entrada debido a un error"];
                }
    
            } else {
    
                $this->mensajes[] = ["tipo" => "danger","mensaje" => 
                "La entrada que desea detallar no existe o no se ha podido acceder a ella"];

            }

            $parametros["mensajes"] = $this->mensajes;

            include_once '../views/listadoUnaEntrada.php';

        }

        public function editarEntrada(){

            $errores = array();
    
            $presenteTitulo = "";
            $presenteImagen = "";
            $presenteDescripcion = "";
    
            if (isset($_POST["submit"])) {
                 
                $id = $_POST["entrada_id"];
    
                $nuevoTitulo = $_POST["titulo"];
                $nuevaImagen = "";
                $nuevaDescripcion = $_POST["descripcion"];
                
                $imagen = NULL;
          
                if (isset($_FILES["imagen"]) and (!empty($_FILES["imagen"]["tmp_name"]))){ 
    
                    if (!is_dir("fotos")){
    
                        $carpeta = mkdir("fotos", 0777, true);
    
                    } else {
    
                        $carpeta = true;
                    }
    
                    if ($carpeta){
    
                        $nombreImagen= time()."-".$_FILES["imagen"]["name"];
    
                        $moverImagen = move_uploaded_file($_FILES["imagen"]["tmp_name"], "fotos/".$nombreImagen);
    
                        $imagen = $nombreImagen;
    
                    }
                }
    
                $nuevaImagen = $imagen;
                  
                $resultadoModelo  = $this->modelo->editarEntrada([
                                                                'entrada_id' => $id,
                                                                'titulo' => $nuevoTitulo, 
                                                                'imagen' => $nuevaImagen,
                                                                'descripcion' => $nuevaDescripcion
                                                                ]);
                
                if ($resultadoModelo ["correcto"]){

                    $this->mensajes[] = ["tipo" => "success", "mensaje" => "La entrada se ha actualizado correctamente"];

                    //Registro Log                                             
                    $ahora = date('Y-m-d H:i:s');
                    $this->modelo->insertarLog(['usuario_id' => $_COOKIE["usuario_id"],
                                                'fecha' => $ahora,
                                                'operacion' => "Se ha actualizado una entrada"]);

                }else{

                    $this->mensajes[] = ["tipo" => "danger", "mensaje" => "La entrada no ha podido actualizar sus datos <br>"];

                }
          
                $presenteTitulo = $nuevoTitulo;
                $presenteImagen = $nuevaImagen;
                $presenteDescripcion = $nuevaDescripcion;
    
            } else {
    
                if (isset($_GET['entrada_id']) && (is_numeric($_GET['entrada_id']))) {

                    $id = $_GET['entrada_id'];

                    $resultadoModelo = $this->modelo->listarUnaEntrada($id);
                    
                    if ($resultadoModelo ["correcto"]){

                        $this->mensajes[] = ["tipo" => "success", "mensaje" => "Se han recuperado correctamente los datos de la entrada"];

                        $presenteTitulo = $resultadoModelo ["datos"]["titulo"];
                        $presenteImagen = $resultadoModelo ["datos"]["imagen"];
                        $presenteDescripcion = $resultadoModelo ["datos"]["descripcion"];

                    } else{

                        $this->mensajes[] = ["tipo" => "danger",
                        "mensaje" => "No se han podido recuperar los datos de la entrada <br>"];

                    }
                }

              }

              $parametros = [
                "titulo" => "Mi Blog PHP-MVC",
                "datos" => [
                    "titulo" => $presenteTitulo,
                    "imagen" => $presenteImagen,
                    "descripcion" => $presenteDescripcion
                ],
                "mensajes" => $this->mensajes
              ];                  
                
              include_once '../views/formularioActualizaEntrada.php'; 
    
        }
        
        public function buscarEntrada(){

            $descripcion = $_GET["descripcion"];

            $parametros = ["titulo" => "Mi Blog PHP-MVC", "datos" => NULL, "mensajes" => []];
    
            if (isset($_GET["descripcion"])){
    
                $resultadoModelo = $this->modelo->buscarEntrada($descripcion);
    
                if ($resultadoModelo["correcto"]){

                    $parametros["datos"] = $resultadoModelo["datos"];
    
                    $this->mensajes[] = ["tipo" => "success", "mensaje" => "Aquí se muestran los resultados de la búsqueda <br>"];
    
                } else {
    
                    $this->mensajes[] = ["tipo" => "danger", "mensaje" => "No se han encontrado entradas con los parámetros de búsqueda debido a un error"];
                }
    
            } else {
    
                $this->mensajes[] = ["tipo" => "danger","mensaje" => 
                "La entrada que busca no existe o no se ha podido acceder a ella"];

            }

            $parametros["mensajes"] = $this->mensajes;

            include_once '../views/listadoBusqueda.php';

        }

        public function listarPDF(){

            $parametros = ["titulo" => "Mi Blog PHP-MVC", "datos" => NULL];
    
            $resultadoModelo = $this->modelo->listarPDF();
    
            if ($resultadoModelo["correcto"]){
    
                $parametros["datos"] = $resultadoModelo["datos"];
    
            }
    
            include_once '../views/previaPDF.php';
    
        }

    }

?>