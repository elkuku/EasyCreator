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
     * Gets the greetings.
     *
     * @return ObjectList The greetings to be displayed to the user
     */
    function getGreetings()
    {
        $db =& JFactory::getDBO();

        $query = 'SELECT greeting FROM #__ECR_COM_TBL_NAME';
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
