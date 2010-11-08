<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Frontend
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 24-Sep-2008
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

jimport('jalhoo.language');
jimport('joomla.filesystem.file');

$debug = false;

try
{
    //TEMP@@debug
//    JALHOO::cleanStorage();//@@DEBUG

    JALHOO::setDebug($debug);

    //-- Get our special language file
    JALHOO::loadLanguage('', '', 'nafuini');
}
catch(Exception $e)
{
    JError::raiseWarning(0, $e->getMessage());
}//try


//--Global functions
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'functions.php';

//-- Load helpers
ecrLoadHelper('project');
ecrLoadHelper('projecthelper');

//-- Global constants
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'defines.php';

$document = JFactory::getDocument();

//-- Add css
$document->addStyleSheet('components/com_easycreator/assets/css/default.css');

//-- Add javascript
$document->addScript(JURI::root().'components/com_easycreator/assets/js/easycreator.js');

//-- Include standard html
JLoader::import('helpers.html', JPATH_COMPONENT);

//-- Require the base controller
require_once JPATH_COMPONENT.DS.'controller.php';
$controller = new EasyCreatorController();

easyHTML::start();

//-- Perform the Request task
$controller->execute(JRequest::getVar('task'));

easyHTML::end();

($debug) ? JALHOO::debugPrintTranslateds(true) : null;
($debug) ? JALHOO::debugPrintTranslateds() : null;
($debug) ? var_dump(JALHOO::getStrings()) : null;

//-- Redirect if set by the controller
$controller->redirect();
