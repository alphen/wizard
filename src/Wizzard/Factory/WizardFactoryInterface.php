<?php 

namespace Wizard\Factory;

use Wizard\Collection\StepCollectionInterface;

interface WizardFactoryInterface{
    
    public function create($collection = null);

}

?>