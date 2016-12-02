<?php 

namespace Wizard\Collection\Form;

use Wizard\Collection\OptionsInterface;
use Wizard\Exception\InvalidArgumentException;

class Options implements OptionsInterface{
    
    
    public $options;
    
    public function __construct(array $options){
        if(!isset($options[self::ROUTEONNEXT])){
            throw new InvalidArgumentException('Option "'.self::ROUTEONNEXT.'" does not exsist.');
        }
    	$this->options = $options;
    	return $this->options;
    }
    
    public function getOptions(){
    	return $this->options;
    }
    
    public function getOption($key){
        return $this->options[$key];
    }
    
    public function has($key){
        if(isset($this->options[$key])){
            return true;
        }
        return false;
    }
}

?>