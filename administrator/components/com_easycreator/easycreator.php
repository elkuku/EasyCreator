<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Base
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 06-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/*
 This is our SQL INSERT query (for manual install)

 ** J! 1.5
 INSERT INTO `#__components`
 (`name`, `link`, `menuid`, `parent`, `admin_menu_link`
 , `admin_menu_alt`, `option`, `ordering`
 , `admin_menu_img`, `iscore`, `params`, `enabled`)
 VALUES
 ('EasyCreator', 'option=com_easycreator', 0, 0, 'option=com_easycreator'
 , 'EasyCreator', 'com_easycreator', 0
 , 'components/com_easycreator/assets/images/ico/icon-16-easycreator.png', 0, '', 1);

 ** J! 1.6
 Use the new 'Discover' feature from the Joomla! installer - works great =;)
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

//-- Dev mode - internal use =;)
define('ECR_DEV_MODE', 1);//@@DEBUG

jimport('joomla.error.profiler');
$profiler = JProfiler::getInstance('Application');

JDEBUG ? $profiler->mark('com_easycreator starting') : null;

//-- Global constants
require_once 'defines.php';


//-- Global functions
require_once 'functions.php';

if(ECR_DEV_MODE)
{
    //-- Setup debugger
    if(JComponentHelper::getParams('com_easycreator')->get('ecr_debug'))
    {
        ecrLoadHelper('debug');

        //-- Set debugging ON
        define('ECR_DEBUG', 1);
    }
    else
    {
        define('ECR_DEBUG', 0);
    }

    if(JComponentHelper::getParams('com_easycreator')->get('ecr_debug_lang', 0))
    {
        //-- Set debugging ON
        define('ECR_DEBUG_LANG', 1);
    }
    else
    {
        define('ECR_DEBUG_LANG', 0);
    }
}
else
{
    define('ECR_DEBUG', 0);
}

//-- Load helpers
ecrLoadHelper('easycreator');
ecrLoadHelper('html');
ecrLoadHelper('projecthelper');
ecrLoadHelper('languagehelper');

//-- Load the special Language
try
{
    if( ! jimport('g11n.language'))
    {
        //-- Load dummy language handler - english only !
        ecrLoadHelper('g11n_dummy');

        ecrScript('g11n_dummy');
        ecrScript('php2js');
        //        throw new Exception('The g11n language library is required to run this extension.');
    }
    else
    {
            g11n::cleanStorage();//@@DEBUG
        //TEMP@@debug
        if(ECR_DEV_MODE && ECR_DEBUG_LANG)
        {
            g11n::setDebug(ECR_DEBUG_LANG);
        }

        //TEMP@@debug

        //-- Get our special language file
        g11n::loadLanguage();
    }
}
catch(Exception $e)
{
    JError::raiseWarning(0, $e->getMessage());

    return;
}//try

/**
 * EasyCreator Version
 */
//-- Get the component XML manifest data
define('ECR_VERSION', EasyProjectHelper::parseXMLInstallFile(
JPATH_COMPONENT_ADMINISTRATOR.DS.'easycreator.xml')->version);

/**
 * Check the Joomla! version
 */
switch (ECR_JVERSION)
{
    case '1.5':
    case '1.6':
        //We're all OK =;)
    break;

    case '1.7':
        JError::raiseNotice(0, sprintf(jgettext('EasyCreator version %s is in testing stage with your Joomla! version %s'), ECR_VERSION, ECR_JVERSION));
    break;

    default:
        JError::raiseWarning(0, sprintf(jgettext('EasyCreator version %s may not work well with your Joomla! version %s'), ECR_VERSION, ECR_JVERSION));
    break;
}//switch

//-- Add CSS
ecrStylesheet('default');
ecrStylesheet('toolbar');
ecrStylesheet('icon');

//-- Add javascript
ecrScript('global_vars');
ecrScript('easycreator');

JFactory::getDocument()->addScriptDeclaration("var ECR_JVERSION = '".ECR_JVERSION."';".NL);
JFactory::getDocument()->addScriptDeclaration("var ECR_VERSION = '".ECR_VERSION."';".NL);

//-- Setup tooltips - used almost everywhere..
JHTML::_('behavior.tooltip');
JHTML::_('behavior.tooltip', '.hasEasyTip', array('className' => 'easy'));

if(version_compare(JVERSION, '1.6', '>'))
{
    //-- Joomla! 1.6+ compat

    error_reporting(E_ALL);
    //error_reporting(E_STRICT);//...when ¿

    //-- Mootools compat for 1.2, 1.3
    ecrScript('compat_mootools');
}
else
{
    /*
     * Joomla! 1.5 legacy stuff
     */

    error_reporting(E_ALL);

    $MTVersion = JFactory::getApplication()->get('MooToolsVersion');

    if( ! $MTVersion)
    JError::raiseWarning(0, jgettext('Please activate the MooTools Upgrade Plugin in Extensions->Plugin manager'));

    //-- J! 1.6 stuff not present in J! 1.5
    ecrLoadHelper('databasequery');
    ecrScript('compat_joomla');
}

$controller = EasyCreatorHelper::getController();

if(JRequest::getCmd('tmpl') == 'component')
{
    //-- Perform the Request task only - raw view
    $controller->execute(JRequest::getCmd('task'));
}
else
{
    //-- Display the menu
    ecrHTML::easyMenu();

    //-- Perform the Request task
    $controller->execute(JRequest::getCmd('task'));

    if(ECR_DEV_MODE && ECR_DEBUG_LANG)
    {
        g11n::debugPrintTranslateds(true);
        g11n::debugPrintTranslateds();
        //    var_dump(g11n::getStrings());
    }

    //-- Display the footer
    ecrHTML::footer();

    JDEBUG ? $profiler->mark('com_easycreator finished') : null;
}

//-- Re-set error_reporting
error_reporting(E_ALL);

//-- Redirect if set by the controller
$controller->redirect();//-- We don't do this very often =;)
