<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 10-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Project definitions.
 */
abstract class EcrProject extends JObject
{
    public $legacy = false;//removed in 1.6

    public $method = '';

    public $JCompat = '1.5';

    public $phpVersion = '4';

    public $fromTpl = '';

    public $dbId = 0;

    public $name = '';

    public $comName = '';

    public $scope = '';

    public $group = '';

    public $version = '1.0';

    public $description = '';

    public $author = '';

    public $authorEmail = '';

    public $authorUrl = '';

    public $license = '';

    public $copyright = '';

    public $creationDate = '';

    public $menu = array('text' => '', 'link' => '', 'img' => '', 'menuid' => '');

    public $submenu = array();

    public $langs = array();

    public $langSeparateJavascript = false;

    public $modules = array();

    public $plugins = array();

    public $tables = array();

    public $autoCodes = array();

    public $listPostfix = 'List';

    public $extensionPrefix = '';

    public $entryFile = '';

    public $copies = array();

    public $buildPath = false;

    public $zipPath = '';

    public $buildOpts = array();

    public $dbTypes = array();

    public $type;

    private $_substitutes = array();

    /** Package elements */
    public $elements = array();

    public $updateServers = array();

    private $basePath = '';

    /** Special : g11n Language handling */
    public $langFormat = 'ini';

    /** Flag to identify a *somewhat* invalid project */
    public $isValid = true;

    /**
     * @var string
     */
    public $headerType;

    /**
     * Constructor.
     *
     * @param string $name Project name.
     */
    public function __construct($name = '')
    {
        if( ! $name
        || ! $this->readProjectXml($name))
        {
            return;
        }

        $this->findCopies();
        $this->langs = EcrLanguageHelper::discoverLanguages($this);

        if($this->type == 'component')
        $this->readMenu();

        $this->readJoomlaXml();
        $this->dbId = $this->getId();
    }//function

    // @codingStandardsIgnoreStart

    public abstract function findCopies();

    public function getLanguageScopes() {}
    public function getLanguagePaths() {}
    public function getLanguageFileName() {}
    public function getJoomlaManifestPath() {}
    public function getJoomlaManifestName() {}
    public function getDTD($jVersion) {}
    public function getEcrXmlFileName() {}
    public function getId() {}
    abstract public function getExtensionPath();
    abstract public function getAllProjects($scope);
    abstract public function getCoreProjects($scope);
    protected function readMenu(){}


    protected function updateAdminMenu() { return true; }

    // @codingStandardsIgnoreEnd

    /**
     * Read the Joomla! XML setup file.
     *
     * @return boolean
     */
    private function readJoomlaXml()
    {
        $fileName = EcrProjectHelper::findManifest($this);

        if( ! $fileName)
        {
            $this->isValid = false;

            return false;
        }

        $data = EcrProjectHelper::parseXMLInstallFile(JPATH_ROOT.DS.$fileName);

        if( ! $data)
        return false;

        $this->method = (string)$data->attributes()->method;

        foreach($data as $key => $value)
        {
            $this->$key = (string)$value;
        }//foreach

        return true;
    }//function

    /**
     * Update project settings.
     *
     * @param boolean $testMode Set true for testing
     *
     * @return boolean
     */
    public function update($testMode = false)
    {
        return $this->writeProjectXml($testMode);
    }//function

