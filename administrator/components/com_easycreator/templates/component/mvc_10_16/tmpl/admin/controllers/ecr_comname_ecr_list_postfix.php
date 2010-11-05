<?php
##*HEADER*##

//-- Import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * _ECR_COM_NAME_ List controller.
 *
 * @package _ECR_COM_NAME_
 */
class _ECR_COM_NAME_Controller_ECR_COM_NAME__ECR_LIST_POSTFIX_ extends JController
{
    /**
     * Remove record(s).
     *
     * @return void
     */
    public function remove()
    {
        JRequest::checkToken() || jexit(JText::_('JInvalid_Token'));

        $model = $this->getModel('_ECR_COM_NAME__ECR_LIST_POSTFIX_');

        if($model->remove())
        {
            $msg = JText::_('Records have been removed');
            $type = 'message';
        }
        else
        {
            $msg = JText::sprintf('One or more records could not be deleted %s', implode('<br />', $model->getErrors()));
            $type = 'error';
        }

        $this->setRedirect('index.php?option=_ECR_COM_COM_NAME_', $msg, $type);
    }//function
}//class
