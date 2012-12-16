<?php
##*HEADER*##

jimport('joomla.application.component.controller');

/**
 * ECR_COM_NAME default Controller.
 *
 * @package    ECR_COM_NAME
 * @subpackage Controllers
 */
class ECR_COM_NAMEController extends JController
{
    /**
     * Method to display the view.
     *
     * @return void
     */
    public function display($cachable = false, $urlparams = false)
    {
        //-- Setting the default view
        JRequest::setVar('view', JRequest::getCmd('view', 'ECR_COM_NAMEECR_LOWER_LIST_POSTFIX'));

        parent::display($cachable, $urlparams);

        ECR_COM_NAMEHelper::addSubmenu('ECR_COM_TBL_NAMEECR_LOWER_LIST_POSTFIX');
    }//function
}//class
