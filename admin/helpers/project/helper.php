<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath
 * @author     Created on 05-Oct-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * EcrProject Helper.
 *
 * @package  EasyCreator
 */
class EcrProjectHelper
{
    /**
     * Gets known project types.
     *
     * @return array
     */
    public static function getProjectTypesTags()
    {
        $tags = array();

        /* @var EcrProjectBase $project */
        foreach(self::getProjectTypes() as $project)
        {
            $tags[$project->prefix] = $project->type;
        }

        return $tags;
    }

    /**
     * Get an EasyCreator project.
     *
     * @param string  $name   Project name.
     * @param boolean $reload Reload the project.
     *
     * @throws Exception
     * @return EcrProjectBase
     */
    public static function getProject($name = '', $reload = false)
    {
        static $projects = array();
        static $defaultName = '';

        if( ! $defaultName)
            $defaultName = JFactory::getApplication()->input->get('ecr_project');

        if( ! $name)
            $name = $defaultName;

        if( ! $name)
            throw new Exception(jgettext('Empty project name'));

        if(isset($projects[$name]) && ! $reload)
            return $projects[$name];

        $type = substr($name, 0, 4);

        //-- Internal - new, register
        if($type == 'ecr_')
            return '';

        $projectTypes = self::getProjectTypesTags();

        if( ! array_key_exists($type, $projectTypes))
            throw new Exception(sprintf('Invalid project type: %s ', $type));

        $className = 'EcrProjectType'.ucfirst($projectTypes[$type]);

        $project = new $className($name);

        if( ! $project->dbId)
        {
            //-- All projects *except packages* must be installed in the database
            if('package' != $project->type)
                throw new Exception(sprintf(jgettext('Project %s not found'), $name));
        }

        $projects[$name] = $project;

        return $projects[$name];
    }//function

    /**
     * Creates an empty project.
     *
     * @param string $type Project type (component, plugin, etc.)
     *
     * @return EcrProjectBase
     */
    public static function newProject($type)
    {
        $className = 'EcrProjectType'.ucfirst($type);

        return new $className;
    }//function

    /**
     * Findes the Joomla! install xml file for a given extension.
     *
     * @param EcrProjectBase $project The project
     *
     * @return mixed [boolean false on error | string path on success]
     */
    public static function findManifest(EcrProjectBase $project)
    {
        $path = $project->getJoomlaManifestPath();

        if( ! JFolder::exists($path))
        {
            return false;
        }

        //-- @Joomla!-version-check
        switch(ECR_JVERSION)
        {
            case '2.5':
            case '3.0':
            case '3.1':
            case '3.2':
            case '3.3':
            case '3.4':
                if('library' == $project->type
                || 'package' == $project->type)
                {
                    $xmlFiles = array($path.DS.$project->getJoomlaManifestName());
                }
                break;

            default:
                EcrHtml::message(__METHOD__.' - Unknown JVersion', 'error');

            return false;
            break;
        }

        if(empty($xmlFiles))
        {
            $xmlFiles = JFolder::files($path, '.xml$', false, true);
        }

        if(empty($xmlFiles))
        {
            return false;
        }

        //-- If at least one xml file exists
        foreach($xmlFiles as $fileName)
        {
            if( ! JFile::exists($fileName))
            {
                /*
                 JXXError::raiseWarning(100, 'File not found '.$fileName);
                EcrHtml::displayMessage('Unable to load XML file '.$fileName, 'error');
                */
                return false;
            }

            $xml = self::getXML($fileName);

            //-- Invalid XML file
            if( ! $xml)
            return false;

            if($xml->getName() == 'extension')
            {
                //-- Valid xml manifest found
                return str_replace(JPATH_ROOT.DS, '', JPath::clean($fileName));
            }
        }//foreach

        //-- None of the xml files found were valid install files
        EcrHtml::message(sprintf(jgettext('Manifest not found for type: %s - name: %s'), $project->type, $fileName), 'error');

        return false;
    }//function

    /**
     * Find a config XML file for a specific project type.
     *
     * @param string $type Extension type e.g. component or plugin
     * @param string $name Project name
     *
     * @return mixed [boolean false on error | string file name on success]
     */
    public static function findConfigXML($type, $name)
    {
        switch($type)
        {
            case 'component':
                $path = JPATH_ADMINISTRATOR.DS.'components'.DS.$name;
                break;

            default:

                return false;
            break;
        }//switch

        $xmlFiles = JFolder::files($path, '.xml$', true, true);

        if(empty($xmlFiles))
        {
            return false;
        }

        //-- If at least one xml file exists
        foreach($xmlFiles as $fileName)
        {
            $xml = self::getXML($fileName);

            if( ! $xml)
            {
                continue;
            }

            if($xml->getName() == 'config')
            {
                //-- Valid config xml found
                return $fileName;
            }
        }//foreach

        return false;
    }//function

