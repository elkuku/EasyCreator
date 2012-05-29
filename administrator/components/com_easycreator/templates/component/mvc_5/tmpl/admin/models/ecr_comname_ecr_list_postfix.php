<?php
##*HEADER*##

jimport('joomla.application.component.model');

/**
 * ECR_COM_NAME Model.
 *
 * @package    ECR_COM_NAME
 * @subpackage Models
 */
class ECR_COM_NAMEECR_LIST_POSTFIXModelECR_COM_NAMEECR_LIST_POSTFIX extends JModel
{
    /**
     * ECR_COM_NAMEECR_LIST_POSTFIX data array
     *
     * @var array
     */
    protected $_data;

    /**
     * Items total
     *
     * @var integer
     */
    protected $_total = null;

    /**
     * Pagination object
     *
     * @var object
     */
    protected $_pagination = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $app = JFactory::getApplication('administrator');

        //-- Get pagination request variables
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitstart = $app->getUserStateFromRequest('ECR_COM_COM_NAME.limitstart', 'limitstart', 0, 'int');

        //-- In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
    }

    /**
     * Returns the query.
     *
     * @return string The query to be used to retrieve the rows from the database
     */
    private function _buildQuery()
    {
        $query = 'SELECT * '
            .' FROM #__ECR_COM_TBL_NAME ';

        return $query;
    }

    /**
     * Retrieves the data.
     *
     * @return array Array of objects containing the data from the database
     */
    public function getData()
    {
        //-- Lets load the data if it doesn't already exist
        if(empty($this->_data))
        {
            $query = $this->_buildQuery();
            $this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_data;
    }

    /**
     * Get the items total.
     *
     * @return integer
     */
    public function getTotal()
    {
        //-- Load the content if it doesn't already exist
        if(empty($this->_total))
        {
            $query = $this->_buildQuery();
            $this->_total = $this->_getListCount($query);
        }

        return $this->_total;
    }

    /**
     * Get the pagination object.
     *
     * @return object JPagination
     */
    public function getPagination()
    {
        //-- Load the pagination object if it doesn't already exist
        if(empty($this->_pagination))
        {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_pagination;
    }
}//class
