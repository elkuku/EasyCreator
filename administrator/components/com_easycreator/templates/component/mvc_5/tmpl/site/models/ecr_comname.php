<?php
##*HEADER*##

jimport('joomla.application.component.model');

/**
 * _ECR_COM_NAME_ Model.
 *
 * @package    _ECR_COM_NAME_
 * @subpackage Models
 */
class _ECR_COM_NAME_Model_ECR_COM_NAME_ extends JModel
{
    /**
     * Gets the data.
     *
     * @return string The data to be displayed to the user
     */
    function getData()
    {
        $db =& JFactory::getDBO();

        $query = 'SELECT * FROM #___ECR_COM_TBL_NAME_';
        $db->setQuery($query);
        $data = $db->loadObjectList();

        return $data;
    }//function
}//class
