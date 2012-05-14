<?php
##*HEADER*##

jimport('joomla.application.component.model');

/**
 * ECR_COM_NAME Model.
 *
 * @package    ECR_COM_NAME
 * @subpackage Models
 */
class ECR_COM_NAMEModelECR_COM_NAME extends JModel
{
    /**
     * Gets the data.
     *
     * @return string The greeting to be displayed to the user.
     */
    function getData()
    {
        $db =& JFactory::getDBO();

        $query = 'SELECT * FROM #__ECR_COM_TBL_NAME';
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
