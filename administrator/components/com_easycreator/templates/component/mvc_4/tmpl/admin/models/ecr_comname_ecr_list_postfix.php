<?php
##*HEADER*##

jimport('joomla.application.component.model');

/**
 * _ECR_COM_NAME__ECR_LIST_POSTFIX_ Model.
 *
 * @package    _ECR_COM_NAME_
 * @subpackage Models
 */
class _ECR_COM_NAME__ECR_LIST_POSTFIX_Model_ECR_COM_NAME__ECR_LIST_POSTFIX_ extends JModel
{
    /**
     * _ECR_COM_NAME__ECR_LIST_POSTFIX_ data array
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
        . ' FROM #___ECR_COM_TBL_NAME_ ';

        return $query;
    }//function
}//class
