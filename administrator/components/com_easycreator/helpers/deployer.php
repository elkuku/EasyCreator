<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elkuku
 * Date: 19.04.12
 * Time: 11:37
 * To change this template use File | Settings | File Templates.
 */

class EcrDeployer
{
    /**
     * @var JFTP
     */
    private static $ftp = null;

    /**
     * @var EcrGithub
     */
    private static $github = null;

    private static $credentials = null;

    /**
     * @static
     * @return array|mixed
     * @throws Exception
     */
    public static function getPackageList()
    {
        $input = JFactory::getApplication()->input;

        self::setupLog();

        JLog::add('|¯¯¯ Starting');

        $type = $input->get('type');

        self::connect($type);

        JLog::add('| << '.jgettext('Obtaining download list ...'));

        switch($type)
        {
            case 'ftp':
                $downloads = self::$ftp->listDetails(self::$credentials->downloads, 'files');

                if(count($downloads))
                {
                    $downloads = JArrayHelper::toObject($downloads);

                    if(! $downloads)
                        return array();

                    foreach($downloads as &$download)
                    {
                        $download->html_url = '';
                    }
                }

                break;

            case 'github':
                $downloads = self::$github->downloads->getList($input->get('owner'), $input->get('repo'));

                break;

            default:
                throw new Exception(__METHOD__.' - Unknown deploy type: '.$input->get('type'));
                break;
        }

        JLog::add('|___ Finished');

        //-- Give the logger a chance to finish.
        sleep(1);

        return $downloads;
    }

    /**
     * @static
     *
     * @throws Exception
     * @return array
     */
    public static function syncFiles()
    {
        self::setupLog();

        JLog::add('|¯¯¯ Starting');

        $input = JFactory::getApplication()->input;

        switch($input->get('type'))
        {
            case 'ftp':
                self::connect('ftp');

                $project = EcrProjectHelper::getProject();

                $syncList = array();

                //-- Get the sync list
                foreach($project->copies as $folder)
                {
                    $path = str_replace(JPATH_ROOT, self::$credentials->directory, $folder);

                    $syncList += self::scan($path);
                }

                self::writeSyncList($syncList);

                $fileList = self::getSyncList();

                JLog::add('|___ Finished');

                //-- Give the logger a chance to finish.
                sleep(1);
                return $fileList;

                break;

            default:
                throw new Exception(__METHOD__.' - Unknown deploy type: '.$input->get('type'));
                break;
        }
    }

    /**
     * Create the sync file.
     *
     * @param array $syncList
     *
     * @throws Exception
     */
    private static function writeSyncList($syncList)
    {
        $lines = array();
        $project = EcrProjectHelper::getProject();

        foreach($syncList as $path => $item)
        {
            $lines[] = $path."\t".$item->size;
        }

        $path = JPATH_COMPONENT.'/data/sync/'.$project->comName.'.sync.txt';

        $contents = implode("\n", $lines);

        if(! JFile::write($path, $contents))
            throw new Exception('Can not write the sync file');
    }

