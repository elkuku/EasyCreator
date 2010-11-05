<?php
##*HEADER*##

/**
 * Class for table _ECR_TABLE_NAME_.
 *
 */
class Table_ECR_ELEMENT_NAME_ extends JTable
{
##ECR_TABLE_VARS##
   /**
    * Constructor.
    *
    * @param object &$db Database connector object.
    */
    public function __construct(& $db)
    {
        parent::__construct('#___ECR_TABLE_NAME_', 'id', $db);
    }//function
}//class
