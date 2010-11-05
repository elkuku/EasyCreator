<?php
##*HEADER*##

/**
 * Helper class for _ECR_COM_NAME_.
 */
class Mod_ECR_COM_NAME_Helper
{
    /**
     * Returns a list of random users.
     *
     * @param integer $userCount How many users to display
     *
     * @return array
     */
    public function getItems($userCount)
    {
        //-- Get a reference to the database
        $db = &JFactory::getDBO();

        //-- Get a list of all users
        $query = 'SELECT a.name FROM `#__users` AS a';
        $db->setQuery($query);

        $items =($items = $db->loadObjectList()) ? $items : array();

        //-- Create a new array and fill it up with random users
        $actualCount = count($items);

        if($actualCount < $userCount)
        {
            $userCount = $actualCount;
        }

        $items2 = array();
        $rands = array_rand($items, $userCount);

        foreach($rands as $rand)
        {
            $items2[] = $items[$rand];
        }//foreach

        return $items2;
    }//function
}//class
