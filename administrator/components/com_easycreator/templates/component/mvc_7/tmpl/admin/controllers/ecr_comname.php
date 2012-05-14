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
     * Display the edit form.
     * @return void
     */
    function edit()
    {
        JRequest::setVar('view', 'ECR_COM_NAME');
        JRequest::setVar('layout', 'form');
        JRequest::setVar('hidemainmenu', 1);

        parent::display();
    }//function

    /**
     * Save a record (and redirect to main page).
     * @return void
     */
    function save()
    {
        $model = $this->getModel('ECR_COM_NAME');
        $link = 'index.php?option=ECR_COM_COM_NAME';

        if($model->store())
        {
            $msg = JText::_('Record saved');
            $this->setRedirect($link, $msg);
        }
        else
        {
            $msg = JText::_('Error Saving Record');
            $this->setRedirect($link, $msg, 'error');
        }
    }//function

    /**
     * Remove record(s).
     * @return void
     */
    function remove()
    {
        $model = $this->getModel('ECR_COM_NAME');
        $link = 'index.php?option=ECR_COM_COM_NAME';

        if($model->delete())
        {
            $msg = JText::_('Records Deleted');
            $this->setRedirect($link, $msg);
        }
        else
        {
            $msg = JText::_('One or more records could not be deleted');
            $this->setRedirect($link, $msg, 'error');
        }
    }//function

    /**
     * Cancel editing a record.
     * @return void
     */
    function cancel()
    {
        $msg = JText::_('Operation cancelled');
        $this->setRedirect('index.php?option=ECR_COM_COM_NAME', $msg);
    }//function
}//class
