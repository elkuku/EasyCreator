<?php
##*HEADER*##

jimport('joomla.application.component.view');

/**
 * HTML View class for the _ECR_COM_NAME_ Component
 *
 * @package    _ECR_COM_NAME_
 * @subpackage Views
 */

/**
 * Enter description here ...@todo class doccomment.
 *
 */
class xx_ECR_COM_NAME_View_ECR_COM_NAME__ECR_LIST_POSTFIX_ extends JView
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





// Die Joomla! View Bibliothek importieren
jimport('joomla.application.component.view');

/*
 * HTML View class for the _ECR_COM_NAME_ Component
 *
 * @package    _ECR_COM_NAME_
 * @subpackage Views
 */
class _ECR_COM_NAME_View_ECR_COM_NAME__ECR_LIST_POSTFIX_ extends JView
//class HalloWeltViewHalloWeltList extends JView
{
    /**
     * _ECR_COM_NAME__ECR_LIST_POSTFIX_ view display method
     *
     * @return void
     */
    function display($tpl = null)
    {
        //-- Get data from the model
        $this->items = $this->get('Items');

        //-- Get a JPagination object
        $this->pagination = $this->get('Pagination');

        // Die Toolbar hinzufügen
        $this->addToolBar();

        // Auf Fehler prüfen
        $errors = $this->get('Errors');

        if (count($errors))
        {
            JError::raiseError(500, implode('<br />', $errors));

            return false;
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
        JToolBarHelper::title(JText::_('_ECR_COM_NAME__MANAGER__ECR_COM_NAME_LIST')
        , 'hallowelt');

        JToolBarHelper::deleteList('', '_ECR_LOWER_COM_NAME__ECR_LOWER_LIST_POSTFIX_.delete');
        JToolBarHelper::editList('_ECR_LOWER_COM_NAME_.edit');
        JToolBarHelper::addNew('_ECR_LOWER_COM_NAME_.add');

        // CSS Klasse für das 48x48 Icon der Toolbar
        JFactory::getDocument()->addStyleDeclaration(
        '.icon-48-hallowelt {background-image: url(../media/_ECR_COM_COM_NAME_/images/tux-48x48.png);}'
        );

    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        JFactory::getDocument()->setTitle(JText::_('_ECR_UPPER_COM_NAME__ADMINISTRATION'));
    }
}
