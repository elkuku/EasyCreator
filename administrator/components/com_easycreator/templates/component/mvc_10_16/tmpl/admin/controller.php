<?php
##*HEADER*##

//-- Import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * General Controller _ECR_COM_NAME_ component.
 *
 * @package _ECR_COM_NAME_
 */
class _ECR_COM_NAME_Controller extends JController
{
    /**
     * Display task.
     *
     * @return void
     */
    public function display($cachable = false)
    {
        //-- Set default view if not set
        JRequest::setVar('view', JRequest::getCmd('view', '_ECR_COM_NAME__ECR_LIST_POSTFIX_'));

        //-- Call parent behavior
        parent::display($cachable);

        //-- Add submenu and icons
        require_once JPATH_COMPONENT.DS.'helpers'.DS.'_ECR_COM_TBL_NAME_.php';

        _ECR_COM_NAME_Helper::addSubmenu('_ECR_COM_NAME_');
    }//function
}//class
