<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 01-Jul-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//--No direct access
defined('_JEXEC') || die('=;)');

/**
 * Database updater.
 *
 */
class dbUpdater
{
    private $fileList = array();

    private $versions = array();

    private $project = null;

    private $adapter = null;

    private $nameQuote = '`';//@todo db specific quotes

    /**
     *
     * Enter description here ...
     * @param EasyProject $project
     * @param string $adapter
     */
    public function __construct(EasyProject $project, $adapter = 'mysql')
    {
        if( ! $this->setAdapter($adapter))
        return false;

        $buildsPath = $project->getZipPath();

        if( ! JFolder::exists($buildsPath))
        return;

        $folders = JFolder::folders($buildsPath);

        if( ! $folders)
        return;

        $this->versions = $folders;
        $this->project = $project;
    }//function

    private function setAdapter($adapter)
    {
        if( ! ecrLoadHelper('dbadapters.'.$adapter))
        {
            ecrHTML::displayMessage(__METHOD__.': Invalid adapter '.$adapter);

            return false;
        }

        //-- @todo support case insensitive class names until PHP supports it =;)
        $className = 'dbAdapter'.ucfirst($adapter);

        if( ! class_exists($className))
        {
//            ecrHTML::displayMessage(__METHOD__.': Class name not found '.$className);

            throw new Exception('Class name not found '.$className);

//            return false;
        }

        $this->adapter = new $className;

        return true;
    }//function

    /**
     * Make some properties public accessible.
     *
     * @param string $what
     *
     * @return mixed
     */
    public function __get($what)
    {
        if(in_array($what, array('versions', 'tmpPath')))
        return $this->$what;

        ecrHTML::displayMessage(get_class($this).' - Undefined property: '.$what, 'error');
    }//function

    /**
     *
     * Enter description here ...
     */
    public function buildFromECRBuildDir()
    {
        if( ! $this->project)
        return;

        ecrLoadHelper('updater');

        $updater = new extensionUpdater($this->project);

        if( ! $updater->hasUpdates)
        return;

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
        }//foreach

        var_dump($this->fileList);

        if( ! array_key_exists($this->project->version, $this->fileList))
        {
            //-- Search for current install file
            $fileName = $this->findInstallFile(JPATH_ADMINISTRATOR.'/components/'
            .$this->project->comName.'/install/sql');

            if( ! $fileName)
            {
                echo 'No install.sql file for '.$this->project->version;

                continue;
            }

            $this->fileList[$this->project->version] = $fileName;
        }

        $files = $this->parseFiles();

        var_dump($files);

        $path = JPATH_ADMINISTRATOR.'/components/'.$this->project->comName.'/install/sql/updates/mysql';

        foreach($files as $file)
        {
            $fileName = $file->version.'.sql';

            if( ! JFile::write($path.'/'.$fileName, $file->query))
            {
                echo 'Can not write file to '.$path.'/'.$fileName;

                return false;
            }
        }//foreach
    }//function

    private function findInstallFile($path)
    {
        $files = JFolder::files($path);

        $fileName = '';

        foreach($files as $file)
        {
            if(0 == strpos($file, 'install'))
            {
                $fileName = $path.'/'.$file;
                break;
            }
        }//foreach

        return $fileName;
    }//function

    public function parseFiles()
    {

        if( ! $this->fileList)
        return array();

        $creates = array();

        $db = JFactory::getDbo();

        foreach($this->fileList as $version => $path)
        {
            if( ! JFile::exists($path))
            continue;

            $contents = JFile::read($path);

            $qs = $db->splitSql($contents);

            $creates[$version] = array();

            $item = new stdClass;
            $item->version = $version;
            $item->tables = array();

            try
            {
                foreach($qs as $q)
                {
                    $q = trim($q);

                    if( ! $q)
                    continue;

                    $this->adapter->setQuery($q);

                    if('create' == $this->adapter->queryType)
                    {
                        $query = substr($q, 7);
                        $parsed = $this->adapter->parseCreate();
                    }
                    else
                    {
                        //-- Not a CREATE query
                        continue;
                    }

                    if(0 == strpos($q, 'CREATE'))//@todo check for a CREATE in adapter
                    {
                        $query = substr($q, 7);
                    }
                    else
                    {
                        //-- Not a CREATE query
                        continue;
                    }

                    //-- Invoke the PEAR parser
                    //@todo parser by adapter ?

                    $t = new stdClass;
                    $t->name = $parsed['table_names'][0];
                    $t->fields = $parsed['column_defs'];
                    $t->raw = $q;

                    $item->tables[$t->name] = $t;
                }//foreach

                $creates[$version] = $item;
            }
            catch(Exception $e)
            {
                JError::raiseWarning(0, $e->getMessage());
            }//try
        }//foreach

        $previous = null;

        $parsedQueries = array();

        foreach($creates as $item)
        {
            $statement = '';

            if( ! is_object($item))
            continue;//@todo: bad coder :(

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
                if( ! array_key_exists($table->name, $previous->tables))
                {
                    //-- New table

                    $statement .= $table->raw.NL;

                    continue;
                }

                //-- Computing alter table statements

                $alter = '';
                $alters = array();

                foreach($table->fields as $fName => $field)
                {
                    if( ! array_key_exists($fName, $previous->tables[$table->name]->fields))
                    {
                        //-- New column
                        $alters[] = 'ADD '.$this->quote($fName).$this->parseField($field).NL;

                        continue;
                    }

                    $pField = $previous->tables[$table->name]->fields[$fName];

                    if($pField['type'] != $field['type']
                    || $pField['length'] != $field['length'])
                    {
                        $alters[] = 'MODIFY '.$this->quote($fName).$this->parseField($field).NL;
                    }
                    else
                    {
                        foreach($field['constraints'] as $c)
                        {
                            foreach($pField['constraints'] as $pC)
                            {
                                if( ! isset($pC['type']) || ! isset($c['type']))
                                continue;

                                if($pC['type'] == $c['type'])
                                {
                                    if($pC['value'] != $c['value'])
                                    {
                                        $alters[] = 'MODIFY '.$this->quote($fName).$this->parseField($field).NL;
                                    }

                                    continue 2;
                                }
                            }//foreach
                        }//foreach
                    }
                }//foreach

                foreach($previous->tables[$table->name]->fields as $fName => $field)
                {
                    if( ! array_key_exists($fName, $table->fields))
                    {
                        $alters[] = 'DROP COLUMN '.$this->quote($fName).NL;
                    }
                }//foreach

                $alter =($alters) ? implode(', ', $alters) : '';

                $statement .=($alter) ? 'ALTER TABLE '.$this->quote($table->name).NL.$alter.NL : '';
            }//foreach

            $qq->query = $statement;

            $parsedQueries[] = $qq;

            $previous = $item;
        }//foreach

        return $parsedQueries;
    }//function

    private function parseField($field)
    {
        $parsed = ' ';

        $parsed .= strtoupper($field['type']);
        $parsed .= '('.$field['length'].')';

        foreach($field['constraints'] as $c)
        {
            if( ! isset($c['type']))
            continue;

            //var_dump($c);
            switch($c['type'])
            {
                case 'not_null' :
                    $parsed .= ' NOT NULL';
                    break;

                case 'comment' :
                    $parsed .= " COMMENT '".$c['value']."'";
                    break;

                default:
                    ecrHTML::displayMessage('Unknown field type '.$c['type'], 'error');
                    break;
            }//switch
        }//foreach

        return $parsed;
    }//function

    private function quote($string)
    {
        return $this->nameQuote.$string.$this->nameQuote;
    }//function
}//class
