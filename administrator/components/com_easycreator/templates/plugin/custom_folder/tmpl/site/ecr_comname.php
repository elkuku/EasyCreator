<?php
##*HEADER*##

/**
 * _ECR_COM_SCOPE_ Plugin.
 *
 * @package     _ECR_COM_NAME_
 * @subpackage  Plugin
 */
class plg_ECR_COM_SCOPE__ECR_COM_NAME_ extends JPlugin
{
    /**
     * Constructor.
     */
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
    }//function

    /**
     * This will trigger the event 'OnDoSomething'.
     *
     * @return string Demo: 'Did something'
     */
    function onDoSomething()
    {
        return 'Did something';
    }//function
}//class
