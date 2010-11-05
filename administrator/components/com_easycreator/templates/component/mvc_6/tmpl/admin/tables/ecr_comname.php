<?php
##*HEADER*##

/**
 * _ECR_COM_NAME_ Table class.
 *
 * @package    _ECR_COM_NAME_
 */
class Table_ECR_COM_NAME_ extends JTable
{
    /**
     * Primary Key
     *
     * @var int
     */
    var $id = null;

    /**
     * @var string
     */
    var $greeting = null;

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function Table_ECR_COM_NAME_(& $db)
    {
        parent::__construct('#___ECR_COM_TBL_NAME_', 'id', $db);
    }//function
}//class
