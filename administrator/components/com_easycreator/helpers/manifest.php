<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers
 *
 * @author  Ian McLennan
 * @former  package    IansTools - by Ian McLennan
 * @former  subpackage ManifestMaker
 * @license GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Manifest builder.
 *
 * FULL Credits to Ian McLennan =;)
 *
 * @package EasyCreator
 * @former-package IansTools
 */
class EcrManifest extends JObject
{
    /**
     * @var SimpleXMLElement
     */
    private $manifest = null;

    /**
     * @var EcrProject
     */
    private $project = null;

    /**
     * Constructor.
     *
     * @access  public
     */
    public function __construct()
    {
    }//function

    /**
     * Method to create the manifest file.
     *
     * @param EcrProject $project The project.
     *
     * @return boolean true on success
     */
    public function create(EcrProject $project)
    {
        if( ! $project->type)
        {
            $this->setError(__METHOD__.' - Invalid project given');

            return false;
        }

        $this->project = $project;

        $rootTag = '';

        switch($this->project->JCompat)
        {
            case '1.6':
            case '1.7':
            case '2.5':
                $rootTag = 'extension';
                break;

            case '1.5':
                $rootTag = 'install';
                break;
            default:
                JFactory::getApplication()->enqueueMessage(
                    __METHOD__.'Unknown JCompat: '.$this->project->JCompat, 'error');
                break;
        }//switch

        $this->manifest = new EcrXMLElement('<?xml version="1.0" encoding="utf-8" ?><'.$rootTag.' />');

        if( ! $this->manifest instanceof EcrXMLElement)
        {
            $this->setError('Could not create XML builder');

            return false;
        }

        try
        {
            $this->setUp()
            ->processCredits()
            ->processInstall()
            ->processUpdates()
            ->processSite()
            ->processAdmin()
            ->processMedia()
            ->processPackageModules()
            ->processPackagePlugins()
            ->processPackageElements()
            ->processParameters()
            ;
        }
        catch(Exception $e)
        {
            EcrHtml::displayMessage($e);

            return false;
        }

        if($this->project->isNew)
        {
            //--New project
            $path = JPath::clean($this->project->basepath
                .'/'.$this->project->getJoomlaManifestName());
        }
        else
        {
            //--Building project
            $path = JPath::clean($this->project->basepath
                .'/'.JFile::getName(EcrProjectHelper::findManifest($this->project)));
        }

        $xml = $this->formatXML();

        if( ! JFile::write($path, $xml))
        {
            $this->setError('Could not save XML file!');

            return false;
        }

        return true;
    }//function

    /**
     * Setup the manifest building process.
     *
     * @return EcrManifest
     */
    private function setUp()
    {
        $buildVars = JRequest::getVar('buildvars', array());

        $this->manifest->addAttribute('type', $this->project->type);
        $this->manifest->addAttribute('version', $this->project->JCompat);

        //-- Default 'method'
        $method = $this->project->method;

        //-- Method value from request overrides default 'method'
        if(array_key_exists('method', $buildVars))
        {
            $method = $buildVars['method'];
        }

        if($method)
        {
            $this->manifest->addAttribute('method', $method);
        }

        switch($this->project->type)
        {
            case 'component':
                break;
            case 'module':
            case 'template':
                if($this->project->scope == 'admin')
                {
                    $this->manifest->addAttribute('client', 'administrator');
                }
                break;
            case 'plugin':
                $this->manifest->addAttribute('group', $this->project->scope);
                break;
            case 'library' :
                $this->manifest->addChild('libraryname', $this->project->comName);
                break;

            case 'package':
                $this->manifest->addChild('packagename', strtolower($this->project->name));
                break;
            default :
                throw new Exception(__METHOD__.' - unknown project type: '.$this->project->type);
                break;
        }//switch

        return $this;
    }//function

