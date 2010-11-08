<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage ProjectTypes
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 24-Mar-2010
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...
 *
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
                ecrHTML::displayMessage('Unsupported JVersion in EasyProjectComponent::getId()');

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
                return $this->comName.'.xml';
                break;

            default:
                ecrHTML::displayMessage('Unsupported JVersion in EasyProjectComponent::getJoomlaManifestName()');

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
        $v = substr($jVersion, 0, 3);

        $dtd = false;

        switch($v)
        {
            case '1.5':
                $dtd = array(
                'type' => 'install'
                , 'public' => '-//Joomla! 1.5//DTD component 1.0//EN'
                , 'uri' => 'http://joomla.org/xml/dtd/1.5/component-install.dtd');
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
                $query = new JDatabaseQuery();

                $query->from('#__components AS c');
                $query->select('c.id');
                $query->where('c.option = '.$db->quote($this->comName));
                $query->where('c.parent = 0');
                break;

            case '1.6':
                $query = $db->getQuery(true);

                $query->from('#__extensions AS e');
                $query->select('e.extension_id');
                $query->where('e.element = '.$db->quote($this->comName));
                $query->where('e.type = '.$db->quote('component'));
                break;

            default:
                ecrHTML::displayMessage('Unsupported JVersion in EasyProjectComponent::getId()');

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
                $projects = array(
                'com_admin', 'com_banners', 'com_cache', 'com_categories', 'com_checkin', 'com_config'
                , 'com_contact', 'com_content', 'com_cpanel', 'com_installer', 'com_languages', 'com_login'
                , 'com_media', 'com_menus', 'com_messages', 'com_modules', 'com_newsfeeds', 'com_plugins'
                , 'com_redirect', 'com_search', 'com_templates', 'com_users', 'com_weblinks'
                );
                break;

            default:
                JError::raiseWarning(100, 'EasyProjectComponent::getCoreProjects Unknown scope');
                break;
        }//switch

        return $projects;
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

                //--Get submenu entries
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
                $query = $db->getQuery(true);

                $query->from('#__menu AS m');
                $query->select('m.*');
                $query->where('m.title = '.$db->quote($this->comName));
                $query->where('m.parent_id = 1');

                $db->setQuery($query);

                $dbRow = $db->loadObject();

                if( ! $dbRow)
                return;

                $this->menu['text'] = $dbRow->alias;//...
                $this->menu['link'] = $dbRow->link;
                $this->menu['img'] = $dbRow->img;
                $this->menu['menuid'] = $dbRow->id;

                //--Get submenu entries
                $query->clear('where');

                $query->where('m.parent_id = '.$this->menu['menuid']);

                $submenus = $db->loadObjectList();

                if( ! $submenus)
                return;

                $i = 0;

                foreach($submenus as $submenu)
                {
                    //-- Submenu entries
                    $this->submenu[$i]['text'] = $submenu->alias;
                    $this->submenu[$i]['link'] = $submenu->link;
                    $this->submenu[$i]['img'] = $submenu->img;
                    $this->submenu[$i]['ordering'] = $submenu->ordering;
                    $this->submenu[$i]['menuid'] = $submenu->id;

                    $i++;
                }//foreach
                break;

            default:
                JError::raiseWarning(100, 'Invalid project id in project::readmenu');

                return false;
                break;
        }//switch

        return;
    }//function

    /**
     * Updates a menu entry in database / Insert new one if not exists.
     *
     * @param array $item The menu.
     *
     * @return bool true on success
     * @todo move
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
                $table	= JTable::getInstance('menu');

                if( ! $item['menuid'])
                {
                    //-- New item
                    //                    $query->clear();
                    //                    $query->insert('#__menu');
                    //                    $query->set('menutype = '.$db->quote('_adminmenu'));
                    //                    $query->set('title = '.$db->quote($this->comName));
                    //                    $query->set('alias = '.$db->quote($item['text']));
                    //                    $query->set('link = '.$db->quote($item['link']));
                    //                    $query->set('img = '.$db->quote($item['img']));
                    //                    $query->set('ordering = '.(int)$item['ordering']);
                    //
                    //                    $query->set('component_id = '.(int)$this->dbId);
                    //                    $query->set('published = 0');
                    //                    $query->set('home = 0');
                    //                    $query->set('parent_id = '.(int)$item['parent']);

                    $data = array();
                    $data['menutype'] = '_adminmenu';
                    $data['title'] = $this->comName;
                    $data['alias'] = $item['text'];
                    $data['type'] = 'component';
                    $data['published'] = 0;
                    $data['parent_id'] = (int)$item['parent'];
                    $data['component_id'] = (int)$this->dbId;
                    $data['img'] = $item['img'];
                    $data['link'] = $item['link'];
                    $data['home'] = 0;
                    $data['params'] = '';

                    //if( ! $table->setLocation(1, 'last-child')
                    if( ! $table->bind($data)
                    || ! $table->check()
                    || ! $table->store())
                    {
                        $this->setError($table->getErrorMsg());

                        return false;
                    }

                    return true;
                }
                else
                {
                    //-- Update existing item
                    //                    $query->clear();
                    //                    $query->update('#__menu');
                    //                    $query->where('id = '.(int)$item['menuid']);
                    //                    $query->set('alias = '.$db->quote($item['text']));
                    //                    $query->set('link = '.$db->quote($item['link']));
                    //                    $query->set('img = '.$db->quote($item['img']));
                    //                    $query->set('ordering = '.(int)$item['ordering']);
                    $data = array();
                    $data['id'] = (int)$item['menuid'];
                    $data['menutype'] = '_adminmenu';
                    $data['title'] = $this->comName;
                    $data['alias'] = $item['text'];
                    $data['type'] = 'component';
                    $data['published'] = 0;
                    $data['parent_id'] =(isset($item['parent'])) ? (int)$item['parent'] : 1;
                    $data['component_id'] = (int)$this->dbId;
                    $data['img'] = $item['img'];
                    $data['link'] = $item['link'];
                    $data['home'] = 0;
                    $data['params'] = '';

                    //if( ! $table->setLocation(1, 'last-child')
                    if( ! $table->bind($data)
                    || ! $table->check()
                    || ! $table->store())
                    {
                        $this->setError($table->getError());

                        return false;
                    }

                    return true;
                }

                break;

            default:
                ecrHTML::displayMessage('Unknown JVersion in Easyroject::setDbMenuItem', 'error');

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
}//class
