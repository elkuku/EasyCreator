<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage ProjectTypes
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 24-Mar-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * EasyCreator project type component.
 */
class EasyProjectComponent extends EasyProject
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
     * Find all files and folders belonging to the project.
     *
     * @return array
     */
    public function findCopies()
    {
        if($this->copies)
        return $this->copies;

        if(JFolder::exists(JPATH_ADMINISTRATOR.'/components/'.$this->comName))
        {
            $this->copies[] = JPATH_ADMINISTRATOR.'/components/'.$this->comName;
        }

        if(JFolder::exists(JPATH_SITE.'/components/'.$this->comName))
        {
            $this->copies[] = JPATH_SITE.'/components/'.$this->comName;
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
        return array('site', 'admin', 'menu', 'js_admin', 'js_site');
    }//function

    /**
     * Gets the paths to language files.
     *
     * @param string $scope The scope - admin, site. etc.
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

        switch($this->JCompat)
        {
            case '1.5':
                $paths['admin'] = JPATH_ADMINISTRATOR;
                $paths['menu'] = JPATH_ADMINISTRATOR;
                $paths['site'] = JPATH_SITE;

                if(isset($this->buildOpts['lng_separate_javascript'])
                && ($this->buildOpts['lng_separate_javascript']) == 'ON')
                {
                    $paths['js_admin'][] = JPATH_ADMINISTRATOR.'/components/'.$this->comName;
                    $paths['js_site'][] = JPATH_SITE.'/components/'.$this->comName;
                }
                break;

            case '1.6':
            case '1.7':
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
                ecrHTML::displayMessage(__METHOD__.' - Unsupported JVersion');

                return array();
                break;
        }//switch

        if($scope && ! array_key_exists($scope, $paths))
        throw new Exception('Unknown scope: '.$scope);

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
     * Get a Joomla! manifest XML file name.
     *
     * @return string
     */
    public function getJoomlaManifestName()
    {
        switch(ECR_JVERSION)
        {
            case '1.5':
                return 'manifest.xml';
                break;

            case '1.6':
            case '1.7':
                return $this->comName.'.xml';
                break;

            default:
                ecrHTML::displayMessage(__METHOD__.' - Unsupported JVersion');

                return array();
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

        switch(ECR_JVERSION)
        {
            case '1.5':
                $dtd = array(
                'type' => 'install'
                , 'public' => '-//Joomla! 1.5//DTD component 1.0//EN'
                , 'uri' => 'http://joomla.org/xml/dtd/1.5/component-install.dtd');
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
        return $this->comName;
    }//function

    /**
     * Get the project Id.
     *
     * @return integer Id
     */
    public function getId()
    {
        $db = JFactory::getDbo();

        switch(ECR_JVERSION)
        {
            case '1.5':
                $query = new JDatabaseQuery;

                $query->from('#__components AS c');
                $query->select('c.id');
                $query->where('c.option = '.$db->quote($this->comName));
                $query->where('c.parent = 0');
                break;

            case '1.6':
            case '1.7':
                $query = $db->getQuery(true);

                $query->from('#__extensions AS e');
                $query->select('e.extension_id');
                $query->where('e.element = '.$db->quote($this->comName));
                $query->where('e.type = '.$db->quote('component'));
                break;

            default:
                ecrHTML::displayMessage(__METHOD__.' - Unsupported JVersion');

                return false;
                break;
        }//switch

        $db->setQuery($query);

        $id = $db->loadResult();

        return $id;
    }//function

    /**
     * Discover all projects.
     *
     * @return array
     */
    public function getAllProjects()
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

        switch(ECR_JVERSION)
        {
            case '1.5':
                $projects = array(
                'com_admin', 'com_banners', 'com_cache', 'com_categories', 'com_checkin', 'com_config'
                , 'com_contact', 'com_content', 'com_cpanel', 'com_frontpage', 'com_installer', 'com_languages', 'com_login'
                , 'com_massmail', 'com_media', 'com_menus', 'com_messages', 'com_modules', 'com_newsfeeds', 'com_plugins'
                , 'com_poll', 'com_search', 'com_sections', 'com_templates', 'com_trash', 'com_users', 'com_weblinks'
                );
                break;

            case '1.6':
            case '1.7':
                $projects = array(
                'com_admin', 'com_banners', 'com_cache', 'com_categories', 'com_checkin', 'com_config'
                , 'com_contact', 'com_content', 'com_cpanel', 'com_installer', 'com_languages', 'com_login'
                , 'com_media', 'com_menus', 'com_messages', 'com_modules', 'com_newsfeeds', 'com_plugins'
                , 'com_redirect', 'com_search', 'com_templates', 'com_users', 'com_weblinks'
                );
                break;

            default:
                JError::raiseWarning(0, __METHOD__.' - Unknown J! version');
                break;
        }//switch

        return $projects;
    }//function

    /**
     * Updates the administration main menu.
     *
     * @return boolean
     */
    protected function updateAdminMenu()
    {
        $menu = JRequest::getVar('menu', array());

        if( ! isset($menu['text'])
        || ! $menu['text'])
        {
            $this->setError(__METHOD__.' - Empty admin menu');

            return false;
        }

        switch(ECR_JVERSION)
        {
            case '1.5':
                break;

            case '1.6':
            case '1.7':
                $db = JFactory::getDbo();

                $query	= $db->getQuery(true);

                $query->from('#__menu AS m');
                $query->leftJoin('#__extensions AS e ON m.component_id = e.extension_id');
                $query->select('m.id, e.extension_id');
                $query->where('m.parent_id = 1');
                $query->where("m.client_id = 1");
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
                JError::raiseWarning(0, __METHOD__.' - Unknown J! version');

                return false;
                break;
        }//switch

        $menu['ordering'] = 0;
        $mId = $this->setDbMenuItem($menu);

        //-- Submenu
        $submenu = JRequest::getVar('submenu', array());

        foreach($submenu as $menu)
        {
            if(isset($menu['text'])
            && $menu['text'])
            {
                switch(ECR_JVERSION)
                {
                    case '1.5':
                        break;

                    case '1.6':
                    case '1.7':
                        $menu['level'] = 2;
                        $menu['parent'] = $mId;
                        break;

                    default:
                        JError::raiseWarning(0, __METHOD__.' - Unknown J! version');

                        return false;
                        break;
                }//switch

                $this->setDbMenuItem($menu);
            }
        }//foreach

        $this->readMenu();

        if('1.5' == ECR_JVERSION)
        {
            //-- Remove admin submenu items
            foreach($this->submenu as $dbMenu)
            {
                $found = false;

                foreach($submenu as $menu)
                {
                    if($dbMenu['menuid'] == $menu['menuid'])
                    {
                        $found = true;
                        break;
                    }
                }//foreach

                if( ! $found)
                {
                    if( ! $this->removeAdminMenu($dbMenu))
                    return false;
                }
            }//foreach
        }

        return true;
    }//function

    /**
     * Add a submenu entry.
     *
     * @param string $text Menu title
     * @param string $link Menu link
     * @param string $image Menu image
     *
     * @return bool true on success
     */
    public function addSubmenuEntry($text, $link, $image = '')
    {
        $item = array();

        $item['menuid'] = 0;
        $item['text'] = $text;//-- J1.5
        $item['alias'] = $text;//-- J1.6
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

        switch(ECR_JVERSION)
        {
            case '1.5':
                $query = new JDatabaseQuery;

                $query->select('c.*');
                $query->from('#__components AS c');
                $query->where('c.admin_menu_link = '.$db->quote('option='.$this->comName));
                $query->where('c.parent = 0');

                $db->setQuery($query);

                $dbRow = $db->loadObject();

                if( ! $dbRow)
                return;

                $this->menu['text'] = $dbRow->name;
                $this->menu['link'] = $dbRow->admin_menu_link;
                $this->menu['img'] = $dbRow->admin_menu_img;
                $this->menu['menuid'] = $dbRow->id;

                //-- Get submenu entries
                $query->clear('where');

                $query->where('parent = '.$this->menu['menuid']);
                $query->order('ordering');

                $db->setQuery($query);

                $subMenus = $db->loadObjectList();

                if( ! $subMenus)
                return;

                $i = 0;

                foreach($subMenus as $subMenu)
                {
                    $this->submenu[$i]['text'] = $subMenu->name;
                    $this->submenu[$i]['link'] = $subMenu->admin_menu_link;
                    $this->submenu[$i]['img'] = $subMenu->admin_menu_img;
                    $this->submenu[$i]['ordering'] = $subMenu->ordering;
                    $this->submenu[$i]['menuid'] = $subMenu->id;

                    $i++;
                }//foreach

                break;

            case '1.6':
            case '1.7':
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

                $this->menu['text'] = $dbRow->title;//...
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
                    $this->submenu[$i]['ordering'] = 0;//$submenu->ordering;
                    $this->submenu[$i]['menuid'] = $submenu->id;

                    $i ++;
                }//foreach
                break;

            default:
                JError::raiseWarning(100, __METHOD__.' - Unknown JVersion');

                return false;
                break;
        }//switch

        return;
    }//function

    /**
     * Method to remove admin menu references to a component
     *
     * @param   object  $component	Component table object
     *
     * @return  boolean  True if successful
     */
    protected function removeAdminMenus($row)
    {
        //-- Initialise Variables
        $db = JFactory::getDbo();
        $table = JTable::getInstance('menu');
        $id = $row->extension_id;

        //-- Get the ids of the menu items
        $query = $db->getQuery(true);

        $query->from('#__menu');
        $query->select('id');
        $query->where('`client_id` = 1');
        $query->where('`component_id` = '.(int)$id);

        $db->setQuery($query);

        $ids = $db->loadResultArray();

        //-- Check for error
        if($error = $db->getErrorMsg() || empty($ids))
        {
            JError::raiseWarning('', jgettext('There was a problem updating the admin menu'));

            if($error && $error != 1)
            {
                JError::raiseWarning(100, $error);
            }

            return false;
        }
        else
        {
            //-- Iterate the items to delete each one.
            foreach($ids as $menuid)
            {
                if( ! $table->delete((int)$menuid))
                {
                    $this->setError($table->getError());

                    return false;
                }
            }//foreach

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
     * @return bool true on success
     */
    protected function setDbMenuItem($item)
    {
        $db = JFactory::getDBO();

        switch(ECR_JVERSION)
        {
            case '1.5':
                $query = new JDatabaseQuery;

                if( ! (int)$item['menuid'])
                {
                    //-- New item - submenus oly
                    $query->insert('#__components');
                    $query->set('name = '.$db->quote($item['text']));
                    $query->set('admin_menu_alt = '.$db->quote($item['text']));
                    $query->set('admin_menu_link = '.$db->quote($item['link']));
                    $query->set('admin_menu_img = '.$db->quote($item['img']));

                    if(isset($item['ordering']))
                    $query->set('ordering = '.(int)$item['ordering']);

                    $query->set('parent = '.(int)$item['parent']);
                }
                else
                {
                    //-- Update existing item
                    $query->update('#__components');
                    $query->set('name = '.$db->quote($item['text']));
                    $query->set('admin_menu_alt = '.$db->quote($item['text']));
                    $query->set('admin_menu_link = '.$db->quote($item['link']));
                    $query->set('admin_menu_img = '.$db->quote($item['img']));
                    $query->set('ordering = '.(int)$item['ordering']);
                    $query->where('id = '.(int)$item['menuid']);
                }
                break;

            case '1.6':
            case '1.7':
                $table	= JTable::getInstance('menu');

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

                if( ! $table->setLocation($data['parent_id'], 'last-child')
                || ! $table->bind($data)
                || ! $table->check()
                || ! $table->store())
                {
                    $this->setError($table->getError());

                    return false;
                }

                $parent_id = $table->id;

                // Rebuild the whole tree
                $table->rebuild();

                return $parent_id;

                break;

            default:
                ecrHTML::displayMessage(__METHOD__.' - Unknown JVersion', 'error');

                return false;
                break;
        }//switch

        $db->setQuery($query);

        if( ! $db->query())
        {
            ecrHTML::displayMessage($db->stderr(true));

            return false;
        }

        return true;
    }//function

    /**
     * Remove an admin menu entry.
     * For Joomla! 1.5 only !
     *
     * @param array $item Item to remove
     */
    private function removeAdminMenu($item)
    {
        $query = new JDatabaseQuery;

        $db = JFactory::getDBO();

        $query->from('#__components');
        $query->delete();
        $query->where('id='.$item['menuid']);

        $db->setQuery($query);

        if( ! $db->query())
        {
            ecrHTML::displayMessage($db->getErrorMsg(), 'error');

            return false;
        }

        return true;
    }//function
}//class
