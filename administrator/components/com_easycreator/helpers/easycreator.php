<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author     Nikolai Plath {@link http://easy-joomla.org}
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
     * @return object Controller
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
}//class
