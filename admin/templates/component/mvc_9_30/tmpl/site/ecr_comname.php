<?php
##*HEADER*##

$controller	= JControllerLegacy::getInstance('ECR_COM_NAME');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

return;
/**
 *  Require the base controller.
 */
require_once JPATH_COMPONENT.DS.'controller.php';

// Require specific controller if requested
$controller = JFactory::getApplication()->input->get('controller');

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
$controller->execute(JFactory::getApplication()->input->get('task'));

//-- Redirect if set by the controller
$controller->redirect();
