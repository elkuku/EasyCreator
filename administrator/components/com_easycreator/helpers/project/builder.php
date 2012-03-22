<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 29-Feb-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * Creates Joomla! extensions from EasyCreator extension templates.
 *
 * @package EasyCreator
 */
class EcrProjectBuilder extends JObject
{
    /**
     * @var EcrProjectBase
     */
    public $project = null;

    private $buildBase = '';

    private $buildDir = '';

    /**
     * @var SimpleXMLElement
     */
    private $buildManifest = null;

    /**
     * @var EcrLogger
     */
    private $logger = null;

    /**
     * @var bool If set to true only files are being build but will not be installed.
     */
    private $testMode = false;

    /**
     * @var EcrProjectReplacement
     */
    public $replacements;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->replacements = new EcrProjectReplacement;

        $this->testMode = (JRequest::getCmd('ecr_test_mode') == 'test') ? true : false;
    }

    /**
     * Creates the frame.
     *
     * @param string $type     Project type (component, plugin...)
     * @param string $template Name of the extension template
     * @param string $name     Baby's name
     *
     * @return EcrProjectBase on success | false on error.
     */
    public function build($type, $template, $name)
    {
        //-- Get component parameters
        $comParams = JComponentHelper::getParams('com_easycreator');

        //-- Setup logging
        $buildOpts = JRequest::getVar('buildopts', array());
        $buildOpts['fileName'] = date('ymd_Hi').'_building.log';

        $this->logger = EcrLogger::getInstance('ecr', $buildOpts);

        $this->buildBase = JPath::clean(ECRPATH_EXTENSIONTEMPLATES.DS.$type.DS.$template);
        $this->logger->log('buildBase set to: '.$this->buildBase);

        $projectTypes = EcrProjectHelper::getProjectTypesTags();

        if(! in_array($type, $projectTypes))
        {
            $this->logger->log(sprintf('Unknown project type [%s]', $type));
            $this->logger->writeLog();

            $this->setError(sprintf('Unknown project type [%s]', $type));

            return false;
        }

        $this->project = EcrProjectHelper::newProject($type);

        $this->project->name = $name;
        $this->project->type = $type;
        $this->project->fromTpl = $template;

        $this->project->dbTypes = JRequest::getVar('dbtypes', array(), 'array');
        $this->project->headerType = JRequest::getCmd('headerType');

        //-- Set custom build names from component defaults
        for($i = 1; $i < 5; $i ++)
        {
            $this->project->buildOpts['custom_name_'.$i] = $comParams->get('custom_name_'.$i);
        }

        //-- Set packing formats from component defaults
        foreach(EcrEasycreator::$packFormats as $name => $ext)
        {
            if($comParams->get($name))
            {
                $this->project->buildOpts[$name] = 'ON';
            }
        }

        if(! $this->customOptions('process'))
        {
            $this->logger->log('Custom options failed');
            $this->logger->writeLog();

            $this->setError('Custom options failed');

            return false;
        }

        try
        {
            $this->setUp()
                ->setUpProject()
                ->readHeader()
                ->createBuildDir()
                ->copyFiles()
                ->processMoreOptions()
                ->createJoomlaManifest()
                ->install()
                ->createEasyCreatorManifest();
        }
        catch(EcrBuilderException $e)
        {
            $this->logger->log('ERROR', $e->getMessage());
            $this->logger->writeLog();

            $this->setError('ERROR: '.$e->getMessage());

            return false;
        }

        $this->logger->log('FINISHED');
        $this->logger->writeLog();

        return $this->project;
    }

    /**
     * Setup the builder.
     *
     * @throws EcrBuilderException
     * @return EcrProjectBuilder
     */
    private function setUp()
    {
        if(! JFile::exists($this->buildBase.DS.'manifest.xml'))
            throw new EcrBuilderException('Failed to open: '.$this->buildBase.DS.'manifest.xml');

        if(! JFolder::exists($this->buildBase.DS.'tmpl'))
            throw new EcrBuilderException('Template must be in folder named tmpl - '
                .$this->buildBase.DS.'tmpl');

        $folders = JFolder::folders($this->buildBase.DS.'tmpl');

        if(! in_array('site', $folders) && ! in_array('admin', $folders))
            throw new EcrBuilderException('Template must contain folders named admin or site');

        $this->buildManifest = EcrProjectHelper::getXML($this->buildBase.DS.'manifest.xml');

        $this->logger->log('Build manifest loaded');

        return $this;
    }

    /**
     * Setup the project.
     *
     * @throws EcrBuilderException
     * @return EcrProjectBuilder
     */
    private function setUpProject()
    {
        switch($this->project->type)
        {
            case 'component':
                $this->project->comName = strtolower($this->project->prefix.$this->project->name);
                $this->project->buildPath = $this->buildDir;
                break;

            case 'module':
                $s = (string)$this->buildManifest->attributes()->scope;
                $this->project->comName = strtolower($this->project->prefix.$this->project->name);
                $this->project->scope = ($s == 'administrator' || ! $s) ? 'admin' : 'site';
                break;

            case 'plugin':
                $this->project->comName = strtolower($this->project->name);

                if(! $this->project->scope)
                {
                    //-- Scope has been set previously by temlate options
                    $this->project->scope = (string)$this->buildManifest->attributes()->scope;
                }
                break;

            case 'template':
                $this->project->comName = strtolower($this->project->name);
                $this->project->scope = (string)$this->buildManifest->attributes()->scope;
                break;

            case 'library':
                $this->project->comName = strtolower($this->project->name);

                if(! $this->project->scope)
                    throw new EcrBuilderException(__METHOD__.': Missing scope for library');
                break;

            case 'package':
                $this->project->comName = $this->project->prefix.strtolower($this->project->name);
                $this->project->buildPath = $this->buildDir;
                break;

            case 'cliapp' :
            case 'webapp' :
                $this->project->comName = strtolower($this->project->name);
                break;

            default:
                throw new EcrBuilderException(__METHOD__.' - Undefined type : '.$this->project->type);
                break;
        }

        $this->project->JCompat = (string)$this->buildManifest->jVersion;

        $this->project->version = JRequest::getVar('version');
        $this->project->description = JRequest::getVar('description');
        $this->project->author = JRequest::getVar('author');
        $this->project->authorEmail = JRequest::getVar('authorEmail');
        $this->project->authorUrl = JRequest::getVar('authorUrl');
        $this->project->copyright = JRequest::getVar('copyright');
        $this->project->license = JRequest::getVar('license');
        $this->project->listPostfix = JRequest::getVar('list_postfix');

        $this->replacements->_ECR_COM_NAME_ = $this->project->name;
        $this->replacements->_ECR_LOWER_COM_NAME_ = strtolower($this->project->name);
        $this->replacements->_ECR_UPPER_COM_NAME_ = strtoupper($this->project->name);
        $this->replacements->_ECR_COM_COM_NAME_ = $this->project->comName;

        if('template' == $this->project->type
            || 'template' == $this->project->type
        )
        {
            $this->replacements->_ECR_UPPER_COM_COM_NAME_ = strtoupper($this->project->prefix.$this->project->comName);
        }
        else
        {
            $this->replacements->_ECR_UPPER_COM_COM_NAME_ = strtoupper($this->project->comName);
        }

        $this->replacements->_ECR_COM_TBL_NAME_ = strtolower($this->project->name);
        $this->replacements->_ECR_ACT_DATE_ = date('d-M-Y');

        $this->replacements->VERSION = $this->project->version;
        $this->replacements->ECR_DESCRIPTION = $this->project->description;
        $this->replacements->ECR_AUTHOR = $this->project->author;
        $this->replacements->AUTHOREMAIL = $this->project->authorEmail;
        $this->replacements->AUTHORURL = $this->project->authorUrl;
        $this->replacements->COPYRIGHT = $this->project->copyright;
        $this->replacements->LICENSE = $this->project->license;

        $this->replacements->addCustom('$@@Id@@$', '$Id$');

        if('component' == $this->project->type)
        {
            //-- AutoCode
            $this->replacements->_ECR_LIST_POSTFIX_ = $this->project->listPostfix;
            $this->replacements->_ECR_LOWER_LIST_POSTFIX_ = strtolower($this->project->listPostfix);
            $this->replacements->_ECR_UPPER_LIST_POSTFIX_ = strtoupper($this->project->listPostfix);

            //-- Menu
            /* @var SimpleXMLElement $buildMenuElement */
            $buildMenuElement = $this->buildManifest->menu;

            if($buildMenuElement instanceof SimpleXmlElement)
            {
                $m = array();

                $s = (string)$buildMenuElement;
                $s = $this->replace($s);

                $m['text'] = $s;
                $m['img'] = $this->replace((string)$buildMenuElement->attributes()->img);

                $this->project->menu = $m;

                //-- SubMenu
                $buildSubMenuElement = $this->buildManifest->submenu;

                if($buildSubMenuElement instanceof SimpleXmlElement
                    && count($buildSubMenuElement->menu)
                )
                {
                    /* @var SimpleXMLElement $subElement */
                    foreach($buildSubMenuElement->menu as $subElement)
                    {
                        $m = array();
                        $m['text'] = $this->replace($subElement);
                        $m['link'] = $this->replace($subElement->attributes()->link);
                        $m['img'] = $this->replace($subElement->attributes()->img);

                        $this->project->submenu[] = $m;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Set the scope.
     *
     * @param string $scope The scope admin/site
     *
     * @return void
     */
    public function setScope($scope)
    {
        $this->project->scope = $scope;
    }

    /**
     * Create the build directory.
     *
     * @throws EcrBuilderException
     * @return EcrProjectBuilder
     */
    private function createBuildDir()
    {
        //-- Create build directory
        if('1.5' == ECR_JVERSION)
        {
            $cfg = new JConfig;
            $this->buildDir = $cfg->tmp_path.DS.uniqid($this->project->comName.'_');
        }
        else
        {
            $this->buildDir = JFactory::getConfig()->get('tmp_path').DS.uniqid($this->project->comName.'_');
        }

        //-- Clean the path @since J 1.7
        $this->buildDir = JPath::clean($this->buildDir);

        if(! JFolder::create($this->buildDir))
            throw new EcrBuilderException('Failed to create build directory: '.$this->buildDir);

        $this->logger->log('TempDir created at: '.$this->buildDir);
        $this->logger->log('Building: '.$this->project->name.'<br />'
            .'aka "'.$this->project->comName.'"<br />Template: '.$this->project->fromTpl);

        $this->project->buildPath = $this->buildDir;

        return $this;
    }

    /**
     * Copy the files.
     *
     * @throws EcrBuilderException
     * @return EcrProjectBuilder
     */
    private function copyFiles()
    {
        $scopes = array('site', 'admin', 'media');

        $tplFolders = JFolder::folders($this->buildBase.DS.'tmpl');

        //-- Copy files
        foreach($scopes as $scope)
        {
            if(! in_array($scope, $tplFolders))
                continue;

            JFolder::create($this->buildDir.DS.$scope);

            $fileList = JFolder::files($this->buildBase.DS.'tmpl'.DS.$scope, '.', true, true);

            foreach($fileList as $fileName)
            {
                $subPack = str_replace($this->buildBase.DS.'tmpl'.DS.$scope.DS, '', $fileName);
                $subPack = ucfirst(substr($subPack, 0, strpos($subPack, DS)));
                $subPack = ($subPack) ? $subPack : 'Base';

                $this->replacements->_ECR_SUBPACKAGE_ = $subPack;

                $fContents = JFile::read($fileName);
                $fContents = $this->replace($fContents);

                $path = str_replace($this->buildBase.DS.'tmpl', $this->buildDir, $fileName);
                $path = str_replace('ecr_comname', strtolower($this->project->name), $path);
                $path = str_replace('_ecr_list_postfix', strtolower($this->project->listPostfix), $path);

                if(! JFile::write($path, $fContents))
                    throw new EcrBuilderException(sprintf(jgettext('Can not write the file at %s'), $path));

                $this->logger->logFileWrite($fileName, $path, $fContents);
            }
        }

        return $this;
    }

    /**
     * Process additional options.
     *
     * @throws EcrBuilderException
     * @return EcrProjectBuilder
     */
    private function processMoreOptions()
    {
        if(! JRequest::getVar('create_changelog'))
        {
            //-- No changelog requested
            return $this;
        }

        $changelog = $this->replace(
            JFile::read(ECRPATH_PARTS.DS.'various'.DS.'changelog'.DS.'tmpl'.DS.'CHANGELOG.php'));

        switch($this->project->type)
        {
            case 'plugin':
                $fileName = $this->project->comName.'_CHANGELOG.php';
                break;

            case 'library':
                $fileName = $this->project->comName.'_CHANGELOG.php';
                break;

            default:
                $fileName = 'CHANGELOG.php';
                break;
        }

        if(JFolder::exists($this->buildDir.DS.'admin'))
        {
            $path = $this->buildDir.DS.'admin';
        }
        else if(JFolder::exists($this->buildDir.DS.'site'))
        {
            $path = $this->buildDir.DS.'site';
        }
        else
        {
            throw new EcrBuilderException('No suiteable path found for CHANGELOG in '.$this->buildDir);
        }

        if(JFile::write($path.DS.$fileName, $changelog))
        {
            $this->logger->logFileWrite('', $path.DS.$fileName, $changelog);
        }
        else
        {
            throw new EcrBuilderException('Cannot create CHANGELOG');
        }

        return $this;
    }

    /**
     * Create the Joomla! manifest.
     *
     * @throws EcrBuilderException
     * @return EcrProjectBuilder
     */
    private function createJoomlaManifest()
    {
        $manifest = new EcrProjectManifest;

        $this->project->isNew = true;
        $this->project->basepath = $this->buildDir;
        $this->project->creationDate = date('d-M-Y');

        if($manifest->create($this->project))
        {
            $this->logger->logFileWrite('', $this->buildDir.DS.'manifest.xml', $manifest->formatXML());
        }
        else
        {
            throw new EcrBuilderException('Error creating manifest file: '
                .implode("\n", $manifest->getErrors()));
        }

        return $this;
    }

    /**
     * Create the EasyCreator manifest.
     *
     * @throws EcrBuilderException
     * @return boolean true on success
     */
    private function createEasyCreatorManifest()
    {
        if($this->project->type == 'plugin'
            || $this->project->type == 'module'
            || $this->project->type == 'library'
        )
        {
            $this->project->name = ucfirst($this->project->scope).' - '.$this->project->name;
        }

        $xmlContents = $this->project->writeProjectXml($this->testMode);

        if(false == $xmlContents)
            throw new EcrBuilderException('Unable to create EasyCreator manifest');

        $this->logger->log('EasyCreator manifest created');
        $this->logger->logFileWrite('', 'ECR'.DS.'EasyCreatorManifest.xml', $xmlContents);

        return $this;
    }

    /**
     * Installs an extension with the standard Joomla! installer.
     *
     * @throws EcrBuilderException
     * @return EcrProjectBuilder
     */
    private function install()
    {
        if($this->testMode)
        {
            //-- Exiting in test mode
            $this->logger->log('TEST MODE - not installing');

            return $this;
        }

        if('cliapp' == $this->project->type
            || 'webapp' == $this->project->type
        )
        {
            $src = $this->buildDir.'/site';
            $dest = $this->project->getExtensionPath(); // JPATH_COMPONENT_ADMINISTRATOR.'/cliapps/'.$this->project->comName;

            if(! JFolder::copy($src, $dest))
                throw new EcrBuilderException(
                    sprintf('Failed to copy the JApplication from %s to %s', $src, $dest));

            $this->logger->log(
                sprintf('JApplication files copied from %s to %s', $src, $dest));

            $src = $this->buildDir.DS.$this->project->getJoomlaManifestName();
            $dest = $this->project->getJoomlaManifestPath().DS.$this->project->getJoomlaManifestName();

            if(! JFile::copy($src, $dest))
                throw new EcrBuilderException(
                    sprintf('Failed to copy package manifest xml from %s to %s', $src, $dest));

            return $this;
        }

        if('package' == $this->project->type)
        {
            //-- J! 1.6 package - only copy the manifest xml
            $src = $this->buildDir.DS.$this->project->getJoomlaManifestName();
            $dest = $this->project->getJoomlaManifestPath().DS.$this->project->getJoomlaManifestName();

            if(! JFile::copy($src, $dest))
                throw new EcrBuilderException(
                    sprintf('Failed to copy package manifest xml from %s to %s', $src, $dest));

            $this->logger->log(
                sprintf('Package manifest xml has been copied from %s to %s', $src, $dest));

            return $this;
        }

        jimport('joomla.installer.installer');
        jimport('joomla.installer.helper');

        $this->logger->log('Starting Install');

        //-- Did you give us a valid package ?
        if(! $type = JInstallerHelper::detectType($this->buildDir))
            throw new EcrBuilderException(jgettext('Path does not have a valid package'));

        //-- Get an installer instance
        $installer = JInstaller::getInstance();

        //-- Install the package
        $result = $installer->install($this->buildDir);

        $this->logger->log('Installer Message: '.$installer->message);
        $this->logger->log('Extension Message: '.$installer->get('extension.message'));

        //-- Clean up the install directory. If we are not debugging.
        ECR_DEBUG ? null : JInstallerHelper::cleanupInstall('', $this->buildDir);

        //-- There was an error installing the package
        if(! $result)
            throw new EcrBuilderException(sprintf(jgettext('An error happened while installing your %s'), jgettext($type)));

        return $this;
    }

    /**
     * Process custom otions.
     *
     * @param string         $action  The action to perform
     * @param EcrProjectBase $project The project
     *
     * @return mixed [array custom options | boolean false on error]
     */
    public function customOptions($action = 'display', EcrProjectBase $project = null)
    {
        static $templateOptions = null;

        if(! $templateOptions)
        {
            $tplType = JRequest::getVar('tpl_type');
            $tplName = JRequest::getVar('tpl_name');

            $template_path = ECRPATH_EXTENSIONTEMPLATES.DS.$tplType.DS.$tplName;

            if(! JFile::exists($template_path.DS.'options.php'))
            {
                if($action == 'requireds')
                    return array();

                if($action == 'process')
                    return true;

                return false;
            }

            include_once $template_path.DS.'options.php';

            if(! class_exists('EasyTemplateOptions'))
            {
                echo sprintf(jgettext('Required class %s not found'), 'EasyTemplateOptions');

                if($action == 'requireds')
                    return array();

                return false;
            }

            $templateOptions = new EasyTemplateOptions;
        }

        switch($action)
        {
            case 'display':
                if(! method_exists('EasyTemplateOptions', 'displayOptions'))
                {
                    echo sprintf(jgettext('Required method %s not found'), 'displayOptions');

                    return false;
                }

                echo '<div class="ecrBigInfo">';
                echo '<h3>'.jgettext('Custom options').'</h3>';
                echo '</div>';

                echo $templateOptions->displayOptions($project);

                echo '<br /><br />';
                break;
            case 'process':
                if(! method_exists('EasyTemplateOptions', 'processOptions'))
                {
                    echo sprintf(jgettext('Required method %s not found'), 'processOptions');

                    return false;
                }

                return $templateOptions->processOptions($this);
                break;
            case 'requireds':
                if(! method_exists('EasyTemplateOptions', 'getRequireds'))
                {
                    echo sprintf(jgettext('Required method %s not found'), 'getRequireds');

                    return array();
                }

                return $templateOptions->getRequireds();
                break;

            default:
                echo sprintf(jgettext('Action %s not defined'), $action);

                return false;
                break;
        }

        return true;
    }

    /**
     * Register an existing project.
     *
     * @param string $type  Project type
     * @param string $name  Project name
     * @param string $scope Project scope e.g. admin, site
     *
     * @return EcrProjectBase on success | false on error
     */
    public function registerProject($type, $name, $scope = '')
    {
        //--Get component parameters
        $comParams = JComponentHelper::getParams('com_easycreator');

        //-- Setup logging
        $options = array();
        $opts = array('logging', 'hotlogging', 'files', 'profile');

        foreach($opts as $o)
        {
            if($comParams->get($o))
            {
                $options[] = $o;
            }
        }

        $options['fileName'] = date('ymd_Hi').'_register.log';

        $this->logger = EcrLogger::getInstance('ecr', $options);

        if(! array_key_exists($type, EcrProjectHelper::getProjectTypes()))
        {
            JFactory::getApplication()->enqueueMessage(sprintf(jgettext('The project type %s is not defined yet'), $type), 'error');
            $this->setError(sprintf(jgettext('The project type %s is not defined yet'), $type));

            return false;
        }

        $project = EcrProjectHelper::newProject($type);

        $project->comName = $name;
        $project->scope = $scope;

        foreach(EcrEasycreator::$packFormats as $name => $ext)
        {
            if($comParams->get($name))
            {
                $project->buildOpts[$name] = 'ON';
            }
        }

        for($i = 1; $i < 5; $i ++)
        {
            $project->buildOpts['custom_name_'.$i] = $comParams->get('custom_name_'.$i);
        }

        //-- Set the Joomla! compatibility version to the version we are actually running on
        $project->JCompat = ECR_JVERSION;

        if(! $xmlPath = EcrProjectHelper::findManifest($project))
        {
            JFactory::getApplication()->enqueueMessage(jgettext('No manifest file found'), 'error');

            $this->setError(jgettext('No manifest file found'));

            return false;
        }

        $data = EcrProjectHelper::parseXMLInstallFile(JPATH_ROOT.DS.$xmlPath);

        $project->name = $data->name;

        $this->logger->log('Registering: '.$project->type.' - '.$project->name.'<br />'
            .'aka "'.$project->comName.'"<br />scope: '.$project->scope);

        $pXml = $project->writeProjectXml();

        if(false == $pXml)
        {
            $this->logger->log('', 'Unable to create EasyCreator manifest');
            $this->logger->writeLog();

            return false;
        }
        else
        {
            $this->logger->log('EasyCreator manifest created');
            $this->logger->logFileWrite('', '', $pXml);
        }

        $this->logger->log('FINISHED');
        $this->logger->writeLog();

        return $project;
    }

    /**
     * Read the header file.
     *
     * @return EcrProjectBuilder
     */
    private function readHeader()
    {
        $types = array('', 'js', 'css');

        $format = JRequest::getCmd('optHeader', 'git');

        foreach($types as $type)
        {
            $path = ECRPATH_EXTENSIONTEMPLATES.'/std/header/'.$format.'/header'.$type.'.txt';

            if(! JFile::exists($path))
                continue;

            $header = JFile::read($path);

            $this->replacements->addCustomPrio('##*HEADER'.strtoupper($type).'*##', $header);
        }

        return $this;
    }

    /**
     * Replaces tags in text from substitutes array.
     *
     * @param string $text The text to process
     *
     * @return string substituted string
     */
    private function replace($text)
    {
        foreach($this->replacements->getReplacements() as $k => $v)
        {
            $text = str_replace($k, $v, $text);
        }

        return $text;
    }

    /**
     * Print out HTML error list.
     *
     * @return string html
     */
    public function printErrors()
    {
        if(count($this->_errors))
        {
            echo '<h4 style="color: red;">ERRORS !</h4>';
            echo '<ul>';

            foreach($this->_errors as $entry)
            {
                echo '<li>'.$entry.'</li>';
            }

            echo '</ul>';
        }
        else
        {
            echo '<h4 style="color: green;">No errors...</h4>';
        }

        return true;
    }

    /**
     * HTML log output.
     *
     * @return string html
     */
    public function printLog()
    {
        return $this->logger->printLog();
    }
}//class