    /**
     * Process credit vars.
     *
     * @return boolean
     */
    private function processCredits()
    {
        $creditElements = array(
        'name'
        , 'creationDate'
        , 'author'
        , 'authorEmail'
        , 'authorUrl'
        , 'copyright'
        , 'license'
        , 'version'
        , 'description'
        );

        foreach($creditElements as $credit)
        {
            $value =(isset($this->project->$credit)) ? $this->project->$credit : '';
            $this->manifest->addChild($credit, $value);
        }//foreach

        //-- Special treatment for plugin names
        if($this->project->type == 'plugin'
        && $this->project->isNew)
        {
            $this->manifest->name = ucfirst($this->project->scope).' - '.$this->project->name;
        }

        return $this;
    }//function

    /**
     * Process install section.
     *
     * @return boolean
     */
    private function processInstall()
    {
        if($this->project->type != 'component')
        {
            //-- Only components have install files
            //-- @todo change for 1.6 + ?
            return $this;
        }

        $installFiles = EcrProjectHelper::findInstallFiles($this->project);

        //-- PHP install scripts

        switch($this->project->JCompat)
        {
            case '1.5':
                $this->manifest->addChild('installfile');
                $this->manifest->addChild('uninstallfile');
                break;

            case '1.6':
            case '1.7':
                $this->manifest->addChild('scriptfile');
                break;

            default:
                break;
        }//switch

        //-- SQL install scripts
        $install = $this->manifest->addChild('install');
        $installSql = $install->addChild('sql');
        $uninstall = $this->manifest->addChild('uninstall');
        $uninstallSql = $uninstall->addChild('sql');

        switch($this->project->JCompat)
        {
            case '1.6':
            case '1.7':
                //-- J! 1.6 update stuff
                $update = $this->manifest->addChild('update');
                $updateSql = $update->addChild('schemas');

                //-- SQL updates
                if(count($installFiles['sql_updates']))
                {
                    foreach($installFiles['sql_updates'] as $file)
                    {
                        $schema = $updateSql->addChild('schemapath', $file->folder.'/'.$file->name);
                        $schema->addAttribute('type', $file->name);
                    }//foreach
                }

                break;

            default:
                break;
        }//switch

        //-- PHP
        if(count($installFiles['php']))
        {
            if(count($installFiles['php']) > 2)
                throw new Exception(__METHOD__.' - Too many PHP install/uninstall files ('.count($installFiles['php']).')');

            foreach($installFiles['php'] as $file)
            {
                $dir =($file->folder) ? $file->folder.'/' : '';
                $dir = str_replace('\\', '/', $dir);

                if(strpos($file->name, 'install') === 0)
                {
                    //-- Install
                    $this->manifest->installfile = $dir.$file->name;
                }
                else if(strpos($file->name, 'uninstall') === 0)
                {
                    //-- Uninstall
                    $this->manifest->uninstallfile = $dir.$file->name;
                }
                else if(strpos($file->name, 'script') === 0)
                {
                    //-- J 1.6 script file
                    $this->manifest->scriptfile = $dir.$file->name;
                }
                else
                {
                    throw new Exception(__METHOD__.' - Unsupported php file: '.$file->name);
                }
            }//foreach
        }

        //-- SQL
        if(count($installFiles['sql']))
        {
            foreach($installFiles['sql'] as $file)
            {
                $dir =($file->folder) ? $file->folder.'/' : '';
                $dir = str_replace('\\', '/', $dir);

                if(strpos($file->name, 'install') === 0)
                {
                    //--Install
                    $sFile = $installSql->addChild('file', $dir.$file->name);
                }

                else if(strpos($file->name, 'uninstall') === 0)
                {
                    //--Uninstall
                    $sFile = $uninstallSql->addChild('file', $dir.$file->name);
                }

                else if(strpos($file->name, 'update') === 0)
                {
                    //-- Update
                    $sFile = $updateSql->addChild('file', $dir.$file->name);
                }

                else
                {
                    throw new Exception(__METHOD__.' - Unsupported sql file: '.$file->name);
                }

                $parts = explode('/', $dir);
                array_pop($parts);
                $driver = array_pop($parts);

                $sFile->addAttribute('driver', $driver);

                if(false == strpos($file->name, 'nonutf')
                && false == strpos($file->name, 'compat'))
                {
                    $sFile->addAttribute('charset', 'utf8');
                }
            }//foreach
        }

        return $this;
    }//function

