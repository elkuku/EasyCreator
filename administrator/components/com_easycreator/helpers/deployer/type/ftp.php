<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 25-Apr-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * EasyCreator FTP deployer.
 */
class EcrDeployerTypeFtp extends EcrDeployer
{
    /**
     * @return array|mixed|object
     */
    public function getPackageList()
    {
        JLog::add('| << '.jgettext('Obtaining download list ...'));

        $downloads = $this->ftp->listDetails($this->credentials->downloads, 'files');

        if(count($downloads))
        {
            $downloads = JArrayHelper::toObject($downloads);

            if( ! $downloads)
                return array();

            foreach($downloads as &$download)
            {
                $download->html_url = '';
                $download->fileName = $download->name;
            }
        }

        return $downloads;
    }

    /**
     * @static
     *
     * @throws Exception
     * @return int|mixed
     */
    public function deployPackage()
    {
        $input = JFactory::getApplication()->input;

        $files = $input->get('file', array(), 'array');

        JLog::add('|    '.sprintf(jgettext('Upload directory: %s'), $this->credentials->downloads));

        foreach($files as $file)
        {
            $fName = JFile::getName($file);

            JLog::add('| >> '.sprintf(jgettext('Uploading %s ...'), $fName));

            if( ! $this->ftp->chdir($this->credentials->downloads))
                throw new Exception(jgettext('Download directory not found on server'));

            if( ! $this->ftp->store($file, $this->credentials->downloads.'/'.$fName))
                throw new Exception(JError::getError());
        }

        return count($files);
    }

    /**
     * @static
     * @throws Exception
     * @return mixed|void
     */
    public function deployFiles()
    {
        $input = JFactory::getApplication()->input;

        $files = $input->get('files', array(), 'array');
        $deletedFiles = $input->get('deletedfiles', array(), 'array');

        $knownDirs = array();

        foreach($files as $file)
        {
            JLog::add('| >> '.sprintf(jgettext('Uploading %s ...'), $file));

            $parts = explode('/', $file);

            array_pop($parts);

            $d = $this->credentials->directory;

            foreach($parts as $part)
            {
                $d .= '/'.$part;

                if(in_array($d, $knownDirs))
                    continue;

                if( ! $this->ftp->chdir($d))
                {
                    if($this->ftp->mkdir($d))
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

            if( ! $this->ftp->store(JPATH_ROOT.'/'.$file, $this->credentials->directory.'/'.$file))
                throw new Exception(JError::getError());
        }

        foreach($deletedFiles as $file)
        {
            JLog::add('| -- '.sprintf(jgettext('Deleting %s ...'), $file));

            if( ! $this->ftp->delete($this->credentials->directory.'/'.$file))
                throw new Exception(JError::getError());
        }

        $syncList = $this->readSyncList();

        foreach($files as $file)
        {
            if( ! array_key_exists($file, $syncList))
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
    public function deletePackage()
    {
        $input = JFactory::getApplication()->input;

        $file = $input->get('file');

        JLog::add('| -- '.sprintf(jgettext('Deleting %s ...'), $file));

        if( ! $this->ftp->delete($this->credentials->downloads.'/'.$file))
            throw new Exception(JError::getError());

        return;
    }

    /**
     * @static
     *
     * @throws Exception
     * @return array
     */
    public function syncFiles()
    {
        $project = EcrProjectHelper::getProject();

        $syncList = array();

        //-- Get the sync list
        foreach($project->copies as $folder)
        {
            $path = str_replace(JPATH_ROOT, $this->credentials->directory, $folder);

            $syncList += $this->scan($path);
        }

        $this->writeSyncList($syncList);

        $fileList = $this->getSyncList();

        return $fileList;
    }

    /**
     * @param $dir
     *
     * @throws Exception
     * @return array
     */
    private function scan($dir)
    {
        static $list = array();

        JLog::add('| ~~ '.sprintf(jgettext('Scanning %s ...'), $dir));

        $items = $this->ftp->listDetails($dir);

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
                $d = str_replace($this->credentials->directory.'/', '', $dir);

                $list[$d.'/'.$item['name']] = JArrayHelper::toObject($item);
            }
        }

        return $list;
    }

    /**
     * @throws Exception
     * @return mixed|void
     */
    protected function connect()
    {
        $credentials = new stdClass;

        /* @var JInput $input */
        $input = JFactory::getApplication()->input;

        $credentials->host = $input->get('ftpHost');
        $credentials->port = $input->getInt('ftpPort');
        $credentials->directory = $input->getString('ftpDirectory');
        $credentials->downloads = $input->getString('ftpDownloads');

        $credentials->user = $input->get('ftpUser');
        $credentials->pass = $input->get('ftpPass');

        $options = null;

        JLog::add('| ^^ '.sprintf(jgettext('Connecting to %s ...'), $credentials->host));

        $this->ftp = EcrFtp::getClient($credentials->host, $credentials->port, $options
            , $credentials->user, $credentials->pass);

        if( ! $this->ftp->isConnected())
            throw new Exception(jgettext('Unable to connect to FTP server'));

        $this->credentials = $credentials;
    }
}
