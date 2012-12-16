<?php
##*HEADER*##

jimport('joomla.application.component.model');

/**
 * ECR_ELEMENT_NAME model.
 *
 * @package    ECR_COM_NAME
 * @subpackage Models
 */
class ECR_COM_NAMEECR_LIST_POSTFIXModelECR_ELEMENT_NAMEs extends JModel
{
    var $_data;

    var $_total = null;

    var $_pagination = null;

    public function __construct()
    {
        parent::__construct();
        global $mainframe, $option;
        $this->filter_order_Dir	= $mainframe->getUserStateFromRequest($option
        .'.filter_order_Dir', 'filter_order_Dir', '', 'word');
        //_ECR_MAT_ORDERING_MODAL1_
        $this->filter_order	= $mainframe->getUserStateFromRequest($option
        .'.filter_order', 'filter_order',	'ordering', 'cmd');

        $this->search = $mainframe->getUserStateFromRequest("$option.search", 'search', '', 'string');
        $this->search = JString::strtolower($this->search);

        $limit = $mainframe->getUserStateFromRequest('global.list.limit'
        , 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = $mainframe->getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
    }

    private function _buildQuery()
    {
        $where = array();

        if($this->search)
        {
            $where[] = 'LOWER(name) LIKE \''.$this->search.'\'';
        }

        $where =(count($where)) ? ' WHERE '.implode(' AND ', $where) : '';
        $orderby = '';

        #_ECR_MAT_FILTER_MODEL1_

        if(($this->filter_order) && ($this->filter_order_Dir))
        {
            $orderby 	= ' ORDER BY '.$this->filter_order.' '.$this->filter_order_Dir;
        }

        $this->_query = ' SELECT *'
        . ' FROM #___ECR_TABLE_NAME_'
        . $where
        . $orderby;

        return $this->_query;
    }

    function getData()
    {
        if(empty($this->_data))
        {
            $query = $this->_buildQuery();
            $this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_data;
    }

    function getList()
    {
        // table ordering
        $lists['order_Dir']	= $this->filter_order_Dir;
        $lists['order']		= $this->filter_order;

        // search filter
        $lists['search'] = $this->search;

        return $lists;
    }

    function getTotal()
    {
        // Load the content if it doesn't already exist
        if(empty($this->_total))
        {
            $query = $this->_buildQuery();
            $this->_total = $this->_getListCount($query);
        }

        return $this->_total;
    }

    function getPagination()
    {
        // Load the content if it doesn't already exist
        if(empty($this->_pagination))
        {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal()
            , $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_pagination;
    }
}//class
