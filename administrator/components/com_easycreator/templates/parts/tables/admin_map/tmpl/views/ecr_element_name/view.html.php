<?php
##*HEADER*##

jimport('joomla.application.component.view');

/**
 * ECR_ELEMENT_NAME view.
 *
 * @package    ECR_COM_NAME
 * @subpackage Views
 */
class ECR_COM_NAMEECR_LIST_POSTFIXViewECR_ELEMENT_NAME extends JView
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
        JHTML::stylesheet('ECR_COM_NAME.css', 'administrator/components/com_ECR_COM_NAME/assets/');

        //-- Data from model
        $item =& $this->get('Data');
        $isNew = ($item->id < 1);
        $text = $isNew ? JText::_('New') : JText::_('Edit');

        JToolBarHelper::title('&nbsp;&nbsp;'.JText::_('ECR_ELEMENT_NAME')
        .': <small><small>[ '.$text.' ]</small></small>', 'ECR_ELEMENT_NAME');

        JToolBarHelper::save();
        JToolBarHelper::cancel();

        #_ECR_SMAT_DESCRIPTION_VIEW0_

        #_ECR_SMAT_PUBLISHED_VIEW0_

        $this->assignRef('item', $item);
        parent::display($tpl);
    }//function
}//class
