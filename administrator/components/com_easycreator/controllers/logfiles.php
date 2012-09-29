<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Controllers
 * @author     Nikolai Plath
 * @author     Created on 23-Sep-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * EasyCreator Controller.
 *
 * @package    EasyCreator
 * @subpackage Controllers
 */
class EasyCreatorControllerLogfiles extends JControllerLegacy
{
    /**
     * @var EcrResponseJson
     */
    private $response;

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct($config = array())
    {
        $this->response = new EcrResponseJson;

        parent::__construct($config);
    }

    /**
     * Standard display method.
     *
     * @param bool       $cachable  If true, the view output will be cached
     * @param array|bool $urlparams An array of safe url parameters and their variable types,
     *                              for valid values see {@link JFilterInput::clean()}.
     *
     * @return \JController|void
     */
    public function display($cachable = false, $urlparams = false)
    {
        JFactory::getApplication()->input->set('view', 'logfiles');
        parent::display($cachable, $urlparams);
    }

    /**
     * Deletes ALL log files (no warning..).
     *
     * @return void
     */
    public function clearLogfiles()
    {
        $logfiles = JFolder::files(ECRPATH_LOGS, 'log', false, true);

        $application = JFactory::getApplication();

        if(count($logfiles))
        {
            if(JFile::delete($logfiles))
            {
                $application->enqueueMessage(jgettext('The logfiles have been deleted'));
                $application->input->set('view', 'easycreator');
            }
            else
            {
                $application->enqueueMessage(jgettext('The logfiles could not be deleted'), 'error');
                $application->input->set('view', 'logfiles');
            }
        }

        parent::display();
    }

    /**
     * Display contents of a log file.
     *
     * @AJAX
     *
     * @return void
     */
    public function showLogfile()
    {
        $fileName = JFactory::getApplication()->input->get('fileName');

        if(false == JFile::exists(ECRPATH_LOGS.DS.$fileName))
        {
            $this->response->status = 1;
            $this->response->message = jgettext('File not found');
            $this->response->debug = ECRPATH_LOGS.DS.$fileName;
        }
        else
        {
            $this->response->message = JFile::read(ECRPATH_LOGS.DS.$fileName);
        }

        echo $this->response;

        jexit();
    }

    /**
     * Poll a log file.
     *
     * @AJAX
     *
     * @return void
     */
    public function pollLog()
    {
        $logPath = JFactory::getConfig()->get('log_path');

        $path = $logPath.'/ecr_log.php';

        if(JFile::exists($path))
        {
            $s = $this->parseLog(JFile::read($path));

            $s .= "\n".'Time '.date('H:i:s');

            $this->response->message = $s;

            if(JFile::exists($logPath.'/ecr_steplog.txt'))
                $this->response->progress = (int)JFile::read($logPath.'/ecr_steplog.txt');
        }
        else
        {
            $this->response->status = 1;
            $this->response->message = jgettext('Log file not found').' --- '.date('H:i:s');
        }

        echo $this->response;

        jexit();
    }

    /**
     * Parse a log file.
     *
     * @param $string
     *
     * @return string
     */
    private function parseLog($string)
    {
        $lines = explode("\n", $string);

        foreach($lines as &$line)
        {
            $parts = explode("\t", $line);

            if(3 == count($parts))
            {
                $t = $parts[0];

                if(25 == strlen($t))
                {
                    $t = substr($t, 11, 8);
                }

                $prio = $parts[1];

                switch($prio)
                {
                    case 'INFO':
                        $prio = '<span style=" color: #00BFFF;">INFO</span>';
                        break;
                    case 'WARNING':
                        $prio = '<span style=" color: orange;">WARNING</span>';
                        break;
                    case 'ERROR':
                        $prio = '<span style=" color: red;">ERROR</span>';
                        break;
                }

                $line = $t."\t".$prio."\t".$parts[2];
            }
        }

        return implode("\n", $lines);
    }
}
