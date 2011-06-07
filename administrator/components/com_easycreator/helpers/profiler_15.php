<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 03-May-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

jimport('joomla.error.profiler');

/**
 * Enter description here ...
 *
 */
class easyProfiler extends JProfiler
{
    /**
     * @var float
     */
    protected $_previous_time = 0.0;

    /**
     * @var float
     */
    protected $_previous_mem = 0.0;

    /**
     * Returns a reference to the global Profiler object.
     *
     * Only creating it if it doesn't already exist.
     *
     * This method must be invoked as:
     *         <pre>  $browser = JProfiler::getInstance( $prefix );</pre>
     *
     * @param string $prefix Prefix used to distinguish profiler objects.
     *
     * @access public
     * @return easyProfiler  The Profiler object.
     */
    public function getInstance($prefix = '')
    {
        static $instances;

        if( ! isset($instances))
        {
            $instances = array();
        }

        if(empty($instances[$prefix]))
        {
            $instances[$prefix] = new easyProfiler($prefix);
        }

        return $instances[$prefix];
    }//function

    /**
     * Output a time mark.
     *
     * @param string $label A label for the time mark
     *
     * @access public
     * @return string Mark
     */
    public function mark($label)
    {
        $current = self::getmicrotime() - $this->_start;

        if(function_exists('memory_get_usage'))
        {
            $current_mem = memory_get_usage() / 1048576;
            $mark = sprintf(
                '<span style="background-color: yellow;">%.3f sec (%.3f); %0.2f Mb (%0.2f)</span>&nbsp;-&nbsp;',
            $current,
            $current - $this->_previous_time,
            $current_mem,
            $current_mem - $this->_previous_mem
            );
        }
        else
        {
            $mark = sprintf(
                '<code>%.3f sec (+%.3f) - </code>',
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
