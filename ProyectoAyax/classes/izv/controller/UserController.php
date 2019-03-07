<?php

namespace izv\controller;

use izv\app\App;
use izv\data\Usuario;
use izv\model\Model;
use izv\tools\Reader;
use izv\tools\Session;
use izv\tools\Util;
use izv\tools\Mail;
use izv\database\Database;
use izv\managedata\ManageUsuario;
use izv\managedata\Pagination;

class UserController extends Controller {
    
    
    function __construct(Model $model) {
        parent::__construct($model);
        //...
    }

    // private function isAdministrator() {
    //     return $this->getSession()->isLogged() && $this->getSession()->getLogin()->getCorreo() === 'admin@admin.es';
    // }
    /*
    proceso general:
    1º control de sesión
    2º lectura de datos
    3º validación de datos
    4º usar el modelo
    5º producir resultado (para la vista)
    */

  
    //  function datos() {
    //     $ordenes = [
    //         'nombre' => 'nombre',
    //         'alias' => 'alias',
    //         'correo' => 'correo'
    //     ];
    //     $pagina = Reader::read('pagina');
    //     if($pagina === null || !is_numeric($pagina)) {
    //         $pagina = 1;
    //     }
    //     $orden = Reader::read('orden');
    //     if(!isset($ordenes[$orden])) {
    //         $orden = 'name';
    //     }
    //     $filtro = Reader::read('filtro');
    //     $r = $this->getModel()->getUsuarios($pagina, $orden, $filtro);
    //     $this->getModel()->add($r);
    // }
    
    
    
        
       
      
    
    
    
    function dologin() {
        //1º control de sesión
        if($this->getSession()->isLogged()) {
            //5º producir resultado -> redirección
            header('Location: ' . App::BASE . 'index?op=login&r=session');
            exit();
        }

        //2º lectura de datos
        $usuario = Reader::readObject('izv\data\Usuario');

        //4º usar el modelo
        $r = $this->getModel()->login($usuario->getCorreo());
        
        if($r !== null){
            $resultado = Util::verificarClave($usuario->getClave(), $r->getClave());
        
            if($r !== false && $r->getActivo()==1 && $resultado){ 
                $r->setclave('');
                $this->getSession()->login($r);
                $r = 1;
            } else {
                $r = 0;
            }
        }
        //5º producir resultado -> redirección
        header('Location: ' . App::BASE . 'index?op=login&r=' . $r);
        exit();
    }

    function dologout() {
        $this->getSession()->logout();
        header('Location: ' . App::BASE . 'index');
        exit();
    }
    
    function listarLinks(){
        $r=0;
        if($this->getSession()->isLogged()){
            $this->getModel()->set('twigFile', '_listalinks.html');
            $pagina = Reader::read('pagina');
            if($pagina === null || !is_numeric($pagina)) {
                $pagina = 1;
            }
            $orden = Reader::read('orden');
            if(!isset($ordenes[$orden])) {
                $orden = 'c.categoria';
            }                       
            $links = $this->getModel()->doctrineLinks($this->getSession()->getLogin()->getId(), $pagina, $orden);

            $this->getModel()->set('lista', $links);
        }else{
            header('Location: ' . App::BASE . 'index/main?op=edituser&r=' . $r);
            exit();
        } 
    }
    
    function añadirLink(){
        $r=0;
        if($this->getSession()->isLogged()){
            $this->getModel()->set('twigFile', '_addlink.html');
            $categorias =  $this->getModel()->categoriasUsuario($this->getSession()->getLogin()->getId() );
            $this->getModel()->set('categorias', $categorias);
        }else{
            header('Location: ' . App::BASE . 'index/main?op=edituser&r=' . $r);
            exit();
        }
    }
    
    function doregister() {
        //1º control de sesión
        if($this->getSession()->isLogged()) {
            //5º producir resultado -> redirección
            header('Location: ' . App::BASE . 'index?op=register&r=session');
            exit();
        }

        //2º lectura de datos
        $usuario = Reader::readObject('izv\data\Usuario');
        $clave2 = Reader::read('clave2');
        echo '<pre>' . var_export($usuario, true) . '</pre>';
        

        //4º usar el modelo
        $usuario->setClave(Util::encriptar($usuario->getClave()));
        echo '<pre>' . var_export($usuario, true) . '</pre>';
        $r = $this->getModel()->register($usuario);

        if($r > 0) {
            $resultado2 = Mail::sendActivation($usuario);
        }

        //5º producir resultado -> redirección
        header('Location: ' . App::BASE . 'index?op=register&r=' . $r);
        exit();
    }

    function login() {
        //1º control de sesión, si está logueado no se muestra el login
        if(!$this->getSession()->isLogged()) {
            //2º lectura de datos    -> no hay
            //3º validación de datos -> no hay
            //4º usar el modelo    -> no hace falta
            //5º producir resultado
            $this->getModel()->set('twigFile', '_login.html');
        }
    }
    
    function register() {
        //1º control de sesión, si está logueado no se muestra el registro
        if(!$this->getSession()->isLogged()) {
            //5º producir resultado
            $this->getModel()->set('twigFile', '_register.html');
        }
    }
    
    function doactivate(){
        $id = Reader::read('id');
        $code = Reader::read('code');
        
        $sendedMail = \Firebase\JWT\JWT::decode($code, App::JWT_KEY, array('HS256'));
        
        $r = $this->getModel()->activacion($id,$sendedMail);
        
        header('Location: ' . App::BASE . 'index/main?op=doactivate&r=' . $r);
        exit();
       
        

    }

