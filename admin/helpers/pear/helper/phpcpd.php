<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author		Nikolai Plath
 * @author		Created on 28-Sep-2009
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * PHP Copy & Paste detector helper.
 *
 * @package EasyCreator
 */
class EcrPearHelperPhpcpd extends EcrPearHelperConsole
{
    /**
     * Runs the duplicated code detection.
     *
     * @param array $arguments Indexed array with arguments.
     * @param mixed $dirs One or more directories.
     *
     * @return string HTML (?)
     */
    public function detect($arguments, $dirs)
    {
        if(ECR_DEBUG)
        var_dump($dirs);

        if(is_array($dirs))
        {
//            ###    	$dir = JPATH_ROOT.DS.$dirs[1];
            //@todo - when phpcpd supports multiple dirs..
        }
        else
        {
            /*
             * Parse directories
             * clean path
             */
            $dir = JPATH_ROOT.DS.str_replace('/', DS, $dirs);
        }

        $args = array();

        if(count($arguments))
        {
            foreach($arguments as $name => $value)
            {
                $args[] = '--'.$name.' '.$value;
            }//foreach
        }

        $args[] = $dir;

        $results = $this->cliExec('phpcpd', $args);

        //@todo save to file

        return $results;
    }//function
}//class
