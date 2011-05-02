<?php
##*HEADER*##

// Die Joomla! Controllerbibliothek importieren
jimport('joomla.application.component.controller');

// Die Helperdatei registrieren
JLoader::register('_ECR_COM_NAME_Helper', JPATH_COMPONENT.'/helpers/_ECR_COM_TBL_NAME_.php');

// Eine Instanz des Controllers mit dem Präfix 'HalloWelt' beziehen
$controller = JController::getInstance('_ECR_COM_NAME_');

// Den 'task' der im Request übergeben wurde ausführen
$controller->execute(JRequest::getCmd('task'));

// Einen Redirect durchführen wenn er im Controller gesetzt ist
$controller->redirect();
