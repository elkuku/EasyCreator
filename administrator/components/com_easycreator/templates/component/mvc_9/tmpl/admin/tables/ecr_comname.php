<?php
##*HEADER*##

/**
 * _ECR_COM_NAME_ Table class.
 *
 * @package    _ECR_COM_NAME_
 * @subpackage Components
 */
class Table_ECR_COM_NAME_ extends JTable
{
#admin.tableclass.classvar._ECR_COM_TBL_NAME_.var#
    /**
    * Constructor.
    *
    * @param object $db Database connector object
    */
    function Table_ECR_COM_NAME_(& $db)
    {
        parent::__construct('#___ECR_COM_TBL_NAME_', 'id', $db);
    }//function
}//class
