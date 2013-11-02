<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Base
 * @author     Nikolai Plath
 * @author     Created on 19-Mar-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

$params = JComponentHelper::getParams('com_easycreator');

/**
 * The OS specific directory separator -
 *
 * @todo remove ?
 */
defined('DS') || define('DS', DIRECTORY_SEPARATOR);

/**
 * A newline character for cleaner HTML styling.
 */
defined('BR') || define('BR', '<br />');

/**
 * A newline character for cleaner <pre> styling.
 */
defined('NL') || define('NL', "\n");

/**
 * Path for extension templates.
 */
define('ECRPATH_EXTENSIONTEMPLATES', JPath::clean(JPATH_COMPONENT_ADMINISTRATOR.'/templates'));

/**
 * Path for AutoCodes.
 */
define('ECRPATH_AUTOCODES', JPath::clean(ECRPATH_EXTENSIONTEMPLATES.'/autocodes'));

/**
 * Path for Parts.
 */
define('ECRPATH_PARTS', JPath::clean(ECRPATH_EXTENSIONTEMPLATES.'/parts'));

/**
 * Path for Helpers.
 */
define('ECRPATH_HELPERS', JPath::clean(JPATH_COMPONENT_ADMINISTRATOR.'/helpers'));

$dataDir = $params->get('local_data_dir');

if($dataDir)
{
    if(0 === strpos($dataDir, 'JROOT'))
        $dataDir = str_replace('JROOT', JPATH_ROOT, $dataDir);

    if(false == JFolder::exists($dataDir))
    {
        if(0 == strpos($dataDir, JPATH_ROOT))
        {
            //-- The data_dir is inside the J! root - try to create it.
            if(JFolder::create($dataDir))
            {
                JFactory::getApplication()->enqueueMessage(sprintf(
                    'The data directory has been created in %s'
                    , $dataDir
                ));
            }
            else
            {
                throw new DomainException(sprintf(
                    'Unable to create the data directory in %s'
                    , $dataDir
                ));
            }
        }
        else
        {
            throw new DomainException(sprintf(
                '%1$s - The data directory you specified does not exist - Please create it: %2$s'
                , basename(__FILE__), $dataDir
            ));
        }
    }

    /**
     * Path for user data.
     */
    define('ECRPATH_DATA', realpath($dataDir));
}
else
{
    /**
     * Path for user data.
     */
    define('ECRPATH_DATA', JPath::clean(JPATH_COMPONENT_ADMINISTRATOR.'/data'));
}

/**
 * Path for Logs.
 */
define('ECRPATH_LOGS', JPath::clean(ECRPATH_DATA.'/logs'));

/**
 * Path for Scripts.
 */
define('ECRPATH_SCRIPTS', JPath::clean(ECRPATH_DATA.'/projects'));

/**
 * Path for Builds.
 */
define('ECRPATH_BUILDS', JPath::clean(ECRPATH_DATA.'/builds'));

/**
 * Path for Exports.
 */
define('ECRPATH_EXPORTS', JPath::clean(ECRPATH_DATA.'/exports'));

$parts = explode('.', JVERSION);

if(count($parts) < 2)
    throw new Exception(__FILE__.' - Unfortunately we are not able to determine your Joomla! version :( :(');

/**
 * Joomla! version - only the important part..
 */
define('ECR_JVERSION', $parts[0].'.'.$parts[1]);

/**
 * EasyCreator Documentation location - might change sometimes =;)
 */
define('ECR_DOCU_LINK', 'http://joomla-wiki.de/dokumentation/Benutzer:Elkuku/Proyektz/EasyCreator');

/**
 * EasyCreator HELP mode.
 */
define('ECR_HELP', $params->get('ecr_help'));

/**
 * Display toolbar icons.
 */
define('ECR_TBAR_ICONS', $params->get('toolbar_icons', 1));

/**
 * Toolbar button size.
 */
define('ECR_TBAR_SIZE', $params->get('toolbar_size', ' btn-mini'));

$updateserverDir = $params->get('local_updateserver_dir');

if($updateserverDir)
{
    if(0 === strpos($dataDir, 'JROOT'))
        $dataDir = str_replace('JROOT', JPATH_ROOT, $dataDir);

    /**
     * Path for local update server.
     */
    define('ECRPATH_UPDATESERVER', JPath::clean($updateserverDir));

    if(0)
    {
        /**
         * Path for local update server.
         */
        define('ECRPATH_UPDATESERVER', JPath::clean(JPATH_ROOT.'/'.$updateserverDir));
    }
}
else
{
    /**
     * Path for local update server.
     */
    define('ECRPATH_UPDATESERVER', JPath::clean(ECRPATH_DATA.'/updateserver'));
}

define('ECRPATH_UPDATESERVER_URL', $params->get('updateserver_url'));

/*
 * Debug settings
 */
if(ECR_DEV_MODE)
{
    //-- Setup debugger
    if(JComponentHelper::getParams('com_easycreator')->get('ecr_debug'))
    {
        //-- Set debugging ON
        define('ECR_DEBUG', true);
    }
    else
    {
        define('ECR_DEBUG', false);
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
    define('ECR_DEBUG_LANG', 0);
}
