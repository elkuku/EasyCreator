<?php
/**
 * @package    EasyCreator
 * @subpackage ProjectTypes
 * @author     Nikolai Plath
 * @author     Created on 16-May-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * EasyCreator project type library.
 */
class EcrProjectTypeLibrary extends EcrProjectBase
{
    /**
     * Project type.
     *
     * @var string
     */
    public $type = 'library';

    /**
     * Project prefix.
     *
     * @var string
     */
    public $prefix = 'lib_';

    //-- @Joomla!-compat 2.5
    public $JCompat = '2.5';

    /**
     * Translate the type
     * @return string
     */
    public function translateType()
    {
        return jgettext('Library');
    }

    /**
     * Translate the plural type
     * @return string
     */
    public function translateTypePlural()
    {
        return jgettext('Libraries');
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
        return jngettext('%d Library', '%d Libraries', $n);
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

        if(JFolder::exists(JPATH_PLATFORM.DS.$this->comName))
        {
            $this->copies[] = JPATH_PLATFORM.DS.$this->comName;
        }
        else if(JFolder::exists(JPATH_LIBRARIES.DS.$this->comName))
        {
            $this->copies[] = JPATH_LIBRARIES.DS.$this->comName;
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
     * @param string $scope
     *
     * @return array
     */
    public function getLanguagePaths($scope = '')
    {
        return array('site' => JPATH_SITE);
    }//function

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
     * @return string The file name
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
        return JFolder::folders(JPATH_LIBRARIES);
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
        //-- @Joomla!-version-check
        switch(ECR_JVERSION)
        {
            case '2.5':
                return array('cms', 'joomla', 'phpmailer', 'phputf8', 'simplepie');
                break;

            case '3.0':
            case '3.1':
                return array('cms', 'compat', 'joomla', 'legacy', 'phpmailer', 'phputf8', 'simplepie');
                break;

            case '3.2':
	        return array('cms', 'compat', 'fof', 'framework', 'idna_convert', 'joomla', 'legacy', 'phpmailer',
                    'phputf8', 'simplepie');
                break;

	        case '3.3':
		        return array('cms', 'compat', 'fof', 'framework', 'idna_convert', 'joomla', 'legacy', 'phpass',
		                     'phpmailer', 'phputf8', 'simplepie');
                break;

	        case '3.4':
		        return array('cms', 'compat', 'fof', 'framework', 'idna_convert', 'joomla', 'legacy', 'phpass',
		                     'phpmailer', 'phputf8', 'simplepie', 'vendor');
		        break;

	        default:
                EcrHtml::message(__METHOD__.' - Unknown J! version');
            break;
        }//switch

        return array();
    }//function
}//class
