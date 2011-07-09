<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 05-Oct-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

ecrLoadHelper('project');

/**
 * EasyProject Helper.
 *
 * @package    EasyCreator
 */
class EasyProjectHelper
{
    /**
     * Gets known project types.
     *
     * @return array
     */
    public static function getProjectTypesTags()
    {
        $projectTypes = array(
        'com' => 'component'
        , 'mod' => 'module'
        , 'plg' => 'plugin'
        , 'tpl' => 'template'
        );

        switch(ECR_JVERSION)
        {
            case '1.5' :
                break;

            case '1.6':
            case '1.7':
                $projectTypes += array(
                'lib' => 'library'
                , 'pkg' => 'package');
                break;

            default:
                JError::raiseWarning(0, __METHOD__.' - Unknown J! version');
                break;
        }//switch

        return $projectTypes;
    }//function

    /**
     * Get an EasyCreator project.
     *
     * @param string $name Project name.
     * @param boolean $reload Reload the project.
     *
     * @return object EasyProject [EasyProject project on success | boolean false on error]
     */
    public static function getProject($name = '', $reload = false)
    {
        static $projects = array();
        static $defaultName = '';

        if( ! $defaultName)
        $defaultName = JRequest::getCmd('ecr_project');

        if( ! $name)
        $name = $defaultName;

        if( ! $name)
        throw new Exception(jgettext('Empty project name'));

        if(isset($projects[$name])
        && ! $reload)
        {
            return $projects[$name];
        }

        $projectTypes = self::getProjectTypesTags();

        $type = substr($name, 0, 3);

        if($type == 'ecr')
        return; //-- Internal - new, register

        if( ! array_key_exists($type, $projectTypes))
        throw new Exception(sprintf('Invalid project type: %s ', $type));

        if( ! ecrLoadHelper('projecttypes.'.$projectTypes[$type]))
        throw new Exception(sprintf('No helper found for project type %s', $projectTypes[$type]));

        $className = 'EasyProject'.ucfirst($projectTypes[$type]);

        if( ! class_exists($className))
        throw new Exception(sprintf('Required class [%s] not found', $className));

        $project = new $className($name);

        if( ! $project->dbId)
        {
            if('package' != $project->type)
            {
                //-- All projects *except packages* must be installed in the database
                //return $project;
                throw new Exception(sprintf(jgettext('Project %s not found'), $name));
            }
        }

        $projects[$name] = $project;

        return $projects[$name];
    }//function

    /**
     * Creates an empty project.
     *
     * @param string $type Project type (component, plugin, etc.)
     *
     * @return mixed [object EasyProject | boolean false on error]
     */
    public static function newProject($type)
    {
        if( ! ecrLoadHelper('projecttypes.'.$type))
        {
            JError::raiseWarning(100, sprintf('No helper found for project type %s', $type));

            return false;
        }

        $className = 'EasyProject'.ucfirst($type);

        if( ! class_exists($className))
        {
            JError::raiseWarning(100, sprintf('Required class [%s] not found', $className));

            return false;
        }

        $project = new $className;

        return $project;
    }//function

    /**
     * Findes the Joomla! install xml file for a given extension.
     *
     * @param EasyProject $project The project
     *
     * @return mixed [boolean false on error | string path on success]
     */
    public static function findManifest(EasyProject $project)
    {
        $path = $project->getJoomlaManifestPath();

        if( ! JFolder::exists($path))
        {
            return false;
        }

        switch(ECR_JVERSION)
        {
            case '1.5':
                if('plugin' == $project->type)
                {
                    $xmlFiles = array($path.DS.$project->comName.'.xml');
                }
                break;

            case '1.6':
            case '1.7':
                if('library' == $project->type
                || 'package' == $project->type)
                {
                    $xmlFiles = array($path.DS.$project->getJoomlaManifestName());
                }
                break;

            default:
                ecrHTML::displayMessage(__METHOD__.' - Unknown JVersion', 'error');

                return false;
                break;
        }//switch

        if(empty($xmlFiles))
        {
            $xmlFiles = JFolder::files($path, '.xml$', false, true);
        }

        /*
         if(ECR_JVERSION == '1.5'
         && $project->type == 'plugin')
         {
         /*
         * Special treatment for plugins in 1.5
         $xmlFiles = array($path.DS.$project->comName.'.xml');
         }
         else if(ECR_JVERSION == '1.6'
         && $project->type == 'library')
         {
         /*
         * Very Special treatment for libraries in 1.6
         $xmlFiles = array($path.DS.$project->getJoomlaManifestName());
         }
         else if(ECR_JVERSION == '1.6'
         && $project->type == 'package')
         {
         /*
         * Very Special treatment for packages in 1.6
         $xmlFiles = array($path.DS.$project->getJoomlaManifestName());
         }
         else
         {
         if( ! JFolder::exists($path))
         {
         return false;
         }

         $xmlFiles = JFolder::files($path, '.xml$', false, true);
         }
         */
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
                 JError::raiseWarning(100, 'File not found '.$fileName);
                 ecrHTML::displayMessage('Unable to load XML file '.$fileName, 'error');
                 */
                return false;
            }

