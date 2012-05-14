<?php
##*HEADER*##

jimport('joomla.application.component.model');

/**
 * ECR_COM_NAME Model.
 *
 * @package    ECR_COM_NAME
 * @subpackage Models
 */
class ECR_COM_NAMEModelECR_COM_NAME extends JModel
{
    /**
     * Retrieves the data
     * @return string the greeting
     */
    public function getGreeting()
    {
        $db =& JFactory::getDBO();

        $query = 'SELECT greeting FROM #__ECR_COM_TBL_NAME';
        $db->setQuery($query);
        $greeting = $db->loadResult();

        return $greeting.' (model)';
    }//function
}//class
