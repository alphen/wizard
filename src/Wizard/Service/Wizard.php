<?php 

namespace Wizard\Service;

use Zend\Session\AbstractContainer;
use Wizard\Collection\StepCollection;
use Wizard\Collection\OptionsInterface;
use Zend\Mvc\Application;
use Zend\Mvc\Controller\Plugin\Redirect;
use Wizard\Service\WizardInterface;
use Wizard\Collection\StepCollectionInterface;
use Interop\Container\ContainerInterface;
use Wizard\Collection\Form\Collection;

class Wizard implements WizardInterface{
    
    private $container;
    private $config;
    private $collection;
    private $redirect;
    private $application;
    private $currentStep;
    private $containerInterface;
    
    private $initialized = false;
    
    public function __construct(
        AbstractContainer $container,
        ContainerInterface $containerInterface
    ){
        $this->containerInterface = $containerInterface;
        $this->config  = $containerInterface->get('Config');
        $this->application = $containerInterface->get('Application');
        $this->redirect = $containerInterface->get('ControllerPluginManager')->get('redirect');
        $this->container = $container;
    }
    
    public function reset(){
        $this->initialized = false;
        $this->factory = false;
        $this->container->getManager()->getStorage()->clear($this->container->getName());
        return $this;
    }
    
    public function setup($options){
        if(isset($options['collection']) && $options['collection'] instanceof StepCollectionInterface){
            $this->collection = $options['collection'];
        }else{
            $this->collection = $this->createStepsFromConfig($this->config, $options);
        }
    }
    
    public function setContainerSteps($totalStepsToWalk){
        for ($i = 0; $i < $totalStepsToWalk; $i++) {
            if(!$this->container->offsetExists($i)){
                $this->container->offsetSet($i, false);
            }
        }
    }
    
    /**
     * @return Container
     */
    public function getContainer(){
        return $this->container;
    }
    
    public function getCurrentStep() : Collection {
        $containerIterator = $this->container->getIterator();//->getIterator();
        $collectionIterator = $this->collection->getIterator();
        /* iterate over the collection */
        while($collectionIterator->valid()){
            if(!$containerIterator->offsetExists('step-'.($collectionIterator->key() + 1 ))){
                $this->currentStep = $collectionIterator->current();
                return $this->currentStep;
            }
            $collectionIterator->next();
        }
        return $this->currentStep;
    }
    
    public function hasNext(){
        $containerIterator = $this->container->getIterator()->getIterator();
        while($containerIterator->valid()){
            if(false === $containerIterator->current()){
                return true;
            }
            $containerIterator->next();
        }
        return false;
    }
    
    public function next(){
        return $this->moveToNext();
    }
    
    protected function moveToNext(){
        $iterator = $this->collection->getIterator();
        while($iterator->valid()){
            if($this->container->offsetExists($iterator->key()) && 
                false === $this->container->offsetGet($iterator->key()))
            {
                $this->currentStep = $iterator->current();
                $this->container->offsetSet($iterator->key(),true);
                break;
            }
            $iterator->next();
        }
        return $this;
    }
    
    public function dispatch(array $params = array()){
        if($this->currentStep->hasOption(OptionsInterface::ROUTEONNEXT)){
            $route = $this->currentStep->getOption(OptionsInterface::ROUTEONNEXT);
            if($route !== false){
                header('Location: '. $route);
                exit;
            }
            return $this->redirect->toRoute(
                $this->application
                    ->getMvcEvent()
                    ->getRouteMatch()
                    ->getMatchedRouteName()
            );
        }
    }
     
    /**
     * @return array : StepCollection
     */
    public function getCollection() : StepCollection{
        $this->isActive = true;
        return $this->collection;
    }
    
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
    
    private function getTypeCollection($steps, $collectionType){
        $wizzardCollection = 'Wizard\\Collection\\'.$collectionType.'\\Collection';
        $wizzardCollection = new $wizzardCollection();
    
        foreach($steps as $object => $step){
            if($object == 'model'){
                $wizzardCollection->setModel($this->containerInterface->get($step));
            }else{
                $wizzardCollection->setOptions($step);
            }
        }
        return $wizzardCollection;
    }
    
}

?>