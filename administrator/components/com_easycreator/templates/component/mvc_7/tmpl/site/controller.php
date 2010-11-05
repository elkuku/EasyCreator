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
        $link = 'index.php?option=_ECR_COM_COM_NAME_';

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
        $this->setRedirect('index.php?option=_ECR_COM_COM_NAME_');
    }//function
}//class
