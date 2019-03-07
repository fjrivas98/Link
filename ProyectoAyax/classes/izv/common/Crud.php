<?php 

namespace izv\common;

trait Crud {
    
    private $prefix = '\izv\data\\';
    
    function getLink($clase, array $data = ['id' => '']) {
        return $this->gestor->getRepository($clase)->findOneBy($data);
    }
    function get($clase, array $data = ['id' => '']) {
        return $this->gestor->getRepository($this->prefix . $clase)->findOneBy($data);
    }
    
    function getAll ($clase) {
        return $this->gestor->getRepository($this->prefix . $clase)->findAll();
    }
    
  
    
    function update ($item) {
        $this->gestor->persist($item);
        $this->gestor->flush();
        return $item;
    }
    
    // function delete($clase, $id) {
    //     $item = $this->get($clase, ['id' => $id]);
    //     $this->gestor->remove($item);
    //     $this->gestor->flush();
    //     return $item;
    // }
    
}