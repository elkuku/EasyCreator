<?php
##*HEADER*##

jimport('joomla.application.component.controller');

/**
 * ECR_ELEMENT_NAME Controller.
 *
 * @package    ECR_COM_NAME
 * @subpackage Controllers
 */
class ECR_COM_NAMEControllerECR_ELEMENT_NAME extends ECR_COM_NAMEController
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
     * display the edit form
     * @return void
     */
    function edit()
    {
        JRequest::setVar('view', 'ECR_ELEMENT_NAME');
        JRequest::setVar('layout', 'form');
        JRequest::setVar('hidemainmenu', 1);

        parent::display();
    }//function

    /**
     * save a record (and redirect to main page)
     * @return void
     */
    function save()
    {
        $model = $this->getModel('ECR_ELEMENT_NAME');

        if($model->store())
        {
            $msg = JText::_('Record Saved');
        }
        else
        {
            $msg = JText::_('Error Saving Record');
        }

        $link = 'index.php?option=ECR_COM_COM_NAME';
        $this->setRedirect($link, $msg);
    }//function

    /**
     * remove record(s)
     * @return void
     */
    function remove()
    {
        $model = $this->getModel('ECR_ELEMENT_NAME');

        if( ! $model->delete())
        {
            $msg = JText::_('Error: One or More Records Could not be Deleted');
        }
        else
        {
            $msg = JText::_('Records Deleted');
        }

        $this->setRedirect('index.php?option=ECR_COM_COM_NAME', $msg);
    }//function

    /**
     * cancel editing a record
     * @return void
     */
    function cancel()
    {
        $msg = JText::_('Operation Cancelled');
        $this->setRedirect('index.php?option=ECR_COM_COM_NAME', $msg);
    }//function
}//class