    /**
     * @static
     * @return array
     * @throws Exception
     */
    private static function readSyncList()
    {
        $syncList = array();

        $project = EcrProjectHelper::getProject();

        $path = JPATH_COMPONENT.'/data/sync/'.$project->comName.'.sync.txt';

        if(! JFile::exists($path))
            return false;

        $lines = explode("\n", JFile::read($path));

        foreach($lines as $line)
        {
            if(! trim($line))
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
                if(! array_key_exists($fShort, $syncList))
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
            if(! in_array(JPATH_ROOT.'/'.$item->path, $allCopies))
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
     * @static
     * @throws Exception
     */
    public static function deployFiles()
    {
        self::setupLog();

        JLog::add('|¯¯¯ Starting');

        $input = JFactory::getApplication()->input;

        $files = $input->get('files', array(), 'array');
        $deletedFiles = $input->get('deletedfiles', array(), 'array');

        switch($input->get('type'))
        {
            case 'ftp':
                self::connect('ftp');

                $knownDirs = array();

                foreach($files as $file)
                {
                    JLog::add('| >> '.sprintf(jgettext('Uploading %s ...'), $file));

                    $parts = explode('/', $file);

                    array_pop($parts);

                    $d = self::$credentials->directory;

                    foreach($parts as $part)
                    {
                        $d .= '/'.$part;

                        if(in_array($d, $knownDirs))
                            continue;

                        if(! self::$ftp->chdir($d))
                        {
                            if(self::$ftp->mkdir($d))
                            {
                                JLog::add('| ++ '.sprintf(jgettext('Created directory: %s'), $d));
                            }
                            else
                            {
                                throw new Exception(__METHOD__.' - Can not create FTP directory '.$d);
                            }
                        }

                        $knownDirs[] = $d;
                    }

                    if(! self::$ftp->store(JPATH_ROOT.'/'.$file, self::$credentials->directory.'/'.$file))
                        throw new Exception(JError::getError());
                }

                foreach($deletedFiles as $file)
                {
                    JLog::add('| -- '.sprintf(jgettext('Deleting %s ...'), $file));

                    if(! self::$ftp->delete(self::$credentials->directory.'/'.$file))
                        throw new Exception(JError::getError());
                }

                break;

            default:
                throw new Exception(__METHOD__.' - Unknown deploy type: '.$input->get('type'));
                break;
        }

        JLog::add('|___ Finished');

        //-- Give the logger a chance to finish.
        sleep(1);

        $syncList = self::readSyncList();

        foreach($files as $file)
        {
            if(! array_key_exists($file, $syncList))
            {
                $f = new stdClass;
                $f->path = $file;
                $f->size = filesize(JPATH_ROOT.'/'.$f->path);

                $syncList[$f->path] = $f;
            }
            else
            {
                $syncList[$file]->size = filesize(JPATH_ROOT.'/'.$file);
            }
        }

        foreach($deletedFiles as $file)
        {
            unset($syncList[$file]);
        }

        self::writeSyncList($syncList);
    }

    /**
     * @static
     * @return mixed
     * @throws Exception
     */
    public static function deletePackage()
    {
        self::setupLog();

        JLog::add('|¯¯¯ Starting');

        $input = JFactory::getApplication()->input;

        $type = $input->get('type');

        self::connect($type);

        switch($type)
        {
            case 'github':

                $id = $input->getInt('id');

                JLog::add('| -- '.sprintf('Deleting %s ...', $id));

                self::$github->downloads->delete($input->get('owner'), $input->get('repo'), $id);
                break;

            case 'ftp':
                $file = $input->get('file');

                JLog::add(sprintf(jgettext('| -- Deleting %s ...'), $file));

                if(! self::$ftp->delete(self::$credentials->downloads.'/'.$file))
                    throw new Exception(JError::getError());

                break;

            default:
                throw new Exception(__METHOD__.' - Unknown deploy type: '.$input->get('type'));
                break;
        }

        JLog::add('|___ Finished');

        //-- Give the logger a chance to finish.
        sleep(1);

        return;
    }

    /**
     * @static
     * @throws Exception
     * @return int
     */
    public static function deployPackage()
    {
        self::setupLog();

        JLog::add('|¯¯¯ Starting');

        $input = JFactory::getApplication()->input;

        $files = $input->get('file', array(), 'array');

        $type = $input->get('type');

        self::connect($type);

        switch($type)
        {
            case 'github':
                foreach($files as $file)
                {
                    JLog::add('| >> '.sprintf(jgettext('Uploading %s ...'), str_replace(JPATH_ROOT, 'JROOT', $file)));

                    self::$github->downloads->add($input->get('owner'), $input->get('repo'), $file);
                }

                break;

            case 'ftp':
                JLog::add(sprintf(jgettext('|    Upload directory: %s'), self::$credentials->downloads));

                foreach($files as $file)
                {
                    $fName = JFile::getName($file);

                    JLog::add('| >> '.sprintf(jgettext('Uploading %s ...'), $fName));

                    if(! self::$ftp->chdir(self::$credentials->downloads))
                        throw new Exception(jgettext('Download directory not found on server'));

                    if(! self::$ftp->store($file, self::$credentials->downloads.'/'.$fName))
                        throw new Exception(JError::getError());
                }

                break;

            default:
                throw new Exception(__METHOD__.' - Unknown deploy type: '.$input->get('type'));
                break;
        }

        JLog::add('|___ Finished');

        //-- Give the logger a chance to finish.
        sleep(1);

        return count($files);
    }

    /**
     * @param $dir
     *
     * @throws Exception
     * @return array
     */
    private static function scan($dir)
    {
        static $list = array();

        JLog::add(sprintf(jgettext('| ~~ Scanning %s ...'), $dir));

        $items = self::$ftp->listDetails($dir);

        if(false == $items)
            throw new Exception(JError::getError());

        foreach($items as $item)
        {
            if($item['type'] == 1)
            {
                //-- It's a folder
                self::scan($dir.'/'.$item['name']);
            }
            else
            {
                //-- It's a file
                $d = str_replace(self::$credentials->directory.'/', '', $dir);

                $list[$d.'/'.$item['name']] = JArrayHelper::toObject($item);
            }
        }

        return $list;
    }

    /**
     * @param $destination
     *
     * @throws Exception
     */
    private static function connect($destination)
    {
        $credentials = self::getCredentials($destination);

        switch($destination)
        {
            case 'github':
                $config = new JRegistry;

                $config->set('api.username', $credentials->user);
                $config->set('api.password', $credentials->pass);

                JLog::add('| ^^ '.sprintf(jgettext('Connecting to %s ...'), $destination));

                self::$github = new EcrGithub($config);

                break;

            case 'ftp':

                $options = null;

                JLog::add('| ^^ '.sprintf(jgettext('Connecting to %s ...'), $credentials->host));

                self::$ftp = EcrFtp::getClient($credentials->host, $credentials->port, $options
                    , $credentials->user, $credentials->pass);

                if(! self::$ftp->isConnected())
                    throw new Exception(jgettext('Unable to connect to FTP server'));

                break;

            default:
                throw new Exception(__METHOD__.' - Unknown deploy type: '.$destination);

                break;
        }

        self::$credentials = $credentials;
    }

    /**
     * @param $type
     *
     * @return \stdClass
     * @throws Exception
     */
    private static function getCredentials($type)
    {
        $credentials = new stdClass;

        /* @var JInput $input */
        $input = JFactory::getApplication()->input;

        switch($type)
        {
            case 'github':
                $credentials->user = $input->get('user');
                $credentials->pass = $input->get('pass');

                break;

            case 'ftp':

                $credentials->host = $input->get('ftpHost'); //'www5.subdomain.com';
                $credentials->port = $input->getInt('ftpPort');
                $credentials->directory = $input->getString('ftpDirectory'); //'/www';
                $credentials->downloads = $input->getString('ftpDownloads'); //'/www';

                $credentials->user = $input->get('ftpUser'); //'user2033242';
                $credentials->pass = $input->get('ftpPass'); //'kuku4711';

                break;

            default:
                throw new Exception(__METHOD__.' - Unknown deploy type: '.$type);

                break;
        }

        return $credentials;
    }

    /**
     * @static
     *
     */
    private static function setupLog()
    {
        $path = JFactory::getConfig()->get('log_path');

        $fileName = 'ecr_deploy.php';
        $entry = '----------------------------------------------';

        if('preserve' == JFactory::getApplication()->input->get('logMode')
            && JFile::exists($path.'/'.$fileName)
        )
        {
            $entry = '----------------------------------------------';
        }
        else
        {
            JFile::delete($path.'/'.$fileName);
        }

        JLog::addLogger(
            array(
                'text_file' => $fileName
            , 'text_entry_format' => '{DATETIME}	{PRIORITY}	{MESSAGE}'
            )
            , JLog::INFO | JLog::ERROR
        );

        if($entry)
            JLog::add($entry);
    }

}
