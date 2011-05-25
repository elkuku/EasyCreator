<?php
##*HEADER*##

//-- Import the Class JControllerAdmin
jimport('joomla.application.component.controlleradmin');

/**
 * HalloWeltList Controller.
 */
class _ECR_COM_NAME_Controller_ECR_COM_NAME__ECR_LIST_POSTFIX extends JControllerAdmin
{
    /**
     * Proxy for getModel.
     */
    public function getModel($name = '_ECR_COM_NAME_', $prefix = '_ECR_COM_NAME_Model', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }//function
}//class
