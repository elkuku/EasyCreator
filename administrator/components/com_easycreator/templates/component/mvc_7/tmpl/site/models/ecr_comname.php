<?php
##*HEADER*##

jimport('joomla.application.component.model');

/**
 * _ECR_COM_NAME_ Model.
 *
 * @package    _ECR_COM_NAME_
 * @subpackage Models
 */
class _ECR_COM_NAME_Model_ECR_COM_NAME_ extends JModel
{
    /**
     * Gets the data.
     *
     * @return string The greeting to be displayed to the user.
     */
    function getData()
    {
        $db =& JFactory::getDBO();

        $query = 'SELECT * FROM #___ECR_COM_TBL_NAME_';
        $db->setQuery($query);
        $data = $db->loadObject();

        return $data;
    }//function

    /**
     * Store a record
     *
     * @param mixed $data
     * @return bool true on success
     */
    function store($data)
    {
        //-- Get the table
        $row =& $this->getTable();

        //-- Bind the form fields to the table
        if( ! $row->bind($data))
        {
            $this->setError($this->_db->getError());
            return false;
        }

        //-- Make sure the record is valid
        if( ! $row->check())
        {
            $this->setError($this->_db->getError());
            return false;
        }

        //-- Store the table to the database
        if( ! $row->store())
        {
            $this->setError($row->getError());
            return false;
        }

        return true;
    }//function
}//class
