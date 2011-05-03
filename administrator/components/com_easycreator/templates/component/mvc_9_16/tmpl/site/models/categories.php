<?php
##*HEADER*##

jimport('joomla.application.component.model');

/**
 * _ECR_COM_NAME_ Categories Model.
 *
 * @package _ECR_COM_NAME_
 * @subpackage	Models
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
        $user =& JFactory::getUser();
        $aid = $user->get('aid', 0);

        //-- Query to retrieve all categories that belong under the _ECR_COM_NAME_ section
        //-- and that are published.
        $query = 'SELECT cc.*, COUNT(a.id) AS numitems,'
        .' cc.id as slug'
        .' FROM #__categories AS cc'
        .' LEFT JOIN #___ECR_COM_TBL_NAME_ AS a ON a.catid = cc.id'
//        .' WHERE a.published = 1'
        .' WHERE extension = \'_ECR_COM_COM_NAME_\''
        .' AND cc.published = 1'
//        .' AND cc.access <= '.(int)$aid
        .' GROUP BY cc.id'
        .' ORDER BY cc.ordering';

        return $query;
    }//function
}//class
