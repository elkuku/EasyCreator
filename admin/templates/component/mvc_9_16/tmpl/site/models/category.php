<?php
##*HEADER*##

jimport('joomla.application.component.model');

/**
 * ECR_COM_NAME Model.
 *
 * @package    ECR_COM_NAME
 * @subpackage Models
 */
class ECR_COM_NAMEModelCategory extends JModel
{
    /**
     * Category id
     *
     * @var int
     */
    private $_id = null;

    /**
     * Category data array
     *
     * @var array
     */
    private $_data = null;

    /**
     * Category total
     *
     * @var integer
     */
    private $_total = null;

    /**
     * Category data
     *
     * @var object
     */
    private $_category = null;

    /**
     * Pagination object
     *
     * @var object
     */
    private $_pagination = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $app = JFactory::getApplication('site');
        $config = JFactory::getConfig();

        parent::__construct();

        //-- Get the pagination request variables
        $this->setState('limit', $app->getUserStateFromRequest('ECR_COM_COM_NAME.limit'
        , 'limit', $config->get('config.list_limit'), 'int'));

        $this->setState('limitstart', JRequest::getVar('limitstart', 0, '', 'int'));

        //-- In case limit has been changed, adjust limitstart accordingly
        $this->setState('limitstart', ($this->getState('limit') != 0
        ? (floor($this->getState('limitstart') / $this->getState('limit')) * $this->getState('limit'))
        : 0));

        //-- Get the filter request variables
        $this->setState('filter_order', JRequest::getCmd('filter_order', 'id'));
        $this->setState('filter_order_dir', JRequest::getCmd('filter_order_Dir', 'ASC'));

        $id = JRequest::getVar('id', 0, '', 'int');
        $this->setId((int)$id);
    }

    /**
     * Method to set the category id.
     *
     * @param integer $id Category ID number
     *
     * @return void
     */
    private function setId($id)
    {
        //-- Set category ID and wipe data
        $this->_id = $id;
        $this->_category = null;
    }

    /**
     * Method to get ECR_COM_NAME item data for the category.
     *
     * @access public
     * @return array
     */
    public function getData()
    {
        //-- Lets load the content if it doesn't already exist
        if(empty($this->_data))
        {
            $query = $this->buildQuery();

            $this->_data = $this->_getList($query, $this->getState('limitstart')
            , $this->getState('limit'));

            $total = count($this->_data);

            for($i = 0; $i < $total; $i ++)
            {
                $item =& $this->_data[$i];
                $item->slug = $item->id;
            }//for
        }

        return $this->_data;
    }

    /**
     * Method to get the total number of ECR_COM_NAME items for the category.
     *
     * @access public
     * @return integer
     */
    public function getTotal()
    {
        //-- Lets load the content if it doesn't already exist
        if(empty($this->_total))
        {
            $query = $this->buildQuery();
            $this->_total = $this->_getListCount($query);
        }

        return $this->_total;
    }

    /**
     * Method to get a pagination object of the ECR_COM_NAME items for the category.
     *
     * @access public
     * @return object JPagination
     */
    public function getPagination()
    {
        //-- Lets load the content if it doesn't already exist
        if(empty($this->_pagination))
        {
            jimport('joomla.html.pagination');

            $this->_pagination = new JPagination($this->getTotal()
            , $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_pagination;
    }

    /**
     * Method to get category data for the current category.
     *
     * @return object Category
     */
    public function getCategory()
    {
        //-- Load the Category data
        if($this->loadCategory())
        {
            //-- Make sure we have a category
            if( ! $this->_category)
            {
                JFactory::getApplication()->enqueueMessage(JText::_('Invalid category'), 'error');

                return false;
            }

            //-- Make sure the category is published
            if( ! $this->_category->published)
            {
                JFactory::getApplication()->enqueueMessage(JText::_('Resource Not Found'), 'error');

                return false;
            }

            //-- Check whether category access level allows access
            if($this->_category->access > JFactory::getUser()->get('aid', 0))
            {
//                JFactory::getApplication()->enqueueMessage(JText::_('ALERTNOTAUTH'), 'error');

//                return false;
            }
        }

        return $this->_category;
    }

    /**
     * Method to load category data if it doesn't exist.
     *
     * @return boolean True on success
     */
    private function loadCategory()
    {
        if(empty($this->_category))
        {
            // current category info
            $query = $this->_db->getQuery(true)
                ->from($this->_db->quoteName('#__categories').' AS c')
                ->select('c.*, c.id as slug')
                ->where('c.id = '.(int)$this->_id)
                ->where('c.extension = '.$this->_db->quote('ECR_COM_COM_NAME'));

            $this->_db->setQuery($query, 0, 1);
            $this->_category = $this->_db->loadObject();
        }

        return true;
    }

    /**
     * Build the SELECT query.
     *
     * @return string
     */
    private function buildQuery()
    {
        $filter_order = $this->getState('filter_order');
        $filter_order_dir = $this->getState('filter_order_dir');

        $filter_order = JFilterInput::clean($filter_order, 'cmd');
        $filter_order_dir = JFilterInput::clean($filter_order_dir, 'word');

        // We need to get a list of all weblinks in the given category
        $query = $this->_db->getQuery(true)
            ->from($this->_db->quoteName('#__ECR_COM_TBL_NAME'))
            ->select('*')
            ->where('catid = '.(int)$this->_id)
            ->order($filter_order.' '.$filter_order_dir);

        return $query;
    }
}