    /**
     * Process Updates.
     *
     * @return bool
     */
    private function processUpdates()
    {
        if( ! count($this->project->updateServers))
        return $this;

        //-- Update site
        $updateServers = $this->manifest->addChild('updateservers');

        foreach($this->project->updateServers as $server)
        {
            $sElement = $updateServers->addChild('server', $server->url);

            $sElement->addAttribute('name', $server->name);
            $sElement->addAttribute('type', $server->type);
            $sElement->addAttribute('priority', $server->priority);
        }//foreach

        return $this;
    }//function

    /**
     * Process media section.
     *
     * @return boolean
     */
    private function processMedia()
    {
        $baseFolders = JFolder::folders($this->project->basepath);

        if( ! in_array('media', $baseFolders))
        return $this;

        $folders = JFolder::folders($this->project->basepath.DS.'media');
        $files = JFolder::files($this->project->basepath.DS.'media');

        if( ! count($folders)
        && ! count($files))
        return $this;

        $mediaElement = $this->manifest->addChild('media');
        $mediaElement->addAttribute('destination', $this->project->comName);
        $mediaElement->addAttribute('folder', 'media');

        foreach($folders as $folder)
        {
            $mediaElement->addChild('folder', $folder);
        }//foreach

        foreach($files as $file)
        {
            $mediaElement->addChild('filename', $file);
        }//foreach

        return $this;
    }//function

    /**
     * Process site section.
     *
     * @return boolean
     */
    private function processSite()
    {
        $folders = JFolder::folders($this->project->basepath);
        $languageFiles = array();

        if(in_array('site', $folders))
        {
            if(count(JFolder::files($this->project->basepath.DS.'site', '.', true, false)))
            {
                $siteFolders = JFolder::folders($this->project->basepath.DS.'site');
                $siteFiles = JFolder::files($this->project->basepath.DS.'site');

                if(JFolder::exists($this->project->basepath.DS.'site'.DS.'language'))
                {
                    $languageFiles = JFolder::files($this->project->basepath.DS.'site'.DS.'language', '', true, true);
                }

                $siteFileElement = $this->manifest->addChild('files');
                $siteFileElement->addAttribute('folder', 'site');

                foreach($siteFolders as $siteFolder)
                {
                    $siteFileElement->addChild('folder', $siteFolder);
                }//foreach

                foreach($siteFiles as $siteFile)
                {
                    $siteElement = $siteFileElement->addChild('filename', $siteFile);

                    if($this->project->type == 'plugin'
                    || $this->project->type == 'module')
                    {
                        $s = JFile::stripExt($siteFile);

                        if($s == $this->project->comName)
                        {
                            $siteElement->addAttribute($this->project->type, $s);
                        }
                    }
                }//foreach
            }
        }

        if(count($languageFiles)
        && $this->project->JCompat == '1.5')
        {
            $languagesElement = $this->manifest->addChild('languages');
            $languagesElement->addAttribute('folder', 'site/language');
            $substrlen = strlen($this->project->basepath.DS.'site'.DS.'language'.DS);

            foreach($languageFiles as $languageFile)
            {
                $t = str_replace(DS, '/', substr($languageFile, $substrlen));
                $languageElement = $languagesElement->addChild('language', $t);
                $languageElement->addAttribute('tag', substr(basename($languageFile), 0, 5));
            }//foreach
        }

        return $this;
    }//function

