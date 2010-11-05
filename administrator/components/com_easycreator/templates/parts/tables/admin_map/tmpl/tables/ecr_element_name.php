<?php
##*HEADER*##

/**
 * Class for table _ECR_TABLE_NAME_.
 *
 */
class Table_ECR_ELEMENT_NAME_ extends JTable
{
    var $db = null;

##ECR_TABLE_VARS##
    /**
    * Constructor
    *
    * @param $_db object Database connector object
    */
    public function __construct(&$_db)
    {
        parent::__construct('#___ECR_TABLE_NAME_', 'id', $_db);
        $this->db = $_db;
    }//function
}//class