    /**
     * Parse a Joomla! install xml file.
     *
     * @param string $path Path to xml file
     *
     * @return JXMLElement on success | false on error
     */
    public static function parseXMLInstallFile($path)
    {
        if( ! JFile::exists($path))
        {
            JFactory::getApplication()->enqueueMessage(sprintf(jgettext('File not found %s'), $path), 'error');

            return false;
        }

        //-- Read the file to see if it's a valid component XML file
        $manifest = self::getXML($path);

        if( ! $manifest instanceof SimpleXMLElement)
        {
            JFactory::getApplication()->enqueueMessage(sprintf(jgettext('Invalid manifest file %s'), $path), 'error');
            unset($manifest);

            return false;
        }

        /*
         * Check for a valid XML root tag.
        */
        if($manifest->getName() != 'extension')
        {
            EcrHtml::message(sprintf('Invalid install manifest at %s', $path), 'error');

            unset($manifest);

            return false;
        }

        return $manifest;
    }//function

    /**
     * Get a list of registered projects.
     *
     * @param string $type Project type
     *
     * @return array
     */
    public static function getProjectList($type = '')
    {
        static $projectList;

        if( ! $projectList)
        {
            $projectList = array();

            if( ! JFolder::exists(ECRPATH_SCRIPTS))
                return $projectList;

            $xmlfiles = JFolder::files(ECRPATH_SCRIPTS, '.xml$', true, true);

            if( ! $xmlfiles)
                return $projectList;

            foreach($xmlfiles as $fileName)
            {
                $xml = EcrProjectHelper::getXML($fileName);

                if( ! $xml)
                continue;

                if($xml->getName() != 'easyproject')
                continue;

                $p = new stdClass;
                $p->type = (string)$xml->attributes()->type;
                $p->name = (string)$xml->name;
                $p->comName = (string)$xml->comname;

                $p->scope = (string)$xml->attributes()->scope;
                $p->position = (string)$xml->position;
                $p->ordering = (string)$xml->ordering;

                $p->fileName = JFile::stripExt(JFile::getName($fileName));

                $projectList[$p->type][] = $p;
            }//foreach
        }

        if($type)
        {
            return (isset($projectList[$type])) ? $projectList[$type] : array();
        }

        return $projectList;
    }//function

    /**
     * Get a simple list of projects.
     *
     * @param string $type Project type
     * @param string $scope Project scope
     *
     * @return array
     */
    public static function getSimpleList($type, $scope)
    {
        static $list = array();

        $key = $type.$scope;

        if(isset($list[$key]))
        {
            return $list[$key];
        }

        $projects = self::getProjectList($type);
        $list[$key] = array();

        foreach($projects as $project)
        {
            if($project->scope == $scope)
            {
                $list[$key][] = $project->comName;
            }
        }//foreach

        return $list[$key];
    }//function

    /**
     * Get a list of known project types.
     *
     * @return array List of project types.
     */
    public static function getProjectTypes()
    {
        static $types = array();

        if(count($types))
            return $types;

        $list = JFolder::files(ECRPATH_HELPERS.'/project/type');

        foreach($list as $item)
        {
            if('empty.php' == $item)
                continue;

            $types[JFile::stripExt($item)] = self::newProject(JFile::stripExt($item));
        }

        return $types;
    }

    /**
     * Get a list of known project scopes.
     *
     * @return array
     */
    public static function getProjectScopes()
    {
        $scopes = array(
            'component' => ''
        , 'module' => 'admin,site'
        , 'plugin' => implode(',', JFolder::folders(JPATH_ROOT.DS.'plugins', '.', false, false, array('tmp', '.svn')))
        , 'template' => 'admin,site'
        , 'library' => '');

        return $scopes;
    }//function

    /**
     * Format a filename for a package file.
     *
     * @param EcrProjectBase $project The project
     * @param string $format The format to use
     *
     * @return string
     */
    public static function formatFileName(EcrProjectBase $project, $format)
    {
        $vcsRev = EcrHtml::getVersionFromCHANGELOG($project->comName, true);

        preg_match('%\*DATETIME(.*?)\*%', $format, $matches);

        if($matches && isset($matches[1]))
        {
            $format = str_replace($matches[0], date($matches[1]), $format);
        }

        $format = str_replace('*VCSREV*', $vcsRev, $format);
        $format = str_replace('*VERSION*', $project->version, $format);

        return $format;
    }//function

