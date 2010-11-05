<?php
##*HEADER*##

//-- Import Joomla view library
jimport('joomla.application.component.view');

/**
 * _ECR_COM_NAME_ View.
 *
 * @package _ECR_COM_NAME_
 */
class _ECR_COM_NAME_View_ECR_COM_NAME_ extends JView
{
    /**
     * View form
     *
     * @var form
     */
    protected $form = null;

    /**
     * View script
     */
    protected $script = null;

    /**
     * _ECR_COM_NAME_ view display method.
     *
     * @param string $tpl The name of the template file to parse;
     *
     * @return void
     */
    public function display($tpl = null)
    {
        //-- Get the Form
        $form = & $this->get('Form');

        //-- Get the Data
        $data = & $this->get('Data');

        //-- Get the script
        $this->script = & $this->get('Script');

        //-- Check for errors
        if(count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode('<br />', $errors));

            return;
        }

        //-- Bind the Data
        $form->bind($data);

        //-- Assign the form
        $this->form = $form;

        //-- Set the toolbar
        $this->setToolBar();

        //-- Display the template
        parent::display($tpl);

        // Set the document
        $this->setDocument();
    }//function

    /**
     * Set the toolbar.
     *
     * @return void
     */
    protected function setToolBar()
    {
        JRequest::setVar('hidemainmenu', 1);

        $isNew = ($this->form->getValue('id') < 1);

        JToolBarHelper::title(
        JText::_('_ECR_COM_NAME_ Manager')
        . ': <small><small>[ '
        .($isNew ? JText::_('JToolBar_New') : JText::_('JToolBar_Edit'))
        .' ]</small></small>'
        , '_ECR_COM_COM_NAME_');

        JToolBarHelper::save('_ECR_COM_NAME_.save');

        JToolBarHelper::cancel('_ECR_COM_NAME_.cancel', $isNew
        ? 'JToolBar_Cancel'
        : 'JToolBar_Close'
        );
    }//function

    /**
     * Method to set up the document properties.
     *
     * @return void
     */
    protected function setDocument()
    {
        $isNew = ($this->form->getValue('id') < 1);
        $document = &JFactory::getDocument();
        $document->setTitle(
        JText::_('_ECR_COM_NAME_ Administration')
        . ' - '
        .($isNew
        ? JText::_('_ECR_COM_COM_NAME___ECR_COM_NAME__Creating')
        : JText::_('_ECR_COM_COM_NAME___ECR_COM_NAME__Editing'))
        );

        $document->addScript(JURI::root().$this->script);
        $document->addScript(JURI::root()
        .'administrator/components/_ECR_COM_COM_NAME_/views/_ECR_COM_TBL_NAME_/submitbutton.js');

        JText::script('Some values are unacceptable');
    }//function
}//class
