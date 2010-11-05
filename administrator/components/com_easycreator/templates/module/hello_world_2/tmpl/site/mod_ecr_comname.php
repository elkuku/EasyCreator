<?php
##*HEADER*##

//-- Include the helper file
require_once dirname(__FILE__).DS.'helper.php';

//-- Get a parameter from the module's configuration
$userCount = $params->get('usercount', 10);

//-- Get the items to display from the helper
$items = Mod_ECR_COM_NAME_Helper::getItems($userCount);

//-- Include the template for display
require JModuleHelper::getLayoutPath('_ECR_COM_COM_NAME_');