    /**
     * This will update the config file.
     *
     * @return boolean true on success
     */
    public function updateProjectFromRequest()
    {
        $buildVars = JRequest::getVar('buildvars', array());
        $buildOpts = JRequest::getVar('buildopts', array());
        $this->dbTypes = JRequest::getVar('dbtypes', array());
        $this->headerType = JRequest::getCmd('headerType');

        //--Package modules
        $this->modules = array();
        $items = JRequest::getVar('package_module', array(), 'post');

        foreach($items as $item)
        {
            $m = new stdClass;
            $m->scope = $item['client'];
            $m->name = $item['name'];
            $m->title = $item['title'];
            $m->position = $item['position'];
            $m->ordering = $item['ordering'];

            $this->modules[] = $m;
        }//foreach

        //--Package plugins
        $this->plugins = array();
        $items = JRequest::getVar('package_plugin', array(), 'post');

        foreach($items as $item)
        {
            $m = new stdClass;
            $m->name = $item['name'];
            $m->title = $item['title'];
            $m->scope = $item['client'];
            $m->ordering = $item['ordering'];

            $this->plugins[] = $m;
        }//foreach

        $packageElements = (string)JRequest::getVar('package_elements');
        $packageElements =($packageElements) ? explode(',', $packageElements) : array();

        if(count($packageElements))
        {
            $this->elements = array();

            foreach($packageElements as $element)
            {
                $this->elements[$element] = $element;
            }//foreach
        }

        //-- Process credit vars
        foreach($buildVars as $name => $var)
        {
            if(property_exists($this, $name))
            {
                $this->$name = $var;
            }
        }//foreach

        //-- Method special treatment for checkboxes
        $this->method =(isset($buildVars['method'])) ? $buildVars['method'] : '';
        $this->buildOpts['lng_separate_javascript'] =(in_array('lng_separate_javascript', $buildOpts)) ? 'ON' : 'OFF';

        //-- Build options
        $this->buildOpts['archive_zip'] =(in_array('archive_zip', $buildOpts)) ? 'ON' : 'OFF';
        $this->buildOpts['archive_tgz'] =(in_array('archive_tgz', $buildOpts)) ? 'ON' : 'OFF';
        $this->buildOpts['archive_bz2'] =(in_array('archive_bz2', $buildOpts)) ? 'ON' : 'OFF';
        $this->buildOpts['create_indexhtml'] =(in_array('create_indexhtml', $buildOpts)) ? 'ON' : 'OFF';
        $this->buildOpts['create_md5'] =(in_array('create_md5', $buildOpts)) ? 'ON' : 'OFF';
        $this->buildOpts['create_md5_compressed'] =(in_array('create_md5_compressed', $buildOpts)) ? 'ON' : 'OFF';
        $this->buildOpts['include_ecr_projectfile'] =(in_array('include_ecr_projectfile', $buildOpts)) ? 'ON' : 'OFF';
        $this->buildOpts['remove_autocode'] =(in_array('remove_autocode', $buildOpts)) ? 'ON' : 'OFF';

        $this->updateServers = array();

        $updateServers = JRequest::getVar('updateServers', array());

        if($updateServers)
        {
            foreach($updateServers['name'] as $i => $value)
            {
                $u = new stdClass;
                $u->name = $value;
                $u->priority = $updateServers['priority'][$i];
                $u->type = $updateServers['type'][$i];
                $u->url = $updateServers['url'][$i];
                $this->updateServers[$i] = $u;
            }//foreach
        }

        $this->JCompat = JRequest::getString('jcompat');

        if( ! $this->writeProjectXml())
        {
            JFactory::getApplication()->enqueueMessage(jgettext('Can not update EasyCreator manifest'), 'error');

            return false;
        }

        if( ! $this->writeJoomlaManifest())
        {
            JFactory::getApplication()->enqueueMessage(jgettext('Can not update Joomla! manifest'), 'error');

            return false;
        }

        if( ! $this->updateAdminMenu())
        {
            JFactory::getApplication()->enqueueMessage(jgettext('Can not update Admin menu'), 'error');

            return false;
        }

        return true;
    }//function

    /**
     * Write the Joomla! manifest file.
     *
     * @return boolean
     */
    private function writeJoomlaManifest()
    {
        $installXML = EcrProjectHelper::findManifest($this);

        $xmlBuildVars = array(
        'version'
        , 'description'
        , 'author'
        , 'authorEmail'
        , 'authorUrl'
        , 'license'
        , 'copyright'
        );

        $manifest = EcrProjectHelper::getXML(JPATH_ROOT.DS.$installXML);

        if( ! $manifest)
        {
            JFactory::getApplication()->enqueueMessage(
                sprintf(jgettext('Can not load xml file %s'), $installXML), 'error');

            return false;
        }

        if($this->method)
        {
            if($manifest->attributes()->method)
            {
                $manifest->attributes()->method = $this->method;
            }
            else
            {
                $manifest->addAttribute('method', $this->method);
            }
        }
        else
        {
            //-- Set the method to empty
            if($manifest->attributes()->method)
            {
                $manifest->attributes()->method = '';
            }
        }

        //--Process credit vars
        foreach($xmlBuildVars as $xmlName)
        {
            $manifest->$xmlName = $this->$xmlName;
        }//foreach

        $dtd = $this->getDTD($this->JCompat);

        $root = '';
        $root .= '<?xml version="1.0" encoding="UTF-8"?>'.NL;

        if($dtd)
        {
            $root .= '<!DOCTYPE '.$dtd['type'].' PUBLIC "'.$dtd['public'].'"'.NL.'"'.$dtd['uri'].'">';
        }

        $output = $root.$manifest->asFormattedXML();

        //--Write XML file to disc
        if( ! JFile::write(JPATH_ROOT.DS.$installXML, $output))
        {
            JFactory::getApplication()->enqueueMessage(
                jgettext('Unable to write file'), 'error');
            JFactory::getApplication()->enqueueMessage(JPATH_ROOT.DS.$installXML, 'error');

            return false;
        }

        if(ECR_DEBUG)
        {
            $screenOut = $output;
            $screenOut = str_replace('<', '&lt;', $screenOut);
            $screenOut = str_replace('>', '&gt;', $screenOut);
            echo '<div class="ecr_debug">';
            echo '<pre>'.$screenOut.'</pre>';
            echo '</div>';
        }

        return true;
    }//function

