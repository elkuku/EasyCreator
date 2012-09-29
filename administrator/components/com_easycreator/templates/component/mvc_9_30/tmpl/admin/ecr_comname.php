<?php
##*HEADER*##

if( ! JFactory::getUser()->authorise('core.manage', 'ECR_COM_COM_NAME'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
}

JLoader::register('ECR_COM_NAMEHelper', JPATH_COMPONENT.'/helpers/ECR_COM_TBL_NAME.php');

$controller	= JControllerLegacy::getInstance('ECR_COM_NAME');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