    /**
     * Get a list of all unregistered projects.
     *
     * @param string $type Project type e.g. component, plugin
     * @param string $scope Project scope e.g. admin, site etc.
     * @param boolean $showCore Set true to also show the Joomla! core Projects
     *
     * @return array
     */
    public static function getUnregisteredProjects($type = 'component', $scope = '', $showCore = false)
    {
        switch($type)
        {
            case 'component':
            case 'module':
            case 'plugin':
            case 'template':
            case 'library':
                break;

            default:
                JFactory::getApplication()->enqueueMessage('UNKNOWN TYPE: '.$type, 'error');

            return array();
            break;
        }//switch

        /* @var EcrProjectBase $project */
        $project = self::newProject($type);

        if($showCore)
        {
            $result = array_diff($project->getAllProjects($scope), self::getSimpleList($type, $scope));
        }
        else
        {
            $result = array_diff($project->getAllProjects($scope)
            , self::getSimpleList($type, $scope), $project->getCoreProjects($scope));
        }

        return $result;
    }//function

    /**
     * Get groups of template parts.
     *
     * @return array of folders
     */
    public static function getPartsGroups()
    {
        static $folders = array();

        if(count($folders))
        {
            return $folders;
        }

        $excludes = array('std', 'utl', '.svn');
        $folders = JFolder::folders(ECRPATH_PARTS, '.', false, false, $excludes);

        return $folders;
    }//function

    /**
     * Get template parts of a specific group.
     *
     * @param string $group Groups name
     *
     * @return array of folders
     */
    public static function getParts($group)
    {
        static $folders = array();

        if(isset($folders[$group]))
        {
            return $folders[$group];
        }

        $path = ECRPATH_PARTS.DS.$group;
        $excludes = array('.svn');
        $folders[$group] = JFolder::folders($path, '.', false, false, $excludes);

        return $folders[$group];
    }//function

    /**
     * Get a template part.
     *
     * @param string $group Group name
     * @param string $part Part name
     * @param string $element Element name
     * @param string $scope The scope e.g. admin, site etc.
     * @param boolean $edit Open the part to edit
     *
     * @return EcrProjectPart on success | null on error
     */
    public static function getPart($group, $part, $element, $scope, $edit = false)
    {
        $subDir =($edit) ? DS.'data' : '';

        $path = ECRPATH_PARTS.$subDir.DS.$group.DS.$part;
        $fileName = $path.DS.'part.php';

        if( ! JFile::exists($fileName))
        {
            EcrHtml::message(array(jgettext('File not found'), $fileName), 'error');

            return null;
        }

        require_once $fileName;

        $className = 'part'.ucfirst($group).ucfirst($part);

        if( ! class_exists($className))
        {
            EcrHtml::message(array(jgettext('Class not found'), $className), 'error');

            return null;
        }

        $part = new $className($element, $scope, $path);

        return $part;
    }//function

    /**
     * Finds PHP and SQL install files.
     *
     * @param EcrProjectBase $project The project
     *
     * @return array Object array
     */
    public static function findInstallFiles(EcrProjectBase $project)
    {
        $installFiles = array();
        $installFiles['php'] = array();
        $installFiles['sql'] = array();
        $installFiles['sql_updates'] = array();

        if($project->type != 'component')
        {
            //-- Only components can have install files..
            //-- @todo change in 1.6
            return $installFiles;
        }

        if($project->buildPath)
        {
            //-- If $project->buildPath is set we are building a NEW project)
            if(JFolder::exists($project->buildPath.'/install'))
            {
                $base = $project->buildPath.DS.'install';
            }
            else
            {
                $base = $project->buildPath.DS.'admin';
            }
        }
        else
        {
            //-- Look in J!s component directory
            $base = JPath::clean(JPATH_ADMINISTRATOR.'/components/'.$project->comName);
        }

        if( ! JFolder::exists($base))
        {
            EcrHtml::message(array(sprintf(jgettext('Project %s not found'), $project->comName)), 'error');

            return $installFiles;
        }

        //-- Look in components 'root'
        $files = JFolder::files($base, '(^install|^uninstall|^update|^script)([\.a-z0-9])+(sql$|php$|xml$)');

        foreach($files as $file)
        {
            $f = new stdClass;
            $f->folder = '';
            $f->name = $file;

            $installFiles[JFile::getExt($file)][] = $f;
        }//foreach

        //-- Look in 'install' folder
        if(JFolder::exists($base.'/install'))
        {
            $files = JFolder::files($base.DS.'install'
            , '(^install|^uninstall|^update|^script)([\.a-z0-9])+(sql$|php$|xml$)', true, true);

            foreach($files as $file)
            {
                $file = str_replace('/', DS, $file);
                $folder = str_replace($base.DS, '', $file);
                $folder = str_replace(DS.JFile::getName($file), '', $folder);

                $f = new stdClass;
                $f->folder = $folder;
                $f->name = JFile::getName($file);

                $ext = JFile::getExt($file);

                if('xml' == $ext)
                    $ext = 'sql';

                $installFiles[$ext][] = $f;
            }//foreach
        }

        //-- Look for update folder - must be in specific location
        $locTest = '/install/sql/updates';

        if(JFolder::exists($base.$locTest))
        {
            $folders = JFolder::folders($base.$locTest);

            foreach($folders as $folder)
            {
                $f = new stdClass;
                $f->folder = $locTest;
                $f->name = $folder;

                $installFiles['sql_updates'][] = $f;
            }//foreach
        }

        return $installFiles;
    }//function

