<?php
##*HEADER*##

/**
 * _ECR_COM_NAME_ Table class.
 *
 * @package    _ECR_COM_NAME_
 * @subpackage Components
 */
class _ECR_COM_NAME_Table_ECR_COM_NAME_ extends JTable
{
    /**
     * Constructor.
     *
     * @param object &$db Database connector object
     */
    public function __construct(& $db)
    {
        parent::__construct('#___ECR_COM_TBL_NAME_', 'id', $db);
    }//function
}//class
