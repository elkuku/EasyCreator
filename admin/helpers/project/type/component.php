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
 * EasyCreator project type component.
 */
class EcrProjectTypeComponent extends EcrProjectBase
{
    /**
     * Project type.
     *
     * @var string
     */
    public $type = 'component';

    /**
     * Project prefix.
     *
     * @var string
     */
    public $prefix = 'com_';

    /**
     * Translate the type
     * @return string
     */
    public function translateType()
    {
        return jgettext('Component');
    }

    /**
     * Translate the plural type
     * @return string
     */
    public function translateTypePlural()
    {
        return jgettext('Components');
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
        return jngettext('%d Component', '%d Components', $n);
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

        if(JFolder::exists(JPATH_ADMINISTRATOR.'/components/'.$this->comName))
            $this->copies[] = JPATH_ADMINISTRATOR.'/components/'.$this->comName;

        if(JFolder::exists(JPATH_SITE.'/components/'.$this->comName))
            $this->copies[] = JPATH_SITE.'/components/'.$this->comName;

        return $this->copies;
    }//function

    /**
     * Gets the language scopes for the extension type.
     *
     * @return array Indexed array.
     */
    public function getLanguageScopes()
    {
        return array('site', 'admin', 'menu', 'js_admin', 'js_site');
    }//function

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
        static $paths = array();

        if($paths)
        {
            if($scope && isset($paths[$scope]))
            return $paths[$scope];

            return $paths;
        }

        if($this->langFormat != 'ini')
        {
            $paths['admin'][] = JPATH_ADMINISTRATOR.'/components/'.$this->comName.'/language/sources';
            $paths['sys'][] = JPATH_ADMINISTRATOR.'/components/'.$this->comName.'/language/sources';
            $paths['site'][] = JPATH_SITE.'/components/'.$this->comName.'/language/sources';

            if(isset($this->buildOpts['lng_separate_javascript'])
            && ($this->buildOpts['lng_separate_javascript']) == 'ON')
            {
                $paths['js_admin'][] = JPATH_ADMINISTRATOR.'/components/'.$this->comName.'/language/sources';
                $paths['js_site'][] = JPATH_SITE.'/components/'.$this->comName.'/language/sources';
            }

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
                if($scope == 'menu')
                    $scope = 'sys';

                $paths['admin'][] = JPATH_ADMINISTRATOR.'/components/'.$this->comName;
                $paths['admin'][] = JPATH_ADMINISTRATOR;
                $paths['sys'][] = JPATH_ADMINISTRATOR.'/components/'.$this->comName;
                $paths['sys'][] = JPATH_ADMINISTRATOR;
                $paths['site'][] = JPATH_SITE.'/components/'.$this->comName;
                $paths['site'][] = JPATH_SITE;

                if(isset($this->buildOpts['lng_separate_javascript'])
                && ($this->buildOpts['lng_separate_javascript']) == 'ON')
                {
                    $paths['js_admin'][] = JPATH_ADMINISTRATOR.'/components/'.$this->comName;
                    $paths['js_site'][] = JPATH_SITE.'/components/'.$this->comName;
                }
                break;

            default:
                EcrHtml::message(__METHOD__.' - Unsupported JVersion'.$this->JCompat, 'error');

                return array();
                break;
        }//switch

        if($scope && ! array_key_exists($scope, $paths))
            throw new Exception(__METHOD__.' - Unknown scope: '.$scope);

        if($scope && isset($paths[$scope]))
            return $paths[$scope];

