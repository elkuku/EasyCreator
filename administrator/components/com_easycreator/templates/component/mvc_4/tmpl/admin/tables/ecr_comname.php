<?php
##*HEADER*##

/**
 * ECR_COM_NAME Table class.
 *
 * @package    ECR_COM_NAME
 * @subpackage Tables
 */
class TableECR_COM_NAME extends JTable
{
    /**
     * Primary Key
     *
     * @var int
     */
    public $id = null;

    /**
     * @var string
     */
    public $greeting = null;

    /**
     * Constructor
     *
     * @param object $db Database connector object
     */
    public function __construct($db)
    {
        parent::__construct('#__ECR_COM_TBL_NAME', 'id', $db);
    }//function
}//class
