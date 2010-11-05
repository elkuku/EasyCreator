<?php
##*HEADER*##

jimport('joomla.application.component.view');

/**
 * HTML View class for the _ECR_COM_NAME_ Component.
 *
 * @package    _ECR_COM_NAME_
 * @subpackage Views
 */
class _ECR_COM_NAME__ECR_LIST_POSTFIX_View_ECR_COM_NAME__ECR_LIST_POSTFIX_ extends JView
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
        JToolBarHelper::title(JText::_('_ECR_COM_NAME_ Manager'), 'generic.png');
        JToolBarHelper::deleteList();
        JToolBarHelper::editListX();
        JToolBarHelper::addNewX();

        //-- Get data from the model
        $items =& $this->get('Data');
        $pagination =& $this->get('Pagination');

        //-- Push data into the template
        $this->assignRef('items', $items);
        $this->assignRef('pagination', $pagination);

        parent::display($tpl);
    }//function
}//class
