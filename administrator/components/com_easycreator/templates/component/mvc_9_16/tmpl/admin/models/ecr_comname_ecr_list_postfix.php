<?php
##*HEADER*##

jimport('joomla.application.component.model');

/**
 * _ECR_COM_NAME_ Model.
 *
 * @package    _ECR_COM_NAME_
 * @subpackage Models
 */
class _ECR_COM_NAME_Model_ECR_COM_NAME__ECR_LIST_POSTFIX_ extends JModel
{
    /**
     * _ECR_COM_NAME_ data array
     *
     * @var array
     */
    private $_data;

    /**
     * Items total
     *
     * @var integer
     */
    private $_total = null;

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
        parent::__construct();

        $application = JFactory::getApplication('administrator');

        //-- Get pagination request variables
        $limit = $application->getUserStateFromRequest('global.list.limit'
        , 'limit', $application->getCfg('list_limit'), 'int');

        $limitstart = $application->getUserStateFromRequest('_ECR_COM_COM_NAME_.limitstart'
        , 'limitstart', 0, 'int');

        //-- In case limit has been changed, adjust it
        $limitstart =($limit != 0) ? floor($limitstart / $limit) * $limit : 0;

        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
    }//function

    /**
     * Build the query.
     *
     * @return string The query to be used to retrieve the rows from the database
     */
    private function _buildQuery()
    {
/*admin.models.model._ECR_COM_TBL_NAME_.buildquery*/

        return $query;
    }//function

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

            $this->_data = $this->_getList($query, $this->getState('limitstart')
            , $this->getState('limit'));
        }

        return $this->_data;
    }//function

    /**
     * Get the records total.
     *
     * @return integer Total
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
    }//function

    /**
     * Get the pagination.
     *
     * @return object JPagination
     */
    public function getPagination()
    {
        //-- Load the content if it doesn't already exist
        if(empty($this->_pagination))
        {
            jimport('joomla.html.pagination');

            $this->_pagination = new JPagination($this->getTotal()
            , $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_pagination;
    }//function
}//class