    /**
     * Updates the EasyCreator configuration file for the project.
     *
     * @param boolean $testMode If set to 'true' xml file will be generated but not written to disk
     *
     * @return mixed [string xml string on success | boolean false on error]
     */
    public function writeProjectXml($testMode = false)
    {
        $xml = EcrProjectHelper::getXML('<easyproject'
            .' type="'.$this->type.'"'
            .' scope="'.$this->scope.'"'
            .' version="'.ECR_VERSION.'"'
            .' tpl="'.$this->fromTpl
            .'" />'
            , false);

        $xml->addChild('name', $this->name);
        $xml->addChild('comname', $this->comName);
        $xml->addChild('JCompat', $this->JCompat);
        $xml->addChild('extensionPrefix', $this->extensionPrefix);
        $xml->addChild('langFormat', $this->langFormat);
        $xml->addChild('zipPath', $this->zipPath);

        //-- Database types
        $xml->addChild('dbTypes', implode(',', $this->dbTypes));

        $xml->addChild('headerType', $this->headerType);

        //-- Package Modules
        if(count($this->modules))
        {
            $modsElement = $xml->addChild('modules');

            foreach($this->modules as $module)
            {
                $modElement = $modsElement->addChild('module');
                $modElement->addAttribute('name', $module->name);
                $modElement->addAttribute('title', $module->title);

                if($module->scope)
                {
                    $modElement->addAttribute('scope', $module->scope);
                }

                if($module->position)
                {
                    $modElement->addAttribute('position', $module->position);
                }

                if($module->ordering)
                {
                    $modElement->addAttribute('ordering', $module->ordering);
                }
            }//foreach
        }

        //-- Package Plugins
        if(count($this->plugins))
        {
            $modsElement = $xml->addChild('plugins');

            foreach($this->plugins as $plugin)
            {
                $modElement = $modsElement->addChild('plugin');
                $modElement->addAttribute('name', $plugin->name);
                $modElement->addAttribute('title', $plugin->title);

                if($plugin->scope)
                {
                    $modElement->addAttribute('scope', $plugin->scope);
                }

                if($plugin->ordering)
                {
                    $modElement->addAttribute('ordering', $plugin->ordering);
                }
            }//foreach
        }

        //-- Tables
        if(count($this->tables))
        {
            $tablesElement = $xml->addChild('tables');

            foreach($this->tables as $table)
            {
                $tableElement = $tablesElement->addChild('table');

                $tableElement->addChild('name', $table->name);
                $tableElement->addChild('foreign', $table->foreign);

                if($table->getRelations())
                {
                    $relsElement = $tableElement->addChild('relations');

                    foreach($table->getRelations() as $relation)
                    {
                        $relElement = $relsElement->addChild('relation');

                        $relElement->addChild('type', $relation->type);
                        $relElement->addChild('field', $relation->field);
                        $relElement->addChild('onTable', $relation->onTable);
                        $relElement->addChild('onField', $relation->onField);

                        $aliasesElement = $relElement->addChild('aliases');

                        foreach($relation->aliases as $alias)
                        {
                            $aliasElement = $aliasesElement->addChild('alias');

                            $aliasElement->addChild('name', $alias->alias);
                            $aliasElement->addChild('field', $alias->aliasField);
                        }//foreach
                    }//foreach
                }
            }//foreach
        }

        //-- AutoCodes
        if(count($this->autoCodes))
        {
            $autoCodesElement = $xml->addChild('autoCodes');

            foreach($this->autoCodes as $autoCode)
            {
                $autoCodeElement = $autoCodesElement->addChild('autoCode');
                $autoCodeElement->addAttribute('scope', $autoCode->scope);
                $autoCodeElement->addAttribute('group', $autoCode->group);
                $autoCodeElement->addAttribute('name', $autoCode->name);
                $autoCodeElement->addAttribute('element', $autoCode->element);

                if(count($autoCode->options))
                {
                    $optionsElement = $autoCodeElement->addChild('options');

                    foreach($autoCode->options as $key => $option)
                    {
                        $option = (string)$option;
                        $optionElement = $optionsElement->addChild('option', $option);
                        $optionElement->addAttribute('name', $key);
                    }//foreach
                }

                if(count($autoCode->fields))
                {
                    foreach($autoCode->fields as $key => $fields)
                    {
                        $fieldsElement = $autoCodeElement->addChild('fields');
                        $fieldsElement->addAttribute('key', $key);

                        foreach($fields as $field)
                        {
                            $fieldElement = $fieldsElement->addChild('field');

                            $oVars = get_object_vars($field);

                            $k = $oVars['name'];

                            $fieldElement->addAttribute('name', $k);

                            foreach($oVars as $oKey => $oValue)
                            {
                                if( ! $oValue)
                                continue;

                                $fieldElement->addChild($oKey, $oValue);
                            }//foreach
                        }//foreach
                    }//foreach
                }
            }//foreach
        }

        if($this->type == 'package')
        {
            //-- J! 1.6 Package
            $filesElement = $xml->addChild('elements');

            foreach($this->elements as $element)
            {
                $filesElement->addChild('element', $element);
            }//foreach
        }

        //-- Buildopts
        if(count($this->buildOpts))
        {
            $buildElement = $xml->addChild('buildoptions');

            foreach($this->buildOpts as $k => $opt)
            {
                $buildElement->addChild($k, $opt);
            }//foreach
        }

        //-- Update servers
        if(count($this->updateServers))
        {
            $element = $xml->addChild('updateservers');

            foreach($this->updateServers as $server)
            {
                $sElement = $element->addChild('server', $server->url);

                $sElement->addAttribute('name', $server->name);
                $sElement->addAttribute('type', $server->type);
                $sElement->addAttribute('priority', $server->priority);
            }//foreach
        }

        $root = '';
        $root .= '<?xml version="1.0" encoding="UTF-8"?>'.NL;
        $root .= '<!DOCTYPE easyproject PUBLIC "-//EasyCreator 0.0.14.1//DTD project 1.0//EN"'.NL;
        //        $root .= '"http://xml.der-beta-server.de/dtd/easycreator/0.0.14.1/project.dtd">';
        $root .= '"http://joomlacode.org/gf/project/elkuku/scmsvn/?action=browse'
        .'&path=/*checkout*/dtd/easycreator/0.0.14.1/project.dtd">';

        $output = $root.$xml->asFormattedXML();

        if(ECR_DEBUG)
        echo '<pre>'.htmlentities($output).'</pre>';

        $path = ECRPATH_SCRIPTS.DS.$this->getEcrXmlFileName();

        if( ! $testMode)
        {
            if( ! JFile::write(JPath::clean($path), $output))
            {
                $this->setError('Could not save XML file!');

                return false;
            }
        }

        return $output;
    }//function

