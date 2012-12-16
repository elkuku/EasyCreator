<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath
 * @author     Created on 24-Sep-2010
 * @license    GNU/GPL
 */

/**
 * EcrEasycreator helper class.
 */
class EcrEasycreator
{
    /**
     * @var array Supported compression formats for creating packages
     */
    public static $packFormats = array(
        'archiveZip' => 'zip'
    , 'archiveTgz' => 'tgz'
    , 'archiveBz2' => 'bz2'
    );

    /**
     * Get a specific controller.
     *
     * @return JController
     * @throws Exception
     */
    public static function getController()
    {
        $controller = JFactory::getApplication()->input->getWord('controller');

        if(strpos($controller, '.'))
        {
            throw new Exception('SubController separated by dot (.) - not implemented yet '.__CLASS__);
        }

        if($controller)
        {
            //-- Require specific controller if requested
            $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';

            if(false == file_exists($path))
                throw new Exception(sprintf(jgettext('Controller %s not found'), $controller));

            require_once $path;
        }
        else
        {
            //-- Require the base controller
            require_once JPATH_COMPONENT.DS.'controller.php';
        }

        //-- Create the controller
        $classname = 'EasyCreatorController'.$controller;

        if(false == class_exists($classname))
            throw new Exception(sprintf(jgettext('Controller class %s not found'), $classname));

        return new $classname;
    }
}
