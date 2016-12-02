<?php 

namespace Wizard\Collection;

interface OptionsInterface{

    const ROUTEONNEXT = 'route_on_next';
    
    public function getOptions();
    
    public function getOption($key);
    
    public function has($key);
}

?>