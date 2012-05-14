<?php
##*HEADER*##

/**
 * ECR_COM_NAME Table class.
 *
 * @package    ECR_COM_NAME
 * @subpackage Components
 */
class TableECR_COM_NAME extends JTable
{
#admin.tableclass.classvar.ECR_COM_TBL_NAME.var#
    /**
    * Constructor.
    *
    * @param object $db Database connector object
    */
    function TableECR_COM_NAME(& $db)
    {
        parent::__construct('#__ECR_COM_TBL_NAME', 'id', $db);
    }//function
}//class
