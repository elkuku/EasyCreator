<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Base
 * @author     Nikolai Plath
 * @author     Created on 19-Mar-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * The OS specific directory separator - @todo remove ?
 */
defined('DS') || define('DS', DIRECTORY_SEPARATOR);

/**
 * A newline character for cleaner HTML styling
 */
defined('BR') || define('BR', '<br />');

/**
 * A newline character for cleaner <pre> styling
 */
defined('NL') || define('NL', "\n");

/**
 * Path for extension templates
 */
define('ECRPATH_EXTENSIONTEMPLATES', JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'templates');

/**
 * Path for AutoCodes
 */
define('ECRPATH_AUTOCODES', ECRPATH_EXTENSIONTEMPLATES.DIRECTORY_SEPARATOR.'autocodes');

/**
 * Path for Parts
 */
define('ECRPATH_PARTS', ECRPATH_EXTENSIONTEMPLATES.DIRECTORY_SEPARATOR.'parts');

/**
 * Path for Logs
 */
define('ECRPATH_LOGS', JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'logs');

/**
 * Path for Scripts
 */
define('ECRPATH_SCRIPTS', JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'scripts');

/**
 * Path for Scripts
 */
define('ECRPATH_HELPERS', JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers');

/**
 * Path for Builds
 */
define('ECRPATH_BUILDS', JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'builds');

/**
 * Path for Exports
 * @deprecated
 */
define('ECRPATH_EXPORTS', JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'exports');

/**
 * Path for Exports
 */
define('ECRPATH_DATA', JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'data');

$parts = explode('.', JVERSION);

if(3 != count($parts))
{
    throw new Exception(__FILE__.' - Unfortunately we are not able to determine your Joomla! version :( :(');
}

/**
 * Joomla! version - only the important part..
 */
define('ECR_JVERSION', $parts[0].'.'.$parts[1]);

/**
 * EasyCreator Documentation location - might change sometimes =;)
 */
define('ECR_DOCU_LINK', 'http://wiki.joomla-nafu.de/joomla-dokumentation/Benutzer:Elkuku/Proyektz/EasyCreator');

/**
 * EasyCreator HELP mode.
 */
define('ECR_HELP', JComponentHelper::getParams('com_easycreator')->get('ecr_help'));
