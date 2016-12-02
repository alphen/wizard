<?php 

namespace Wizard\Collection\Form;

use Wizard\Collection\CollectionInterface;
use Wizard\Collection\Form\Model;
use Wizard\Collection\Form\Options;
use Wizard\Collection\OptionsInterface;
use Zend\Form\FormInterface;

class Collection implements CollectionInterface {
    
    private $collectionOptions;
    
    private $collectionModel;
    
    public function __construct(
        OptionsInterface $options = null, 
        FormInterface $form  = null
    ){
        if($form)
            $this->setModel($form);
        if($options)
            $this->setOptions($options->getOptions());
    }
    
    public function getCollection(){
        return $this;
    }
    
    public function setOptions($options){
        if(isset($options['entity']) && is_string($options['entity'])){
            $options['entity'] = new $options['entity']();
        }
        $this->collectionOptions = new Options($options);
    }
    
    public function getOptions(){
        return $this->collectionOptions->getOptions();
    }
    
    public function getOption($key){
    	return $this->collectionOptions->getOption($key);
    }
    
    public function hasOption($key){
        return $this->collectionOptions->has($key);
    }
    
    public function getModel(){
        return $this->collectionModel->getModel();
    }
    
    public function setModel($form){
        $this->collectionModel = new Model($form);
    }
  
}

?>