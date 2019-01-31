<?php

namespace izv\model;

use izv\data\City;
use izv\data\Usuario;
use izv\database\Database;
use izv\managedata\ManageCity;
use izv\managedata\ManageUsuario;
use izv\tools\Pagination;
use  izv\tools\Reader;
use  izv\tools\Util;

class UserModel extends Model {

    

    function login(Usuario $usuario) {
        $manager = new ManageUsuario($this->getDatabase());
        return $manager->login($usuario->getCorreo(), $usuario->getClave());
    }

    function register(Usuario $usuario) {
        $manager = new ManageUsuario($this->getDatabase());
        $r = $manager->add($usuario);
        if($r > 0) {
            $usuario->setId($r);
        }
        return $r;
    }
    
    function adduser(Usuario $usuario){
        $manager = new ManageUsuario($this->getDatabase());
        $usuario->setActivo($usuario->getActivo() === 'on' ? 1 : 0);
        $usuario->setAdmin($usuario->getAdmin() === 'on' ? 1 : 0);
        echo Util::varDump($usuario);
        $r = $manager->add($usuario);
        if($r > 0) {
            $usuario->setId($r);
        }
        return $r;
    }
    
    function deleteUser($id){

        $db = new Database();
        $manager = new ManageUsuario($this->getDatabase());
        $r=0;
        if($id !== null ){
            $resultado = $manager->remove($id);
            $r=1;
        }
        return $r;
    }
    
    
    function get2($id){
        $db = new Database();
        $manager = new ManageUsuario($this->getDatabase());
        $user = $manager->get($id);
        return $user;
        
    }
    
    function editarUsuario(){
        $db = new Database();
        $manager = new ManageUsuario($this->getDatabase());
        $user = Reader::readObject('izv\data\Usuario');
        $user->setActivo($user->getActivo() === 'on' ? 1 : 0);
        $user->setAdmin($user->getAdmin() === 'on' ? 1 : 0);
        $r = 0;
        if($user->getClave()==='') {
            $resultado = $manager->edit($user);
            $r=1;
        } else {
            $user->setClave(Util::encriptar($user->getClave()));
            $resultado = $manager->editWithPassword($user);
            $r=1;
        }
        
        
       return $r;

       
       
    }
    
     function editarUsuarioSimple(){
        $db = new Database();
        $manager = new ManageUsuario($this->getDatabase());
        $user = Reader::readObject('izv\data\Usuario');
        $user->setActivo($user->getActivo() === 'on' ? 1 : 0);
        $user->setAdmin($user->getAdmin() === 'on' ? 1 : 0);
        $r = 0;
        if($user->getClave()==='') {
            $resultado = $manager->edit($user);
            $r=1;
        } else {
            $user->setClave(Util::encriptar($user->getClave()));
            $resultado = $manager->editWithPassword($user);
        }
        
        
       return $r;

       
       
    }
    
    
    
    function activacion($id,$sendedMail){
        $db = new Database();
        $manager = new ManageUsuario($this->getDatabase());
        $user = $manager->get($id);
        $r=0;
        if($user !== null && $user->getCorreo() === $sendedMail) {
            $user->setActivo(1);
            $user->setAdmin(0);
            $resultado = $manager->edit($user);
            $r=1;
        }
        return $r;
        
    }
    
    function getAlllusers(){
        $db = new Database();
        $manager = new ManageUsuario($this->getDatabase());
        $users = $manager->getAll();
        return $users;
        
    }
    
    
    
     function get($id) {
        $usuario = null;
        if($this->db->connect()) {
            $sql = 'select * from usuario where id = :id';
            $array = array('id' => $id);
            if($this->db->execute($sql, $array)) {
                if($fila = $this->db->getSentence()->fetch()) {
                    $usuario = new Usuario();
                    $usuario->set($fila);
                }
            }
        }
        return $usuario;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    function getUsuarios($pagina = 1, $orden = 'nombre', $filtro = null) {
        $total = $this->getTotalUsuarios();
        $paginacion = new Pagination($total, $pagina);
        $offset = $paginacion->offset();
        $rpp = $paginacion->rpp();

        $parametros = array(
            'offset' => array($offset, \PDO::PARAM_INT),
            'rpp' => array($rpp, \PDO::PARAM_INT)
        );
        
       
        if($filtro === null) {
            $sql = 'select * from usuario order by '. $orden .', nombre, alias, correo limit :offset, :rpp';
        } else {
            $sql = 'select * from usuario
                    where nombre like :filtro or alias like :filtro or correo like :filtro 
                    order by '. $orden .', nombre, alias, correo limit :offset, :rpp';
            $parametros['filtro'] = '%' . $filtro . '%';
        }
        $array = [];

        
        if($this->getDatabase()->connect()) {
            if($this->getDatabase()->execute($sql, $parametros)) {

              
                while($fila = $this->getDatabase()->getSentence()->fetch()) {
                    $objeto = new Usuario();
                    $objeto->set($fila);
                    $array[] = $objeto;
                }
            }
        }
        
        $enlaces = $paginacion->values();
        
        return [
            'usuario' => $array,
            'paginacion' => $enlaces,
            'filtro' => $filtro,
            'orden' => $orden,
            'pagina' => $pagina
        ];
    }

    function getTotalUsuarios() {
        $users = 0;
        if($this->getDatabase()->connect()) {
            $sql = 'select count(*) from usuario';
            if($this->getDatabase()->execute($sql)) {
                if($fila = $this->getDatabase()->getSentence()->fetch()) {
                    $users = $fila[0];
                }
            }
        }
        return $users;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}