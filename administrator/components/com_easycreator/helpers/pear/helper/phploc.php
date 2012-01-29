<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath
 * @author     Created on 12-Aug-2011
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * PHP count lines of code.
 *
 * @package EasyCreator
 */
class EcrPearHelperPhploc extends EcrPearHelperConsole
{
    /**
     * Count the lines.
     *
     * @param mixed $dirs One or more directories.
     * @param array $arguments Indexed array with arguments.
     *
     * @return string HTML (?)
     */
    public function count($dirs, $arguments = array())
    {
//         if(ECR_DEBUG)
//         var_dump($dirs);

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

        $results = $this->cliExec('phploc', $args);

        //-- @todo save to file

        return $results;
    }//function
}//class
