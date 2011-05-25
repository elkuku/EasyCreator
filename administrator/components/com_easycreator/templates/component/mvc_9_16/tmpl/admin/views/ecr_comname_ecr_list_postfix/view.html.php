<?php
##*HEADER*##

jimport('joomla.application.component.view');

/**
 * HTML View class for the _ECR_COM_NAME_ Component.
 *
 * @package    _ECR_COM_NAME_
 * @subpackage Views
 */
class _ECR_COM_NAME_View_ECR_COM_NAME__ECR_LIST_POSTFIX_ extends JView
{
    /**
     * _ECR_COM_NAME__ECR_LIST_POSTFIX_ view display method
     *
     * @return void
     */
    public function display($tpl = null)
    {
        //-- Get data from the model
        $this->items = $this->get('Items');

        //-- Get a JPagination object
        $this->pagination = $this->get('Pagination');

        // Die Toolbar hinzufügen
        $this->addToolBar();

        // Auf Fehler prüfen
        $errors = $this->get('Errors');

        if(count($errors))
        {
            JError::raiseError(500, implode('<br />', $errors));

            return false;
        }

        // Das Template wird aufgerufen
        parent::display($tpl);

        // Set the document
        $this->setDocument();
    }//function

    /**
     * Setting the toolbar
     */
    protected function addToolBar()
    {
        JToolBarHelper::title(JText::_('_ECR_UPPER_COM_COM_NAME__MANAGER__ECR_UPPER_COM_NAME__ECR_UPPER_LIST_POSTFIX_')
        , '_ECR_LOWER_COM_NAME_');

        JToolBarHelper::deleteList('', '_ECR_LOWER_COM_NAME__ECR_LOWER_LIST_POSTFIX_.delete');
        JToolBarHelper::editList('_ECR_LOWER_COM_NAME_.edit');
        JToolBarHelper::addNew('_ECR_LOWER_COM_NAME_.add');

        // CSS class for the 48x48 toolbar icon
        JFactory::getDocument()->addStyleDeclaration(
       '.icon-48-_ECR_LOWER_COM_NAME_'
       .' {background-image: url(components/_ECR_COM_COM_NAME_/assets/images/_ECR_COM_COM_NAME_-48.png)}');
    }//function

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        JFactory::getDocument()->setTitle(JText::_('_ECR_UPPER_COM_COM_NAME___ECR_UPPER_COM_NAME__ADMINISTRATION'));
    }//function
}//class
