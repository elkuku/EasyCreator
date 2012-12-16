<?php
##*HEADER*##

jimport('joomla.application.component.view');

/**
 * ECR_ELEMENT_NAME view.
 *
 * @package    ECR_COM_NAME
 * @subpackage Views
 */
class ECR_COM_NAMEsViewECR_ELEMENT_NAME extends JView
{
    /**
     * ECR_COM_NAMEs view display method.
     *
     * @param string $tpl The name of the template file to parse;
     *
     * @return void
     */
    public function display($tpl = null)
    {
        //-- Custom css
        JHTML::stylesheet('ECR_COM_NAME.css', 'administrator/components/com_ECR_COM_NAME/assets/');

        //-- Get data from the model
        $item =& $this->get('Data');
        $isNew = ($item->id < 1);
        $text = $isNew ? JText::_('New') : JText::_('Edit');

        JToolBarHelper::title('&nbsp;&nbsp;'.JText::_('ECR_ELEMENT_NAME')
        .': <small><small>[ '.$text.' ]</small></small>', 'ECR_ELEMENT_NAME');

        JToolBarHelper::save();
        JToolBarHelper::cancel();

        $this->assignRef('item', $item);

        parent::display($tpl);
    }//function
}//class
