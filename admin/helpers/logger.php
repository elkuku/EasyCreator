<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 25-May-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * Easy Logger.
 *
 * @package EasyCreator
 */
class EcrLogger
{
    private $fileName = '';

    private $logging = true;

    private $hot = false;

    private $fileContents = false;

    private $profile = false;

    private $profiler = null;

    private $log = array();

    private $cntCodeBoxes = 0;

    /**
     *
     * Get a logger instance.
     *
     * @param string $name    Custom name for the instance
     * @param array  $options Log options
     *
     * @return EcrLogger
     *
     * @throws EcrExceptionLog
     */
    public static function getInstance($name, $options = array())
    {
        static $instances = array();

        if(isset($instances[$name]))
            return $instances[$name];

        $fileName = (isset($options['fileName']) && $options['fileName'])
            ? $options['fileName']
            : '/log_'.time().'.log';

        if(false == JFolder::exists(ECRPATH_LOGS)
            && ! JFolder::create(ECRPATH_LOGS)
        )
            throw new EcrExceptionLog('Can not create log path '.ECRPATH_LOGS);

        if(false == touch(ECRPATH_LOGS.DS.$fileName))
            throw new EcrExceptionLog('Can not create log file '.$fileName);

        $instances[$name] = new EcrLogger($fileName, $options);

        return $instances[$name];
    }

    /**
     * Constructor.
     *
     * @param string $fileName Log file name
     * @param array  $options  Logging options
     */
    protected function __construct($fileName, $options)
    {
        $this->fileName = $fileName;

        $this->logging = (in_array('logging', $options)) ? true : false;
        $this->hot = (in_array('hotlogging', $options)) ? true : false;
        $this->fileContents = (in_array('files', $options)) ? true : false;
        $this->profile = (in_array('profile', $options)) ? true : false;

        if($this->profile)
        {
            $this->profiler = EcrProfiler::getInstance('EcrLogger');
        }
    }

    /**
     * Log a string.
     *
     * @param string $string The string to log
     * @param string $error  Error message
     * @param int    $priority
     *
     * @return void
     */
    public function log($string, $error = '', $priority = JLog::INFO)
    {
        if(false == $this->logging)
            return;

        $ret = '';

        if($this->profile)
            $ret .= $this->profiler->mark('log');

        $ret .= ($error) ? '<div class="ebc_error">'.$error.'</div>' : '';
        $ret .= $string;

        $this->log[] = $ret;

        if($this->hot)
            $this->writeLog();

        if('cli' == PHP_SAPI)
        {
            $s = str_replace('<br />', NL, $ret);
            $s = strip_tags($s);
            echo $s.NL;
        }

        $s = $string;
        $s = str_replace('<br />', NL, $s);
        $s = str_replace(array('<strong>', '</strong>'), '', $s);
        $s = str_replace(JPATH_ROOT, 'JROOT', $s);

        JLog::add($s, $priority);
    }

    /**
     * Logs file write attempts.
     *
     * @param string $from         Full path to template file
     * @param string $to           Full path to output file
     * @param string $fileContents File contents
     * @param string $error        Error message
     *
     * @return void
     */
    public function logFileWrite($from = '', $to = '', $fileContents = '', $error = '')
    {
        if(false == $this->logging)
            return;

        $noFileContents = array('php', 'css', 'js', 'xml', 'ini', 'po', 'sql');
        $fileContents = (in_array(JFile::getExt($to), $noFileContents)) ? $fileContents : '';

        if($from)
        {
            $from = str_replace(JPATH_ROOT, 'JROOT', $from);
            $fromFile = basename($from);
            $from = str_replace($fromFile, '', $from);
        }

        if($to)
        {
            $to = str_replace(JPATH_ROOT, 'JROOT', $to);
            $toFile = basename($to);
            $to = str_replace($toFile, '', $to);
        }

        $ret = '';
        $ret .= ($this->profile) ? $this->profiler->mark('fileWrite') : '';
        $ret .= '<strong>Writing file</strong><br />';
        $ret .= ($error) ? '<div class="ebc_error">'.$error.'</div>' : '';
        $ret .= ($from) ? 'From: '.$from.BR.'<strong style="color: blue;">'.$fromFile.'</strong>'.BR : '';
        $ret .= ($to) ? 'To:   '.$to.BR.'<strong style="color: blue;">'.$toFile.'</strong>'.BR : '';

        if($fileContents)
        {
            $ret .= '<div class="ecr_codebox_header"'
                .' onclick="toggleDiv(\'ecr_codebox_'.$this->cntCodeBoxes.'\');">'
                .jgettext('File Contents').'</div>';
            $ret .= '<div id="ecr_codebox_'.$this->cntCodeBoxes.'" style="display: none;">';
            $ret .= '<div class="ebc_code">'.highlight_string($fileContents, true).'</div>';
            $ret .= '</div>';
        }

        $ret .= '<hr />';

        $this->cntCodeBoxes ++;

        $this->log[] = $ret;

        if($this->hot)
        {
            $this->writeLog();
        }
    }

