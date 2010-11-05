<?php
##*HEADER*##

// import joomla controller library
jimport('joomla.application.component.controller');

// Get an instance of the controller prefixed by _ECR_COM_NAME_
$controller = JController::getInstance('_ECR_COM_NAME_');

// Perform the Request task
$controller->execute(JRequest::getCmd('task'));

// Redirect if set by the controller
$controller->redirect();
