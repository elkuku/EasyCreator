<?php
/**
 * @package    EasyCreator
 * @subpackage Base
 * @author     Nikolai Plath
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
        EcrHtml::displayMessage(sprintf(jgettext('Helper file not found : %s'), $name), 'error');

        $helpers[$name] = false;

        return $helpers[$name];
    }

    $helpers[$name] = JLoader::import('helpers.'.$name, JPATH_COMPONENT_ADMINISTRATOR);

    return $helpers[$name];
}//function

/**
 * Adds a CSS stylesheet filename from standard CSS directory to the document.
 *
 * Multiple file names may be passed as arguments.
 *
 * @return boolean
 */
function ecrStylesheet()
{
    $args = func_get_args();

    $document = JFactory::getDocument();
    $path = EcrEasycreator::getAdminComponentUrlPath();

    foreach($args as $name)
    {
        $document->addStylesheet(JURI::root(true).'/'.$path.'/assets/css/'.$name.'.css');
    }//foreach
}//function

/**
 * Adds a Javascript file from standard Javascript directory to the document.
 *
 * Multiple file names may be passed as arguments.
 *
 * @return boolean
 */
function ecrScript()
{
    $args = func_get_args();

    $document = JFactory::getDocument();
    $path = EcrEasycreator::getAdminComponentUrlPath();

    foreach($args as $name)
    {
        $document->addScript(JURI::root(true).'/'.$path.'/assets/js/'.$name.'.js');
    }//foreach
}//function

if(version_compare(PHP_VERSION, '5.3', '<'))
{
    spl_autoload_register('easy_creator_loader', true);
}
else
{
    spl_autoload_register('easy_creator_loader', true, true);
}

/**
 * Autoloader.
 *
 * @param $className
 *
 * @return mixed
 */
function easy_creator_loader($className)
{
    if(0 !== strpos($className, 'Ecr'))
    {
        if('1.5' == ECR_JVERSION)
            JLoader::load($className);

        return;
    }

    $base = JPATH_COMPONENT_ADMINISTRATOR.'/helpers';

    $file = strtolower(substr($className, 3)).'.php';

    $path = $base.'/'.$file;

    if(file_exists($path))
    {
        include $path;

        return;
    }

    $parts = preg_split('/(?<=[a-z])(?=[A-Z])/x', substr($className, 3));

    $path = $base.'/'.strtolower(implode('/', $parts)).'.php';

    if(file_exists($path))
    {
        include $path;

        return;
    }
}//function
