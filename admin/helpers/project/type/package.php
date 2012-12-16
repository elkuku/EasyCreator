<?php
/**
 * @package    EasyCreator
 * @subpackage ProjectTypes
 * @author     Nikolai Plath
 * @author     Created on 29-Mar-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * EasyCreator project type package.
 */
class EcrProjectTypePackage extends EcrProjectBase
{
    /**
     * Project type.
     *
     * @var string
     */
    public $type = 'package';

    /**
     * Project prefix.
     *
     * @var string
     */
    public $prefix = 'pkg_';

    /**
     * Project elements.
     *
     * @var string
     */
    public $elements = array();

    /**
     * Translate the type
     * @return string
     */
    public function translateType()
    {
        return jgettext('Package');
    }

    /**
     * Translate the plural type
     * @return string
     */
    public function translateTypePlural()
    {
        return jgettext('Packages');
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
        return jngettext('%d Package', '%d Packages', $n);
    }

    /**
     * Get the path for the Joomla! XML manifest file.
     *
     * @return string
     */
    public function getJoomlaManifestPath()
    {
        return JPATH_MANIFESTS.DS.'packages';
    }

    /**
     * Get the extension base path.
     *
     * @return string
     */
    public function getExtensionPath()
    {
        //-- Varies =;)

        return '';
    }

    /**
     * Get a list of known core projects.
     *
     * @param string $scope The scope - admin, site. etc.
     *
     * @return array
     */
    public function getCoreProjects($scope)
    {
        return array();
    }

    /**
     * Get a Joomla! manifest XML file name.
     *
     * @return string File name.
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
        $db = JFactory::getDbo();

        $query = $db->getQuery(true)
            ->from('#__extensions AS e')
            ->select('e.extension_id')
            ->where('e.element = '.$db->quote($this->comName))
            ->where('e.type = '.$db->quote('package'));

        $db->setQuery($query);

        return $db->loadResult();
    }

    /**
     * Get a file name for a EasyCreator setup XML file.
     *
     * @return string
     */
    public function getEcrXmlFileName()
    {
        return $this->comName.'.xml';
    }

    /**
     * Get a common file name.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->comName;
    }

    /**
     * Find all files and folders belonging to the project.
     *
     * @todo changes in 1.6
     *
     * @return array
     */
    public function findCopies()
    {
        return $this->copies;
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
        return array();
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
        return false;
    }

    /**
     * Gets the language scopes for the extension type.
     *
     * @return array Indexed array.
     */
    public function getLanguageScopes()
    {
        return array();
    }

    /**
     * Get the name for language files.
     *
     * @param string $scope The scope - admin, site. etc.
     *
     * @return string
     */
    public function getLanguageFileName($scope = '')
    {
        return '';
    }

    /**
     * Gets the paths to language files.
     *
     * @param string $scope The scope - admin, site. etc.
     *
     * @throws Exception
     * @return array
     */
    public function getLanguagePaths($scope = '')
    {
        return array();
    }
}//class