    /**
     * Process admin section.
     *
     * @return boolean
     */
    private function processAdmin()
    {
        $basepath = $this->project->basepath;
        $folders = JFolder::folders($basepath);
        $mediaFiles = array();

        if( ! in_array('admin', $folders))
        {
            return $this;
        }

        if($this->project->type == 'component')
        {
            $administration = $this->manifest->addChild('administration');

            //-- Build the menu
            $def_menu =(isset($this->project->menu)) ? $this->project->menu : '';

            if($def_menu)
            {
                $menu = $administration->addChild('menu', $def_menu['text']);

                if(1.5 == ECR_JVERSION)
                {
                    $menu->addAttribute('img', $def_menu['img']);
                }
                else
                {
                    $s = $def_menu['img'];

                    if($s
                    && false == strpos($s, '/'))
                    {
                        $s = 'class:'.$s;
                    }

                    $menu->addAttribute('img', $s);
                }

                if(isset($def_menu['link'])
                && $this->project->JCompat == '1.5')
                {
                    //-- Admin menu link only for J 1.5
                    $menu->addAttribute('link', $def_menu['link']);
                }
            }
            else
            {
                $menu = $administration->addChild('menu', $this->project->name);
            }

            if(isset($this->project->submenu) && count($this->project->submenu))
            {
                $submenu = $administration->addChild('submenu');

                foreach($this->project->submenu as $item)
                {
                    $menu = $submenu->addChild('menu', $item['text']);

                    if(1.5 == ECR_JVERSION)
                    {
                        $menu->addAttribute('img', $item['img']);
                    }
                    else
                    {
                        $s = $item['img'];

                        if($s
                        && false == strpos($s, '/'))
                        {
                            $s = 'class:'.$s;
                        }

                        $menu->addAttribute('img', $s);
                    }

                    switch($this->project->JCompat)
                    {
                        case '1.5' :
                            $menu->addAttribute('link', $item['link']);
                            break;
                        case '1.6' :
                        case '1.7' :
                            //-- 'index.php?' from the link
                            $menu->addAttribute('link', str_replace('index.php?', '', $item['link']));
                            break;
                        default :

                            break;
                    }//switch
                }//foreach
            }
        }

        $adminFolders = JFolder::folders($basepath.DS.'admin');
        $adminFiles = JFolder::files($basepath.DS.'admin');

        $languageFiles = array();

        if(JFolder::exists($basepath.DS.'admin'.DS.'language'))
        {
            $languageFiles = JFolder::files($basepath.DS.'admin'.DS.'language', '', true, true);
        }

        if(JFolder::exists($basepath.DS.'admin'.DS.'media'))
        {
            $mediaFiles = JFolder::files($basepath.DS.'admin'.DS.'media', '', true, true);
        }

        if($this->project->type == 'component')
        {
            $adminFileElement = $administration->addChild('files');
        }
        else
        {
            $adminFileElement = $this->manifest->addChild('files');
        }

        $adminFileElement->addAttribute('folder', 'admin');

        foreach($adminFolders as $adminFolder)
        {
            $adminFileElement->addChild('folder', $adminFolder);
        }//foreach

        foreach($adminFiles as $adminFile)
        {
            $adminElement = $adminFileElement->addChild('filename', $adminFile);

            if($this->project->type == 'module')
            {
                $s = JFile::stripExt($adminFile);

                if($s == $this->project->comName)
                {
                    $adminElement->addAttribute('module', $s);
                }
            }
        }//foreach

        if(count($languageFiles)
        && $this->project->JCompat == '1.5')
        {
            //-- We only need language file entries for J 1.5
            if($this->project->type == 'component')
            {
                $languagesElement = $administration->addChild('languages');
            }
            else
            {
                $languagesElement = $this->manifest->addChild('languages');
            }

            $languagesElement->addAttribute('folder', 'admin/language');
            $substrlen = strlen($basepath.DS.'admin'.DS.'language'.DS);

            foreach($languageFiles as $languageFile)
            {
                $t = str_replace(DS, '/', substr($languageFile, $substrlen));
                $languageElement = $languagesElement->addChild('language', $t);
                $languageElement->addAttribute('tag', substr(basename($languageFile), 0, 5));
            }//foreach
        }

        if(count($mediaFiles))
        {
            $mediaElement = $administration->addChild('media');
            $mediaElement->addAttribute('folder', 'admin/media');
            $substrlen = strlen($basepath.DS.'admin'.DS.'media'.DS);

            foreach($mediaFiles as $mediaFile)
            {
                $t = str_replace(DS, '/', substr($mediaFile, $substrlen));
                $medElement = $mediaElement->addChild('filename', $t);
            }//foreach
        }

        return $this;
    }//function

