<?php 

namespace Wizard\Factory;

use Zend\ServiceManager\ServiceManager;
use Core\Zend\Wizard\Collection\StepCollectionInterface;
use Core\Zend\Wizard\Exception\InvalidArgumentException;
use Core\Zend\Wizard\Collection\StepCollection;
use Core\Zend\Wizard\Collection\Form\Collection;
use Core\Zend\Wizard\Exception\ClassNotFoundException;

class WizardFactory implements WizardFactoryInterface{
    
    /**
     * @var ServiceManager
     */
    private $serviceManager;
    
    private $config = array();
    
    private $collection;
    
    /**
     * @param ServiceManager $serviceManager
     * @return \Zend\Wizard\Factory\WizardFactory
     */
    public function __construct(ServiceManager $serviceManager){
    	$this->serviceManager = $serviceManager;
    
    	$config = $this->serviceManager->get('Config');
    	if (isset($config['wizard'])){
    		$this->config = $config['wizard'];
    	}
    	return $this;
    }
    
    /* (non-PHPdoc)
     * @see \Zend\Wizard\Factory\WizardFactoryInterface::create()
     */
    public function create($collection = null){
        if(!$collection instanceof StepCollectionInterface){
            if($this->isValid($collection)){
               $this->collection = $this->createFromConfig($collection);
           }
        }else{
            $this->collection = $collection;
        }
    }
    
    /**
     * @param string $collection
     * @throws InvalidArgumentException
     * @throws ClassNotFoundException
     * @return boolean 
     */
    private function isValid($collection, $throw = true){
        try{
            if(!isset($this->config[$collection])){
            	throw new InvalidArgumentException('Option "'.$collection.'" does not exsist.');
            }
            
            if(!isset($this->config[$collection]['collection_type'])){
            	throw new InvalidArgumentException('Option "collection_type" does not exsist.');
            }
            
            $collectionType = $this->config[$collection]['collection_type'];
            
            if(!class_exists($class = 'Core\\Zend\\Wizard\\Collection\\'.$collectionType.'\\Collection')){
            	throw new ClassNotFoundException('class "'.$class.'" does not exsist');
            }
            
            if(!class_exists($class = 'Core\\Zend\\Wizard\\Collection\\'.$collectionType.'\\Model')){
            	throw new ClassNotFoundException('class "'.$class.'" does not exsist');
            }
            
            if(!class_exists($class = 'Core\\Zend\\Wizard\\Collection\\'.$collectionType.'\\Options')){
            	throw new ClassNotFoundException('class "'.$class.'" does not exsist');
            }
        }catch (\Exception $e){
            if($throw){
                throw new \Exception($e->getMessage(),$e->getCode(),$e->getPrevious());
            }
            return false;
        }
        
        return true;
    }
    
    /**
     * @param string $collection
     * @return \Zend\Wizard\Collection\StepCollection
     */
    private function createFromConfig($collection){
        $collectionType = $this->config[$collection]['collection_type'];
        
        $stepCollection = new StepCollection();
        
        foreach($this->config[$collection] as $key => $steps){
            
        	if(is_array($steps)){
        		$stepCollection->add($key, $this->getTypeCollection($steps, $collectionType));
        	}
        }
        return $stepCollection;
    }
    
    /**
     * @param array $steps
     * @param string $collectionType
     * @return Collection|routeCollection@todo
     */
    private function getTypeCollection($steps, $collectionType){
        $wizzardCollection = 'Core\\Zend\\Wizard\\Collection\\'.$collectionType.'\\Collection';
        $wizzardCollection = new $wizzardCollection();
        
        foreach($steps as $object => $step){
        	if($object == 'model'){
        		$wizzardCollection->setModel(new $step($this->serviceManager));
        	}else{
        		$wizzardCollection->setOptions($step);
        	}
        }
        return $wizzardCollection->getCollection();
    }
    
    
    /**
     * @return StepCollection
     */
    public function getCollection(){
        return $this->collection;
    }
    
    /**
     * @return multitype:
     */
    private function getConfig(){
        return $this->config;
    }
}

?>