    /**
     * Log a database query.
     *
     * @param string  $query The query
     * @param boolean $error Error happened during execution
     *
     * @return void
     */
    public function logQuery($query, $error = false)
    {
        if(false == $this->logging)
        {
            return;
        }

        $ret = '';

        if($this->profile)
        {
            $ret = $this->profiler->mark('execute Query');
        }

        $ret .= '<strong>Executing query</strong>';

        if($error)
        {
            $ret .= '<h2 style="background-color: #ffb299;">'.jgettext('Error').'</h2>';
        }

        $ret .= '<div class="ecr_codebox_header"'
            .' onclick="toggleDiv(\'ecr_codebox_'.$this->cntCodeBoxes.'\');">'
            .jgettext('Query').'</div>';

        $ret .= '<div id="ecr_codebox_'.$this->cntCodeBoxes.'" style="display: none;">';
        $ret .= '<pre class="ebc_code">'.htmlentities($query).'</pre>';
        $ret .= '</div>';
        $this->cntCodeBoxes ++;

        if($error)
        {
            $ret .= '<div class="ecr_codebox_header"'
                .' onclick="toggleDiv(\'ecr_codebox_'.$this->cntCodeBoxes.'\');">'
                .jgettext('Error').'</div>';

            $ret .= '<div id="ecr_codebox_'.$this->cntCodeBoxes.'" style="display: none;">';
            $ret .= '<pre class="ebc_code">'.htmlentities($error).'</pre>';
            $ret .= '</div>';
            $this->cntCodeBoxes ++;
        }

        $ret .= '<hr />';

        $this->log[] = $ret;

        if($this->hot)
        {
            $this->writeLog();
        }
    }

    /**
     * Write the log to a file.
     *
     * @return boolean true on success
     */
    public function writeLog()
    {
        if(false == $this->logging
            || ! count($this->log)
           // || $this->hot
        )
            return true;

        $log = implode("\n", $this->log);

        if(false == JFile::write(ECRPATH_LOGS.DS.$this->fileName, $log))
        {
            JFactory::getApplication()->enqueueMessage(
                sprintf(jgettext('The file %s could not be written to path %s'), $this->fileName, ECRPATH_LOGS)
                , 'error');

            return false;
        }

        return true;
    }

    /**
     * Prints the log entries.
     *
     * @return string HTML log
     */
    public function printLog()
    {
        $html = '';

        if(false == $this->logging
            || ! count($this->log)
        )
            return $html;

        $html .= '<ul>';

        foreach($this->log as $entry)
        {
            $html .= '<li>'.$entry.'</li>';
        }

        $html .= '</ul>';

        return $html;
    }

    /**
     * @return string
     */
    public function printLogBox()
    {
        $html = '';

        if(false == $this->logging
            || ! count($this->log)
        )
            return $html;

        $html .= '<div class="ecr_codebox_header" style="font-size: 1.4em;" onclick="toggleDiv(\'ecr_logdisplay\');">'
            .jgettext('Log File')
            .'</div>'
            .'<div id="ecr_logdisplay" style="display: none;">'
            .$this->printLog()
            .'</div>';

        return $html;
    }
}//class
