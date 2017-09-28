<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage ProjectTypes
 * @author     Nikolai Plath
 * @author     Created on 24-Mar-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * EasyCreator project type module.
 */
class EcrProjectTypeModule extends EcrProjectBase
{
    /**
     * Project type.
     *
     * @var string
     */
    public $type = 'module';

    /**
     * Project prefix.
     *
     * @var string
     */
    public $prefix = 'mod_';

    /**
     * Translate the type.
     *
     * @return string
     */
    public function translateType()
    {
        return jgettext('Module');
    }

    /**
     * Translate the plural type
     *
     * @return string
     */
    public function translateTypePlural()
    {
        return jgettext('Modules');
    }

    /**
     * Translate the plural type using a count
     *
     * @param int $n The amount
     *
     * @return string
     */
    public function translateTypeCount($n)
    {
        return jngettext('%d Module', '%d Modules', $n);
    }

    /**
     * Find all files and folders belonging to the project.
     *
     * @return array
     */
    public function findCopies()
    {
        if($this->copies)
            return $this->copies;

        if($this->scope == 'admin')
        {
            if(JFolder::exists(JPATH_ADMINISTRATOR.DS.'modules'.DS.$this->comName))
                $this->copies[] = JPATH_ADMINISTRATOR.DS.'modules'.DS.$this->comName;
        }
        else
        {
            if(JFolder::exists(JPATH_SITE.DS.'modules'.DS.$this->comName))
                $this->copies[] = JPATH_SITE.DS.'modules'.DS.$this->comName;
        }

        return $this->copies;
    }

    /**
     * Gets the language scopes for the extension type.
     *
     * @return array Indexed array.
     */
    public function getLanguageScopes()
    {
        $scopes = array();
        $scopes[] = ($this->scope) == 'admin' ? 'admin' : 'site';

        return $scopes;
    }

    /**
     * Gets the scopes for the extension type.
     *
     * @since 0.0.25.6
     * @return array
     */
    public function getInstallScopes()
    {
        return array('admin', 'site');
    }

    /**
     * Get the extension base path.
     *
     * @return string
     */
    public function getExtensionPath()
    {
        $scope = ($this->scope == 'admin') ? JPATH_ADMINISTRATOR : JPATH_SITE;

        return $scope.'/modules/'.$this->comName;
    }

    /**
     * Gets the paths to language files.
     *
     * @param string $scope
     *
     * @throws Exception
     *
     * @return array
     */
    public function getLanguagePaths($scope = '')
    {
        static $paths = array();

        if($paths)
        {
            if($scope && isset($paths[$scope]))
                return $paths[$scope];

            return $paths;
        }

        //-- @Joomla!-version-check
        switch($this->JCompat)
        {
            case '2.5':
            case '3.0':
            case '3.1':
            case '3.2':
	        case '3.3':
	        case '3.4':
	        case '3.5':
	        case '3.6':
	        case '3.7':
	        case '3.8':
	            $paths['admin'][] = JPATH_ADMINISTRATOR.'/modules/'.$this->comName;
                $paths['site'][] = JPATH_SITE.'/modules/'.$this->comName;

                $s = ($this->scope == 'admin') ? JPATH_ADMINISTRATOR : JPATH_SITE;
                $paths['sys'][] = $s.'/modules/'.$this->comName;

                if(isset($this->buildOpts['lng_separate_javascript'])
                    && ($this->buildOpts['lng_separate_javascript']) == 'ON'
                )
                {
                    $paths['js_admin'][] = JPATH_ADMINISTRATOR.'/modules/'.$this->comName;
                    $paths['js_site'][] = JPATH_SITE.'/modules/'.$this->comName;
                }
                break;

            default:
                EcrHtml::message(__METHOD__.' - Unsupported JVersion', 'error');

                return array();
                break;
        }

        if($scope && ! array_key_exists($scope, $paths))
            throw new Exception(__METHOD__.' - Unknown scope: '.$scope);

        if('admin' == $this->scope)
        {
            unset($paths['site']);
        }
        else
        {
            unset($paths['admin']);
        }

        if($scope && isset($paths[$scope]))
            return $paths[$scope];

        return $paths;
    }

    /**
     * Get the name for language files.
     *
     * @param string $scope
     *
     * @return string
     */
    public function getLanguageFileName($scope = '')
    {
        switch($scope)
        {
            case 'sys' :
                return $this->comName.'.sys.'.$this->langFormat;
                break;

            case 'js_admin' :
            case 'js_site' :
                return $this->comName.'.js.'.$this->langFormat;
                break;

            default :
                return $this->comName.'.'.$this->langFormat;
                break;
        }
    }

    /**
     * Gets the DTD for the extension type.
     *
     * @param string $jVersion Joomla! version
     *
     * @return mixed [array index array on success | false if not found]
     */
    public function getDTD($jVersion)
    {
        $dtd = false;

        //-- @Joomla!-version-check
        switch(ECR_JVERSION)
        {
            case '2.5':
            case '3.0':
            case '3.1':
            case '3.2':
	        case '3.3':
	        case '3.4':
	        case '3.5':
	        case '3.6':
	        case '3.7':
	        case '3.8':
	        break;

            default:
                break;
        }

        return $dtd;
    }

    /**
     * Get a file name for a EasyCreator setup XML file.
     *
     * @return string
     */
    public function getEcrXmlFileName()
    {
        return $this->getFileName().'.xml';
    }

    /**
     * Get a common file name.
     *
     * @return string
     */
    public function getFileName()
    {
        return str_replace('mod_', 'mod_'.$this->scope.'_', $this->comName);
    }

    /**
     * Get the path for the Joomla! XML manifest file.
     *
     * @return string
     */
    public function getJoomlaManifestPath()
    {
        $path = ($this->scope == 'admin') ? JPATH_ADMINISTRATOR : JPATH_SITE;
        $path .= DS.'modules'.DS.$this->comName;

        return $path;
    }

    /**
     * Get a Joomla! manifest XML file name.
     *
     * @return string The file name.
     */
    public function getJoomlaManifestName()
    {
        return $this->comName.'.xml';
    }

    /**
     * Get the project Id.
     *
     * @return int Id
     */
    public function getId()
    {
        $db = JFactory::getDBO();
        $clId = ($this->scope == 'admin') ? 1 : 0;

        $query = $db->getQuery(true);

        /* @var JDatabaseQuery $query */

        $query->from('#__extensions AS m');
        $query->select('m.extension_id');
        $query->where('m.element = '.$db->quote($this->comName));
        $query->where('m.client_id = '.(int)$clId);

        $db->setQuery((string)$query);

        return $db->loadResult();
    }

    /**
     * Discover all projects.
     *
     * @param string $scope The scope - admin or site.
     *
     * @return array
     */
    public function getAllProjects($scope)
    {
        switch($scope)
        {
            case 'admin':
                return JFolder::folders(JPATH_ADMINISTRATOR.DS.'modules');
                break;
            case 'site':
                return JFolder::folders(JPATH_SITE.DS.'modules');
                break;
            default:
                JFactory::getApplication()->enqueueMessage(__METHOD__.' - Unknown scope', 'error');

                return array();
                break;
        }
    }

    /**
     * Get a list of known core projects.
     *
     * @param string $scope The scope - admin or site.
     *
     * @return array
     */
    public function getCoreProjects($scope)
    {
        $projects = $this->loadCoreProjects();

        if (isset($projects->module->$scope)) {
            return $projects->module->$scope;
        }

        EcrHtml::message(__METHOD__.' - Unsupported JVersion');

        return array();
    }
}
