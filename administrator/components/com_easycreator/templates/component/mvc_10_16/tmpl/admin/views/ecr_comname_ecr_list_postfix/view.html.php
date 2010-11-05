<?php
##*HEADER*##

//-- Import Joomla view library
jimport('joomla.application.component.view');

/**
 * _ECR_COM_NAME_ List View.
 *
 * @package _ECR_COM_NAME_
 */
class _ECR_COM_NAME_View_ECR_COM_NAME__ECR_LIST_POSTFIX_ extends JView
{
    /**
     * items to be displayed
     */
    protected $items;

    /**
     * pagination for the items
     */
    protected $pagination;

    /**
     * _ECR_COM_NAME_ view display method.
     *
     * @param string $tpl The name of the template file to parse;
     *
     * @return void
     */
    public function display($tpl = null)
    {
        //-- Get data from the model
        $this->items = $this->get('Items');

        //-- Get a pagination object
        $this->pagination = $this->get('Pagination');

        // Set the toolbar
        $this->setToolBar();

        //-- Display the template
        parent::display($tpl);

        //-- Set the document
        $this->setDocument();
    }//function

    /**
     * Setting the toolbar.
     *
     * @return void
     */
    protected function setToolBar()
    {
        JToolBarHelper::title(JText::_('_ECR_COM_NAME_ Manager'), '_ECR_COM_COM_NAME_');
        JToolBarHelper::deleteListX('Are_you_sure_you_want_to_delete_these_greetings'
        , '_ECR_COM_NAME__ECR_LIST_POSTFIX_.remove');

        JToolBarHelper::editListX('_ECR_COM_NAME_.edit');
        JToolBarHelper::addNewX('_ECR_COM_NAME_.add');
        JToolBarHelper::preferences('_ECR_COM_COM_NAME_');
    }//function

    /**
     * Method to set up the document properties.
     *
     * @return void
     */
    protected function setDocument()
    {
        JFactory::getDocument()->setTitle(JText::_('_ECR_COM_NAME_ Administration'));
    }//function
}//class
