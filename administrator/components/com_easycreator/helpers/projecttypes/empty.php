<?php
/**
 * @package    EasyCreator
 * @subpackage ProjectTypes
 * @author     Nikolai Plath
 * @author     Created on 26-May-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Empty project for creating new projects.
 */
class EasyProjectEmpty extends EasyProject
{
    /**
     * Project type.
     *
     * @var string
     */
    public $type = '';

    /**
     * Project prefix.
     *
     * @var string
     */
    public $prefix = '';

    /**
     * Find all files and folders belonging to the project.
     *
     * @return array
     */
    public function findCopies()
    {
        if($this->copies)
        return $this->copies;

        $path = '';

        if(JFolder::exists($path))
        {
            $this->copies[] = $path;
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
        return array('site' => JPATH_SITE);
    }//function

    /**
     * Get the name for language files.
     *
     * @return string
     */
    public function getLanguageFileName()
    {
        return $this->prefix.$this->comName.'.ini';
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
        return $this->prefix.$this->comName;
    }//function

    /**
     * Get the path for the Joomla! XML manifest file.
     *
     * @return string
     */
    public function getJoomlaManifestPath()
    {
        return '';
    }//function

    /**
     * Get a Joomla! manifest XML file name.
     *
     * @return mixed [string file name | boolean false on error]
     */
    public function getJoomlaManifestName()
    {
        return $this->prefix.$this->comName.'.xml';
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

        $query = new JDatabaseQuery;

        $query->from('#__extensions AS e');
        $query->select('e.extension_id');
        $query->where('e.type = '.$db->quote($this->type));
        $query->where('e.element = '.$db->quote($this->prefix.$this->comName));

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
        JFactory::getApplication()->enqueueMessage(__METHOD__.' unfinished', 'warning');

        switch($scope)
        {
            case 'admin':
                return JFolder::folders(JPATH_ADMINISTRATOR.DS.'modules');
                break;

            case 'site':
                return JFolder::folders(JPATH_SITE.DS.'modules');
                break;

            default:
                JFactory::getApplication()->enqueueMessage('EasyProjectModule::getCoreProjects Unknown scope', 'error');

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
        JFactory::getApplication()->enqueueMessage(__METHOD__.' unfinished', 'warning');

        switch($scope)
        {
            case 'admin':
                switch(ECR_JVERSION)
                {
                    case '1.5':
                        return array('mod_custom', 'mod_feed', 'mod_footer', 'mod_latest', 'mod_logged', 'mod_login'
                        , 'mod_menu', 'mod_online', 'mod_popular', 'mod_quickicon', 'mod_stats', 'mod_status', 'mod_submenu'
                        , 'mod_title', 'mod_toolbar', 'mod_unread');
                        break;

                    case '1.6':
                        return array('mod_custom', 'mod_feed', 'mod_latest', 'mod_logged', 'mod_login'
                        , 'mod_menu', 'mod_online', 'mod_popular', 'mod_quickicon', 'mod_status', 'mod_submenu'
                        , 'mod_title', 'mod_toolbar', 'mod_unread');
                        break;

                    default:
                        ecrHTML::displayMessage(__METHOD__.' - Unknown J! version');
                        break;
                }//switch

            case 'site':
                switch(ECR_JVERSION)
                {
                    case '1.5':
                        return array('mod_archive', 'mod_banners', 'mod_breadcrumbs'
                        , 'mod_custom', 'mod_feed', 'mod_footer', 'mod_latestnews', 'mod_login'
                        , 'mod_mainmenu', 'mod_mostread', 'mod_newsflash', 'mod_poll', 'mod_random_image'
                        , 'mod_related_items', 'mod_search', 'mod_sections', 'mod_stats'
                        , 'mod_syndicate', 'mod_whosonline', 'mod_wrapper');
                        break;

                    case '1.6':
                        return array('mod_articles_archive', 'mod_articles_categories'
                        , 'mod_articles_category', 'mod_articles_latest', 'mod_articles_news'
                        , 'mod_articles_popular', 'mod_banners', 'mod_breadcrumbs', 'mod_custom'
                        , 'mod_feed', 'mod_footer', 'mod_languages', 'mod_login', 'mod_menu'
                        , 'mod_random_image', 'mod_related_items', 'mod_search', 'mod_stats'
                        , 'mod_syndicate', 'mod_users_latest', 'mod_weblinks', 'mod_whosonline'
                        , 'mod_wrapper');
                        break;
                    default:
                        ecrHTML::displayMessage(__METHOD__.' - Unknown J! version');
                        break;
                }//switch

                break;

            default:
                JFactory::getApplication()->enqueueMessage('EasyProjectModule::getCoreProjects Unknown scope', 'error');

                return array();
                break;
        }//switch

	    return array();
    }//function
}//class
