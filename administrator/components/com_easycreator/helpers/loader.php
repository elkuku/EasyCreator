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

spl_autoload_register('easycreator_loader', true);

/**
 * Autoloader.
 *
 * @param $className
 *
 * @return mixed
 */
function easycreator_loader($className)
{
    if(0 !== strpos($className, 'Ecr'))
        return;

    $file = strtolower(substr($className, 3));

    $path = __DIR__.'/'.$file.'.php';

    //-- 1. search in 'helpers/<file>.php'
    if(file_exists($path))
    {
        include $path;

        return;
    }

    //-- 2. search in 'helpers/<path>/<file>.php'
    $parts = preg_split('/(?<=[a-z])(?=[A-Z])/x', substr($className, 3));

    $path = __DIR__.'/'.strtolower(implode('/', $parts)).'.php';

    if(file_exists($path))
    {
        include $path;

        return;
    }

    //-- 3. search in 'helpers/<path>/<file>/<file>.php'
    $file = strtolower($parts[count($parts) - 1]);

    $path = __DIR__.'/'.strtolower(implode('/', $parts)).'/'.$file.'.php';

    if(file_exists($path))
    {
        include $path;

        return;
    }
}
