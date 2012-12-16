<?php
##*HEADER*##

//-- Include the helper file
require_once dirname(__FILE__).'/helper.php';

//-- Get a parameter from the module's configuration
$userCount = $params->get('usercount', 10);

//-- Get the items to display from the helper
$items = ModECR_COM_NAMEHelper::getItems($userCount);

//-- Include the template for display
require JModuleHelper::getLayoutPath('ECR_COM_COM_NAME');
