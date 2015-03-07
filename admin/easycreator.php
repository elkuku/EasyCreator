<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Base
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 06-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

// When changing Joomla! versions look for:
// @Joomla!-version-check
// @Joomla!-compat XXXX

// Dev mode - internal use =;)
// @@DEBUG
define('ECR_DEV_MODE', 1);

JDEBUG ? JProfiler::getInstance('Application')->mark('com_easycreator starting') : null;

//@todo legacy imports...
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

JHtml::_('behavior.framework');
JHTML::_('behavior.tooltip');

// Global constants
require JPATH_COMPONENT.'/includes/defines.php';

try
{
    // Global functions
    require JPATH_COMPONENT.'/includes/loader.php';
}
catch(Exception $e)
{
    JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

    return;
}

/**
 * EasyCreator Version
 */
define('ECR_VERSION', EcrProjectHelper::parseXMLInstallFile(
    JPATH_COMPONENT_ADMINISTRATOR.DS.'easycreator.xml')->version);

/**
 * Check the Joomla! version
 *
 * @Joomla!-version-check
 */
switch(ECR_JVERSION)
{
    case '1.5' :
    case '1.6' :
    case '1.7' :
        JFactory::getApplication()->enqueueMessage(sprintf(
                jgettext('EasyCreator %1$s is not compatible with Joomla! %2$s - Sorry.')
                , ECR_VERSION, ECR_JVERSION)
            , 'error');

        return;
        break;

    case '2.5':
        //-- @Joomla!-compat 2.5
        ecrStylesheet('bootstrap');
        break;

    case '3.0':
    case '3.1':
    case '3.2': // @joomla blabla
    case '3.3': // @todo huhu
    case '3.4': // Current
		if (version_compare(JVERSION, '3.2.2-dev', '<'))
		{
	        JFactory::getApplication()->JComponentTitle = 'EasyCreator';
		}
        break;

    case '4': // Get prepared
        $application = JFactory::getApplication();

        $application->JComponentTitle = 'EasyCreator';

        $application->enqueueMessage(sprintf(
                jgettext(
                    'EasyCreator %1$s is in testing stage with Joomla! %2$s'
                )
                , ECR_VERSION, ECR_JVERSION)
            , 'warning'
        );

        break;

    default:
        JFactory::getApplication()->enqueueMessage(sprintf(
            jgettext('EasyCreator version %s may not work well with your Joomla! version %s')
            , ECR_VERSION, ECR_JVERSION), 'error');
        break;
}

// Add CSS
ecrStylesheet('default', 'toolbar', 'icon');

// Add JavaScript
ecrScript('global_vars', 'easycreator');

JFactory::getDocument()->addScriptDeclaration("var ECR_VERSION = '".ECR_VERSION."';".NL);
JFactory::getDocument()->addScriptDeclaration("var ECR_JVERSION = '".ECR_JVERSION."';".NL);

$prevErrorReporting = error_reporting(- 1);

try
{
    $controller = EcrEasycreator::getController();

    $input = JFactory::getApplication()->input;

    if('component' == $input->get('tmpl'))
    {
        // Perform the Request task only - raw view
        $controller->execute($input->get('task'));
    }
    else
    {
        // Display the menu
        EcrHtmlMenu::main();

        // Perform the Request task
        $controller->execute($input->get('task'));

        if(ECR_DEV_MODE && ECR_DEBUG_LANG
            && class_exists('g11n')
        )
        {
            g11n::debugPrintTranslateds(true);
            g11n::debugPrintTranslateds();
        }

        // Display the footer
        EcrHtml::footer();

        JDEBUG ? JProfiler::getInstance('Application')->mark('com_easycreator finished') : null;
    }

    // Restore error_reporting
    error_reporting($prevErrorReporting);

    // Redirect if set by the controller
    // We don't do this very often =;)
    $controller->redirect();
}
catch(Exception $e)
{
    EcrHtml::message($e);
    JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

    if(ECR_DEBUG)
    {
        echo '<pre>'.$e->getTraceAsString().'</pre>';
    }
}

// Restore error_reporting
error_reporting($prevErrorReporting);
