<?php
/**
 * User: elkuku
 * Date: 30.04.12
 * Time: 14:21
 */

/**
 * EasyCreator log console.
 */
class EcrLogconsole
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->runTime = microtime(true);

        $this->setupLog();

        JLog::add('|¯¯¯ Starting');
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        $time = number_format(microtime(true) - $this->runTime, 2);

        JLog::add(sprintf('|___ Finished in %s sec.', $time));

        //-- Give the logger a chance to finish.
        sleep(1);
    }

    /**
     * Set up the log file.
     *
     * @return \EcrDeployer
     */
    private function setupLog()
    {
        $path = JFactory::getConfig()->get('log_path');

        $fileName = 'ecr_log.php';
        $entry = '';

        if('preserve' == JFactory::getApplication()->input->get('logMode')
            && JFile::exists($path.'/'.$fileName)
        )
        {
            $entry = '----------------------------------------------';
        }
        else if(JFile::exists($path.'/'.$fileName))
        {
            JFile::delete($path.'/'.$fileName);
        }

        JLog::addLogger(
            array(
                'text_file' => $fileName
            , 'text_entry_format' => '{DATETIME}	{PRIORITY}	{MESSAGE}'
            , 'text_file_no_php' => true
            )
            , JLog::INFO | JLog::ERROR
        );

        if('' != $entry)
            JLog::add($entry);

        return $this;
    }
}
