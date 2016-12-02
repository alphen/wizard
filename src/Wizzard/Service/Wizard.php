<?php 

namespace Wizard\Service;

use Zend\Session\AbstractContainer;
use Wizard\Collection\StepCollection;
use Wizard\Collection\OptionsInterface;
use Zend\Mvc\Application;
use Zend\Mvc\Controller\Plugin\Redirect;
use Wizzard\WizardInterface;

class Wizard implements WizardInterface{
    
    private $container;
    private $collection;
    private $redirect;
    private $application;
    private $currentStep;
    
    private $initialized = false;
    
    public function __construct(
        AbstractContainer $container,
        StepCollection $collection,
        Application $application,
        Redirect $redirectPlugin
    ){
        $this->container = $container;
        $this->collection = $collection;
        $this->application = $application;
        $this->redirect = $redirectPlugin;
    }
    
    
    public function reset(){
        $this->initialized = false;
        $this->factory = false;
        $this->container->getManager()->getStorage()->clear($this->container->getName());
        return $this;
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
    
    public function getCurrentStep(){
        $containerIterator = $this->container->getIterator()->getIterator();
        while($containerIterator->valid()){
            if(false === $containerIterator->current()){
                $this->currentStep = $this->collection->getIterator()->offsetGet($containerIterator->key());
                break;
            }
            $containerIterator->next();
        }
         
        if(!$this->currentStep){
            $this->currentStep = $this->collection->getIterator()->current();
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
                return $this->redirect->toRoute($route, $params);
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
    
}

?>