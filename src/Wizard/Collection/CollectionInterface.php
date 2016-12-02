<?php 

namespace Wizard\Collection;

interface CollectionInterface{
    
    public function setOptions($options);
    
    public function getOptions();
    
    public function getModel();
    
    public function setModel($model);
    
}

?>