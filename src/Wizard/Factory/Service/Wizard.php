<?php 

namespace Wizard\Factory\Service;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Zend\Session\Container;
use Wizard\Service\Wizard as WizardService;

class Wizard implements FactoryInterface{
    
    private $container;
    private $containerName = 'Wizard';
    
    /**
     * {@inheritDoc}
     * @see \Zend\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(
        ContainerInterface $container, 
        $requestedName, 
        array $options = null
    ){
        $this->container = $container;
        
        return new WizardService(
            new Container($this->containerName),
            $container->get('Config'),
            $container->get('Application'),
            $container->get('ControllerPluginManager')->get('redirect')
        );
    }
    
}

?>