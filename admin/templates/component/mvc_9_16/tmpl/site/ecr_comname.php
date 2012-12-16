<?php
##*HEADER*##

/**
 *  Require the base controller.
 */
require_once JPATH_COMPONENT.DS.'controller.php';

// Require specific controller if requested
$controller = JRequest::getCmd('controller');

if($controller)
{
    $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';

    if(file_exists($path))
    {
        require_once $path;
    }
    else
    {
        $controller = '';
    }
}

//-- Create the controller
$classname = 'ECR_COM_NAMEController'.$controller;

$controller = new $classname;

//-- Perform the Request task
$controller->execute(JRequest::getCmd('task'));

//-- Redirect if set by the controller
$controller->redirect();
