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
     * Gets the Data.
     *
     * @return string The greeting to be displayed to the user
     */
    public function getData()
    {
        $id = JRequest::getInt('id');
        $db = JFactory::getDBO();

        $query = 'SELECT a.*, cc.title AS category '
        . ' FROM #___ECR_COM_TBL_NAME_ AS a '
        . ' LEFT JOIN #__categories AS cc ON cc.id = a.catid '
        . ' WHERE a.id = '.$id;

        $db->setQuery($query);
        $data = $db->loadObject();

        return $data;
    }//function
}//class
