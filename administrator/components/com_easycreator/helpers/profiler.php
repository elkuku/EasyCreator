<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 20-Oct-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

jimport('joomla.error.profiler');

/**
 * EcrProfiler.
 *
 * @package EasyCreator
 */
class EcrProfiler extends JProfiler
{
    /**
     * Returns a reference to the global Profiler object.
     *
     *  Only creating it if it doesn't already exist.
     *
     * This method must be invoked as:
     *         <pre>  $profiler = EcrProfiler::getInstance($prefix);</pre>
     *
     * @param string $prefix Prefix used to distinguish profiler objects.
     *
     * @return EcrProfiler The Profiler object.
     */
    public static function getInstance($prefix = '')
    {
        static $instances;

        if( ! isset($instances))
        {
            $instances = array();
        }

        if(empty($instances[$prefix]))
        {
            $instances[$prefix] = new EcrProfiler($prefix);
        }

        return $instances[$prefix];
    }//function

    /**
     * Output a time mark.
     *
     * The mark is returned as text enclosed in yellow <span> tags.
     *
     * @param string $label A label for the time mark
     *
     * @return string Mark enclosed in <div> tags
     */
    public function mark($label)
    {
        $current = self::getmicrotime() - $this->_start;

        if(function_exists('memory_get_usage'))
        {
            $current_mem = memory_get_usage() / 1048576;
            $mark = sprintf(
                '<span style="background-color: yellow;">%.3f sec (+%.3f); %0.2f Mb (+%0.2f)</span> - ',
                $current,
                $current - $this->_previous_time,
                $current_mem,
                $current_mem - $this->_previous_mem
            );
        }
        else
        {
            $mark = sprintf(
                '<span style="background-color: yellow;">%.3f sec (+%.3f)</span> - ',
                $current,
                $current - $this->_previous_time
            );
        }

        $this->_previous_time = $current;
        $this->_previous_mem = $current_mem;
        $this->_buffer[] = $mark;

        return $mark;
    }//function
}//class
