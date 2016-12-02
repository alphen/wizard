Wizzard
=======


Introduction
------------
Wizzard provides an easy to implement a step-by-step interaction for end-user based on forms and routes.

	'factories' => array(
	    'RecipeWizard' => function($serviceManager){
	        $wizard = new Wizard($serviceManager);
	        $wizard->getFactory()->create('new_recipe');
	        $container = new Container('new_recipe');
	        $wizard->setContainer($container); 
	        $wizard->setContainerSteps($wizard->getCollection()->count());
	        return $wizard;
	    },
	),