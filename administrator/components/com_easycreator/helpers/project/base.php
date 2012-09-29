<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 10-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * Project base class.
 *
 * @property-read array $presets
 */
abstract class EcrProjectBase
{
    //-- Removed in 1.6 - could be reintroduced in J! 3.0
    public $legacy = false;

    public $method = '';

    /**
     * Joomla! compatibility mode
     *
     * @var string
     */
    public $JCompat = '2.5';

    public $phpVersion = '5';

    public $fromTpl = '';

    public $dbId = 0;

    /**
     * @var string Full name - e.g. MyComponent
     */
    public $name = '';

    /**
     * @var string System name - e.g. com_mycomponent
     */
    public $comName = '';

    /**
     * @var string The prefix - e.g. com_
     */
    public $prefix = '';

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

    public $modules = array();

    public $plugins = array();

    public $tables = array();

    public $autoCodes = array();

    public $listPostfix = 'List';

    public $classPrefix = '';

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

   // public $actions = array();

    private $basePath = '';

    private $presets = array();

    public $defaultPreset = 'default';

    /** Special : g11n Language handling */
    public $langFormat = 'ini';

    /** Flag to identify a *somewhat* invalid project */
    public $isValid = true;

    /**
     * @var bool If the project is installable through the Joomla! installer.
     */
    public $isInstallable = true;

    /**
     * @var string
     */
    public $headerType;

    /**
     * @var JRegistry
     */
    public $deployOptions;

    /**
     * Constructor.
     *
     * @param string $name Project name.
     */
    public function __construct($name = '')
    {
        if( ! $name
            || ! $this->readProjectXml($name)
        )
        {
            return;
        }

        $this->findCopies();
        $this->langs = EcrLanguageHelper::discoverLanguages($this);

        if($this->type == 'component')
            $this->readMenu();

        $this->readJoomlaXml();
        $this->dbId = $this->getId();
        $this->readDeployFile();
    }

    /**
     * @param $property
     *
     * @return mixed
     * @throws UnexpectedValueException
     */
    public function __get($property)
    {
        if(in_array($property, array('presets')))
            return $this->$property;

        //return $property;

        EcrHtml::message(__METHOD__.' - Undefined property: '.$property, 'error');
    }

    /**
     * Find all files and folders belonging to the project.
     *
     * @return array
     */
    abstract public function findCopies();

    /**
     * Gets the language scopes for the extension type.
     *
     * @return array Indexed array.
     */
    abstract public function getLanguageScopes();

    /**
     * Gets the paths to language files.
     *
     * @param string $scope The scope - admin, site. etc.
     *
     * @throws Exception
     * @return array
     */
    abstract public function getLanguagePaths($scope = '');

    /**
     * Get the name for language files.
     *
     * @param string $scope The scope - admin, site. etc.
     *
     * @return string
     */
    abstract public function getLanguageFileName($scope = '');

    /**
     * Get the path for the Joomla! XML manifest file.
     *
     * @return string
     */
    abstract public function getJoomlaManifestPath();

    /**
     * Get a Joomla! manifest XML file name.
     *
     * @return string
     */
    abstract public function getJoomlaManifestName();

    /**
     * Gets the DTD for the extension type.
     *
     * @param string $jVersion Joomla! version
     *
     * @return mixed [array index array on success | false if not found]
     */
    abstract public function getDTD($jVersion);

    /**
     * Get a file name for a EasyCreator setup XML file.
     *
     * @return string
     */
    abstract public function getEcrXmlFileName();

    /**
     * Get the project Id.
     *
     * @return integer
     */
    abstract public function getId();

    /**
     * Get the extension base path.
     *
     * @return string
     */
    abstract public function getExtensionPath();

    /**
     * Discover all projects.
     *
     * @param $scope
     *
     * @return array
     */
    abstract public function getAllProjects($scope);

    /**
     * Get a list of known core projects.
     *
     * @param string $scope The scope - admin, site. etc.
     *
     * @return array
     */
    abstract public function getCoreProjects($scope);

    /**
     * Translate the type
     *
     * @return string
     */
    abstract public function translateType();

    /**
     * Translate the plural type
     *
     * @return string
     */
    abstract public function translateTypePlural();

