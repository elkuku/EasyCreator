<?php
##*HEADER*##

jimport('joomla.application.component.controller');

/**
 * ECR_COM_NAME Controller.
 *
 * @package    ECR_COM_NAME
 * @subpackage Controllers
 */
class ECR_COM_NAMEECR_LIST_POSTFIXControllerECR_COM_NAME extends ECR_COM_NAMEECR_LIST_POSTFIXController
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
    public function edit()
    {
        JRequest::setVar('view', 'ECR_COM_NAME');
        JRequest::setVar('layout', 'form');
        JRequest::setVar('hidemainmenu', 1);

        parent::display();
    }//function

    /**
     * Save a record (and redirect to main page).
     *
     * @return void
     */
    public function save()
    {
        $model = $this->getModel('ECR_COM_NAME');
        $link = 'index.php?option=ECR_COM_COM_NAME';

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
    public function remove()
    {
        $model = $this->getModel('ECR_COM_NAME');
        $link = 'index.php?option=ECR_COM_COM_NAME';

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
    public function cancel()
    {
        $msg = JText::_('Operation Cancelled');
        $this->setRedirect('index.php?option=ECR_COM_COM_NAME', $msg, 'notice');
    }//function
}//class
