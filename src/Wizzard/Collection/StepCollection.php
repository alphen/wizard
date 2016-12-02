<?php 

namespace Wizard\Collection;

use Zend\Stdlib\ArrayObject;

class StepCollection extends ArrayObject implements StepCollectionInterface{
    
    public function add($key, CollectionInterface $value){
        $this->offsetSet($key, $value);
    }
    
    public function getStep($key){
        return $this->offsetGet($key);
    }
    
    public function getCollection(){
        return $this->getArrayCopy();
    }
    
}

?>