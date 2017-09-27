<?php
##*HEADER*##

/**
 * ECR_COM_NAME Table class.
 *
 * @package    ECR_COM_NAME
 * @subpackage Components
 */
class ECR_COM_NAMETableECR_COM_NAME extends JTable
{
    /**
     * Constructor.
     *
     * @param object &$db Database connector object
     */
    public function __construct(& $db)
    {
        parent::__construct('#__ECR_COM_TBL_NAME', 'id', $db);
    }
}