    /**
     * Read the project XML file.
     *
     * @param string $projectName Projects name
     *
     * @return boolean
     */
    private function readProjectXml($projectName)
    {
        $fileName = ECRPATH_SCRIPTS.DS.$projectName.'.xml';

        if( ! JFile::exists($fileName))
        {
            JFactory::getApplication()->enqueueMessage(jgettext('Project manifest not found'), 'error');

            return false;
        }

        $manifest = EcrProjectHelper::getXML($fileName);

        if( ! $manifest instanceof SimpleXMLElement
        || $manifest->getName() != 'easyproject')
        {
            JFactory::getApplication()->enqueueMessage(jgettext('Invalid project manifest'), 'error');

            return false;
        }

        $this->type = (string)$manifest->attributes()->type;
        $this->scope = (string)$manifest->attributes()->scope;
        $this->name = (string)$manifest->name;
        $this->comName = (string)$manifest->comname;
        $this->JCompat = ((string)$manifest->JCompat) ? (string)$manifest->JCompat : '1.5';
        $this->langFormat = (string)$manifest->langFormat;
        $this->zipPath = (string)$manifest->zipPath;
        $this->headerType = (string)$manifest->headerType;

        $dbTypes = (string)$manifest->dbTypes;

        if('' != $dbTypes)
        {
            $this->dbTypes = explode(',', $dbTypes);
        }

        $this->extensionPrefix = (string)$manifest->extensionPrefix;

        $this->fromTpl = (string)$manifest->attributes()->tpl;

        /*
         * Modules
         */
        if(isset($manifest->modules->module))
        {
            foreach($manifest->modules->module as $e)
            {
                $c = new stdClass;

                foreach($e->attributes() as $k => $a)
                {
                    $c->$k = (string)$a;
                }//foreach

                $c->scope = (string)$e->attributes()->scope;
                $c->position = (string)$e->attributes()->position;
                $c->ordering = (string)$e->attributes()->ordering;

                $this->modules[] = $c;
            }//foreach
        }

        /*
         * Plugins
         */
        if(isset($manifest->plugins->plugin))
        {
            foreach($manifest->plugins->plugin as $e)
            {
                $c = new stdClass;

                foreach($e->attributes() as $k => $a)
                {
                    $c->$k = (string)$a;
                }//foreach

                $c->scope = (string)$e->attributes()->scope;
                $c->ordering = (string)$e->attributes()->ordering;

                $this->plugins[] = $c;
            }//foreach
        }

        /*
         * Tables
         */
        if(isset($manifest->tables->table))
        {
            foreach($manifest->tables->table as $e)
            {
                $table = new EcrTable($e->name, $e->foreign);

                $t = new stdClass;
                $t->name = (string)$e->name;

                if(isset($e->relations->relation))
                {
                    foreach($e->relations->relation as $r)
                    {
                        $relation = new EcrTableRelation;
                        $relation->type = (string)$r->type;
                        $relation->field = (string)$r->field;
                        $relation->onTable = (string)$r->onTable;
                        $relation->onField = (string)$r->onField;

                        if(isset($r->aliases->alias))
                        {
                            foreach($r->aliases->alias as $elAlias)
                            {
                                $alias = new EcrTableRelationalias;
                                $alias->alias = (string)$elAlias->name;
                                $alias->aliasField = (string)$elAlias->field;

                                $relation->addAlias($alias);
                            }//foreach
                        }

                        $table->addRelation($relation);
                    }//foreach
                    $t->relations = $e->relations;
                }
                else
                {
                    $t->relations = array();
                }

                $this->tables[$table->name] = $table;
            }//foreach
        }

        /*
         * AutoCodes
         */
        if(isset($manifest->autoCodes->autoCode))
        {
            foreach($manifest->autoCodes->autoCode as $code)
            {
                $group = (string)$code->attributes()->group;
                $name = (string)$code->attributes()->name;
                $element = (string)$code->attributes()->element;
                $scope = (string)$code->attributes()->scope;

                $key = "$scope.$group.$name.$element";

                $EasyAutoCode = EcrProjectHelper::getAutoCode($key);

                if( ! $EasyAutoCode)
                {
                    continue;
                }

                if(isset($code->options->option))
                {
                    foreach($code->options->option as $o)
                    {
                        $option = (string)$o;
                        $k = (string)$o->attributes()->name;
                        $EasyAutoCode->options[$k] = (string)$option;
                    }//foreach
                }

                if(isset($code->fields))
                {
                    foreach($code->fields as $fieldsElement)
                    {
                        $key = (string)$fieldsElement->attributes()->key;
                        $fields = array();

                        if(isset($fieldsElement->field))
                        {
                            foreach($fieldsElement->field as $field)
                            {
                                $f = new EcrTableField($field);

                                $k = '';

                                if($field->attributes()->name)
                                {
                                    $k = (string)$field->attributes()->name;
                                }
                                else if(isset($field->name))
                                {
                                    $k = (string)$field->name;
                                }

                                $fields[$k] = $f;
                            }//foreach
                        }

                        $EasyAutoCode->fields[$key] = $fields;
                    }//foreach
                }

                $this->addAutoCode($EasyAutoCode);
            }//foreach
        }

        /*
         * Package elements - 1.6
         */
        if(isset($manifest->elements->element))
        {
            foreach($manifest->elements->element as $e)
            {
                $this->elements[(string)$e] = (string)$e;
            }//foreach
        }

        /*
         * BuildOptions
         */
        foreach($manifest->buildoptions as $opt)
        {
            foreach($opt as $k => $v)
            {
                $this->buildOpts[$k] = (string)$v;
            }//foreach
        }//foreach

        /*
         * Update servers
         */
        if(isset($manifest->updateservers->server))
        {
            foreach($manifest->updateservers->server as $server)
            {
                $u = new stdClass;
                $u->name = (string)$server->attributes()->name;
                $u->priority = (string)$server->attributes()->priority;
                $u->type = (string)$server->attributes()->type;
                $u->url = (string)$server;
                $this->updateServers[] = $u;
            }//foreach
        }

        return true;
    }//function

