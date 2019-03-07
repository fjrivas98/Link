<?php

namespace izv\model;



use izv\tools\Pagination;
use  izv\tools\Reader;
use  izv\tools\Util;
use izv\tools\Bootstrap;
use Doctrine\ORM\Tools\Pagination\Paginator;
use izv\data\Usuario;
use izv\data\Categoria;
use izv\data\Link;

class UserModel extends Model {

    use \izv\common\Crud;

    function login($correo = '') {
        return $this->get('Usuario', ['correo' => $correo]);
    }

    function register(Usuario $usuario) {
        $result = 1;
        try {
            $r = $this->gestor->persist($usuario);
            $this->gestor->flush();
            return $result;    
        }catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e){
            $result = -1;
            echo 'Error al guardar' . $e;
        }    
        catch(\Exception $e){
            $result = 0;
            echo $e;
            exit();
        }
        return $result;
    
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
    
    
    
    function activacion($id,$correo){
        $result = 0;
        $usuario = $this->get('Usuario', ['correo' => $correo, 'id' => $id]);

        if ($usuario !== null) {
            $usuario->setActivo(1);
            $this->gestor->persist($usuario);
            $this->gestor->flush();
            return 1;
        }
        return 0;
        
    }
    
    function getAlllusers(){
        $db = new Database();
        $manager = new ManageUsuario($this->getDatabase());
        $users = $manager->getAll();
        return $users;
        
    }
    
    
    function categoriasUsuario($id){
         $r = $this->gestor->createQuery('SELECT c FROM izv\data\Categoria c JOIN c.usuario u WHERE u.id = :id')
                ->setParameter('id', $id)
                ->getResult();
        return $r;
    }
    
    function doctrineLinks($id,$pagina = 1, $orden = 'c.categoria', $limit = 2){
        $dql = 'SELECT l, c  FROM izv\data\Link l join l.usuario u join l.categoria 
        c WHERE u.id = :id
        ORDER BY ' . $orden . ', c.categoria, l.href, l.comentario, l.id';
        $query = $this->gestor->createQuery($dql)->setParameter('id',$id );
        $paginator = new Paginator($query);
        $paginator->getQuery()
            ->setFirstResult($limit * ($pagina - 1))
            ->setMaxResults($limit);
        $pagination = new Pagination($paginator->count(), $pagina, $limit);
        // return $paginator;
        $links = array();
        $resultado = array();
        foreach($paginator as $link) {
            $categoria = $link->getCategoria()->getCategoria();
            $links['links'] = $link->getUnset(array('categoria','usuario'));
            $links['links']['nombrecategoria'] = $categoria;
            $resultado [] = $links;
            
        }
        return ['link' => $resultado, 'paginas' => $pagination->values()];
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