    /**
     * Process package elements for J! 1.6 packages.
     *
     * @return boolean true on success
     */
    private function processPackageElements()
    {
        if($this->project->type != 'package')
        return $this;

        $filesElement = $this->manifest->addChild('files');

        foreach($this->project->elements as $element => $path)
        {
            //--Get the project
            $project = EcrProjectHelper::getProject($element);

            $fileElement = $filesElement->addChild('file', $path);
            $fileElement->addAttribute('type', $project->type);
            $fileElement->addAttribute('id', $element);
        }//foreach

        return $this;
    }//function

    /**
     * Process parameters section.
     *
     * @return boolean
     */
    private function processParameters()
    {
        if($this->project->isNew)
        {
            //-- No parameters for new projects
            if('template' != $this->project->type)
            return $this;

            //-- Except for templates :(
            $path = $this->project->buildPath;

            $path .= '/site';//@todo admin templates ?

            $fileName = $path.'/templateDetails.xml';

            if( ! JFile::exists($fileName))
            return $this;

            $refXml = EcrProjectHelper::getXML($fileName);

            $params = $this->manifest->addChild('params');
            $this->appendXML($params, $refXml->params);

            $config = $this->manifest->addChild('config');
            $this->appendXML($config, $refXml->config);

            $positions = $this->manifest->addChild('positions');
            $this->appendXML($positions, $refXml->positions);

            return $this;
        }

        //-- Search if there is a config.xml
        $fileName = EcrProjectHelper::findConfigXML($this->project->type, $this->project->comName);

        if($fileName)
        {
            $cfgXml = EcrProjectHelper::getXML($fileName);

            if( ! $cfgXml
            || ! $cfgXml->params)
            {
                return $this;
            }

            $paramsElement = $this->manifest->addChild('params');

            foreach($cfgXml->params as $cfgParams)
            {
                foreach($cfgParams->param as $cfgParam)
                {
                    if($cfgParam->attributes()->type == 'spacer'
                    || $cfgParam->attributes()->type == 'easyspacer')
                    {
                        continue;
                    }

                    if($cfgParam->attributes()->default
                    && $cfgParam->attributes()->default != '0')
                    {
                        $p = $paramsElement->addChild('param');
                        $p->addAttribute('name', $cfgParam->attributes()->name);
                        $p->addAttribute('type', $cfgParam->attributes()->type);
                        $p->addAttribute('label', $cfgParam->attributes()->label);
                        $p->addAttribute('default', $cfgParam->attributes()->default);
                    }
                }//foreach
            }//foreach
        }
        else if(JFile::exists(JPath::clean(JPATH_ROOT.DS.EcrProjectHelper::findManifest($this->project))))
        {
            $refXml = EcrProjectHelper::getXML(JPath::clean(JPATH_ROOT.DS.EcrProjectHelper::findManifest($this->project)));

            $params = $this->manifest->addChild('params');
            $this->appendXML($params, $refXml->params);

            $config = $this->manifest->addChild('config');
            $this->appendXML($config, $refXml->config);

            if('template' == $this->project->type)
            {
                switch($this->project->JCompat)
                {
                    case '1.5':
                        break;
                    case '1.6':
                    case '1.7':
                        $positions = $this->manifest->addChild('positions');
                        $this->appendXML($positions, $refXml->positions);
                        break;

                    default:
                        EcrHtml::displayMessage(__METHOD__.' : Undefined JVersion', 'error');
                        break;
                }//switch
            }

            return $this;

            //            //-- Try the install manifest.xml
            //            $params = new JParameter('', JPath::clean(JPATH_ROOT.DS
            //            .EcrProjectHelper::findManifest($this->project)));
            //
            //            if( ! $params->getNumParams()) return true;
            //
            //            /**
            //             * @todo CHANGE for 1.6 - URGENT !!!! =;)
            //             */
            //
            //            if( ! $params->_xml)
            //            {
            //                return true;
            //            }
            //
            //            foreach($params->_xml as $groupName => $group)
            //            {
            //                $paramsElement = $this->manifest->addChild('params');
            //
            //                if($groupName != '_default')
            //                {
            //                    $paramsElement->addAttribute('group', $groupName);
            //                }
            //
            //                foreach($group->_children as $param)
            //                {
            //                    if( ! $param instanceof JSimpleXMLElement)
            //                    {
            //                        continue;
            //                    }
            //                    $paramElement = $paramsElement->addChild('param');
            //
            //                    foreach($param->_attributes as $k => $v)
            //                    {
            //                        $paramElement->addAttribute($k, $v);
            //                    }//foreach
            //
            //                    foreach($param->_children as $child)
            //                    {
            //                        if($child instanceof JSimpleXMLElement)
            //                        {
            //                            $optionElement = $paramElement->addChild('option', $child->_data);
            //                            $optionElement->addAttribute('value', $child->_attributes['value']);
            //                        }
            //                    }//foreach
            //                }//foreach
            //            }//foreach
        }

        return $this;
    }//function

