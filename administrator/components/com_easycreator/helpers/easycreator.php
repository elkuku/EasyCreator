<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath {@link http://nik-it.de}
 * @author     Created on 24-Sep-2010
 * @license    GNU/GPL
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...
 *
 */
class EasyCreatorHelper
{
    /**
     * Get a specific controller.
     *
     * @return JController
     * @throws Exception
     */
    public static function getController()
    {
        $controller = JRequest::getWord('controller');

        if(strpos($controller, '.'))
        {
            throw new Exception('SubController separated by dot (.) - not implemented yet '.__CLASS__);
        }

        //-- Require specific controller if requested
        if($controller)
        {
            $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';

            if(file_exists($path))
            {
                require_once $path;
            }
            else
            {
                $controller = '';
            }
        }

        //-- Require the base controller
        require_once JPATH_COMPONENT.DS.'controller.php';

        //-- Create the controller
        $classname = 'EasyCreatorController'.$controller;

        if( ! class_exists($classname))
        throw new Exception(sprintf(jgettext('Controller %s not found'), $classname));

        return new $classname;
    }//function

    /**
     *  We do this if the administrator folder is named somewhat other than 'administrator'.
     *
     *  @return string Relative URL path of components administration
     */
    public static function getAdminComponentUrlPath()
    {
        static $adminPath = '';

        if($adminPath)
        return $adminPath;

        $root = str_replace(DS, '/', JPATH_ROOT);
        $component = str_replace(DS, '/', JPATH_COMPONENT_ADMINISTRATOR);
        $adminPath = str_replace($root.'/', '', $component);

        return $adminPath;
    }//function
}//class
