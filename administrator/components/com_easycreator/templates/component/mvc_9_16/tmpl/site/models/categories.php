<?php
##*HEADER*##

jimport('joomla.application.component.model');

/**
 * ECR_COM_NAME Categories Model.
 *
 * @package ECR_COM_NAME
 * @subpackage Models
 */
class ECR_COM_NAMEModelCategories extends JModel
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
     * Method to get ECR_COM_NAME item data for the category.
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
     * Method to get the total number of ECR_COM_NAME items for the category.
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
        //-- Query to retrieve all categories that belong to the ECR_COM_NAME extension.
        //-- and that are published.

        $db = $this->_db;

        $query = $db->getQuery(true);

        $query->from($db->quoteName('#__categories').' AS cc')
            ->select('cc.*, COUNT(a.id) AS numitems')
            ->select('cc.id as slug')
            ->join('left', $db->quoteName('#__ECR_COM_TBL_NAME').' AS a ON a.catid = cc.id')
            ->where('extension = '.$db->quote('ECR_COM_COM_NAME'))
            ->where('cc.published = 1')
            ->group('cc.id');

        return $query;
    }//function
}//class
