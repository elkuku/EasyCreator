<?php
##*HEADER*##

//-- Import the Class JControllerAdmin
jimport('joomla.application.component.controlleradmin');

/**
 * ECR_COM_NAME Controller.
 */
class ECR_COM_NAMEControllerECR_COM_NAMEECR_LIST_POSTFIX extends JControllerAdmin
{
    /**
     * Proxy for getModel.
     */
    public function getModel($name = 'ECR_COM_NAME', $prefix = 'ECR_COM_NAMEModel'
    , $config = array('ignore_request' => true))
    {
        $doSomething = 'here';

        return parent::getModel($name, $prefix, $config);
    }
}
