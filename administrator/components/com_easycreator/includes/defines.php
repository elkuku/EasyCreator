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
define('ECRPATH_EXTENSIONTEMPLATES', JPath::clean(JPATH_COMPONENT_ADMINISTRATOR.'/templates'));

/**
 * Path for AutoCodes
 */
define('ECRPATH_AUTOCODES', JPath::clean(ECRPATH_EXTENSIONTEMPLATES.'/autocodes'));

/**
 * Path for Parts
 */
define('ECRPATH_PARTS', JPath::clean(ECRPATH_EXTENSIONTEMPLATES.'/parts'));

/**
 * Path for Helpers
 */
define('ECRPATH_HELPERS', JPath::clean(JPATH_COMPONENT_ADMINISTRATOR.'/helpers'));

/**
 * Path for Exports
 */
define('ECRPATH_DATA', JPath::clean(JPATH_COMPONENT_ADMINISTRATOR.'/data'));

/**
* Path for Logs
*/
define('ECRPATH_LOGS', JPath::clean(ECRPATH_DATA.'/logs'));

/**
* Path for Scripts
*/
define('ECRPATH_SCRIPTS', JPath::clean(ECRPATH_DATA.'/projects'));

/**
 * Path for Builds
 */
define('ECRPATH_BUILDS', JPath::clean(ECRPATH_DATA.'/builds'));

/**
 * Path for Exports
 */
define('ECRPATH_EXPORTS', JPath::clean(ECRPATH_DATA.'/exports'));

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
