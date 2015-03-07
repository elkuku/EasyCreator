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
 * EasyCreator project type plugin.
 */
class EcrProjectTypePlugin extends EcrProjectBase
{
    /**
     * Project type.
     *
     * @var string
     */
    public $type = 'plugin';

    /**
     * Project prefix.
     *
     * @var string
     */
    public $prefix = 'plg_';

    /**
     * Translate the type
     * @return string
     */
    public function translateType()
    {
        return jgettext('Plugin');
    }

    /**
     * Translate the plural type
     * @return string
     */
    public function translateTypePlural()
    {
        return jgettext('Plugins');
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
        return jngettext('%d Plugin', '%d Plugins', $n);
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

        $base = JPATH_SITE.DS.'plugins'.DS.$this->scope.DS.$this->comName;

        //-- J! 1.6
        if(JFolder::exists($base))
            $this->copies[] = $base;

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
        $scopes[] = 'site';

        return $scopes;
    }//function

    /**
     * Get the extension base path.
     *
     * @return string
     */
    public function getExtensionPath()
    {
        return JPATH_SITE.DS.'plugins'.DS.$this->scope.DS.$this->comName;
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
        $paths = array();

        //-- This is NOT an error but a strange J! behavior....
        //-- Language files for plugins always "live" in the "administrator" section.
        $paths['admin'] = JPATH_ADMINISTRATOR;
        $paths['sys'] = JPATH_ADMINISTRATOR;

        return $paths;
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
        $base = $this->prefix.$this->scope.'_'.$this->comName;

        switch($scope)
        {
            case 'sys' :
                return $base.'.sys.'.$this->langFormat;
                break;

            case 'js_admin' :
            case 'js_site' :
                return $base.'.js.'.$this->langFormat;
                break;

            default :
                return $base.'.'.$this->langFormat;
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
        return $this->prefix.$this->scope.'_'.$this->comName;
    }//function

    /**
     * Get the path for the Joomla! XML manifest file.
     *
     * @return string
     */
    public function getJoomlaManifestPath()
    {
        //-- @Joomla!-version-check
        switch(ECR_JVERSION)
        {
            case '2.5':
            case '3.0':
            case '3.1':
            case '3.2':
	        case '3.3':
	        case '3.4':
	        return JPATH_SITE.DS.'plugins'.DS.$this->scope.DS.$this->comName;
                break;

            default:
                EcrHtml::message('Unsupported JVersion', 'error');

                return false;
                break;
        }//switch
    }//function

    /**
     * Get a Joomla! manifest XML file name.
     *
     * @return string The file name
     */
    public function getJoomlaManifestName()
    {
        return $this->comName.'.xml';
    }//function

    /**
     * Get the project Id.
     *
     * @return int Id
     */
    public function getId()
    {
        $db = JFactory::getDBO();

        //-- @Joomla!-version-check
        switch(ECR_JVERSION)
        {
            case '2.5':
            case '3.0':
            case '3.1':
            case '3.2':
	        case '3.3':
	        case '3.4':
	        $query = $db->getQuery(true);

                $query->from('#__extensions AS e');
                $query->select('e.extension_id');
                $query->where('e.element = '.$db->quote($this->comName));
                $query->where('e.type = '.$db->quote('plugin'));

                $db->setQuery($query);
                break;

            default:
                EcrHtml::message('Unsupported JVersion in EcrProjectComponent::getId()');

                return false;
                break;
        }//switch

        $id = $db->loadResult();

        return $id;
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
        $projects = array();

        //-- @Joomla!-version-check
        switch(ECR_JVERSION)
        {
            case '2.5':
            case '3.0':
            case '3.1':
            case '3.2':
	        case '3.3':
	        case '3.4':
		        $projects = JFolder::folders(JPATH_SITE.DS.'plugins'.DS.$scope);
                break;
            default:
                EcrHtml::message(__METHOD__.' - Unsupported JVersion');
                break;
        }//switch

        return $projects;
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
        $projects = array();

        //-- @Joomla!-version-check
        switch(ECR_JVERSION)
        {
            case '2.5':
                switch($scope)
                {
                    case 'authentication':
                        $projects = array('gmail', 'joomla', 'ldap');
                        break;
                    case 'captcha':
                        $projects = array('recaptcha');
                        break;
                    case 'content':
                        $projects = array('emailcloak', 'geshi', 'joomla', 'loadmodule', 'pagebreak'
                        , 'pagenavigation', 'vote', 'finder');
                        break;
                    case 'editors':
                        $projects = array('none', 'tinymce', 'codemirror');
                        break;
                    case 'editors-xtd':
                        $projects = array('article', 'image', 'pagebreak', 'readmore');
                        break;
                    case 'extension':
                        $projects = array('joomla');
                        break;
                    case 'finder':
                        $projects = array('categories', 'contacts', 'content', 'newsfeeds', 'weblinks');
                        break;
                    case 'quickicon':
                        $projects = array('extensionupdate', 'joomlaupdate');
                        break;
                    case 'search':
                        $projects = array('categories', 'contacts', 'content', 'newsfeeds', 'weblinks');
                        break;
                    case 'system':
                        $projects = array('cache', 'debug', 'finder', 'highlight', 'languagefilter', 'languagecode'
                        , 'log', 'logout', 'p3p', 'redirect', 'remember', 'sef');
                        break;
                    case 'user':
                        $projects = array('contactcreator', 'joomla', 'profile');
                        break;
                    default :
                        EcrHtml::message(sprintf(jgettext('%s - Unknown scope: %s'), __METHOD__, $scope), 'error');
                        break;
                }//switch
                break;

            case '3.0':
            case '3.1':
                switch($scope)
                   {
                    case 'authentication':
                        $projects = array('gmail', 'joomla', 'ldap');
                        break;
                    case 'captcha':
                        $projects = array('recaptcha');
                        break;
                    case 'content':
                        $projects = array('emailcloak', 'geshi', 'joomla', 'loadmodule', 'pagebreak'
                        , 'pagenavigation', 'vote', 'finder');
                        break;
                    case 'editors':
                        $projects = array('none', 'tinymce', 'codemirror');
                        break;
                    case 'editors-xtd':
                        $projects = array('article', 'image', 'pagebreak', 'readmore');
                        break;
                    case 'extension':
                        $projects = array('joomla');
                        break;
                    case 'finder':
                        $projects = array('categories', 'contacts', 'content', 'newsfeeds', 'weblinks');
                        break;
                    case 'quickicon':
                        $projects = array('extensionupdate', 'joomlaupdate');
                        break;
                    case 'search':
                        $projects = array('categories', 'contacts', 'content', 'newsfeeds', 'weblinks');
                        break;
                    case 'system':
                        $projects = array('cache', 'debug', 'finder', 'highlight', 'languagefilter', 'languagecode'
                        , 'log', 'logout', 'p3p', 'redirect', 'remember', 'sef');
                        break;
                    case 'user':
                        $projects = array('contactcreator', 'joomla', 'profile');
                        break;
                    default :
                        EcrHtml::message(sprintf(jgettext('%s - Unknown scope: %s'), __METHOD__, $scope), 'error');
                        break;
                }//switch
                break;

            case '3.2':
                switch($scope)
                {
	                case 'authentication':
		                $projects = array('gmail', 'joomla', 'ldap', 'cookie');
		                break;
	                case 'captcha':
		                $projects = array('recaptcha');
		                break;
	                case 'content':
		                $projects = array('emailcloak', 'finder', 'joomla', 'loadmodule', 'pagebreak',
		                                  'pagenavigation', 'vote');
		                break;
	                case 'editors':
		                $projects = array('none', 'tinymce', 'codemirror');
		                break;
	                case 'editors-xtd':
		                $projects = array('article', 'image', 'pagebreak', 'readmore');
		                break;
	                case 'extension':
		                $projects = array('joomla');
		                break;
	                case 'finder':
		                $projects = array('categories', 'contacts', 'content', 'newsfeeds', 'weblinks', 'tags');
		                break;
	                case 'quickicon':
		                $projects = array('extensionupdate', 'joomlaupdate');
		                break;
	                case 'search':
		                $projects = array('categories', 'contacts', 'content', 'newsfeeds', 'weblinks');
		                break;
	                case 'system':
		                $projects = array('cache', 'debug', 'highlight', 'languagefilter', 'languagecode'
		                                  , 'log', 'logout', 'p3p', 'redirect', 'remember', 'sef');
		                break;
	                case 'user':
		                $projects = array('contactcreator', 'joomla', 'profile');
		                break;
	                case 'twofactorauth':
		                $projects = array('totp', 'yubikey');
		                break;
	                default :
		                EcrHtml::message(sprintf(jgettext('%s - Unknown scope: %s'), __METHOD__, $scope), 'error');
		                break;
                }
		        break;

                case '3.3':
                case '3.4':
	                switch($scope)
	                {
		                case 'authentication':
			                $projects = array('gmail', 'joomla', 'ldap', 'cookie');
			                break;
		                case 'captcha':
			                $projects = array('recaptcha');
			                break;
		                case 'content':
			                $projects = array('contact', 'emailcloak', 'finder', 'joomla', 'loadmodule', 'pagebreak',
			                                  'pagenavigation', 'vote');
			                break;
		                case 'editors':
			                $projects = array('none', 'tinymce', 'codemirror');
			                break;
		                case 'editors-xtd':
			                $projects = array('article', 'image', 'pagebreak', 'readmore');
			                break;
		                case 'extension':
			                $projects = array('joomla');
			                break;
		                case 'finder':
			                $projects = array('categories', 'contacts', 'content', 'newsfeeds', 'weblinks', 'tags');
			                break;
		                case 'quickicon':
			                $projects = array('extensionupdate', 'joomlaupdate');
			                break;
		                case 'search':
			                $projects = array('categories', 'contacts', 'content', 'newsfeeds', 'tags', 'weblinks');
			                break;
		                case 'system':
			                $projects = array('cache', 'debug', 'highlight', 'languagefilter', 'languagecode'
			                                  , 'log', 'logout', 'p3p', 'redirect', 'remember', 'sef');
			                break;
		                case 'user':
			                $projects = array('contactcreator', 'joomla', 'profile');
			                break;
		                case 'twofactorauth':
			                $projects = array('totp', 'yubikey');
			                break;

		                case 'installer':
			                $projects = array('webinstaller');
			                break;

                    default :
                        EcrHtml::message(sprintf(jgettext('%s - Unknown scope: %s'), __METHOD__, $scope), 'error');
                        break;
                }//switch
                break;
            default:
                JFactory::getApplication()->enqueueMessage(__METHOD__.' - Unknown J! version', 'error');

                return array();
        }//switch

        return $projects;
    }//function
}//class
