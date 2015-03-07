<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage ProjectTypes
 * @author     Nikolai Plath
 * @author     Created on 04-Mar-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * EasyCreator project type Web Application.
 */
class EcrProjectTypeWebapp extends EcrProjectBase
{
    /**
     * @var string $type Project type.
     */
    public $type = 'webapp';

    /**
     * @var string $prefix Project prefix.
     */
    public $prefix = 'wap_';

    //-- @Joomla!-compat 2.5
    public $JCompat = '2.5';

    /**
     * @var bool If the project is installable through the Joomla! installer.
     */
    public $isInstallable = false;

    /**
     * Translate the type
     * @return string
     */
    public function translateType()
    {
        return jgettext('Web Application');
    }

    /**
     * Translate the plural type
     * @return string
     */
    public function translateTypePlural()
    {
        return jgettext('Web Applications');
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
        return jngettext('%d Web Application', '%d Web Applications', $n);
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

        $this->copies = array($this->getExtensionPath());

        if(false == $this->copies)
            $this->copies = array();

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
        return JPATH_SITE.'/webapps/'.$this->comName;
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
                EcrHtml::message(__METHOD__.' - Unknown J! version');
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
     * @return string The file name
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
        return -1;
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
