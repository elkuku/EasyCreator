<?php
##*HEADER*##

jimport('joomla.database.table');

/**
 * _ECR_COM_TBL_NAME_ Table class.
 *
 * @package _ECR_COM_NAME_
 */
class Table_ECR_COM_NAME_ extends JTable
{
    /**
     * Constructor.
     *
     * @param object Database connector object
     */
    public function __construct(&$db)
    {
        parent::__construct('#___ECR_COM_TBL_NAME_', 'id', $db);
    }//function
}//class
