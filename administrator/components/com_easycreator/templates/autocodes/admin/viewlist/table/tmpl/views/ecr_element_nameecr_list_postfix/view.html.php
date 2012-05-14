<?php
##*HEADER*##

jimport('joomla.application.component.view');

/**
 * Enter description here ...@todo class doccomment.
 *
 */
class ECR_COM_NAMEsViewECR_ELEMENT_NAMEs extends JView
{
    /**
     * ECR_COM_NAME view display method.
     *
     * @param string $tpl The name of the template file to parse;
     *
     * @return void
     */
    public function display($tpl = null)
    {
        JHTML::stylesheet('ECR_COM_NAME.css', 'administrator/components/com_ECR_COM_NAME/assets/');
        JToolBarHelper::title('&nbsp;&nbsp;'.JText::_('ECR_ELEMENT_NAMEs'), 'ECR_ELEMENT_NAME');

        JToolBarHelper::deleteList();
        JToolBarHelper::editListX();
        JToolBarHelper::addNewX();

        $items  = & $this->get('Data');
        $pagination =& $this->get('Pagination');

        $lists = & $this->get('List');

        $this->assignRef('items', $items);
        $this->assignRef('pagination', $pagination);
        $this->assignRef('lists', $lists);

        parent::display($tpl);
    }//function
}//class
