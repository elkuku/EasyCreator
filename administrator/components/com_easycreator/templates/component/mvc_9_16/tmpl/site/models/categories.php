<?php
##*HEADER*##

jimport('joomla.application.component.model');

/**
 * _ECR_COM_NAME_ Categories Model.
 *
 * @package _ECR_COM_NAME_
 * @subpackage Models
 */
class _ECR_COM_NAME_ModelCategories extends JModel
{
    /**
     * Categories data array
     *
     * @var array
     */
    private $_data = null;

    /**
     * Categories total
     *
     * @var integer
     */
    private $_total = null;

    /**
     * Constructor
     *
     * @since
     */

    /**
     * Method to get _ECR_COM_NAME_ item data for the category.
     *
     * @access public
     * @return array
     */
    public function getData()
    {
        // Lets load the content if it doesn't already exist
        if(empty($this->_data))
        {
            $query = $this->_buildQuery();
            $this->_data = $this->_getList($query);
        }

        return $this->_data;
    }//function

    /**
     * Method to get the total number of _ECR_COM_NAME_ items for the category.
     *
     * @access public
     * @return integer
     */
    public function getTotal()
    {
        // Lets load the content if it doesn't already exist
        if(empty($this->_total))
        {
            $query = $this->_buildQuery();
            $this->_total = $this->_getListCount($query);
        }

        return $this->_total;
    }//function

    /**
     * Build the SELECT query.
     *
     * @return string
     */
    public function _buildQuery()
    {
        //-- Query to retrieve all categories that belong to the _ECR_COM_NAME_ extension.
        //-- and that are published.

        $db = $this->_db;

        $query = $db->getQuery(true);

        $query->from($db->quoteName('#__categories').' AS cc')
            ->select('cc.*, COUNT(a.id) AS numitems')
            ->select('cc.id as slug')
            ->join('left', $db->quoteName('#___ECR_COM_TBL_NAME_').' AS a ON a.catid = cc.id')
            ->where('extension = '.$db->quote('_ECR_COM_COM_NAME_'))
            ->where('cc.published = 1')
            ->group('cc.id');

        return $query;
    }//function
}//class
