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
     * Gets the greetings.
     *
     * @return ObjectList The greetings to be displayed to the user
     */
    function getGreetings()
    {
        $db =& JFactory::getDBO();

        $query = 'SELECT greeting FROM #___ECR_COM_TBL_NAME_';
        $db->setQuery($query);
        $greetings = $db->loadObjectList();

        return $greetings;
    }//function

    /**
     * gets a random greeting
     *
     * @return string a random greeting
     */
    function getRandom()
    {
        $greetings = $this->getGreetings();

        return $greetings[rand(0, count($greetings) - 1)]->greeting;
    }//function
}// class
