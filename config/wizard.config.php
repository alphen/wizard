<?php 

use Wizard\Collection\OptionsInterface;

return array(
    'wizard' => array(
        'YourConfigKey' => array(
            'collection_type' => 'Form',
            array(
                'options' => array(
                    OptionsInterface::ROUTEONNEXT => 'step-2',
                    //'entity' => 'Application\Entity\Entity',
                    //'bindEntity' => true,
                    'stepInfo' => 'Step 1',
                    'step' => '1',
                ),
                //'model' => 'Application\Form\Form'
            ),
        ),
    ),
);
?>