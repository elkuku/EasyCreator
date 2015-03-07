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
        switch($scope)
        {
            case 'admin':
                //-- @Joomla!-version-check
                switch(ECR_JVERSION)
                {
                    case '2.5':
                        return array('mod_custom', 'mod_feed', 'mod_latest', 'mod_logged', 'mod_login'
                        , 'mod_menu', 'mod_online', 'mod_popular', 'mod_quickicon', 'mod_status', 'mod_submenu'
                        , 'mod_title', 'mod_toolbar', 'mod_unread', 'mod_multilangstatus', 'mod_version');

                    case '3.0':
                    case '3.1':
                    case '3.2':
	                case '3.3':
	                case '3.4':
		                return array('mod_custom', 'mod_feed', 'mod_latest', 'mod_logged', 'mod_login'
                        , 'mod_menu', 'mod_online', 'mod_popular', 'mod_quickicon', 'mod_status', 'mod_submenu'
                        , 'mod_title', 'mod_toolbar', 'mod_unread', 'mod_multilangstatus', 'mod_version'
                        , 'mod_stats_admin');

                    default:
                        EcrHtml::message(__METHOD__.' - Unsupported JVersion');

                        return array();
                }

            case 'site':
                //-- @Joomla!-version-check
                switch(ECR_JVERSION)
                {
                    case '2.5':
                        return array('mod_articles_archive', 'mod_articles_categories', 'mod_articles_category'
                        , 'mod_articles_latest', 'mod_articles_news', 'mod_articles_popular', 'mod_banners'
                        , 'mod_breadcrumbs', 'mod_custom', 'mod_feed', 'mod_footer', 'mod_languages'
                        , 'mod_login', 'mod_menu', 'mod_random_image', 'mod_related_items', 'mod_search', 'mod_stats'
                        , 'mod_syndicate', 'mod_users_latest', 'mod_weblinks', 'mod_whosonline', 'mod_wrapper'
                        , 'mod_finder');
                        break;

                    case '3.0':
                    case '3.1':
                        return array('mod_articles_archive', 'mod_articles_categories', 'mod_articles_category'
                    , 'mod_articles_latest', 'mod_articles_news', 'mod_articles_popular', 'mod_banners'
                    , 'mod_breadcrumbs', 'mod_custom', 'mod_feed', 'mod_footer', 'mod_languages'
                    , 'mod_login', 'mod_menu', 'mod_random_image', 'mod_related_items', 'mod_search', 'mod_stats'
                    , 'mod_syndicate', 'mod_users_latest', 'mod_weblinks', 'mod_whosonline', 'mod_wrapper'
                    , 'mod_finder');
                    break;

                    case '3.2':
	                case '3.3':
	                case '3.4':
		                return array('mod_articles_archive', 'mod_articles_categories', 'mod_articles_category'
                        , 'mod_articles_latest', 'mod_articles_news', 'mod_articles_popular', 'mod_banners'
                        , 'mod_breadcrumbs', 'mod_custom', 'mod_feed', 'mod_footer', 'mod_languages'
                        , 'mod_login', 'mod_menu', 'mod_random_image', 'mod_related_items', 'mod_search', 'mod_stats'
                        , 'mod_syndicate', 'mod_users_latest', 'mod_weblinks', 'mod_whosonline', 'mod_wrapper'
                        , 'mod_finder', 'mod_tags_popular', 'mod_tags_similar');
                    break;

                    default:
                        EcrHtml::message(__METHOD__.' - Unsupported JVersion');

                        return array();
                }
                break;

            default:
                EcrHtml::message(__METHOD__.' - Unknown scope');

                return array();
        }
    }
}//class
