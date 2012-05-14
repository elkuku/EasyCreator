<?php
##*HEADER*##

jimport('joomla.application.component.view');

/**
 * HTML View class for the ECR_COM_NAME Component
 *
 * @package    ECR_COM_NAME
 * @subpackage Views
 */

/**
 * Enter description here ...@todo class doccomment.
 *
 */
class ECR_COM_NAMEECR_LIST_POSTFIXViewECR_COM_NAMEECR_LIST_POSTFIX extends JView
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
        JToolBarHelper::title(JText::_('ECR_COM_NAME Manager'), 'generic.png');
        JToolBarHelper::deleteList();
        JToolBarHelper::editListX();
        JToolBarHelper::addNewX();

        //-- Get data from the model
        $items = & $this->get('Data');

        $this->assignRef('items', $items);

        parent::display($tpl);
    }//function
}//class
