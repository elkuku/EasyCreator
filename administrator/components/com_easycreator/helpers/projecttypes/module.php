<?php
/**
 * @package    EasyCreator
 * @subpackage ProjectTypes
 * @author     Nikolai Plath
 * @author     Created on 24-Mar-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * EasyCreator project type module.
 */
class EasyProjectModule extends EasyProject
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
     * Find all files and folders belonging to the project.
     *
     * @return array
     */
    public function findCopies()
    {
        if($this->copies)
        return $this->copies;

        $this->copies = array();

        if($this->scope == 'admin')
        {
            if(JFolder::exists(JPATH_ADMINISTRATOR.DS.'modules'.DS.$this->comName))
            {
                $this->copies[] = JPATH_ADMINISTRATOR.DS.'modules'.DS.$this->comName;
            }
        }
        else
        {
            if(JFolder::exists(JPATH_SITE.DS.'modules'.DS.$this->comName))
            {
                $this->copies[] = JPATH_SITE.DS.'modules'.DS.$this->comName;
            }
        }

        return $this->copies;
    }//function

    /**
     * Gets the language scopes for the extension type.
     *
     * @return array Indexed array.
     */
    public function getLanguageScopes()
    {
        $scopes = array();
        $scopes[] =($this->scope) == 'admin' ? 'admin' : 'site';

        return $scopes;
    }//function

    /**
     * Gets the paths to language files.
     *
     * @return array
     */
    public function getLanguagePaths()
    {
        $paths = array();
        $scope =($this->scope == 'admin') ? 'admin' : 'site';
        $paths[$scope] =($this->scope == 'admin') ? JPATH_ADMINISTRATOR : JPATH_SITE;

        return $paths;
    }//function

    /**
     * Get the name for language files.
     *
     * @return string
     */
    public function getLanguageFileName()
    {
        return $this->comName.'.ini';
    }//function

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

        switch(ECR_JVERSION)
        {
            case '1.5':
                $dtd = array(
                'type' => 'install'
                , 'public' => '-//Joomla! 1.5//DTD module 1.0//EN'
                , 'uri' => 'http://joomla.org/xml/dtd/1.5/module-install.dtd');
                break;

            case '1.6':
            case '1.7':
                break;

            default:
                break;
        }//switch

        return $dtd;
    }//function

    /**
     * Get a file name for a EasyCreator setup XML file.
     *
     * @return string
     */
    public function getEcrXmlFileName()
    {
        return $this->getFileName().'.xml';
    }//function

    /**
     * Get a common file name.
     *
     * @return string
     */
    public function getFileName()
    {
        return str_replace('mod_', 'mod_'.$this->scope.'_', $this->comName);
    }//function

    /**
     * Get the path for the Joomla! XML manifest file.
     *
     * @return string
     */
    public function getJoomlaManifestPath()
    {
        $path =($this->scope == 'admin') ? JPATH_ADMINISTRATOR : JPATH_SITE;
        $path .= DS.'modules'.DS.$this->comName;

        return $path;
    }//function

    /**
     * Get a Joomla! manifest XML file name.
     *
     * @return string File name.
     */
    public function getJoomlaManifestName()
    {
        return $this->comName.'.xml';
    }//function

    /**
     * Get the project Id.
     *
     * @return integer Id
     */
    public function getId()
    {
        $db = JFactory::getDBO();
        $clId =($this->scope == 'admin') ? 1 : 0;

        switch(ECR_JVERSION)
        {
            case '1.5':
                $query = new JDatabaseQuery;
                break;

            case '1.6':
            case '1.7':
                $query = $db->getQuery(true);
                break;

            default:
	            ecrHTML::displayMessage(__METHOD__.' - Unsupported JVersion');
                break;
        }//switch

        $query->from('#__modules AS m');
        $query->select('m.id');
        $query->where('m.module = '.$db->quote($this->comName));
        $query->where('m.client_id = '.(int)$clId);

        $db->setQuery((string)$query);

        return $db->loadResult();
    }//function

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
                JError::raiseWarning(100, __METHOD__.' - Unknown scope');

                return array();
                break;
        }//switch
    }//function

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
                switch(ECR_JVERSION)
                {
                    case '1.5':
                        return array('mod_custom', 'mod_feed', 'mod_footer', 'mod_latest', 'mod_logged', 'mod_login'
                        , 'mod_menu', 'mod_online', 'mod_popular', 'mod_quickicon', 'mod_stats', 'mod_status', 'mod_submenu'
                        , 'mod_title', 'mod_toolbar', 'mod_unread');

                    case '1.6':
                    case '1.7':
                        return array('mod_custom', 'mod_feed', 'mod_latest', 'mod_logged', 'mod_login'
                        , 'mod_menu', 'mod_online', 'mod_popular', 'mod_quickicon', 'mod_status', 'mod_submenu'
                        , 'mod_title', 'mod_toolbar', 'mod_unread');

                    case '2.5':
                        return array('mod_custom', 'mod_feed', 'mod_latest', 'mod_logged', 'mod_login'
                        , 'mod_menu', 'mod_online', 'mod_popular', 'mod_quickicon', 'mod_status', 'mod_submenu'
                        , 'mod_title', 'mod_toolbar', 'mod_unread', 'mod_multilangstatus');

                    default:
	                    ecrHTML::displayMessage(__METHOD__.' - Unsupported JVersion');

                        return array();
                }//switch
            case 'site':
                switch(ECR_JVERSION)
                {
                    case '1.5':
                        return array('mod_archive', 'mod_banners', 'mod_breadcrumbs', 'mod_custom'
                        , 'mod_feed', 'mod_footer', 'mod_latestnews', 'mod_login', 'mod_mainmenu'
                        , 'mod_mostread', 'mod_newsflash', 'mod_poll', 'mod_random_image'
                        , 'mod_related_items', 'mod_search', 'mod_sections', 'mod_stats'
                        , 'mod_syndicate', 'mod_whosonline', 'mod_wrapper');

                    case '1.6':
                    case '1.7':
                        return array('mod_articles_archive', 'mod_articles_categories', 'mod_articles_category'
                        , 'mod_articles_latest', 'mod_articles_news', 'mod_articles_popular', 'mod_banners'
                        , 'mod_breadcrumbs', 'mod_custom', 'mod_feed', 'mod_footer', 'mod_languages'
                        , 'mod_login', 'mod_menu', 'mod_random_image', 'mod_related_items', 'mod_search', 'mod_stats'
                        , 'mod_syndicate', 'mod_users_latest', 'mod_weblinks', 'mod_whosonline', 'mod_wrapper');

                    case '2.5':
                        return array('mod_articles_archive', 'mod_articles_categories', 'mod_articles_category'
                        , 'mod_articles_latest', 'mod_articles_news', 'mod_articles_popular', 'mod_banners'
                        , 'mod_breadcrumbs', 'mod_custom', 'mod_feed', 'mod_footer', 'mod_languages'
                        , 'mod_login', 'mod_menu', 'mod_random_image', 'mod_related_items', 'mod_search', 'mod_stats'
                        , 'mod_syndicate', 'mod_users_latest', 'mod_weblinks', 'mod_whosonline', 'mod_wrapper'
                        , 'mod_finder');

                    default:
	                    ecrHTML::displayMessage(__METHOD__.' - Unsupported JVersion');

                        return array();
                }//switch
                break;

            default:
	            ecrHTML::displayMessage(__METHOD__.' - Unknown scope');

                return array();
        }//switch
    }//function
}//class
