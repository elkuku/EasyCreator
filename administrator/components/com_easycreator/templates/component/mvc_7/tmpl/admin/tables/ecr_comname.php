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
     * @var string
     */
    var $content = null;

    /**
     * Constructor
     *
     * @param object $db Database connector object
     */
    function TableECR_COM_NAME(& $db)
    {
        parent::__construct('#__ECR_COM_TBL_NAME', 'id', $db);
    }//function
}//class