            $xml = self::getXML($fileName);

            //-- Invalid XML file
            if( ! $xml)
            return false;

            if($xml->getName() == 'install'//J! 1.5
            || $xml->getName() == 'extension'//J! 1.6+
            )
            {
                //-- Valid xml manifest found
                return str_replace(JPATH_ROOT.DS, '', $fileName);
            }
        }//foreach

        //-- None of the xml files found were valid install files
        ecrHTML::displayMessage(sprintf(jgettext('Manifest not found for type: %s - name: %s'), $project->type, $fileName), 'error');

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
     * @return [mixed array credits data | boolean false on error]
     */
    public static function parseXMLInstallFile($path)
    {
        if( ! JFile::exists($path))
        {
            JError::raiseWarning(100, sprintf(jgettext('File not found %s'), $path));

            return false;
        }

        //-- Read the file to see if it's a valid component XML file
        $manifest = self::getXML($path);

        if( ! $manifest instanceof SimpleXMLElement)
        {
            JError::raiseWarning(100, sprintf(jgettext('Invalid manifest file %s'), $path));
            unset($manifest);

            return false;
        }

        /*
         * Check for a valid XML root tag.
         */
        if($manifest->getName() != 'install'//J! 1.5
        && $manifest->getName() != 'extension'//J! 1.6
        )
        {
            ecrHTML::displayMessage(sprintf('Invalid install manifest at %s', $path), 'error');

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
            $xmlfiles = JFolder::files(ECRPATH_SCRIPTS, '.xml$', true, true);
            $projectList = array();

            foreach($xmlfiles as $fileName)
            {
                $xml = EasyProjectHelper::getXML($fileName);

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
        {
            return $types;
        }

        //-- We don't like these roject types - for now..
        $unwanted = array('language', 'file');

        //-- Defined for translation
        $comTypes = array(
          'component' => array(jgettext('Components'), jgettext('Component'))
        , 'module' => array(jgettext('Modules'), jgettext('Module'))
        , 'plugin' => array(jgettext('Plugins'), jgettext('Plugin'))
        , 'library' => array(jgettext('Libraries'), jgettext('Library'))
        , 'package' => array(jgettext('Packages'), jgettext('Package'))
        , 'template' => array(jgettext('Templates'), jgettext('Template'))
        );

        //-- Degfined for automated plural translations
        if(0)
        {
            jngettext('%d Component', '%d Components', 0);
            jngettext('%d Module', '%d Modules', 0);
            jngettext('%d Plugin', '%d Plugins', 0);
            jngettext('%d Library', '%d Libraries', 0);
            jngettext('%d Package', '%d Packages', 0);
            jngettext('%d Template', '%d Templates', 0);
        }

        //-- Get a list of J! installer adapters
        $adapters = JFolder::files(JPATH_LIBRARIES.DS.'joomla'.DS.'installer'.DS.'adapters', '.php$');

        foreach($adapters as $aName)
        {
            $a = JFile::stripExt($aName);

            if(in_array($a, $unwanted))
            {
                continue;
            }

            $n =(array_key_exists($a, $comTypes)) ? $comTypes[$a][0] : ucfirst($a);

            $types[$a] = $n;
        }//foreach

        return $types;
    }//function

    /**
     * Format a filename for a package file.
     *
     * @param EasyProject $project The project
     * @param string $format The format to use
     *
     * @return string
     */
    public static function formatFileName(EasyProject $project, $format)
    {
        $svnRev = ecrHTML::getVersionFromCHANGELOG($project->comName, true);

        $dateTime = preg_match('%\*DATETIME(.*?)\*%', $format, $matches);

        if($matches && isset($matches[1]))
        {
            $format = str_replace($matches[0], date($matches[1]), $format);
        }

        $format = str_replace('*SVNREV*', $svnRev, $format);
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
                JError::raiseWarning(0, 'UNKNOWN TYPE: '.$type);

                return array();
                break;
        }//switch

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
     * @return mixed [EasyPart on success | null on error]
     */
    public static function getPart($group, $part, $element, $scope, $edit = false)
    {
        ecrLoadHelper('EasyPart');

        $subDir =($edit) ? DS.'data' : '';

        $path = ECRPATH_PARTS.$subDir.DS.$group.DS.$part;
        $fileName = $path.DS.'part.php';

        if( ! JFile::exists($fileName))
        {
            ecrHTML::displayMessage(array(jgettext('File not found'), $fileName), 'error');

            return null;
        }

        require_once $fileName;

        $className = 'part'.ucfirst($group).ucfirst($part);

        if( ! class_exists($className))
        {
            ecrHTML::displayMessage(array(jgettext('Class not found'), $className), 'error');

            return null;
        }

        $part = new $className($element, $scope, $path);

        return $part;
    }//function

    /**
     * Finds PHP and SQL install files.
     *
     * @param EasyProject $project The project
     *
     * @return array Object array
     */
    public static function findInstallFiles(EasyProject $project)
    {
        $installFiles = array();
        $installFiles['php'] = array();
        $installFiles['sql'] = array();
        $installFiles['sql_updates'] = array();

        if($project->type != 'component')
        {
            //-- Only components can have install files..
            //@todo change in 1.6
            return $installFiles;
        }

        if($project->buildPath)
        {
            //-- if $project->buildPath is set we are building a NEW project)
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
            $base = JPATH_ADMINISTRATOR.DS.'components'.DS.$project->comName;
        }

        if( ! JFolder::exists($base))
        {
            ecrHTML::displayMessage(array(sprintf(jgettext('Project %s not found'), $project->comName)), 'error');

            return $installFiles;
        }

        //-- Look in components 'root'
        $files = JFolder::files($base, '(^install|^uninstall|^update|^script)([\.a-z0-9])+(sql$|php$)');

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
            , '(^install|^uninstall|^update|^script)([\.a-z0-9])+(sql$|php$)', true, true);

            foreach($files as $file)
            {
                $folder = str_replace($base.DS, '', $file);
                $folder = str_replace(DS.JFile::getName($file), '', $folder);

                $f = new stdClass;
                $f->folder = $folder;
                $f->name = JFile::getName($file);

                $installFiles[JFile::getExt($file)][] = $f;
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
            }
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
     * Gets an EasyAutoCode.
     *
     * @param string $key Separated by dots (.) - scope.group.name.element
     *
     * @return mixed [object EasyAutoCode on success | boolean false on error]
     */
    public static function getAutoCode($key)
    {
        ecrLoadHelper('autocode');

        static $AutoCodes = array();

        if(array_key_exists($key, $AutoCodes))
        {
            return $AutoCodes[$key];
        }

        $parts = explode('.', $key);

        if( ! count($parts))
        {
            JError::raiseWarning(100, 'Expecting a key separated by dots (.)');

            return false;
        }

        if(count($parts) < 4)
        {
            JError::raiseWarning(100, 'Autocode key must have 4 (four) parts');

            return false;
        }

        $path = ECRPATH_AUTOCODES.DS.$parts[0].DS.$parts[1].DS.$parts[2];

        if( ! JFolder::exists($path))
        {
            JError::raiseWarning(100, sprintf('Autocode key %s not found in path %s ', $key, $path));

            return false;
        }

        if( ! JFile::exists($path.DS.'autocode.php'))
        {
            JError::raiseWarning(100, sprintf('autocode.php not found for %s', $key));

            return false;
        }

        require_once $path.DS.'autocode.php';

        $className = 'AutoCode'.ucfirst($parts[0]).ucfirst($parts[1]).ucfirst($parts[2]);

        if( ! class_exists($className))
        {
            JError::raiseWarning(100, sprintf('Required Autocode class %s not found', $className));

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
     *
     * @return mixed [JXMLElement on success | false on error].
     */
    public static function getXML($data, $isFile = true)
    {
        ecrLoadHelper('xmlelement');

        // Disable libxml errors and allow to fetch error information as needed
        libxml_use_internal_errors(true);

        if($isFile)
        {
            //-- Try to load the xml file
            $xml = simplexml_load_file($data, 'EasyXMLElement');
        }
        else
        {
            //-- Try to load the xml string
            $xml = simplexml_load_string($data, 'EasyXMLElement');
        }

        if(empty($xml))
        {
            //-- There was an error
            JError::raiseWarning(100, jgettext('Could not load XML file'));

            if($isFile)
            {
                JError::raiseWarning(100, $data);
            }

            foreach(libxml_get_errors() as $error)
            {
                JError::raiseWarning(100, 'XML: '.$error->message);
            }//foreach
        }

        return $xml;
    }//function
}//class
