<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 25-Apr-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * EasyCreator deployer.
 */
abstract class EcrDeployer
{
    /**
     * @var JFTP
     */
    protected $ftp = null;

    /**
     * @var EcrGithub
     */
    protected $github = null;

    protected $credentials = null;

    private $runTime = 0;

    /**
     * @static
     * @throws Exception
     * @return EcrDeployer
     */
    public static function getInstance()
    {
        $deployTarget = JFactory::getApplication()->input->get('deployTarget');

        if( ! $deployTarget)
            throw new Exception(__METHOD__.' - No type given');

        $className = 'EcrDeployerType'.ucfirst($deployTarget);

        return new $className;
    }

    /**
     * Constructor.
     */
    protected function __construct()
    {
        $this->runTime = microtime(true);

        $this->setupLog();

        JLog::add('|¯¯¯ Starting');

        $this->connect();
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
     * @abstract
     * @return mixed
     */
    abstract protected function connect();

    /**
     * @abstract
     * @return mixed
     */
    abstract public function getPackageList();

    /**
     * @abstract
     * @return mixed
     */
    abstract public function deployPackage();

    /**
     * @abstract
     * @return mixed
     */
    abstract public function deployFiles();

    /**
     * @abstract
     * @return mixed
     */
    abstract public function deletePackage();

    /**
     * Create the sync list file.
     *
     * @param array $syncList
     *
     * @throws Exception
     */
    protected function writeSyncList($syncList)
    {
        $lines = array();
        $project = EcrProjectHelper::getProject();

        foreach($syncList as $path => $item)
        {
            $lines[] = $path."\t".$item->size;
        }

        $path = JPATH_COMPONENT.'/data/sync/'.$project->comName.'.sync.txt';

        $contents = implode("\n", $lines);

        if(false == JFile::write($path, $contents))
            throw new Exception('Can not write the sync file');
    }

    /**
     * Read the sync list.
     *
     * @static
     * @return array
     * @throws Exception
     */
    protected static function readSyncList()
    {
        $syncList = array();

        $project = EcrProjectHelper::getProject();

        $path = JPATH_COMPONENT.'/data/sync/'.$project->comName.'.sync.txt';

        if(false == JFile::exists($path))
            return false;

        $lines = explode("\n", JFile::read($path));

        foreach($lines as $line)
        {
            if('' == trim($line))
                continue;

            $parts = explode("\t", $line);

            if(2 != count($parts))
                throw new Exception(__METHOD__.' - expecting 2 parts not '.count($parts));

            $f = new stdClass;
            $f->path = $parts[0];
            $f->size = $parts[1];

            $syncList[$f->path] = $f;
        }

        return $syncList;
    }

    /**
     * Create the files to sync list.
     *
     * @return array
     * @throws Exception
     */
    public static function getSyncList()
    {
        $fileList = array();
        $project = EcrProjectHelper::getProject();
        $syncList = self::readSyncList();

        if(false === $syncList)
            throw new Exception(jgettext('No synchronization list found - Please synchronize with your remote'));

        $allCopies = array();

        foreach($project->copies as $copy)
        {
            $files = JFolder::files($copy, '.', true, true);

            $allCopies = array_merge($files, $allCopies);

            foreach($files as $file)
            {
                $fShort = str_replace(JPATH_ROOT.'/', '', $file);

                //-- File does not exist
                if( ! array_key_exists($fShort, $syncList))
                {
                    $f = new stdClass;
                    $f->path = $fShort;
                    $f->status = 'new';

                    $fileList[$fShort] = $f;
                }
                else
                {
                    $f = $syncList[$fShort];

                    //-- File size is different
                    if($f->size != filesize($file))
                    {
                        $f->status = 'changed';

                        $fileList[$fShort] = $f;
                    }
                }
            }
        }

        foreach($syncList as $item)
        {
            if( ! in_array(JPATH_ROOT.'/'.$item->path, $allCopies))
            {
                $f = new stdClass;
                $f->path = $item->path;
                $f->status = 'deleted';

                $fileList[$item->path] = $f;
            }
        }

        ksort($fileList);

        return $fileList;
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
