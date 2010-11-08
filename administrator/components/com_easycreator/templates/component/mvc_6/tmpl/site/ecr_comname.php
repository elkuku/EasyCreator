<?php
##*HEADER*##

/**
 *  Require the base controller.
 */
require_once JPATH_COMPONENT.DS.'controller.php';

//-- Require specific controller if requested
if($controller = JRequest::getCmd('controller'))
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
$classname = '_ECR_COM_NAME_Controller'.$controller;
$controller = new $classname();

//-- Perform the Request task
$controller->execute( JRequest::getCmd('task'));

//-- Redirect if set by the controller
$controller->redirect();
