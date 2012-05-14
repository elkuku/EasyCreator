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
     * Method to display the view
     *
     * @access	public
     */
    public function display()
    {
        $foo = 'Do something here..';

        parent::display();
    }//function

    /**
     * Method to call the edit form
     */
    function edit()
    {
        JRequest::setVar('layout', 'form');
        parent::display();
    }//function

    /**
     * Method to get the data from the form and let the model save it
     */
    function save()
    {
        //-- Check for request forgeries
        JRequest::checkToken() || jexit(JText::_('Invalid Token'));

        //-- Get the model
        $model =& $this->getModel();

        //--get data from request
        $post = JRequest::get('post');
        $post['content'] = JRequest::getVar('content', '', 'post', 'string', JREQUEST_ALLOWRAW);

        //--let the model save it
        $link = 'index.php?option=ECR_COM_COM_NAME';

        if($model->store($post))
        {
            $message = JText::_('Success');
            $this->setRedirect($link, $message);
        }
        else
        {
            $message = JText::_('Error while saving');
            $message .= ' ['.$model->getError().'] ';
            $this->setRedirect($link, $message, 'error');
        }
    }//function

    /**
     * Cancel, redirect to component
     */
    function cancel()
    {
        $this->setRedirect('index.php?option=ECR_COM_COM_NAME');
    }//function
}//class
