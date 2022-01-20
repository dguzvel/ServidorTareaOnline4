<?php

    require_once '../models/modelo.php';

    class controlador {
        
        private $modelo;
        private $mensajes;
        
        public function __construct() {

            $this->modelo = new modelo();
            $this->mensajes = [ ];

        }

    }

?>