    /**
     * Adds AutoCode to the project.
     *
     * @param EcrAutoCode $autoCode The AutoCode
     *
     * @return void
     */
    public function addAutoCode(EcrAutoCode $autoCode)
    {
        $this->autoCodes[$autoCode->getKey()] = $autoCode;
    }//function

    /**
     * Deletes a project.
     *
     * @param boolean $complete Set true to remove the whole project
     *
     * @return boolean true on success
     */
    public function remove($complete = false)
    {
        if( ! $this->dbId)
        {
            echo EcrHtml::displayMessage(jgettext('Invalid Project'), 'error');

            return false;
        }

        if($complete)
        {
            //-- Uninstall the extension

            $clientId =($this->scope == 'admin') ? 1 : 0;

            jimport('joomla.installer.installer');

            //-- Get an installer object
            $installer = JInstaller::getInstance();

            //-- Uninstall the extension
            if( ! $installer->uninstall($this->type, $this->dbId, $clientId))
            {
                echo EcrHtml::displayMessage(jgettext('JInstaller: Unable to remove project'), 'error');

                return false;
            }
        }

        //-- Remove the config script
        $fileName = $this->getEcrXmlFileName();

        if( ! JFile::exists(ECRPATH_SCRIPTS.DS.$fileName))
        {
            echo EcrHtml::displayMessage(sprintf(jgettext('File not found %s'), ECRPATH_SCRIPTS.DS.$fileName), 'error');

            return false;
        }

        if( ! JFile::delete(ECRPATH_SCRIPTS.DS.$fileName))
        {
            echo EcrHtml::displayMessage(jgettext('Unable to delete file'), 'error');
            echo EcrHtml::displayMessage(ECRPATH_SCRIPTS.DS.$fileName, 'error');

            return false;
        }

        return true;
    }//function

