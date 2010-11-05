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
     * Gets the greetings
     *
     * @return string The greeting to be displayed to the user
     */
    public function getGreetings()
    {
        $db =& JFactory::getDBO();

        $query = 'SELECT greeting FROM #___ECR_COM_TBL_NAME_';
        $db->setQuery($query);
        $greetings = $db->loadObjectList();

        return $greetings;
    }//function
}//class
