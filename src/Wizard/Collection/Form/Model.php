<?php 

namespace Wizard\Collection\Form;

use Zend\Form\FormInterface;
use Wizard\Collection\ModelInterface;

class Model implements ModelInterface{
    
    public $object;
    
    public function __construct(FormInterface $form){
        $this->object = $form;
        return $this;
    }
    
    public function getModel(){
        return $this->object;
    }
    
}

?>