<?php
##*HEADER*##

jimport('joomla.application.component.model');

/**
 * ECR_COM_NAMEECR_LIST_POSTFIX Model.
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
    var $_data;

    /**
     * Retrieves the hello data.
     *
     * @return array Array of objects containing the data from the database
     */
    function getData()
    {
        //-- Lets load the data if it doesn't already exist
        if(empty($this->_data))
        {
            $query = $this->_buildQuery();
            $this->_data = $this->_getList($query);
        }

        return $this->_data;
    }//function

    /**
     * Returns the query.
     *
     * @return string The query to be used to retrieve the rows from the database
     */
    private function _buildQuery()
    {
        $query = ' SELECT * '
        . ' FROM #__ECR_COM_TBL_NAME ';

        return $query;
    }//function
}//class
