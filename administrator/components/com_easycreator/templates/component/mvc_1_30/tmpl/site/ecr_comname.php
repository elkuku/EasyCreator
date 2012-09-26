<?php
##*HEADER*##

//-- Get an instance of the controller with the prefix 'ECR_COM_NAME'
$controller = JControllerLegacy::getInstance('ECR_COM_NAME');

//-- Execute the 'task' from the Request
$controller->execute(JFactory::getApplication()->input->get('task'));

//-- Redirect if set by the controller
$controller->redirect();