    /**
     * Translate the type using a count
     *
     * @param int $n The amount
     *
     * @return string
     */
    abstract public function translateTypeCount($n);

    /**
     * Read the J! main menu entries for a component from the core components table.
     *
     * @return void
     */
    protected function readMenu()
    {
    }

    /**
     * Updates the administration main menu.
     *
     * @return boolean
     */
    protected function updateAdminMenu()
    {
        return true;
    }

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
        }

        return true;
    }

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
    }

    /**
     * This will update the config file.
     *
     * @return boolean true on success
     */
    public function updateFromRequest()
    {
        $input = JFactory::getApplication()->input;

        $buildVars = $input->get('buildvars', array(), 'array');
        $buildOpts = $input->get('buildopts', array(), 'array');
        $this->dbTypes = $input->get('dbtypes', array(), 'array');
        $this->headerType = $input->get('headerType');

        $packageElements = $input->getString('package_elements');
        $packageElements = ($packageElements) ? explode(',', $packageElements) : array();

        if(count($packageElements))
        {
            $this->elements = array();

            foreach($packageElements as $element)
            {
                $this->elements[$element] = $element;
            }
        }

        //-- Process credit vars
        foreach($buildVars as $name => $var)
        {
            if(property_exists($this, $name))
            {
                $this->$name = $var;
            }
        }

        //-- Method special treatment for checkboxes
        $this->method = (isset($buildVars['method'])) ? $buildVars['method'] : '';
        $this->buildOpts['lng_separate_javascript'] = (in_array('lng_separate_javascript', $buildOpts)) ? '1' : '0';

        /*
        //-- Build options
        $this->buildOpts['archiveZip'] = (in_array('archiveZip', $buildOpts)) ? 'ON' : 'OFF';
        $this->buildOpts['archiveTgz'] = (in_array('archiveTgz', $buildOpts)) ? 'ON' : 'OFF';
        $this->buildOpts['archiveBz2'] = (in_array('archiveBz2', $buildOpts)) ? 'ON' : 'OFF';
        $this->buildOpts['create_indexhtml'] = (in_array('createIndexhtml', $buildOpts)) ? 'ON' : 'OFF';
        $this->buildOpts['createMD5'] = (in_array('createMD5', $buildOpts)) ? 'ON' : 'OFF';
        $this->buildOpts['createMD5Compressed'] = (in_array('createMD5Compressed', $buildOpts)) ? 'ON' : 'OFF';
        $this->buildOpts['includeEcrProjectfile'] = (in_array('includeEcrProjectfile', $buildOpts)) ? 'ON' : 'OFF';
        $this->buildOpts['removeAutocode'] = (in_array('removeAutocode', $buildOpts)) ? 'ON' : 'OFF';

        $ooo = new JRegistry($buildOpts);

        for($i = 1; $i < 5; $i ++)
        {
            $this->buildOpts['custom_name_'.$i] = $ooo->get('custom_name_'.$i);
        }

        //-- Build actions
        $actions = JxRequest::getVar('actions', array(), 'default', 'array');
        $actionFields = JxRequest::getVar('fields', array(), 'default', 'array');

        foreach($actions as $event => $fields)
        {
            foreach($fields as $i => $type)
            {
                $a = EcrProjectAction::getInstance($type, $event)
                    ->setOptions($actionFields[$i]);

                $this->actions[$i] = $a;
            }
        }
        */

        //-- Build presets
        $defaultPreset = $input->get('preset');

        $actions = $input->get('actions', array(), 'array');
        $actionFields = $input->get('fields', array(), 'array');

        $ooo = new JRegistry($buildOpts);

        $p = new EcrProjectModelBuildpreset;

        $p->loadValues($buildOpts);

        $p->buildFolder = $buildVars['zipPath'];

        foreach(array(
            'archiveZip', 'archiveTgz', 'archiveBz2',
            'createIndexhtml', 'createMD5', 'createMD5Compressed',
            'includeEcrProjectfile', 'removeAutocode',
                ) as $var)
        {
            $p->$var = (in_array($var, $buildOpts)) ? 1 : 0;
        }

/*
        $p->archiveZip = (in_array('archiveZip', $buildOpts)) ? 'ON' : 'OFF';
        $p->archiveTgz = (in_array('archiveTgz', $buildOpts)) ? 'ON' : 'OFF';
        $p->archiveBz2 = (in_array('archiveBz2', $buildOpts)) ? 'ON' : 'OFF';

        $p->createIndexhtml = (in_array('createIndexhtml', $buildOpts)) ? 'ON' : 'OFF';
        $p->createMD5 = (in_array('createMD5', $buildOpts)) ? 'ON' : 'OFF';
        $p->createMD5Compressed = (in_array('createMD5Compressed', $buildOpts)) ? 'ON' : 'OFF';
        $p->includeEcrProjectfile = (in_array('includeEcrProjectfile', $buildOpts)) ? 'ON' : 'OFF';
        $p->removeAutocode = (in_array('removeAutocode', $buildOpts)) ? 'ON' : 'OFF';
*/
        for($i = 1; $i < 5; $i ++)
        {
            $p->{'custom_name_'.$i} = $ooo->get('custom_name_'.$i);
        }

        foreach($actions as $event => $fields)
        {
            foreach($fields as $i => $type)
            {
                $p->actions[] = EcrProjectAction::getInstance($type, $event)
                    ->setOptions($actionFields[$i]);
            }
        }

        $saveas = $input->get('preset_saveas');

        if($saveas)
        {
            $this->presets[$saveas] = $p;
        }
        else
        {
            $this->presets[$defaultPreset] = $p;
        }

        //-- Update servers
        $this->updateServers = array();

        $updateServers = $input->get('updateServers', array(), 'array');

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
            }
        }

        $this->JCompat = $input->getString('jcompat');

        if( ! $this->update())
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

        $this->deployOptions->set('ftp.host', $input->getString('ftpHost'));
        $this->deployOptions->set('ftp.port', $input->getString('ftpPort'));
        $this->deployOptions->set('ftp.basedir', $input->getString('ftpBasedir'));
        $this->deployOptions->set('ftp.downloads', $input->getString('ftpDownloads'));
        $this->deployOptions->set('ftp.user', $input->getString('ftpUser'));
        $this->deployOptions->set('ftp.pass', $input->getString('ftpPass'));

        $this->deployOptions->set('github.repoowner', $input->getString('githubRepoOwner'));
        $this->deployOptions->set('github.reponame', $input->getString('githubRepoName'));
        $this->deployOptions->set('github.user', $input->getString('githubUser'));
        $this->deployOptions->set('github.pass', $input->getString('githubPass'));

        $this->writeDeployFile();

        return true;
    }

    /**
     * Write the deploy information file.
     *
     * @throws Exception
     * @return EcrProjectBase
     */
    private function writeDeployFile()
    {
        $path = ECRPATH_DATA.'/deploy/'.$this->getEcrXmlFileName('deploy');

        $xml = EcrProjectHelper::getXML('<ecrdeploy'
                .' version="'.ECR_VERSION.'"'
                .' />'
            , false);

        $ftp = $xml->addChild('ftp');

        $ftp->addChild('host', $this->deployOptions->get('ftp.host'));
        $ftp->addChild('port', $this->deployOptions->get('ftp.port'));
        $ftp->addChild('basedir', $this->deployOptions->get('ftp.basedir'));
        $ftp->addChild('downloads', $this->deployOptions->get('ftp.downloads'));
        $ftp->addChild('user', $this->deployOptions->get('ftp.user'));
        $ftp->addChild('pass', $this->deployOptions->get('ftp.pass'));

        $github = $xml->addChild('github');

        $github->addChild('repoowner', $this->deployOptions->get('github.repoowner'));
        $github->addChild('reponame', $this->deployOptions->get('github.reponame'));
        $github->addChild('user', $this->deployOptions->get('github.user'));
        $github->addChild('pass', $this->deployOptions->get('github.pass'));

        $contents = $xml->asFormattedXML();

        if(false == JFile::write($path, $contents))
            throw new Exception(__METHOD__.' - Unable to write deploy file to: '.$path);

        return $this;
    }

    /**
     * Read the deploy information file.
     *
     * @throws Exception
     *
     * @return \EcrProjectBase
     */
    private function readDeployFile()
    {
        $path = ECRPATH_DATA.'/deploy/'.$this->getEcrXmlFileName('deploy');

        $this->deployOptions = new JRegistry;

        if(false == JFile::exists($path))
            return $this;

        $xml = EcrProjectHelper::getXML($path);

        if(false == $xml)
            throw new Exception(__METHOD__.' - Invalid deploy file');

        $this->deployOptions->set('ftp.host', (string)$xml->ftp->host);
        $this->deployOptions->set('ftp.port', (string)$xml->ftp->port);
        $this->deployOptions->set('ftp.basedir', (string)$xml->ftp->basedir);
        $this->deployOptions->set('ftp.downloads', (string)$xml->ftp->downloads);
        $this->deployOptions->set('ftp.user', (string)$xml->ftp->user);
        $this->deployOptions->set('ftp.pass', (string)$xml->ftp->pass);

        $this->deployOptions->set('github.repoowner', (string)$xml->github->repoowner);
        $this->deployOptions->set('github.reponame', (string)$xml->github->reponame);
        $this->deployOptions->set('github.user', (string)$xml->github->user);
        $this->deployOptions->set('github.pass', (string)$xml->github->pass);

        return $this;
    }

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

        if(false == $manifest)
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

        //-- Process credit vars
        foreach($xmlBuildVars as $xmlName)
        {
            $manifest->$xmlName = $this->$xmlName;
        }

        $dtd = $this->getDTD($this->JCompat);

        $root = '';
        $root .= '<?xml version="1.0" encoding="UTF-8"?>'.NL;

        if($dtd)
        {
            $root .= '<!DOCTYPE '.$dtd['type'].' PUBLIC "'.$dtd['public'].'"'.NL.'"'.$dtd['uri'].'">';
        }

        $output = $root.$manifest->asFormattedXML();

        //-- Write XML file to disc
        if(false == JFile::write(JPATH_ROOT.DS.$installXML, $output))
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
    }

    /**
     * Updates the EasyCreator configuration file for the project.
     *
     * @param boolean $testMode If set to 'true' xml file will be generated but not written to disk
     *
     * @throws DomainException
     * @return mixed [string xml string on success | boolean false on error]
     */
    private function writeProjectXml($testMode = false)
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
            }
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
            }
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
                        }
                    }
                }
            }
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
                    }
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
                            }
                        }
                    }
                }
            }
        }

        if($this->type == 'package')
        {
            //-- J! 1.6 Package
            $filesElement = $xml->addChild('elements');

            foreach($this->elements as $element)
            {
                $filesElement->addChild('element', $element);
            }
        }

        //-- Build presets
        $psElement = $xml->addChild('presets');

        foreach($this->presets as $name => $values)
        {
            $pElement = $psElement->addChild('preset');
            $pElement->addAttribute('name', $name);

            $actions = array();

            foreach($values as $k => $v)
            {
                if('actions' == (string)$k)
                {
                    $actions = $v;
                }
                else
                {
                    $pElement->addChild($k, $v);
                }
            }

            $aElement = $pElement->addChild('actions');

            foreach($actions as $action)
            {
                /* @var SimpleXMLElement $sElement */
                $sElement = $aElement->addChild('action');

                $sElement->addAttribute('type', $action->type);
                $sElement->addAttribute('event', $action->event);

                foreach($action->getProperties() as $k => $v)
                {
                    $sElement->addChild($k, $v);
                }
            }
        }

        //-- Buildopts
        if(count($this->buildOpts))
        {
            $buildElement = $xml->addChild('buildoptions');

            foreach($this->buildOpts as $k => $opt)
            {
                $buildElement->addChild($k, $opt);
            }
        }

