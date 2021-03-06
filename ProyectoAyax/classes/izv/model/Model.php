<?php

namespace izv\model;

use izv\tools\Bootstrap;

class Model {

    protected $bootstrap;
    protected $gestor;
    private $datosVista = array();

    function __construct() {
        $this->bootstrap = Bootstrap::getInstance();
        $this->gestor = $this->bootstrap->getEntityManager();
    }
    

    
    function add(array $array) {
        foreach($array as $indice => $valor) {
            $this->set($indice, $valor);
        }
    }

    function get($name) {
        if(isset($this->datosVista[$name])) {
            return $this->datosVista[$name];
        }
        return null;
    }



    function getViewData() {
        return $this->datosVista;
    }

    function set($name, $value) {
        $this->datosVista[$name] = $value;
        return $this;
    }
}