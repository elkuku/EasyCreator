<?php
##*HEADER*##

jimport('joomla.application.component.view');

/**
 * HTML View class for the ECR_COM_NAME Component.
 *
 * @package    ECR_COM_NAME
 * @subpackage Views
 */
class ECR_COM_NAMEECR_LIST_POSTFIXViewECR_COM_NAME extends JView
{
    /**
     * ECR_COM_NAMEECR_LIST_POSTFIX view display method.
     *
     * @param string $tpl The name of the template file to parse;
     *
     * @return void
     */
    public function display($tpl = null)
    {
        $option = JRequest::getCmd('option');

        //-- Get the ECR_COM_NAME
        $ECR_COM_NAME	=& $this->get('Data');
        $isNew = ($ECR_COM_NAME->id < 1);

        $text = $isNew ? JText::_('New') : JText::_('Edit');
        JToolBarHelper::title('ECR_COM_NAME: <small><small>[ '.$text.' ]</small></small>');
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
        $lists['catid'] = JHTML::_('list.category', 'catid', $option, intval($ECR_COM_NAME->catid));

        $this->assignRef('ECR_COM_NAME', $ECR_COM_NAME);
        $this->assignRef('lists', $lists);

        parent::display($tpl);
    }//function
}//class