    /**
     * Insert a part to the project.
     *
     * @param array $options Options for inserting
     * @param EcrLogger $logger The logger
     * @param boolean $overwrite Overwrite existing files
     *
     * @return boolean
     */
    public function insertPart($options, EcrLogger $logger, $overwrite = false)
    {
        $element_scope = JRequest::getVar('element_scope');
        $element_name = JRequest::getVar('element_name', null);
        $element = JRequest::getVar('element', null);

        if( ! isset($options->pathSource)
        || ! $options->pathSource)
        {
            JFactory::getApplication()->enqueueMessage(jgettext('Invalid options'), 'error');
            $logger->log('Invalid options');

            return false;
        }

        /*
         * Define substitutes
         */
        $this->addSubstitute('_ECR_ELEMENT_NAME_', $element_name);
        $this->addSubstitute('_ECR_LIST_POSTFIX_', $this->listPostfix);

        /*
         * Process files
         */
        // @TODO ...
        $basePathDest =($element_scope == 'admin') ? JPATH_ADMINISTRATOR : JPATH_SITE;
        $basePathDest .= DS.'components'.DS.$options->ecr_project;

        $files = JFolder::files($options->pathSource, '.', true, true, array('options', '.svn'));

        foreach($files as $file)
        {
            $fName = str_replace($options->pathSource.DS, '', JPath::clean($file));//JPath::clean @since J 1.7
            $fName = str_replace('ecr_element_name', strtolower($element_name), $fName);
            $fName = str_replace('ecr_list_postfix', strtolower($this->listPostfix), $fName);

            //--Check if file exists
            if(JFile::exists($basePathDest.DS.$fName) && ! $overwrite)
            {
                //-- Replace AutoCode
                $ACName = "$element_scope.$options->group.$options->part.$element";

                if(array_key_exists($ACName, $this->autoCodes))
                {
                    //-- Replace AutoCode
                    $fileContents = JFile::read($basePathDest.DS.$fName);

                    foreach($this->autoCodes as $AutoCode)
                    {
                        foreach($AutoCode->codes as $key => $code)
                        {
                            $fileContents = $AutoCode->replaceCode($fileContents, $key);
                        }//foreach
                    }//foreach
                }
                else
                {
                    JFactory::getApplication()->enqueueMessage(
                        sprintf(jgettext('Autocode key %s not found'), $ACName), 'error');

                    return false;
                }
            }
            else
            {
                //-- Add new file(s)
                $fileContents = JFile::read($file);

                $subPackage = explode(DS, str_replace($options->pathSource.DS, '', $file));
                $subPackage = $subPackage[0];
                $subPackage = str_replace(JFile::getName($file), '', $subPackage);
                $subPackage =($subPackage) ? $subPackage : 'Base';

                $this->addSubstitute('_ECR_SUBPACKAGE_', ucfirst($subPackage));
            }

            $this->substitute($fileContents);

            if( ! JFile::write($basePathDest.DS.$fName, $fileContents))
            {
                JFactory::getApplication()->enqueueMessage(jgettext('Unable to write file'), 'error');

                return false;
            }

            $logger->logFileWrite($file, $basePathDest.DS.$fName, $fileContents);
        }//foreach

        if( ! $this->writeProjectXml())
        {
            return false;
        }

        return true;
    }//function