    function main() {
        //1º control de sesión
        if($this->getSession()->isLogged()) {
        //         $ordenes = [
        //         'nombre' => 'nombre',
        //         'alias' => 'alias',
        //         'correo' => 'correo'
        //     ];
        //     $pagina = Reader::read('pagina');
        //     if($pagina === null || !is_numeric($pagina)) {
        //         $pagina = 1;
        //     }
        //     $orden = Reader::read('orden');
        //     if(!isset($ordenes[$orden])) {
        //         $orden = 'nombre';
        //     }
            // $filtro = Reader::read('filtro');
            // $r = $this->getModel()->getUsuarios($pagina, $orden, $filtro);
            // $this->getModel()->add($r);
            

            $this->getModel()->set('twigFile', '_mainlogged.html');
            $this->getModel()->set('user', $this->getSession()->getLogin()->getCorreo());
            $user = $this->getSession()->getLogin();
            
            
            // $this->getModel()->set('users', $r);
            
            // $this->getModel()->set('paginas', $pagina);
            // $this->getModel()->set('filtro', $filtro);
            // if($this->isAdministrator()) {
            //     $this->getModel()->set('administrador', true);
            // }
        } else {
            //5º producir resultado
            $this->getModel()->set('twigFile', '_main.html');
        }
    }


    
    // function registeradmin(){
    //     $user2 = $this->getSession()->getLogin();
        
    //     if($this->getSession()->isLogged() &&  $user2->getAdmin()==1 ) {
    //         $this->getModel()->set('twigFile', '_registeradmin.html');
    //     }else {
    //         $this->getModel()->set('twigFile', '_main.html');
    //     }
        
    // }
    
    // function singleedit(){
    //      $id = Reader::read('id');
    //      if($this->getSession()->isLogged()) {
    //         $this->getModel()->set('twigFile', '_singleedituser.html');
    //         $user = $this->getModel()->get2($id);
    //         $this->getModel()->set('user', $user);
    //     }else{
    //         header('Location: ' . App::BASE . 'index?op=login&r=session');
    //         exit();
    //     }
    // }
    
    // function doadduser(){
    //     $user2 = $this->getSession()->getLogin();
    //     echo '<pre>' . var_export($user2, true) . '</pre>';

    //     if($this->getSession()->isLogged() && $user2->getAdmin()==0) {
    //         //5º producir resultado -> redirección
    //         header('Location: ' . App::BASE . 'index?op=addregister&r=session');
    //         exit();
    //     }

    //     //2º lectura de datos
    //     $usuario = Reader::readObject('izv\data\Usuario');
    //     $clave2 = Reader::read('clave2');
        
        
        

    //     //4º usar el modelo
    //     echo '<pre>' . var_export($usuario, true) . '</pre>';
        
    //     $usuario->setClave(Util::encriptar($usuario->getClave()));
        
    //     $r = $this->getModel()->adduser($usuario);

    //     //5º producir resultado -> redirección
    //     header('Location: ' . App::BASE . 'index?op=register&r=' . $r);
    //     exit();
    // }
    
    // function edituser() {
    //     $r=0;
    //     $id = Reader::read('id');
    //     $user2 = $this->getSession()->getLogin();
    //     if($this->getSession()->isLogged() && $user2->getAdmin()==1){
    //         $this->getModel()->set('twigFile', '_edituser.html');
    //         $user = $this->getModel()->get2($id);
    //         $this->getModel()->set('user', $user);
            
    //     }else{
    //         header('Location: ' . App::BASE . 'index/main?op=edituser&r=' . $r);
    //         exit();
    //     }
        
        
    // }
    
    
    
    
    // function singledoedit(){
    //     $user2 = $this->getSession()->getLogin();
    //     if($this->getSession()->isLogged()){
    //       $r = $this->getModel()->editarUsuarioSimple($user2->getId());  
    //     //   $this->dologout();
    //     }else{
    //         header('Location: ' . App::BASE . 'index/main?op=edituser&r=0');
    //         exit();
    //     }
    //     header('Location: ' . App::BASE . 'index/main?op=edituser&r=1');
    //         exit();
    // }
    
    
    // function doedit(){
    //     $id = Reader::read('id');
    //     $user2 = $this->getSession()->getLogin();
    //     if($this->getSession()->isLogged() && $user2->getAdmin()==1){
    //       $r = $this->getModel()->editarUsuario($id);  
    //     }else{
    //         header('Location: ' . App::BASE . 'index/main?op=edituser&r=0');
    //         exit();
    //     }
        
        
    //     header('Location: ' . App::BASE . 'index/main?op=edituser&r=' . $r);
    //     exit();
    // }
    
    // function dodelete(){
    //     $id = Reader::read('id');
    //     $user = $this->getSession()->getLogin();
    //     if($this->getSession()->isLogged() && $user->getAdmin()==1){
    //         $r = $this->getModel()->deleteUser($id);
    //     }
    //     header('Location: ' . App::BASE . 'index/main?op=dodelete&r=' . $r);
    //     exit();
    // }
    
    
    
    
    
    
    // function doactivate(){
    //     $id = Reader::read('id');
    //     $code = Reader::read('code');
        
    //     $sendedMail = \Firebase\JWT\JWT::decode($code, App::JWT_KEY, array('HS256'));

    //     $db = new Database();
    //     $manager = new ManageUsuario($db);
    //     $user = $manager->get($id);
    //     if($user !== null && $user->getCorreo() === $sendedMail) {
    //         $user->setActivo(1);
            
    //         $user->setAdmin(0);
    //         echo '<pre>' . var_export($user, true) . '</pre>';
    //         $resultado = $manager->edit($user);
    //     }
    //     header('Location: ' . App::BASE . 'index/main?op=doactivate&r=' . $r);
    //     exit();
        
    // }
    
    
}