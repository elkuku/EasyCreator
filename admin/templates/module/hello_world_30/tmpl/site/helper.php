<?php
##*HEADER*##

/**
 * Helper class for ECR_COM_NAME.
 */
class ModECR_COM_NAMEHelper
{
    /**
     * Returns a list of random users.
     *
     * @param integer $userCount How many users to display
     *
     * @return array
     */
    public static function getItems($userCount)
    {
        //-- Get a reference to the database
        $db = JFactory::getDBO();

        //-- Get a list of all users
        $db->setQuery($db->getQuery(true)
            ->from('#__users AS a')
            ->select('a.name')
        );

        $items = $db->loadObjectList();

        $items = ($items) ?: array();

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
        }

        return $items2;
    }
}