    /**
     * Adds a table to the project.
     *
     * @param EcrTable $table Table name
     *
     * @return boolean
     */
    public function addTable(EcrTable $table)
    {
        if( ! in_array($table->name, $this->tables))
        {
            $this->tables[$table->name] = $table;
        }

        return true;
    }//function

    /**
     * Prepare adding a part.
     *
     * Setup substitutes
     *
     * @param string $ecr_project Project name
     * @param array $substitutes Substitutes to add
     *
     * @return boolean
     */
    public function prepareAddPart($ecr_project, $substitutes = array())
    {
        try
        {
            $project = EcrProjectHelper::getProject($ecr_project);

            $this->addSubstitute('_ECR_COM_NAME_', $project->name);
            $this->addSubstitute('_ECR_COM_COM_NAME_', $project->comName);
            $this->addSubstitute('_ECR_UPPER_COM_COM_NAME_', strtoupper($project->comName));
            $this->addSubstitute('ECR_AUTHOR', $project->author);
            $this->addSubstitute('AUTHORURL', $project->authorUrl);
            $this->addSubstitute('_ECR_ACT_DATE_', date('d-M-Y'));

            foreach($substitutes as $key => $value)
            {
                $this->addSubstitute($key, $value);
            }//foreach

            //-- Read the header file
            $header = JFile::read(ECRPATH_EXTENSIONTEMPLATES.DS.'std'.DS.'header.txt');

            //-- Replace vars in header
            $this->substitute($header);
            $this->addSubstitute('##*HEADER*##', $header);

            return true;
        }
        catch(Exception $e)
        {
            $this->logger->log('Unable to load the project '.$ecr_project.' - '.$e->getMessage(), 'ERROR');

            return false;
        }//try
    }//function

    /**
     * Add a string to the substitution array.
     *
     * @param string $key The key to search for
     * @param string $value The string to substitute
     *
     * @return void
     */
    public function addSubstitute($key, $value)
    {
        $this->_substitutes[$key] = $value;
    }//function

    /**
     * Get a subvstitute by key.
     *
     * @param string $key The key
     *
     * @return string
     */
    public function getSubstitute($key)
    {
        if(array_key_exists($key, $this->_substitutes))
        return $this->_substitutes[$key];

        return '';
    }//function

    /**
     * Replaces tags in a string with values from the substitution array.
     *
     * @param string &$string The string to apply the substitution
     *
     * @return string
     */
    public function substitute(& $string)
    {
        foreach($this->_substitutes as $key => $value)
        {
            $string = str_replace($key, $value, $string);
        }//foreach

        return $string;
    }//function

    /**
     * Get the path to the build directory.
     *
     * @return string
     */
    public function getZipPath()
    {
        //-- 1. Project specific build dir
        if($this->zipPath)
        return $this->zipPath;

        //-- 2. Standard config build dir
        $path = JComponentHelper::getParams('com_easycreator')->get('zipPath');

        if($path)
        return $path.'/'.$this->comName;

        //-- 3. Standard extension build dir
        return ECRPATH_BUILDS.'/'.$this->comName;
    }//function

    /**
     * Convert to a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->comName;
    }//function
}//class
