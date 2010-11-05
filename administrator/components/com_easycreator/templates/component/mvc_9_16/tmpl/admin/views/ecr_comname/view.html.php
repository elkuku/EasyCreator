<?php
##*HEADER*##

jimport('joomla.application.component.view');

/**
 * HTML View class for the _ECR_COM_NAME_ Component.
 *
 * @package    _ECR_COM_NAME_
 * @subpackage Views
 */
class _ECR_COM_NAME__ECR_LIST_POSTFIX_View_ECR_COM_NAME_ extends JView
{
    /**
     * _ECR_COM_NAME__ECR_LIST_POSTFIX_ view display method.
     *
     * @param string $tpl The name of the template file to parse;
     *
     * @return void
     */
    public function display($tpl = null)
    {
        $option = JRequest::getCmd('option');

        //-- Get the _ECR_COM_NAME_
        $_ECR_COM_NAME_	=& $this->get('Data');
        $isNew = ($_ECR_COM_NAME_->id < 1);

        $text = $isNew ? JText::_('New') : JText::_('Edit');
        JToolBarHelper::title('_ECR_COM_NAME_: <small><small>[ '.$text.' ]</small></small>');
        JToolBarHelper::save();

        if($isNew)
        {
            JToolBarHelper::cancel();
        }
        else
        {
            //-- For existing items the button is renamed `close`
            JToolBarHelper::cancel('cancel', JText::_('Close'));
        }

        $lists = array();
        $lists['catid'] = JHTML::_('list.category', 'catid', $option, intval($_ECR_COM_NAME_->catid));

        $this->assignRef('_ECR_COM_NAME_', $_ECR_COM_NAME_);
        $this->assignRef('lists', $lists);

        parent::display($tpl);
    }//function
}//class
