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
     * @var string Project type.
     */
    public $type = 'cliapp';

    /**
     * @var string Project prefix.
     */
    public $prefix = 'cap_';

    public $JCompat = '1.6';

    /**
     * @var bool If the project is installable through the Joomla! installer.
     */
    public $isInstallable = false;

    /**
     * Find all files and folders belonging to the project.
     *
     * @return array
     */
    public function findCopies()
    {
        if($this->copies)
            return $this->copies;

        $this->copies = JFolder::files($this->getExtensionPath(), '.', true, true);

        return $this->copies;
    }

    /**
     * Gets the language scopes for the extension type.
     *
     * @return array Indexed array.
     */
    public function getLanguageScopes()
    {
        JFactory::getApplication()->enqueueMessage(__METHOD__.' unfinished', 'warning');

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
        return JPATH_SITE.'/cliapps/'.$this->comName;
    }

    /**
     * Gets the paths to language files.
     *
     * @param string $scope
     *
     * @return array
     */
    public function getLanguagePaths($scope = '')
    {
        return array('site' => JPATH_SITE);
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
        return $this->prefix.$this->comName.'.ini';
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

        switch(ECR_JVERSION)
        {
            case '1.6':
            case '1.7':
            case '2.5':
                break;

            default:
                EcrHtml::displayMessage(__METHOD__.' - Unknown J! version');
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
        return $this->prefix.$this->comName;
    }

    /**
     * Get the path for the Joomla! XML manifest file.
     *
     * @return string
     */
    public function getJoomlaManifestPath()
    {
        return $this->getExtensionPath();
    }

    /**
     * Get a Joomla! manifest XML file name.
     *
     * @return mixed [string file name | boolean false on error]
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
        return - 1;
    }

    /**
     * Discover all projects.
     *
     * @param $scope
     *
     * @return array
     */
    public function getAllProjects($scope)
    {
        return JFolder::folders($this->getExtensionPath());
    }

    /**
     * Get a list of known core projects.
     *
     * @param $scope
     *
     * @return array
     */
    public function getCoreProjects($scope)
    {
        return array();
    }
}//class