/*
        //-- Actions
        if(count($this->actions))
        {
            $element = $xml->addChild('actions');

            /* @var EcrProjectAction $action /
            foreach($this->actions as $action)
            {
                /* @var SimpleXMLElement $sElement /
                $sElement = $element->addChild('action');

                $sElement->addAttribute('type', $action->type);
                $sElement->addAttribute('event', $action->event);

                foreach($action->getProperties() as $k => $v)
                {
                    $sElement->addChild($k, $v);
                }
            }
        }
*/
        //-- Update servers
        if(count($this->updateServers))
        {
            $element = $xml->addChild('updateservers');

            foreach($this->updateServers as $server)
            {
                /* @var SimpleXMLElement $sElement */
                $sElement = $element->addChild('server', htmlentities($server->url));

                $sElement->addAttribute('name', $server->name);
                $sElement->addAttribute('type', $server->type);
                $sElement->addAttribute('priority', $server->priority);
            }
        }

        $root = '';
        $root .= '<?xml version="1.0" encoding="UTF-8"?>'.NL;
        $root .= '<!DOCTYPE easyproject PUBLIC "-//EasyCreator 0.0.14//DTD project 1.0//EN"'.NL;
        $root .= '"http://elkuku.github.com/dtd/easycreator/0.0.14/project.dtd">';

        $output = $root.$xml->asFormattedXML();

        if(ECR_DEBUG)
            echo '<pre>'.htmlentities($output).'</pre>';

        $path = ECRPATH_SCRIPTS.DS.$this->getEcrXmlFileName();

        if(false == $testMode)
        {
            if(false == JFile::write(JPath::clean($path), $output))
                throw new DomainException('Could not save XML file.', 1);
        }

        return $output;
    }

    /**
     * Read the project XML file.
     *
     * @param string $projectName Projects name
     *
     * @throws Exception
     * @return boolean
     */
    private function readProjectXml($projectName)
    {
        $fileName = ECRPATH_SCRIPTS.DS.$projectName.'.xml';

        if(false == JFile::exists($fileName))
            throw new Exception('Project manifest not found');

        $manifest = EcrProjectHelper::getXML($fileName);

        if( ! $manifest instanceof SimpleXMLElement
            || $manifest->getName() != 'easyproject'
        )
        {
            JFactory::getApplication()->enqueueMessage(jgettext('Invalid project manifest'), 'error');

            return false;
        }

        $this->type = (string)$manifest->attributes()->type;
        $this->scope = (string)$manifest->attributes()->scope;
        $this->name = (string)$manifest->name;
        $this->comName = (string)$manifest->comname;

        //-- @Joomla!-compat 2.5
        $this->JCompat = ((string)$manifest->JCompat) ? (string)$manifest->JCompat : '2.5';

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
                }

                $c->scope = (string)$e->attributes()->scope;
                $c->position = (string)$e->attributes()->position;
                $c->ordering = (string)$e->attributes()->ordering;

                $this->modules[] = $c;
            }
        }

        /*
         * Plugins
         */
        if(isset($manifest->plugins->plugin))
        {
            /* @var SimpleXMLElement $e */
            foreach($manifest->plugins->plugin as $e)
            {
                $c = new stdClass;

                foreach($e->attributes() as $k => $a)
                {
                    $c->$k = (string)$a;
                }

                $c->scope = (string)$e->attributes()->scope;
                $c->ordering = (string)$e->attributes()->ordering;

                $this->plugins[] = $c;
            }
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
                            }
                        }

                        $table->addRelation($relation);
                    }

                    $t->relations = $e->relations;
                }
                else
                {
                    $t->relations = array();
                }

                $this->tables[$table->name] = $table;
            }
        }

        /*
         * AutoCodes
         */
        if(isset($manifest->autoCodes->autoCode))
        {
            /* @var SimpleXMLElement $code */
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
                    /* @var SimpleXMLElement $o */
                    foreach($code->options->option as $o)
                    {
                        $option = (string)$o;
                        $k = (string)$o->attributes()->name;
                        $EasyAutoCode->options[$k] = (string)$option;
                    }
                }

                if(isset($code->fields))
                {
                    /* @var SimpleXMLElement $fieldsElement */
                    foreach($code->fields as $fieldsElement)
                    {
                        $key = (string)$fieldsElement->attributes()->key;
                        $fields = array();

                        if(isset($fieldsElement->field))
                        {
                            /* @var SimpleXMLElement $field */
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
                            }
                        }

                        $EasyAutoCode->fields[$key] = $fields;
                    }
                }

                $this->addAutoCode($EasyAutoCode);
            }
        }

        /*
         * Package elements - 1.6
         */
        if(isset($manifest->elements->element))
        {
            foreach($manifest->elements->element as $e)
            {
                $this->elements[(string)$e] = (string)$e;
            }
        }

        /*
         * BuildOptions
         */
        foreach($manifest->buildoptions as $opt)
        {
            foreach($opt as $k => $v)
            {
                $this->buildOpts[$k] = (string)$v;
            }
        }

        /*
        * Build presets
        */

        //-- Init the defqult preset
        $this->presets['default'] = new EcrProjectModelBuildpreset;

        if(isset($manifest->presets->preset))
        {
            /* @var SimpleXMLElement $preset */
            foreach($manifest->presets->preset as $preset)
            {
                $p = new EcrProjectModelBuildpreset;

                foreach($preset as $k => $v)
                {
                    if('actions' == $k)
                    {
                        /* @var SimpleXMLElement $action */
                        foreach($v as $action)
                        {
                            $p->actions[] = EcrProjectAction::getInstance(
                                (string)$action->attributes()->type, (string)$action->attributes()->event)
                                ->setOptions($action);
                        }
                    }
                    else
                    {
                        if(is_bool($p->$k))
                        {
                            $p->{(string)$k} =('1' == (string)$v) ? true : false;
                        }
                        else
                        {
                            $p->{(string)$k} = (string)$v;
                        }
                    }
                }

                $this->presets[(string)$preset->attributes()->name] = $p;
            }
        }

        /*
         * Update servers
         */
        if(isset($manifest->updateservers->server))
        {
            /* @var SimpleXMLElement $server */
            foreach($manifest->updateservers->server as $server)
            {
                $u = new stdClass;
                $u->name = (string)$server->attributes()->name;
                $u->priority = (string)$server->attributes()->priority;
                $u->type = (string)$server->attributes()->type;
                $u->url = (string)$server;
                $this->updateServers[] = $u;
            }
        }

        /*
         * Actions
         */
