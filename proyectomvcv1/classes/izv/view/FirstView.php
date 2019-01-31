<?php

namespace izv\view;

use izv\model\Model;
use izv\tools\Util;

class FirstView extends View {

    function render($accion) {
        $this->getModel()->set('template_route','twigtemplates/bootstrap/');
        $datos = $this->getModel()->getViewData();
        require_once("classes/vendor/autoload.php");
        $loader = new \Twig_Loader_Filesystem('twigtemplates/bootstrap/');
        $twig = new \Twig_Environment($loader);
        return $twig->render($this->getModel()->get('plantilla'), $datos);
    }
}