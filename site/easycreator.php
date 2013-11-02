<?php
/**
 * @package     EasyCreator
 * @subpackage  Frontend
 * @author      Nikolai Plath (elkuku) <der.el.kuku@gmail.com>
 * @created     24-Sep-2008
 * @copyright   2008 - now()
 * @license     GPL http://gnu.org
 */

defined('_JEXEC') || die('=;)');

// @todo legacy imports...
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

// Global functions
require_once JPATH_COMPONENT_ADMINISTRATOR.'/includes/loader.php';

// Global constants
require_once JPATH_COMPONENT_ADMINISTRATOR.'/includes/defines.php';

$debug = false;

if( ! class_exists('g11n'))
{
    jimport('g11n.language');

    if( ! class_exists('g11n'))
    {
        ecrLoadHelper('g11n_dummy');
    }
}

try
{
    if(class_exists('g11n'))
    {
        // TEMP@@debug
        // @   g11n::cleanStorage();//@@DEBUG

        g11n::setDebug($debug);

        // Get our special language file
        g11n::loadLanguage();
    }
}
catch(Exception $e)
{
    JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
}

$document = JFactory::getDocument();

// Add css
$document->addStyleSheet('media/com_easycreator/site/css/default.css');

// Add javascript
$document->addScript('media/com_easycreator/site/js/easycreator.js');

// Include standard html
JLoader::import('helpers.html', JPATH_COMPONENT);

// Require the base controller
require_once JPATH_COMPONENT.DS.'controller.php';

$controller = new EasyCreatorController;

easyHTML::start();

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));

easyHTML::end();

($debug && class_exists('g11n')) ? g11n::debugPrintTranslateds() : null;

// Redirect if set by the controller
$controller->redirect();
