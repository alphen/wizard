<?php 

namespace Wizard\Factory\Service;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class Form implements FactoryInterface{
    
    public function __invoke(
        ContainerInterface $container, 
        $requestedName, 
        array $options = null
    ){
            
    }
    
}

?>