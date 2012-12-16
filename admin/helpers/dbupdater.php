<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 01-Jul-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * Database updater.
 *
 * @property array  $versions Available versions for the update.
 * @property-get              string $tmpPath  The temporary path.
 */
class EcrDbUpdater
{
    private $fileList = array();

    private $versions = array();

    /**
     * @var EcrProjectBase
     */
    private $project = null;

    private $adapter = null;

    private $logger = null;

    /**
     * Constructor.
     *
     * @param EcrProjectBase $project
     * @param string         $adapter
     */
    public function __construct(EcrProjectBase $project, $adapter = 'mysql')
    {
        if(class_exists('easyLogger'))
            $this->logger = EcrLogger::getInstance('ecr');

        if( ! $this->setAdapter($adapter))
            return false;

        $buildsPath = $project->getZipPath();

        $this->log('Looking for versions in '.$buildsPath);

        if(false == JFolder::exists($buildsPath))
            return;

        $folders = JFolder::folders($buildsPath);

        $this->log(sprintf('Found %d version(s) ', count($folders)));

        if( ! $folders)
            return;

        $this->versions = $folders;
        $this->project = $project;
    }

    /**
     * Log a message.
     *
     * @param string $message Message to log.
     *
     * @return mixed
     */
    private function log($message)
    {
        if( ! $this->logger)
            return;

        $this->logger->log($message);
    }

    /**
     * Log a file write including contents.
     *
     * @param string $path     Path of the written file.
     * @param string $contents File contents.
     *
     * @return mixed
     */
    private function logFile($path, $contents)
    {
        if( ! $this->logger)
            return;

        $this->logger->logFileWrite('dbUpdate', $path, $contents);
    }

    /**
     * Make some properties public accessible.
     *
     * @param string $what What do you want ?
     *
     * @return mixed depends on $what :P
     */
    public function __get($what)
    {
        if(in_array($what, array('versions', 'tmpPath')))
            return $this->$what;

        EcrHtml::message(get_class($this).' - Undefined property: '.$what, 'error');
    }

    /**
     *
     * Enter description here ...
     *
     * @return bool
     */
    public function buildFromECRBuildDir()
    {
        if( ! $this->project)
            return false;

        $dbType = 'mysql';

        $updater = new EcrProjectUpdater($this->project, $this->logger);

        if( ! $updater->hasUpdates)
        {
            $this->log('No updates found');

            return false;
        }

        foreach($this->versions as $version)
        {
            //-- Find a install.sql file

            $fileName = $this->findInstallFile($updater->tmpPath.'/'.$version.'/admin/install/sql');

            if( ! $fileName)
            {
                echo 'No install.sql file for '.$version;

                continue;
            }

            $this->fileList[$version] = $fileName;
        }

        if(false == array_key_exists($this->project->version, $this->fileList))
        {
            //-- Search for current install file
            $fileName = $this->findInstallFile(JPATH_ADMINISTRATOR.'/components/'
                .$this->project->comName.'/install/sql');

            if( ! $fileName)
            {
                echo 'No install.sql file for '.$this->project->version;
            }
            else
            {
                $this->fileList[$this->project->version] = $fileName;

                $this->log('Found install file at: '.$fileName);
            }
        }

        $files = $this->parseFiles();

        $this->log(sprintf('Created %d update files', count($files)));

        $path = JPATH_ADMINISTRATOR.'/components/'.$this->project->comName.'/install/sql/updates/'.$dbType;

        foreach($files as $file)
        {
            $fileName = $file->version.'.sql';

            if(false == JFile::write($path.'/'.$fileName, $file->query))
            {
                echo 'Can not write file to '.$path.'/'.$fileName;

                $this->log('Can not write file to '.$path.'/'.$fileName);

                return false;
            }

            $this->logFile($path.'/'.$fileName, $file->query);
        }

        return true;
    }

