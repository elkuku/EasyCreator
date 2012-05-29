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
     * Gets the data.
     *
     * @return string The data to be displayed to the user
     */
    public function getData()
    {
        $db =& JFactory::getDBO();

        $query = 'SELECT * FROM #__ECR_COM_TBL_NAME';
        $db->setQuery($query);
        $data = $db->loadObjectList();

        return $data;
    }//function
}//class
