<?php
##*HEADER*##

jimport('joomla.application.component.controller');

/**
 * _ECR_COM_NAME_ Controller.
 *
 * @package    _ECR_COM_NAME_
 * @subpackage Controllers
 */
class _ECR_COM_NAME__ECR_LIST_POSTFIX_Controller_ECR_COM_NAME_ extends _ECR_COM_NAME__ECR_LIST_POSTFIX_Controller
{
    /**
     * Constructor (registers additional tasks to methods).
     */
    public function __construct()
    {
        parent::__construct();

        //-- Register Extra tasks
        $this->registerTask('add', 'edit');
    }//function

    /**
     * display the edit form.
     *
     * @return void
     */
    function edit()
    {
        JRequest::setVar('view', '_ECR_COM_NAME_');
        JRequest::setVar('layout', 'form');
        JRequest::setVar('hidemainmenu', 1);

        parent::display();
    }//function

    /**
     * Save a record (and redirect to main page).
     *
     * @return void
     */
    function save()
    {
        $model = $this->getModel('_ECR_COM_NAME_');
        $link = 'index.php?option=_ECR_COM_COM_NAME_';

        if($model->store())
        {
            $msg = JText::_('Record daved');
            $this->setRedirect($link, $msg);
        }
        else
        {
            $msg = $model->getError();
            $this->setRedirect($link, $msg, 'error');
        }
    }//function

    /**
     * Remove record(s).
     *
     * @return void
     */
    function remove()
    {
        $model = $this->getModel('_ECR_COM_NAME_');
        $link = 'index.php?option=_ECR_COM_COM_NAME_';

        if($model->delete())
        {
            $msg = JText::_('Records deleted');
            $this->setRedirect($link, $msg);
        }
        else
        {
            $msg = JText::sprintf('One or more records could not be deleted: %s', $model->getError());
            $this->setRedirect($link, $msg, 'error');
        }
    }//function

    /**
     * Cancel editing a record.
     *
     * @return void
     */
    function cancel()
    {
        $msg = JText::_('Operation Cancelled');
        $this->setRedirect('index.php?option=_ECR_COM_COM_NAME_', $msg, 'notice');
    }//function
}//class
