<?php
##*HEADER*##

jimport('joomla.application.component.view');

/**
 * Enter description here ...@todo class doccomment.
 *
 */
class _ECR_COM_NAME_sView_ECR_ELEMENT_NAME_s extends JView
{
    /**
     * _ECR_COM_NAME_s view display method.
     *
     * @param string $tpl The name of the template file to parse;
     *
     * @return void
     */
    public function display($tpl = null)
    {
        JHTML::stylesheet('_ECR_COM_NAME_.css', 'administrator/components/com__ECR_COM_NAME_/assets/');
        JToolBarHelper::title('&nbsp;&nbsp;'.JText::_('_ECR_ELEMENT_NAME_s'), '_ECR_ELEMENT_NAME_');

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
