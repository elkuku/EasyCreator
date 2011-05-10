<?php
##*HEADER*##

jimport('joomla.application.component.controller');

/**
 * _ECR_COM_NAME_ Controller.
 *
 * @package    _ECR_COM_NAME_
 * @subpackage Controllers
 */
class _ECR_COM_NAME_Controller_ECR_ELEMENT_NAME_ extends _ECR_COM_NAME_Controller
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
        JRequest::setVar('view', '_ECR_ELEMENT_NAME_');
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
        $model = $this->getModel('_ECR_ELEMENT_NAME_');

        if($model->store())
        {
            $msg = JText::_('Record Saved');
        }
        else
        {
            $msg = JText::_('Error Saving Record');
        }

        $link = 'index.php?option=_ECR_COM_COM_NAME_';
        $this->setRedirect($link, $msg);
    }//function

    /**
     * remove record(s)
     * @return void
     */
    function remove()
    {
        $model = $this->getModel('_ECR_ELEMENT_NAME_');
        if( ! $model->delete())
        {
            $msg = JText::_('Error: One or More Records Could not be Deleted');
        }
        else
        {
            $msg = JText::_('Records Deleted');
        }

        $this->setRedirect('index.php?option=_ECR_COM_COM_NAME_', $msg);
    }//function

    /**
     * cancel editing a record
     * @return void
     */
    function cancel()
    {
        $msg = JText::_('Operation Cancelled');
        $this->setRedirect('index.php?option=_ECR_COM_COM_NAME_', $msg);
    }//function
}//class
