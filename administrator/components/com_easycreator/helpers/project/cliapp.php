<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage ProjectTypes
 * @author     Nikolai Plath
 * @author     Created on 25-Feb-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */


/**
 * EasyCreator project type CLI Application.
 */
class EcrProjectCliapp extends EcrProject
{
    /**
     * Project type.
     *
     * @var string
     */
    public $type = 'cliapp';

    /**
     * Project prefix.
     *
     * @var string
     */
    public $prefix = 'cap_';

    public $JCompat = '1.6';

    /**
     * Find all files and folders belonging to the project.
     *
     * @return array
     */
    public function findCopies()
    {
        if($this->copies)
            return $this->copies;

        $this->copies = JFolder::files(JPATH_COMPONENT_ADMINISTRATOR.'/cliapps/'.$this->comName, '.', true, true);

        return $this->copies;
    }//function

    /**
     * Gets the language scopes for the extension type.
     *
     * @return array Indexed array.
     */
    public function getLanguageScopes()
    {
        JFactory::getApplication()->enqueueMessage(__METHOD__.' unfinished', 'warning');
        $scopes = array();
        $scopes[] =($this->scope) == 'admin' ? 'admin' : 'site';

        return $scopes;
    }//function

    /**
     * Get the extension base path.
     *
     * @return string
     */
    public function getExtensionPath()
    {
        return JPATH_LIBRARIES.'/'.$this->comName;
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
            case '1.6':
            case '1.7':
            case '2.5':
                break;

            default:
                EcrHtml::displayMessage(__METHOD__.' - Unknown J! version');
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
        return JPATH_MANIFESTS.DS.'libraries';
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
     * @return int Id
     */
    public function getId()
    {
        $db = JFactory::getDBO();

        $query = $db->getQuery(true);

        $query->from('#__extensions AS e');
        $query->select('e.extension_id');
        $query->where('e.type = '.$db->quote($this->type));
        $query->where('e.element = '.$db->quote($this->prefix.$this->comName));

        $db->setQuery($query);

        return $db->loadResult();
    }//function

    /**
     * Discover all projects.
     *
     * @param $scope
     *
     * @return array
     */
    public function getAllProjects($scope)
    {
        $folders = array();

        /*
        if(defined('JPATH_PLATFORM'))
        {
            $folders = JFolder::folders(JPATH_PLATFORM.'/libraries');
        }
        */

        $folders = array_merge($folders, JFolder::folders(JPATH_LIBRARIES));

        return $folders;
    }//function

    /**
     * Get a list of known core projects.
     *
     * @param $scope
     *
     * @return array
     */
    public function getCoreProjects($scope)
    {
        switch(ECR_JVERSION)
        {
            case '1.6':
            case '1.7':
                return array('joomla', 'phpmailer', 'phputf8', 'simplepie');
                break;

            case '2.5':
                return array('cms', 'joomla', 'phpmailer', 'phputf8', 'simplepie');
                break;

            default:
                EcrHtml::displayMessage(__METHOD__.' - Unknown J! version');
                break;
        }//switch

        return array();
    }//function
}//class