    /**
     * Get a list of installed AutoCodes.
     *
     * @return array
     */
    public static function getAutoCodeList()
    {
        $autocodes = array();

        $scopes = JFolder::folders(ECRPATH_AUTOCODES);

        foreach($scopes as $scope)
        {
            if($scope != 'admin' && $scope != 'site')
            continue;

            $names = JFolder::folders(ECRPATH_AUTOCODES.DS.$scope);

            foreach($names as $name)
            {
                if($name == 'sql')
                continue;

                $types = JFolder::folders(ECRPATH_AUTOCODES.DS.$scope.DS.$name);

                foreach($types as $type)
                {
                    $path = ECRPATH_AUTOCODES.DS.$scope.DS.$name.DS.$type.DS.'tmpl';
                    $files = JFolder::files($path, '.', true, true);

                    if( ! $files)
                    continue;

                    foreach($files as $file)
                    {
                        $autocodes[$scope][$name][$type][] = str_replace($path.DS, '', $file);
                    }//foreach
                }//foreach
            }//foreach
        }//foreach

        return $autocodes;
    }//function

    /**
     * Gets an EcrProjectAutocode.
     *
     * @param string $key Separated by dots (.) - scope.group.name.element
     *
     * @return EcrProjectAutocode [object EcrProjectAutocode on success | boolean false on error]
     */
    public static function getAutoCode($key)
    {
        static $AutoCodes = array();

        if(array_key_exists($key, $AutoCodes))
        {
            return $AutoCodes[$key];
        }

        $parts = explode('.', $key);

        if( ! count($parts))
        {
            JFactory::getApplication()->enqueueMessage('Expecting a key separated by dots (.)', 'error');

            return false;
        }

        if(count($parts) < 4)
        {
            JFactory::getApplication()->enqueueMessage('Autocode key must have 4 (four) parts', 'error');

            return false;
        }

        $path = ECRPATH_AUTOCODES.DS.$parts[0].DS.$parts[1].DS.$parts[2];

        if( ! JFolder::exists($path))
        {
            JFactory::getApplication()->enqueueMessage(
                sprintf('Autocode key %s not found in path %s ', $key, $path), 'error');

            return false;
        }

        if( ! JFile::exists($path.DS.'autocode.php'))
        {
            JFactory::getApplication()->enqueueMessage(sprintf('autocode.php not found for %s', $key), 'error');

            return false;
        }

        require_once $path.DS.'autocode.php';

        $className = 'AutoCode'.ucfirst($parts[0]).ucfirst($parts[1]).ucfirst($parts[2]);

        if( ! class_exists($className))
        {
            JFactory::getApplication()->enqueueMessage(
                sprintf('Required Autocode class %s not found', $className), 'error');

            return false;
        }

        $AutoCodes[$key] = new $className($parts[0], $parts[1], $parts[2], $parts[3]);

        return $AutoCodes[$key];
    }//function

    /**
     * Reads a XML file.
     *
     * @param string $data Full path and file name.
     * @param boolean $isFile Set [true to load a file | false to load a string].
     *
     * @todo This may go in a separate class - error reporting may be improved.
     * @todo Update: drop J1.5 support remove since it is now part of JFactory.
     * @todo Update2: Leave it alone, since it is dropped in 3.0 :(...
     *
     * @return EcrXMLElement on success | false on error
     */
    public static function getXML($data, $isFile = true)
    {
        //-- Disable libxml errors and allow to fetch error information as needed
        libxml_use_internal_errors(true);

        $xml = ($isFile)
            //-- Try to load the xml file
            ? simplexml_load_file($data, 'EcrXMLElement')
            //-- Try to load the xml string
            : $xml = simplexml_load_string($data, 'EcrXMLElement');

        if(false === $xml)
        {
            //-- There was an error
            JFactory::getApplication()->enqueueMessage(jgettext('Could not load XML file'), 'error');

            if($isFile)
                JFactory::getApplication()->enqueueMessage($data, 'error');

            foreach(libxml_get_errors() as $error)
            {
                JFactory::getApplication()->enqueueMessage('XML: '.$error->message, 'error');
            }
        }

        return $xml;
    }
}
