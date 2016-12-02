<?php 

namespace Wizard\Factory\Service;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Zend\Session\Container;
use Wizard\Service\Wizard as WizardService;
use Wizard\Collection\StepCollection;
use Wizard\Collection\StepCollectionInterface;
use Wizard\Collection\CollectionInterface;

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
        if(isset($options['collection']) && $options['collection'] instanceof StepCollectionInterface){
            $stepsCollection = $options['collection'];
        }else{
            $stepsCollection = $this->createStepsFromConfig($container->get('Config'), $options);
        }
        
        return new WizardService(
            new Container($this->containerName),
            $stepsCollection
        );
    }
    
    /**
     * @param array $config
     * @param array $options
     * @throws \Exception
     * @return StepCollection
     */
    private function createStepsFromConfig($config, $options) : StepCollection {
        if(!isset($config['wizard']) || 
            !isset($options['wizard']) || 
            !isset($config['wizard'][$options['wizard']]) 
        ){
            throw new \Exception('Configuration not complete');
        }
        $config = $config['wizard'][$options['wizard']];
        
        $collectionType = 'Form';
        if(isset($config['collection_type'])){
            $collectionType = $config['collection_type'];
        }
        
        $stepCollection = new StepCollection();
        
        foreach($config as $key => $steps){
            if(is_array($steps)){
                $stepCollection->add(
                    $key, 
                    $this->getTypeCollection(
                        $steps, 
                        $collectionType
                ));
            }
        }
        return $stepCollection;
    }
    
    private function getTypeCollection($steps, $collectionType) : CollectionInterface {
        $wizzardCollection = 'Wizard\\Collection\\'.$collectionType.'\\Collection';
        $wizzardCollection = new $wizzardCollection();
    
        foreach($steps as $key => $step){
            if($key == 'model'){
                $wizzardCollection->setModel($this->container->get($step));
            }else{
                $wizzardCollection->setOptions($step);
            }
        }
        return $wizzardCollection;
    }
    
}

?>