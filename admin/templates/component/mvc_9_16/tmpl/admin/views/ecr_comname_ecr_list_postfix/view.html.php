<?php
##*HEADER*##

jimport('joomla.application.component.view');

/**
 * HTML View class for the ECR_COM_NAME Component.
 *
 * @package    ECR_COM_NAME
 * @subpackage Views
 */
class ECR_COM_NAMEViewECR_COM_NAMEECR_LIST_POSTFIX extends JView
{
    /**
     * ECR_COM_NAMEECR_LIST_POSTFIX view display method
     *
     * @param null $tpl
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
            JFactory::getApplication()->enqueueMessage(implode('<br />', $errors), 'error');

            return;
        }

        // Das Template wird aufgerufen
        parent::display($tpl);

        // Set the document
        $this->setDocument();
    }

    /**
     * Setting the toolbar
     */
    protected function addToolBar()
    {
        JToolBarHelper::title(JText::_('ECR_UPPER_COM_COM_NAME_MANAGER_ECR_UPPER_COM_NAMEECR_UPPER_LIST_POSTFIX')
        , 'ECR_LOWER_COM_NAME');

        JToolBarHelper::addNew('ECR_LOWER_COM_NAME.add');
        JToolBarHelper::editList('ECR_LOWER_COM_NAME.edit');
        JToolBarHelper::deleteList('', 'ECR_LOWER_COM_NAMEECR_LOWER_LIST_POSTFIX.delete');

        // CSS class for the 48x48 toolbar icon
        JFactory::getDocument()->addStyleDeclaration(
       '.icon-48-ECR_LOWER_COM_NAME'
       .' {background-image: url(components/ECR_COM_COM_NAME/assets/images/ECR_COM_COM_NAME-48.png)}');
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        JFactory::getDocument()->setTitle(JText::_('ECR_UPPER_COM_COM_NAME_ECR_UPPER_COM_NAME_ADMINISTRATION'));
    }
}
