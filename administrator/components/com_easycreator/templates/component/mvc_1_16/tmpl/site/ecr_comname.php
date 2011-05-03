<?php
##*HEADER*##

//-- Import the class JController
jimport('joomla.application.component.controller');

//-- Get an instance of the controller with the prefix '_ECR_COM_NAME_'
$controller = JController::getInstance('_ECR_COM_NAME_');

//-- Execute the 'task' from the Request
$controller->execute(JRequest::getCmd('task'));

//-- Redirect if set by the controller
$controller->redirect();
