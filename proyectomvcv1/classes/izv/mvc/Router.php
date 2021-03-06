<?php

namespace izv\mvc;

class Router {

    private $rutas, $ruta;
    
    function __construct($ruta) {
        $this->rutas = array(
            'admin' => new Route('AdminModel', 'AdminView' , 'AdminController'),
            'index' => new Route('FirstModel', 'FirstView', 'FirstController'),
            'grayscale' => new Route('FirstModel', 'SecondView', 'FirstController'),
            'index' => new Route('UserModel', 'MaundyView', 'UserController'),
            'ajax' => new Route('UserModel', 'AjaxView', 'AjaxController'),
            'x' => new Route('', '', '')
        );
        $this->ruta = $ruta;
    }

    function getRoute() {
        $ruta = $this->rutas['index'];
        if(isset($this->rutas[$this->ruta])) {
            $ruta = $this->rutas[$this->ruta];
        }
        return $ruta;
    }
}