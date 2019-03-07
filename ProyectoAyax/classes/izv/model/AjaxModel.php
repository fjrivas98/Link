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


class AjaxModel extends Model {
    
    use \izv\common\Crud;

    function createCategory($id, $nombrecategoria) {
        $result = 1;
        $usuario = $this->gestor->getReference('izv\data\Usuario', $id);
        $categoria = new Categoria();
        $categoria->setUsuario($usuario);
        $categoria->setCategoria($nombrecategoria);
        
        try {
            $r = $this->gestor->persist($categoria);
            $this->gestor->flush();
            return $categoria;    
        }catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e){
            $result = -1;
        }    
        catch(\Exception $e){
            $result = 0;
        }
        return $result;
    }
    
    function crearLink($id, $nombrecategoria, $link) {
        $usuario = $this->gestor->getReference('izv\data\Usuario', $id);
        $categoria = $this->gestor->getReference('izv\data\Categoria', $nombrecategoria);
        $link->setUsuario($usuario);
        $link->setCategoria($categoria);

        
        try {
            $r = $this->gestor->persist($link);
            $this->gestor->flush();
            return $categoria;    
        }catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e){
            $result = -1;
        }    
        catch(\Exception $e){
            $result = 0;
        }
        return $result;
    }
    
    function delete($id) {
        $item = $this->getLink('\izv\data\Link', ['id' => $id]);
        $this->gestor->remove($item);
        $this->gestor->flush();
        return $item;
    }
    
    function doctrineLinks($id,$pagina = 1, $orden = 'c.categoria', $filtro, $limit = 2){
        if($filtro !== null){
            $dql = 'SELECT l, c  FROM izv\data\Link l join l.usuario u join l.categoria 
            c WHERE c.categoria like :filtro or  l.href like :filtro or l.comentario like :filtro 
            ORDER BY ' . $orden . ', c.categoria, l.href, l.comentario, l.id';
            $query = $this->gestor->createQuery($dql)->setParameter('filtro','%'.$filtro.'%' );
        }else{
          $dql = 'SELECT l, c  FROM izv\data\Link l join l.usuario u join l.categoria 
            c WHERE u.id = :id
            ORDER BY ' . $orden . ', c.categoria, l.href, l.comentario, l.id';  
            $query = $this->gestor->createQuery($dql)->setParameter('id',$id );
        }
        
        
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
        return ['link' => $resultado, 'paginas' => $pagination->values(), 'filtro' => $filtro];
    }

    
}