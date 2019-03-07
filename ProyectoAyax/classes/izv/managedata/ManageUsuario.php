<?php

namespace izv\managedata;

use \izv\data\Usuario;
use \izv\database\Database;
use \izv\tools\Util;

class ManageUsuario {

    private $db;

    function __construct(Database $db) {
        $this->db = $db;
    }

    function add(Usuario $usuario) {

        $resultado = 0;
        if($this->db->connect()) {
            $sql = 'insert into usuario values(:id, :correo, :alias, :nombre , :clave, :activo, :fechaalta, :admin)';
             echo  '<pre>' . var_export($usuario, true) . '</pre>';
            if($this->db->execute($sql, $usuario->get())) {
                $resultado = $this->db->getConnection()->lastInsertId();
            }
        }
        return $resultado;
    }
     function edit(Usuario $usuario) {
        $resultado = 0;
        if($this->db->connect()) {
            $sql = 'update usuario set correo = :correo, alias = :alias, nombre = :nombre, activo = :activo, admin = :admin where id = :id';
            $array = $usuario->get();
            unset($array['clave']);
            unset($array['fechaalta']);

            if($this->db->execute($sql, $array)) {
                $resultado = $this->db->getSentence()->rowCount();
                
            }
        }
        return $resultado;
    }

    // function edit(Usuario $usuario) {
    //     $resultado = 0;
    //     if($this->db->connect()) {
    //         $sql = 'update usuario set correo = :correo where id = :id';
    //         $array = $usuario->get();
    //         unset($array['clave']);
    //         if($this->db->execute($sql, $array)) {
    //             $resultado = $this->db->getSentence()->rowCount();
    //         }
    //     }
    //     return $resultado;
    // }

    function editWithPassword(Usuario $usuario) {
        $resultado = 0;
        echo Util::varDump($usuario);
        
        if($this->db->connect()) {
            $sql = 'update usuario set correo = :correo, alias = :alias, nombre = :nombre , clave = :clave, activo = :activo, admin = :admin where id = :id';            
            $array = $usuario->get();
            unset($array['fechaalta']);
            
            if($this->db->execute($sql, $array)) {
                $resultado = $this->db->getSentence()->rowCount();
                
            }
        }
        return $resultado;
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

    function getAll() {
        $array = array();
        if($this->db->connect()) {
            $sql = 'select * from usuario order by nombre';
            if($this->db->execute($sql)) {
                while($fila = $this->db->getSentence()->fetch()) {
                    $usuario = new Usuario();
                    $usuario->set($fila);
                    $array[] = $usuario;
                }
            }
        }
        return $array;
    }

    function getAllOrOne($id = null) {
        if($id === null) {
            return $this->getAll();
        }
        return $this->get($id);
    }

    function login($correo, $clave) {
        if($this->db->connect()) {
            $sql = 'select * from usuario where correo = :correo';
            $array = array('correo' => $correo);
            if($this->db->execute($sql, $array)) {
                if($fila = $this->db->getSentence()->fetch()) {
                    $usuario = new Usuario();
                    $usuario->set($fila);
                    $resultado = \izv\tools\Util::verificarClave($clave, $usuario->getClave());
                    if($resultado) {
                        $usuario->setClave('');
                        return $usuario;
                    }
                }
            }
        }
        return false;
    }
    
    function remove($id) {
        $resultado = 0;
        if($this->db->connect()) {
            $sql = 'delete from usuario where id = :id';
            $array = array('id' => $id);
            if($this->db->execute($sql, $array)) {
                $resultado = $this->db->getSentence()->rowCount();
            }
        }
        return $resultado;
    }
}