        return $paths;
    }//function

    /**
     * Get the name for language files.
     *
     * @param string $scope The scope - admin, site. etc.
     *
     * @return string
     */
    public function getLanguageFileName($scope = '')
    {
        switch($scope)
        {
            case 'menu':
                return $this->comName.'.menu.'.$this->langFormat;
                break;

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
        }//switch
    }//function

    /**
     * Get the path for the Joomla! XML manifest file.
     *
     * @return string
     */
    public function getJoomlaManifestPath()
    {
        return JPATH_ADMINISTRATOR.DS.'components'.DS.$this->comName;
    }//function

    /**
     * Get the extension base path.
     *
     * @return string
     */
    public function getExtensionPath()
    {
        return JPATH_ADMINISTRATOR.DS.'components'.DS.$this->comName;
    }//function

    /**
     * Get a Joomla! manifest XML file name.
     *
     * @return string
     */
    public function getJoomlaManifestName()
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
                return $this->comName.'.xml';
                break;

            default:
                EcrHtml::message(__METHOD__.' - Unsupported JVersion');

                return '';
                break;
        }//switch
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
                EcrHtml::message(__METHOD__.' - Unsupported JVersion');

                break;
        }//switch

        return $dtd;
    }//function

    /**
     * Get a file name for a EasyCreator setup XML file.
     *
     * @param string $type
     *
     * @return string
     */
    public function getEcrXmlFileName($type = '')
    {
        $type =($type) ? '.'.$type : '';

        return $this->getFileName().$type.'.xml';
    }//function

    /**
     * Get a common file name.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->comName;
    }//function

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
            ->where('e.type = '.$db->quote('component'));

        return $db->setQuery($query)
            ->loadResult();
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
        return JFolder::folders(JPATH_ADMINISTRATOR.DS.'components');
    }//function

    /**
     * Get a list of known core projects.
     *
     * @param string $scope The scope - admin, site. etc.
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
                $projects = array(
                    'com_admin', 'com_banners', 'com_cache', 'com_categories', 'com_checkin', 'com_config'
                , 'com_contact', 'com_content', 'com_cpanel', 'com_installer', 'com_languages', 'com_login'
                , 'com_media', 'com_menus', 'com_messages', 'com_modules', 'com_newsfeeds', 'com_plugins'
                , 'com_redirect', 'com_search', 'com_templates', 'com_users', 'com_weblinks', 'com_finder'
                , 'com_joomlaupdate'
                );
                break;

            case '3.0':
            case '3.1':
                $projects = array(
                        'com_admin', 'com_banners', 'com_cache', 'com_categories', 'com_checkin', 'com_config'
                    , 'com_contact', 'com_content', 'com_cpanel', 'com_installer', 'com_languages', 'com_login'
                    , 'com_media', 'com_menus', 'com_messages', 'com_modules', 'com_newsfeeds', 'com_plugins'
                    , 'com_redirect', 'com_search', 'com_templates', 'com_users', 'com_weblinks', 'com_finder'
                    , 'com_joomlaupdate'
                );
            break;

            case '3.2':
	        case '3.3':
	        case '3.4':
                $projects = array(
                    'com_ajax', 'com_admin', 'com_banners', 'com_cache', 'com_categories', 'com_checkin', 'com_config'
                    , 'com_contact', 'com_content', 'com_contenthistory', 'com_cpanel', 'com_finder', 'com_installer', 'com_joomlaupdate'
                    , 'com_languages', 'com_login', 'com_media', 'com_menus', 'com_messages', 'com_modules', 'com_newsfeeds', 'com_plugins'
                    , 'com_postinstall', 'com_redirect', 'com_search', 'com_tags', 'com_templates', 'com_users', 'com_weblinks'
                );
            break;

            default:
                EcrHtml::message(__METHOD__.' - Unsupported JVersion');
                break;
        }//switch

        return $projects;
    }//function

    /**
     * Updates the administration main menu.
     *
     * @throws Exception
     * @return bool
     */
    protected function updateAdminMenu()
    {
        $menu = JFactory::getApplication()->input->get('menu', array(), 'array');

        if( ! isset($menu['text']) || ! $menu['text'])
            throw new Exception(__METHOD__.' - Empty admin menu');

        //-- @Joomla!-version-check
        switch(ECR_JVERSION)
        {
            case '2.5':
            case '3.0':
            case '3.1':
            case '3.2':
	        case '3.3':
	        case '3.4':
	        $db = JFactory::getDbo();

                $query = $db->getQuery(true);

                $query->from('#__menu AS m');
                $query->leftJoin('#__extensions AS e ON m.component_id = e.extension_id');
                $query->select('m.id, e.extension_id');
                $query->where('m.parent_id = 1');
                $query->where('m.client_id = 1');
                $query->where('e.element = '.$db->quote($this->comName));

                $db->setQuery($query);

                $componentrow = $db->loadObject();

                if($componentrow)
                {
                    //-- So... in 1.6 we remove the admin menu first
                    $this->removeAdminMenus($componentrow);
                }

                $menu['parent'] = 1;
                $menu['level'] = 1;
                break;

            default:
                EcrHtml::message(__METHOD__.' - Unsupported JVersion');

                return false;
                break;
        }//switch

        $menu['ordering'] = 0;
        $mId = $this->setDbMenuItem($menu);

        //-- Submenu
        $submenu = JFactory::getApplication()->input->get('submenu', array(), 'array');

        foreach($submenu as $menu)
        {
            if(isset($menu['text'])
            && $menu['text'])
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
	                $menu['level'] = 2;
                        $menu['parent'] = $mId;
                        break;

                    default:
                        EcrHtml::message(__METHOD__.' - Unsupported JVersion');

                        return false;
                        break;
                }//switch

                $this->setDbMenuItem($menu);
            }
        }//foreach

        $this->readMenu();

        return true;
    }//function

    /**
     * Add a submenu entry.
     *
     * @param string $text Menu title
     * @param string $link Menu link
     * @param string $image Menu image
     *
     * @return boolean true on success
     */
    public function addSubmenuEntry($text, $link, $image = '')
    {
        $item = array();

        $item['menuid'] = 0;

        //-- J1.5
        $item['text'] = $text;

        //-- J1.6
        $item['alias'] = $text;
        $item['link'] = $link;
        $item['img'] = $image;
        $item['parent'] = $this->menu['menuid'];
        $item['component_id'] = $this->dbId;

        return $this->setDbMenuItem($item);
    }//function

    /**
     * Read the J! main menu entries for a component from the core components table.
     *
     * @return void
     */
    protected function readMenu()
    {
        $db = JFactory::getDBO();

        $query = $db->getQuery(true);

        $query->from('#__menu AS m');
        $query->leftJoin('#__extensions AS e ON m.component_id = e.extension_id');
        $query->select('m.title, m.link, m.img, m.id, e.extension_id');
        $query->where('m.parent_id = 1');
        $query->where("m.client_id = 1");
        $query->where('e.element = '.$db->quote($this->comName));

        $db->setQuery($query);

        $dbRow = $db->loadObject();

        if( ! $dbRow)
        return;

        $this->menu['text'] = $dbRow->title;
        $this->menu['link'] = $dbRow->link;
        $this->menu['img'] = $dbRow->img;
        $this->menu['menuid'] = $dbRow->id;

        //-- Get submenu entries
        $query->clear('where');

        $query->where('m.parent_id = '.$this->menu['menuid']);
        $query->order('m.id');

        $submenus = $db->loadObjectList();

        if( ! $submenus)
        return;

        $i = 0;

        foreach($submenus as $submenu)
        {
            //-- Submenu entries
            $this->submenu[$i]['text'] = $submenu->title;
            $this->submenu[$i]['link'] = $submenu->link;
            $this->submenu[$i]['img'] = $submenu->img;
            $this->submenu[$i]['ordering'] = 0;
            $this->submenu[$i]['menuid'] = $submenu->id;

            $i ++;
        }//foreach

        return;
    }//function

    /**
     * Method to remove admin menu references to a component
     *
     * @param object $row Component table object
     *
     * @throws Exception
     * @internal param object $component
     *
     * @return bool True if successful
     */
    protected function removeAdminMenus($row)
    {
        //-- Initialise Variables
        $db = JFactory::getDbo();

        /* @var JTableMenu $table */
        $table = JTable::getInstance('menu');
        $id = $row->extension_id;

        //-- Get the ids of the menu items
        $query = $db->getQuery(true);

        $query->from('#__menu');
        $query->select('id');
        $query->where('`client_id` = 1');
        $query->where('`component_id` = '.(int)$id);
        $query->where('`parent_id` = 1');

        $db->setQuery($query);

        $menuId = $db->loadResult();

        //-- Check for errors
        $error = $db->getErrorMsg();

        if($error || empty($menuId))
        {
            JFactory::getApplication()->enqueueMessage(jgettext('There was a problem updating the admin menu'), 'error');

            if($error && $error != 1)
            {
                JFactory::getApplication()->enqueueMessage($error, 'error');
            }

            return false;
        }
        else
        {
                //-- Delete only the parent node - children should be killed by JTable
                if( ! $table->delete((int)$menuId))
                    throw new Exception(__METHOD__.' - '.$table->getError());

            //-- Rebuild the whole tree
            $table->rebuild();
        }

        return true;
    }//function

    /**
     * Updates a menu entry in database / Insert new one if not exists.
     *
     * @param array $item The menu.
     *
     * @throws Exception
     * @return bool true on success
     */
    protected function setDbMenuItem($item)
    {
        $db = JFactory::getDBO();

        //-- @Joomla!-version-check
        switch(ECR_JVERSION)
        {
            case '2.5':
                /* @var JTableMenu $table */
                $table = JTable::getInstance('menu');

                $data = array();
                $data['menutype'] = 'main';
                $data['client_id'] = 1;
                $data['title'] = $item['text'];
                $data['alias'] = $item['text'];
                $data['type'] = 'component';
                $data['published'] = 0;
                $data['level'] = $item['level'];
                $data['parent_id'] = (int)$item['parent'];
                $data['component_id'] = (int)$this->dbId;
                $data['img'] = $item['img'];
                $data['link'] = $item['link'];
                $data['home'] = 0;
                $data['params'] = '';

                $table->setLocation($data['parent_id'], 'last-child');

                if( ! $table->bind($data)
                || ! $table->check()
                || ! $table->store())
                    throw new Exception(__METHOD__.' - '.$table->getError());

                $parent_id = $table->id;

                //-- Rebuild the whole tree
                $table->rebuild();

                return $parent_id;

                break;

            case '3.0':
            case '3.1':
            case '3.2':
	        case '3.3':
	        case '3.4':
		        /* @var JTableMenu $table */
                $table = JTable::getInstance('menu');

                $data = array();
                $data['menutype'] = 'main';
                $data['client_id'] = 1;
                $data['title'] = $item['text'];
                $data['alias'] = $item['text'];
                $data['type'] = 'component';
                $data['published'] = 0;
                $data['level'] = $item['level'];
                $data['parent_id'] = (int)$item['parent'];
                $data['component_id'] = (int)$this->dbId;
                $data['img'] = $item['img'];
                $data['link'] = $item['link'];
                $data['home'] = 0;
                $data['params'] = '';

                $table->setLocation($data['parent_id'], 'last-child');

                if( ! $table->bind($data)
                    || ! $table->check()
                    || ! $table->store())
                    throw new Exception(__METHOD__.' - '.$table->getError());

                $parent_id = $table->id;

                //-- Rebuild the whole tree
                $table->rebuild();

                return $parent_id;

                break;

            default:
                EcrHtml::message(__METHOD__.' - Unknown JVersion', 'error');

                return false;
                break;
        }
    }
}