    /**
     * Add one simplexml to another.
     *
     * @param object &$to Object to add to
     * @param object &$from Object to add from
     *
     * @author Boris Korobkov
     * @link http://www.ajaxforum.ru/
     *
     * @return void
     */
    private function appendXML(&$to, &$from)
    {
        foreach($from->children() as $child)
        {
            $temp = $to->addChild($child->getName(), (string)$child);

            foreach($child->attributes() as $key => $value)
            {
                $temp->addAttribute($key, $value);
            }//foreach

            $this->appendXML($temp, $child);
        }//foreach
    }//function

    /**
     * Process modules in a package.
     *
     * @deprecated removed for J! 1.6
     *
     * @return boolean
     */
    private function processPackageModules()
    {
        if( ! count($this->project->modules))
            return $this;

        $modulesElement = $this->manifest->addChild('modules');

        foreach($this->project->modules as $module)
        {
            $s = str_replace('mod_', 'mod_'.$module->scope.'_', $module->name);

            //--Get the project
            $project = EcrProjectHelper::getProject($s);

            $modElement = $modulesElement->addChild('module');
            $modElement->addAttribute('module', $module->name);
            $modElement->addAttribute('title', $module->title);

            if($module->scope)
            {
                $s =($module->scope == 'admin') ? 'administrator' : $module->scope;
                $modElement->addAttribute('client', $s);
            }

            $modElement->addAttribute('position', $module->position);

            if($module->ordering)
            {
                $modElement->addAttribute('ordering', $module->ordering);
            }

            $filesElement = $modElement->addChild('files');
            $filesElement->addAttribute('folder', $module->name);

            foreach($project->copies as $copy)
            {
                if(JFolder::exists($copy))
                {
                    $folders = JFolder::folders($copy);
                    $files = JFolder::files($copy);

                    foreach($folders as $folder)
                    {
                        $filesElement->addChild('folder', $folder);
                    }//foreach

                    foreach($files as $file)
                    {
                        $filesElement->addChild('file', $file);
                    }//foreach
                }
                else if(JFile::exists($copy))
                {
                    $filesElement->addChild('file', JFile::getName($copy));
                }
                else
                {
                    //@todo error
                    $this->_addLog('Not found<br />SRC: '.$copy, 'FILE NOT FOUND');
                }
            }//foreach

            if(count($project->langs))
            {
                $langsElement = $modElement->addChild('languages');
                $langsElement->addAttribute('folder', $module->name.'/language');

                foreach($project->langs as $tag => $scopes)
                {
                    $fileElement = $langsElement->addChild('language', $tag.'.'.$project->getLanguageFileName());
                    $fileElement->addAttribute('tag', $tag);
                }//foreach
            }

            $paramsElement = $modElement->addChild('params');

            $path = JPATH_ROOT.DS.EcrProjectHelper::findManifest($project);

            $xml = EcrProjectHelper::getXML($path);

            if( ! $xml)
                throw new Exception(sprintf(jgettext('Unable to load the xml file %s'), $path));

            if(isset($xml->params->param))
            {
                /* @var SimpleXMLElement $param */
                foreach($xml->params->param as $param)
                {
                    $paramElement = $paramsElement->addChild('param');

                    foreach($param->attributes() as $name => $value)
                    {
                        $paramElement->addAttribute($name, (string)$value);
                    }//foreach

                    if(isset($param->option))
                    {
                        foreach($param->option as $option)
                        {
                            $optionElement = $paramElement->addChild('option', (string)$option);

                            foreach($option->attributes() as $name => $value)
                            {
                                $optionElement->addAttribute($name, (string)$value);
                            }//foreach
                        }//foreach
                    }
                }//foreach
            }
        }//foreach

        return $this;
    }//function