    /**
     * Parse the update files.
     *
     * @return array
     */
    public function parseFiles()
    {
        if( ! $this->fileList)
            return array();

        $creates = array();

        $db = JFactory::getDbo();

        foreach($this->fileList as $version => $path)
        {
            if(false == JFile::exists($path))
                continue;

            $this->log('Parsing file at: '.$path);

            $contents = JFile::read($path);

            $qs = $db->splitSql($contents);

            $this->log(sprintf('Found %d queries', count($qs)));

            $creates[$version] = array();

            $item = new stdClass;
            $item->version = $version;
            $item->tables = array();

            try
            {
                foreach($qs as $q)
                {
                    $q = trim($q);

                    if('' == $q)
                        continue;

                    $this->adapter->setQuery($q);

                    if('create' != $this->adapter->queryType
                    )
                        //-- Not a CREATE query
                        continue;

                    /*
                     $parsed = $this->adapter->parseCreate();

                     $t = new stdClass;
                     $t->name = $parsed['table_names'][0];
                     $t->fields = $parsed['column_defs'];
                     $t->raw = $q;

                     $item->tables[$t->name] = $t;
                     */

                    $parsed = $this->adapter->parseCreate();

                    $item->tables[$parsed->name] = $parsed;
                }

                $creates[$version] = $item;
            }
            catch(Exception $e)
            {
                EcrHtml::message($e);

                $this->log('Exception: '.$e->getMessage());
            }
        }

        $previous = null;

        $parsedQueries = array();

        foreach($creates as $item)
        {
            $statement = '';

            //-- @todo: bad coder :(
            if(false == is_object($item))
                continue;

            $qq = new stdClass;
            $qq->version = $item->version;
            $qq->query = '';

            if( ! $previous)
            {
                $previous = $item;

                $parsedQueries[] = $qq;

                continue;
            }

            foreach($item->tables as $table)
            {
                if(false == array_key_exists($table->name, $previous->tables))
                {
                    //-- New table
                    $this->log('Found a new table '.$table->name);

                    $statement .= $table->raw.NL;

                    continue;
                }

                //-- Computing alter table statements

                $alter = '';
                $alters = array();

                foreach($table->fields as $fName => $field)
                {
                    if(false == array_key_exists($fName, $previous->tables[$table->name]->fields))
                    {
                        //-- New column
                        $this->log(sprintf('Found a new column %s in table %s', $fName, $table->name));

                        $alters[] = $this->adapter->getStatement('addColumn', $fName, $field);

                        continue;
                    }

                    $pField = $previous->tables[$table->name]->fields[$fName];

                    if($pField->type != $field->type
                        || $pField->length != $field->length
                    )
                    {
                        //-- Different length
                        $alters[] = $this->adapter->getStatement('modifyColumn', $fName, $field);

                        $this->log(sprintf('Modified column %s in table %s (different type or length)'
                            , $fName, $table->name));
                    }
                    else
                    {
                        foreach($field->constraints as $c)
                        {
                            foreach($pField->constraints as $pC)
                            {
                                if( ! isset($pC['type']) || ! isset($c['type']))
                                    continue;

                                if($pC['type'] == $c['type'])
                                {
                                    //-- Different value
                                    if($pC['value'] != $c['value'])
                                    {
                                        $alters[] = $this->adapter->getStatement('modifyColumn', $fName, $field);

                                        $this->log(sprintf('Modified column %s in table %s - different type %s'
                                            , $fName, $table->name, $c['type']));
                                    }

                                    continue 2;
                                }
                            }
                        }
                    }
                }

                foreach($previous->tables[$table->name]->fields as $fName => $field)
                {
                    if(false == array_key_exists($fName, $table->fields))
                    {
                        $alters[] = $this->adapter->getStatement('dropColumn', $fName, $field);

                        $this->log(sprintf('Dropping column %s from table %s', $fName, $table->name));
                    }
                }

                $statement .= $this->adapter->getAlterTable($table, $alters);

                $this->log(sprintf('%d alter statements', count($alters)));
            }

            $qq->query = $statement;

            $parsedQueries[] = $qq;

            $previous = $item;
        }

        return $parsedQueries;
    }

    /**
     * Set a database adapter.
     *
     * @param string $adapter Adapter name.
     *
     * @return bool
     * @throws Exception
     */
    private function setAdapter($adapter)
    {
        //  if( ! ecrLoadHelper('dbadapter.'.$adapter))
        // throw new Exception(__METHOD__.': Invalid adapter '.$adapter);

        //-- @todo support case insensitive class names until PHP supports it =;)
        //-- ucfirst is only for the eye
        $className = 'EcrDbadapter'.ucfirst($adapter);

        if(false == class_exists($className))
            throw new Exception(__METHOD__.': Class name not found '.$className);

        $this->adapter = new $className;

        $this->log('Adapter loaded: '.$className);

        return true;
    }

    /**
     * @param $path Path to install files.
     *
     * @return bool|string
     */
    private function findInstallFile($path)
    {
        $files = JFolder::files($path);

        if( ! $files)
            return false;

        $fileName = '';

        foreach($files as $file)
        {
            if(0 == strpos($file, 'install'))
            {
                //-- Pick only the first one @todo
                $fileName = $path.'/'.$file;
                break;
            }
        }

        return $fileName;
    }
}//class
