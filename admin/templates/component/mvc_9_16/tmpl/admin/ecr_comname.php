<?php
##*HEADER*##

// Die Joomla! Controllerbibliothek importieren
jimport('joomla.application.component.controller');

// Die Helperdatei registrieren
JLoader::register('ECR_COM_NAMEHelper', JPATH_COMPONENT.'/helpers/ECR_COM_TBL_NAME.php');

// Eine Instanz des Controllers mit dem Präfix 'HalloWelt' beziehen
$controller = JController::getInstance('ECR_COM_NAME');

// Den 'task' der im Request übergeben wurde ausführen
$controller->execute(JRequest::getCmd('task'));

// Einen Redirect durchführen wenn er im Controller gesetzt ist
$controller->redirect();
