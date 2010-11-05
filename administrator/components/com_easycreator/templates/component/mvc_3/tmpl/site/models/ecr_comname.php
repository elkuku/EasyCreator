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
     * Retrieves the data
     * @return string the greeting
     */
    public function getGreeting()
    {
        $db =& JFactory::getDBO();

        $query = 'SELECT greeting FROM #___ECR_COM_TBL_NAME_';
        $db->setQuery($query);
        $greeting = $db->loadResult();

        return $greeting.' (model)';
    }//function
}//class