/*        if(isset($manifest->actions->action))
        {
            /* @var SimpleXMLElement $action /
            foreach($manifest->actions->action as $action)
            {
                $a = EcrProjectAction::getInstance(
                    (string)$action->attributes()->type, (string)$action->attributes()->event)
                ->setOptions($action);

                $this->actions[] = $a;
            }
        }
*/

        return $this;
    }

    /**
     * Adds AutoCode to the project.
     *
     * @param EcrProjectAutocode $autoCode The AutoCode
     *
     * @return void
     */
    public function addAutoCode(EcrProjectAutocode $autoCode)
    {
        $this->autoCodes[$autoCode->getKey()] = $autoCode;
    }

    /**
     * Deletes a project.
     *
     * @param boolean $complete Set true to remove the whole project
     *
     * @throws Exception
     * @return EcrProjectBase
     */
    public function remove($complete = false)
    {
        if('package' != $this->type)
        {
            if( ! $this->dbId)
                throw new Exception(jgettext('Invalid Project'));

            if($complete)
            {
                //-- Uninstall the extension

                if($this->isInstallable)
                {
                    $clientId = ($this->scope == 'admin') ? 1 : 0;

                    jimport('joomla.installer.installer');

                    //-- Get an installer object
                    $installer = JInstaller::getInstance();

                    //-- Uninstall the extension
                    if( ! $installer->uninstall($this->type, $this->dbId, $clientId))
                        throw new Exception(jgettext('JInstaller: Unable to remove project'));
                }
                else
                {
                    // The extension is not "installable" - so just remove the files

                    if(false == JFolder::delete($this->getExtensionPath()))
                        throw new Exception('Unable to remove the extension');
                }
            }
        }

        //-- Remove the config script
        $fileName = $this->getEcrXmlFileName();

        if(false == JFile::exists(ECRPATH_SCRIPTS.DS.$fileName))
            throw new Exception(sprintf(jgettext('File not found %s'), ECRPATH_SCRIPTS.DS.$fileName));

        if(false == JFile::delete(ECRPATH_SCRIPTS.DS.$fileName))
            throw new Exception(sprintf(jgettext('Unable to delete file at %s'), ECRPATH_SCRIPTS.DS.$fileName));

        return $this;
    }

    /**
     * Insert a part to the project.
     *
     * @param array     $options   Options for inserting
     * @param EcrLogger $logger    The logger
     * @param boolean   $overwrite Overwrite existing files
     *
     * @return boolean
     */
    public function insertPart($options, EcrLogger $logger, $overwrite = false)
    {
        $input = JFactory::getApplication()->input;

        $element_scope = $input->getString('element_scope');
        $element_name = $input->getString('element_name');
        $element = $input->getString('element');

        if(false == isset($options->pathSource) || ! $options->pathSource)
        {
            JFactory::getApplication()->enqueueMessage(jgettext('Invalid options'), 'error');
            $logger->log('Invalid options');

            return false;
        }

        /*
         * Define substitutes
         */
        $this->addSubstitute('ECR_ELEMENT_NAME', $element_name);
        $this->addSubstitute('ECR_LIST_POSTFIX', $this->listPostfix);

        /*
         * Process files
         */
        //-- @TODO ...
        $basePathDest = ($element_scope == 'admin') ? JPATH_ADMINISTRATOR : JPATH_SITE;
        $basePathDest .= DS.'components'.DS.$options->ecr_project;

        $files = JFolder::files($options->pathSource, '.', true, true, array('options', '.svn'));

        foreach($files as $file)
        {
            $fName = str_replace($options->pathSource.DS, '', JPath::clean($file));
            $fName = str_replace('ecr_element_name', strtolower($element_name), $fName);
            $fName = str_replace('ecr_list_postfix', strtolower($this->listPostfix), $fName);

            //-- Check if file exists
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
                        }
                    }
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
                $subPackage = ($subPackage) ? $subPackage : 'Base';

                $this->addSubstitute('ECR_SUBPACKAGE', ucfirst($subPackage));
            }

            $this->substitute($fileContents);

            if(false == JFile::write($basePathDest.DS.$fName, $fileContents))
            {
                JFactory::getApplication()->enqueueMessage(jgettext('Unable to write file'), 'error');

                return false;
            }

            $logger->logFileWrite($file, $basePathDest.DS.$fName, $fileContents);
        }

        if( ! $this->update())
        {
            return false;
        }

        return true;
    }

    /**
     * Adds a table to the project.
     *
     * @param EcrTable $table Table name
     *
     * @return boolean
     */
    public function addTable(EcrTable $table)
    {
        if(false == in_array($table->name, $this->tables))
        {
            $this->tables[$table->name] = $table;
        }

        return true;
    }

    /**
     * Prepare adding a part.
     *
     * Setup substitutes
     *
     * @param string $ecr_project Project name
     * @param array  $substitutes Substitutes to add
     *
     * @return boolean
     */
    public function prepareAddPart($ecr_project, $substitutes = array())
    {
        try
        {
            $project = EcrProjectHelper::getProject($ecr_project);

            $this->addSubstitute('ECR_COM_NAME', $project->name);
            $this->addSubstitute('ECR_COM_COM_NAME', $project->comName);
            $this->addSubstitute('ECR_UPPER_COM_COM_NAME', strtoupper($project->comName));
            $this->addSubstitute('ECR_AUTHORNAME', $project->author);
            $this->addSubstitute('ECR_AUTHORURL', $project->authorUrl);
            $this->addSubstitute('ECR_ACT_DATE', date('d-M-Y'));

            foreach($substitutes as $key => $value)
            {
                $this->addSubstitute($key, $value);
            }

            $path = ECRPATH_EXTENSIONTEMPLATES.'/std/header/'.$project->headerType.'/header.txt';

            //-- Read the header file
            $header = (JFile::exists($path)) ? JFile::read($path) : '';

            //-- Replace vars in header
            $this->substitute($header);
            $this->addSubstitute('##*HEADER*##', $header);

            return true;
        }
        catch(Exception $e)
        {
            $this->logger->log('Unable to load the project '.$ecr_project.' - '.$e->getMessage(), 'ERROR');

            return false;
        }
    }

    /**
     * Add a string to the substitution array.
     *
     * @param string $key   The key to search for
     * @param string $value The string to substitute
     *
     * @return void
     */
    public function addSubstitute($key, $value)
    {
        $this->_substitutes[$key] = $value;
    }

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
    }

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
        }

        return $string;
    }

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
    }

    /**
     * Get a preset by name.
     *
     * @param string $name
     *
     * @throws UnexpectedValueException
     * @return EcrProjectModelBuildpreset
     */
    public function getPreset($name = 'default')
    {
        if(false == isset($this->presets[$name]))
            throw new UnexpectedValueException(__METHOD__.' - Invalid preset: '.$name);

        return $this->presets[$name];
    }

    /**
     * Convert to a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->comName;
    }
}//class
