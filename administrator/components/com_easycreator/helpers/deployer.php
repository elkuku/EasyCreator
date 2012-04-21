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

    private static $credentials = null;

    /**
     * @static
     * @return array|mixed
     * @throws Exception
     */
    public static function getDownloads()
    {
        $input = JFactory::getApplication()->input;

        self::setupLog();

        JLog::add('Starting');

        switch($input->get('type'))
        {
            case 'ftp':
                self::connect('ftp');

                JLog::add('Obtaining downloads ...');

                $downloads = self::$ftp->listDetails(self::$credentials->directory, 'files');

                break;

            case 'github':
                $input = JFactory::getApplication()->input;

                $config = new JRegistry;

                JLog::add('Connecting to GitHub ...');

                $github = new EcrGithub($config);

                JLog::add('Connected');

                JLog::add('Obtaining downloads ...');

                $downloads = $github->downloads->getList($input->get('owner'), $input->get('repo'));

                break;

            default:
                throw new Exception(__METHOD__.' - Unknown deploy type: '.$input->get('type'));
                break;
        }

        JLog::add('Finished');

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

        JLog::add('Starting');

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

                JLog::add('Finished');

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
            throw new Exception(jgettext('No synclist found - Please synchronize'));

        foreach($project->copies as $copy)
        {
            $files = JFolder::files($copy, '.', true, true);

            foreach($files as $file)
            {
                $fShort = str_replace(JPATH_ROOT.'/', '', $file);

                //-- File does not exist
                if(! array_key_exists($fShort, $syncList))
                {
                    $f = new stdClass;
                    $f->path = $fShort;
                    $f->exists = false;

                    $fileList[$fShort] = $f;
                }
                else
                {
                    $f = $syncList[$fShort];

                    //-- File size is different
                    if($f->size != filesize($file))
                    {
                        $f->exists = true;

                        $fileList[$fShort] = $f;
                    }
                }
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

        JLog::add('Starting');

        $input = JFactory::getApplication()->input;

        $files = $input->get('file', array(), 'array');

        switch($input->get('type'))
        {
            case 'ftp':
                self::connect('ftp');

                $knownDirs = array();

                foreach($files as $file)
                {
                    JLog::add(sprintf('Uploading %s ...', $file));

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
                                JLog::add('Created directory: '.$d);
                            }
                            else
                            {
                                throw new Exception(__METHOD__.' - Can not create FTP directory '.$d);
                            }
                        }

                        $knownDirs[] = $d;
                    }

                    //                  self::$ftp->chdir(self::$credentials->directory);

                    if(! self::$ftp->store(JPATH_ROOT.'/'.$file, self::$credentials->directory.'/'.$file))
                        throw new Exception(JError::getError());
                }

                break;

            default:
                throw new Exception(__METHOD__.' - Unknown deploy type: '.$input->get('type'));
                break;
        }

        JLog::add('Finished');

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

        self::writeSyncList($syncList);
    }

    /**
     * @static
     * @throws Exception
     */
    public static function deployPackage()
    {
        self::setupLog();

        JLog::add('Starting');

        $input = JFactory::getApplication()->input;

        $files = $input->get('file', array(), 'array');

        switch($input->get('type'))
        {
            case 'github':
                $config = new JRegistry;

                $config->set('api.username', $input->get('user'));
                $config->set('api.password', $input->get('pass'));

                $github = new EcrGithub($config);

                foreach($files as $file)
                {
                    JLog::add(sprintf('Uploading %s ...', str_replace(JPATH_ROOT, 'JROOT', $file)));

                    $github->downloads->add($input->get('owner'), $input->get('repo'), $file);
                }

                break;

            case 'ftp':
                self::connect('ftp');

                JLog::add(sprintf('Upload directory: %s', self::$credentials->directory));

                foreach($files as $file)
                {
                    JLog::add(sprintf('Uploading %s ...', str_replace(JPATH_ROOT, 'JROOT', $file)));

                    self::$ftp->store($file, self::$credentials->directory.'/'.JFile::getName($file));
                }

                break;

            default:
                throw new Exception(__METHOD__.' - Unknown deploy type: '.$input->get('type'));
                break;
        }

        JLog::add('Finished');

        //-- Give the logger a chance to finish.
        sleep(1);
    }

    /**
     * @param $dir
     *
     * @return array
     */
    private static function scan($dir)
    {
        static $list = array();

        JLog::add(sprintf('Scanning %s ...', $dir));

        $items = self::$ftp->listDetails($dir);

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
        switch($destination)
        {
            case 'ftp':
                $credentials = self::getCredentials('ftp');

                $options = null;

                JLog::add(sprintf('Connecting to %s ...', $credentials->host));

                self::$ftp = EcrFtp::getClient($credentials->host, $credentials->port, $options
                    , $credentials->user, $credentials->pass);

                JLog::add('Connected');

                self::$credentials = $credentials;
                break;

            default:
                throw new Exception(__METHOD__.' - Unknown deploy type: '.$destination);
                break;
        }
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
        $input = JFactory::getApplication()->input;

        switch($type)
        {
            case 'github':
                $credentials->set = $input->get('user');
                $credentials->password = $input->get('pass');

                break;

            case 'ftp':

                $credentials->host = 'www5.subdomain.com';
                $credentials->port = $input->getInt('port');
                $credentials->directory = '/www';
                $credentials->user = 'user2033242';
                $credentials->pass = 'kuku4711';

                break;

            default:
                throw new Exception(__METHOD__.' - Unknown deploy type: '.$type);

                break;
        }

        return $credentials;
    }

    private static function setupLog()
    {
        $path = JFactory::getConfig()->get('log_path');

        $fileName = 'ecr_deploy.php';

        if(JFile::exists($path.'/'.$fileName))
            JFile::delete($path.'/'.$fileName);

        JLog::addLogger(
            array(
                'text_file' => $fileName
            )
            , JLog::INFO | JLog::ERROR
        );
    }

}
