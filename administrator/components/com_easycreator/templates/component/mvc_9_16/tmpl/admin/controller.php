<?php
##*HEADER*##

jimport('joomla.application.component.controller');

/**
 * _ECR_COM_NAME_ default Controller.
 *
 * @package    _ECR_COM_NAME_
 * @subpackage Controllers
 */
class _ECR_COM_NAME_Controller extends JController
{
    /**
     * Method to display the view.
     *
     * @return void
     */
    public function display($cachable = false, $urlparams = false)
    {
        //-- Setting the default view
        JRequest::setVar('view', JRequest::getCmd('view', '_ECR_COM_NAME__ECR_LOWER_LIST_POSTFIX_'));

        parent::display($cachable, $urlparams);

        _ECR_COM_NAME_Helper::addSubmenu('_ECR_COM_TBL_NAME__ECR_LOWER_LIST_POSTFIX_');
    }//function
}//class
