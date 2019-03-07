<?php

namespace izv\controller;

use izv\app\App;
use izv\data\Usuario;
use izv\model\Model;
use izv\tools\Reader;
use izv\tools\Session;
use izv\tools\Util;
use izv\data\Categoria;
use izv\data\Link;

class AjaxController extends Controller {

    function __construct(Model $model) {
        parent::__construct($model);
        //...
    }
    
    function listavalores() {
        $array = [];
        $array[] = ['codigo' => 1, 'descripcion' => 'hola'];
        $array[] = ['codigo' => 2, 'descripcion' => 'adios'];
        sleep(3);
        $this->getModel()->set('resultado' , $array);
        
    }
    
    function listarLinks(){
        $r=0;
        if($this->getSession()->isLogged()){
            $filtro = Reader::read('filtro');
    
            $pagina = Reader::read('pagina');
            $orden = Reader::read('orden');
            
            if($filtro === ''){
                $filtro = null;
            }
 
            $links = $this->getModel()->doctrineLinks($this->getSession()->getLogin()->getId(), $pagina, $orden,$filtro);
            $this->getModel()->add($links);
        }else{
            header('Location: ' . App::BASE . 'index/main?op=edituser&r=' . $r);
            exit();
        } 
    }
    
    function addlink() {
        if($this->getSession()->isLogged()) {
        $id = $this->getSession()->getLogin()->getId();
        $categoria = Reader::read('categoria');
        $link = Reader::readObject('izv\data\Link');
        $result = $this->getModel()->crearLink($id, $categoria, $link);
        if($result->getId()>0){
            $this->getModel()->set('resultado',  1);
        }else{
            $this->getModel()->set('resultado',  0);
        }
        }
    }
    
    function borrar() {
        $linkId = Reader::read('id');
        $result = $this->getModel()->delete($linkId);
        $this->getModel()->set('result', ($result->getId() === null) ? 1 : 0);
    }
    
    function addcategory() {
        if($this->getSession()->isLogged()) {
            $categoria = trim(Reader::read('categoria'));
            $id = $this->getSession()->getLogin()->getId();
            $r=$this->getModel()->createCategory($id , $categoria);
            $this->getModel()->set('id_categoria',  $r->getId());
            $this->getModel()->set('nombre',  $r->getCategoria());
        }
    }
}