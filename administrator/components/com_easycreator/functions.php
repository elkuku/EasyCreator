<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Base
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 19-Mar-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

jimport('joomla.filesystem.file');

/*
 * Global functions.
 */

/**
 * Loads EasyCreator Helpers.
 *
 * @param string $name Path in JLoader dot syntax.
 *
 * @return mixed [string path | boolean false on error]
 */
function ecrLoadHelper($name)
{
    static $helpers = array();

    if(isset($helpers[$name]))
    {
        return $helpers[$name];
    }

    if( ! JFile::exists(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/'.str_replace('.', '/', $name).'.php'))
    {
        ecrHTML::displayMessage(sprintf(jgettext('Helper file not found : %s'), $name), 'error');

        $helpers[$name] = false;

        return $helpers[$name];
    }

    $helpers[$name] = JLoader::import('helpers.'.$name, JPATH_COMPONENT_ADMINISTRATOR);

    return $helpers[$name];
}//function

/**
 * Adds a CSS stylesheet filename from standard CSS directory to the document.
 *
 * @param string $name Name of the style sheet
 *
 * @return boolean
 */
function ecrStylesheet($name)
{
    return JFactory::getDocument()->addStylesheet(JURI::root(true)
    .'/'.EasyCreatorHelper::getAdminComponentUrlPath()
    .'/assets/css/'.$name.'.css');
}//function

/**
 * Adds a Javascript file from standard Javascript directory to the document.
 *
 * @param string $name Name of the script
 *
 * @return boolean
 */
function ecrScript($name)
{
    $adminPath = getAdminComponentUrlPath();

    return JFactory::getDocument()->addScript(JURI::root(true)
    .'/'.EasyCreatorHelper::getAdminComponentUrlPath()
    .'/assets/js/'.$name.'.js');
}//function
