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
     * Gets the greeting
     * @return string The greeting to be displayed to the user
     */
    public function getGreeting()
    {
        return 'Hello World (model) !';
    }//function
}//class
