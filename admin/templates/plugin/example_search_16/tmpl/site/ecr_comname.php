<?php
##*HEADER*##

jimport('joomla.plugin.plugin');

/**
 * ECR_COM_NAME Search plugin.
 *
 * @package    ECR_COM_NAME
 * @subpackage Plugin
 */
class plgSearchECR_COM_NAME extends JPlugin
{
    /**
     * Constructor.
     *
     * @param object $subject The object to observe
     * @param array $config  An array that holds the plugin configuration
     */
    public function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);

        //Loads the plugin language file:
        $this->loadLanguage();
    }//function

    /**
     * Sets the checkbox(es) to be diplayed in the Search Only box:
     * @return array An array of search areas
     */
    public function onContentSearchAreas()
    {
        static $areas = array(
            'Example' => 'PLG_SEARCH_'
            );

        return $areas;
    }//function

    /**
     * Example Search method
     *
     * The sql must return the following fields that are used in a common display
     * routine:
     - title;
     - href:            link associated with the title;
     - browsernav    if 1, link opens in a new window, otherwise in the same window;
     - section        in parenthesis below the title;
     - text;
     - created;

     * @param string Target search string
     * @param string matching option, exact|any|all
     * @param string ordering option, newest|oldest|popular|alpha|category
     * @param mixed An array if the search it to be restricted to areas, null if search all
     *
     * @return array Search results
     */
    public function onContentSearch($text, $phrase = '', $ordering = '', $areas = null)
    {
        return array();
    }//function
}//class