    /**
     * Process plugins in a package.
     *
     * @deprecated removed for J! 1.6
     *
     * @return boolean
     */
    private function processPackagePlugins()
    {
        if( ! count($this->project->plugins))
        {
            return $this;
        }

        $pluginsElement = $this->manifest->addChild('plugins');

        foreach($this->project->plugins as $item)
        {
            //--Get the project
            $project = EcrProjectHelper::getProject('plg_'.$item->scope.'_'.$item->name);

            $f = JPATH_ROOT.DS.EcrProjectHelper::findManifest($project);

            $plgElement = $pluginsElement->addChild('plugin');
            $plgElement->addAttribute('plugin', $item->name);
            $plgElement->addAttribute('group', $item->scope);
            $plgElement->addAttribute('title', $item->title);

            if($item->ordering)
            {
                $plgElement->addAttribute('order', $item->ordering);
            }

            $plgFilesElement = $plgElement->addChild('files');
            $plgFilesElement->addAttribute('folder', 'plg_'.$item->scope.'_'.$item->name);

            foreach($project->copies as $copy)
            {
                if(JFolder::exists($copy))
                {
                    $tName = str_replace('plugins'.DS.$item->scope.DS, '', $copy);
                    $plgFolderElement = $plgFilesElement->addChild('folder', $tName);
                }
                else if(JFile::exists($copy))
                {
                    $plgFileElement = $plgFilesElement->addChild('file', JFile::getName($copy));
                }
                else
                {
                    //@todo error
                    $this->_addLog('Not found<br />SRC: '.$copy, 'FILE NOT FOUND');
                }
            }//foreach

            if(count($project->langs))
            {
                $plgLangsElement = $plgElement->addChild('languages');
                $plgLangsElement->addAttribute('folder', 'plg_'.$item->scope.'_'.$item->name.'/language');

                foreach($project->langs as $tag => $scopes)
                {
                    $plgFileElement = $plgLangsElement->addChild('language', $tag.'.'.$project->getLanguageFileName());
                    $plgFileElement->addAttribute('tag', $tag);
                }//foreach
            }

            $xml = EcrProjectHelper::getXML($f);

            if(false == $xml)
            throw new Exception(sprintf(jgettext('Unable to load the xml file %s'), $f));

            $paramsElement = $plgElement->addChild('params');

            if(isset($xml->params->param))
            {
                foreach($xml->params->param as $param)
                {
                    $paramElement = $paramsElement->addChild('param');

                    foreach($param->attributes() as $name => $value)
                    {
                        $paramElement->addAttribute($name, (string)$value);
                    }//foreach

                    if(isset($param->option))
                    {
                        foreach($param->option as $option)
                        {
                            $optionElement = $paramElement->addChild('option', (string)$option);

                            foreach($option->attributes() as $Name => $Value)
                            {
                                $optionElement->addAttribute($Name, (string)$Value);
                            }//foreach
                        }//foreach
                    }
                }//foreach
            }
        }//foreach

        return $this;
    }//function

    /**
     * Formats SimpleXML.
     *
     * @return string XML
     */
    public function formatXML()
    {
        $errRep = ini_get('error_reporting');

        //-- DOMImplementation throws strict errors :(
        ini_set('error_reporting', 0);

	    $dtd = $this->project->getDTD(JVERSION);

        if($dtd)
        {
            $doctype = DOMImplementation::createDocumentType($dtd['type'], $dtd['public'], $dtd['uri']);
            $document = DOMImplementation::createDocument('', '', $doctype);
        }
        else
        {
            $this->setError('no DTD found for '.JVERSION.' - '.$this->project->type);
            $document = DOMImplementation::createDocument();
        }

        $domnode = dom_import_simplexml($this->manifest);

        $domnode = $document->importNode($domnode, true);
        $domnode = $document->appendChild($domnode);

        $document->encoding = 'utf-8';
        $document->formatOutput = true;

        ini_set('error_reporting', $errRep);

        return $document->saveXML();
    }//function
}//class
