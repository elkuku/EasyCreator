<?php
##*HEADER*##

jimport('joomla.application.component.controller');

/**
 * _ECR_COM_NAME_ default Controller.
 *
 * @package    _ECR_COM_NAME_
 * @subpackage Controllers
 */
class _ECR_COM_NAME__ECR_LIST_POSTFIX_Controller extends JController
{
    /**
     * Method to display the view.
     *
     * @return void
     */
    public function display()
    {
        require_once JPATH_COMPONENT.'/helpers/_ECR_COM_TBL_NAME_.php';

        parent::display();

        _ECR_COM_NAME_Helper::addSubmenu(JRequest::getWord('view', '_ECR_COM_TBL_NAME__ECR_LOW_LIST_POSTFIX_'));
    }//function
}//class
