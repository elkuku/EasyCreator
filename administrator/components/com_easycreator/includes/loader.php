<?php defined('_JEXEC') || die('=;)');
/**
 * This file contains global functions.
 *
 * @package    EasyCreator
 * @subpackage Base
 * @author     Nikolai Plath
 * @author     Created on 19-Mar-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

if(version_compare(PHP_VERSION, '5.3', '<'))
{
    //-- @todo php 5.2
    spl_autoload_register('easy_creator_loader', true);
}
else
{
    spl_autoload_register('easy_creator_loader', true, true);
}

//-- Load the special Language

//-- 1) Check if g11n is installed as a PEAR package - see: http://elkuku.github.com/pear/

//@include 'elkuku/g11n/language.php';

//-- @todo: check for installed g11n PEAR package to remove the "shut-up"
//-- @require_once 'elkuku/g11n/language.php';

if( ! class_exists('g11n'))
{
    //-- 2) Check the libraries folder

    if( ! jimport('g11n.language'))
    {
        //-- 3) Load a dummy language handler -> english only !

        ecrLoadHelper('g11n_dummy');

        ecrScript('g11n_dummy', 'php2js');
    }
}

if(class_exists('g11n'))
{
    //-- TEMP@@debug
    if(ECR_DEV_MODE && ECR_DEBUG_LANG)
    {
        //-- @@DEBUG
        g11n::cleanStorage();
        g11n::setDebug(ECR_DEBUG_LANG);
    }

    //-- Get our special language file
    g11n::loadLanguage();
}

/*
 * Functions
 */

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
        return;

    $base = JPATH_COMPONENT_ADMINISTRATOR.'/helpers';

    $file = strtolower(substr($className, 3));

    $path = $base.'/'.$file.'.php';

    //-- 1. search in 'helpers/<file>.php'
    if(file_exists($path))
    {
        include $path;

        return;
    }

    //-- 2. search in 'helpers/<path>/<file>.php'
    $parts = preg_split('/(?<=[a-z])(?=[A-Z])/x', substr($className, 3));

    $path = $base.'/'.strtolower(implode('/', $parts)).'.php';

    if(file_exists($path))
    {
        include $path;

        return;
    }

    //-- 3. search in 'helpers/<path>/<file>/<file>.php'
    $file = strtolower($parts[count($parts) - 1]);

    $path = $base.'/'.strtolower(implode('/', $parts)).'/'.$file.'.php';

    if(file_exists($path))
    {
        include $path;

        return;
    }
}

/**
 * Loads EasyCreator Helpers.
 *
 * @param string $name Path in JLoader dot syntax.
 *
 * @deprecated - 2B replaced by the autoloader
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
        EcrHtml::message(sprintf(jgettext('Helper file not found : %s'), $name), 'error');

        $helpers[$name] = false;

        return $helpers[$name];
    }

    $helpers[$name] = JLoader::import('helpers.'.$name, JPATH_COMPONENT_ADMINISTRATOR);

    return $helpers[$name];
}

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

    foreach($args as $name)
    {
        $name = (ECR_DEBUG) ? $name : $name.'.min';
        $document->addStylesheet(JUri::root(true).'/media/com_easycreator/admin/css/'.$name.'.css');
    }
}

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

    foreach($args as $name)
    {
        $name = (ECR_DEBUG) ? $name : $name.'.min';
        $document->addScript(JURI::root(true).'/media/com_easycreator/admin/js/'.$name.'.js');
    }
}

function ecrLoadMedia()
{
    $args = func_get_args();

    $document = JFactory::getDocument();

    foreach($args as $name)
    {
        $name = (ECR_DEBUG) ? $name : $name.'.min';

        $document->addStylesheet(JUri::root(true).'/media/com_easycreator/admin/css/'.$name.'.css');
        $document->addScript(JURI::root(true).'/media/com_easycreator/admin/js/'.$name.'.js');
    }
}
