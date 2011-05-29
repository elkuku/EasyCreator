<?php
##*HEADER*##

jimport('joomla.application.component.view');

/**
 * HTML View class for the _ECR_COM_NAME_ Component.
 *
 * @package _ECR_COM_NAME_
 * @subpackage Views
 */
class _ECR_COM_NAME_View_ECR_COM_NAME_ extends JView
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
        // Die Daten werden bezogen
        $this->item = $this->get('Item');

        // Das Formular
        $this->form = $this->get('Form');

        // JavaScript
        $this->script = $this->get('Script');

        // Auf Fehler prüfen
        $errors = $this->get('Errors');

        if(count($errors))
        {
            JError::raiseError(500, implode('<br />', $errors));

            return false;
        }

        // Die Toolbar hinzufügen
        $this->addToolBar();

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
        JRequest::setVar('hidemainmenu', true);

        $isNew = ($this->item->id == 0);

        JToolBarHelper::title($isNew
        ? JText::_('_ECR_UPPER_COM_COM_NAME__MANAGER__ECR_UPPER_COM_NAME__NEW')
        : JText::_('_ECR_UPPER_COM_COM_NAME__MANAGER__ECR_UPPER_COM_NAME__EDIT')
        , '_ECR_LOWER_COM_NAME_');

        JToolBarHelper::save('_ECR_LOWER_COM_NAME_.save');

        JToolBarHelper::cancel('_ECR_LOWER_COM_NAME_.cancel'
        , $isNew
        ? 'JTOOLBAR_CANCEL'
        : 'JTOOLBAR_CLOSE');

        // CSS Klasse für das 48x48 Icon der Toolbar
        JFactory::getDocument()->addStyleDeclaration(
        '.icon-48-_ECR_LOWER_COM_NAME_ {background-image: url('
        .'components/_ECR_COM_COM_NAME_/assets/images/_ECR_COM_COM_NAME_-48.png);}'
        );
    }//function

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $isNew = ($this->item->id < 1);

        $document = JFactory::getDocument();

        $document->setTitle($isNew
        ? JText::_('_ECR_UPPER_COM_COM_NAME___ECR_UPPER_COM_NAME__CREATING')
        : JText::_('_ECR_UPPER_COM_COM_NAME___ECR_UPPER_COM_NAME__EDITING'));

        $document->addScript(JURI::root(true).$this->script);

        $document->addScript(JURI::root(true)
        .'/administrator/components/_ECR_COM_COM_NAME_/views/_ECR_LOWER_COM_NAME_/submitbutton.js');

        JText::script('_ECR_UPPER_COM_COM_NAME___ECR_UPPER_COM_NAME__ERROR_UNACCEPTABLE');
    }//function
}//class
