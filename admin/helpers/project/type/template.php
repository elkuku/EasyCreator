<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage ProjectTypes
 * @author     Nikolai Plath
 * @author     Created on 24-Mar-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * EasyCreator project type template.
 */
class EcrProjectTypeTemplate extends EcrProjectBase
{
    /**
     * Project type.
     *
     * @var string
     */
    public $type = 'template';

    /**
     * Project prefix.
     *
     * @var string
     */
    public $prefix = 'tpl_';

    /**
     * Translate the type
     *
     * @return string
     */
    public function translateType()
    {
        return jgettext('Template');
    }

    /**
     * Translate the plural type
     *
     * @return string
     */
    public function translateTypePlural()
    {
        return jgettext('Templates');
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
        return jngettext('%d Template', '%d Templates', $n);
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
            if(JFolder::exists(JPATH_ADMINISTRATOR.DS.'templates'.DS.$this->comName))
                $this->copies[] = JPATH_ADMINISTRATOR.DS.'templates'.DS.$this->comName;
        }
        else
        {
            if(JFolder::exists(JPATH_SITE.DS.'templates'.DS.$this->comName))
                $this->copies[] = JPATH_SITE.DS.'templates'.DS.$this->comName;
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
        $scopes[] = 'admin';

        if($this->scope != 'admin')
        {
            $scopes[] = 'site';
        }

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
        $scope = ('admin' == $this->scope) ? JPATH_ADMINISTRATOR : JPATH_SITE;

        return $scope.'/templates/'.$this->comName;
    }

    /**
     * Gets the paths to language files.
     *
     * @param string $scope
     *
     * @throws Exception
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
	            $paths['admin'][] = JPATH_ADMINISTRATOR.'/templates/'.$this->comName;
                $paths['site'][] = JPATH_SITE.'/templates/'.$this->comName;

                $s = ($this->scope == 'admin') ? JPATH_ADMINISTRATOR : JPATH_SITE;
                $paths['sys'][] = $s.'/templates/'.$this->comName;

                if(isset($this->buildOpts['lng_separate_javascript'])
                    && ($this->buildOpts['lng_separate_javascript']) == 'ON'
                )
                {
                    $paths['js_admin'][] = JPATH_ADMINISTRATOR.'/templates/'.$this->comName;
                    $paths['js_site'][] = JPATH_SITE.'/templates/'.$this->comName;
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
                return $this->prefix.$this->comName.'.sys.'.$this->langFormat;
                break;

            case 'js_admin' :
            case 'js_site' :
                return $this->prefix.$this->comName.'.js.'.$this->langFormat;
                break;

            default :
                return $this->prefix.$this->comName.'.'.$this->langFormat;
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
                EcrHtml::message(__METHOD__.' - Unsupported JVersion');
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
        return $this->prefix.$this->scope.'_'.$this->comName;
    }

    /**
     * Get the path for the Joomla! XML manifest file.
     *
     * @return string
     */
    public function getJoomlaManifestPath()
    {
        $path = ($this->scope == 'admin') ? JPATH_ADMINISTRATOR : JPATH_SITE;
        $path .= DS.'templates'.DS.$this->comName;

        return $path;
    }

    /**
     * Get a Joomla! manifest XML file name.
     *
     * @return string The file name
     */
    public function getJoomlaManifestName()
    {
        return 'templateDetails.xml';
    }

    /**
     * Get the project Id.
     *
     * @return int Id
     */
    public function getId()
    {
        $db = JFactory::getDbo();

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
            $query = $db->getQuery(true);

                $query->from('#__extensions AS e');
                $query->select('e.extension_id');
                $query->where('e.element = '.$db->quote($this->comName));
                $query->where('e.type = '.$db->quote('template'));
                break;

            default:
                EcrHtml::message(__METHOD__.' - Unsupported JVersion');

                return false;
                break;
        }

        $db->setQuery($query);

        $id = $db->loadResult();

        return $id;
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
        $projects = array();

        switch($scope)
        {
            case 'admin':
                $projects = JFolder::folders(JPATH_ADMINISTRATOR.DS.'templates');
                break;

            case 'site':
                $projects = JFolder::folders(JPATH_SITE.DS.'templates');
                break;
        }

        return $projects;
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

        if (isset($projects->template->$scope)) {
            return $projects->template->$scope;
        }

        EcrHtml::message(__METHOD__.' - Unsupported JVersion');

        return array();
    }
}
