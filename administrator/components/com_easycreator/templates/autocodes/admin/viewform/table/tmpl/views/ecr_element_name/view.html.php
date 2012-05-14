<?php
##*HEADER*##

jimport('joomla.application.component.view');

/**
 * HTML View class for the ECR_COM_NAME Component.
 *
 * @package    ECR_COM_NAME
 * @subpackage Views
 */
class ECR_COM_NAMEsViewECR_COM_NAME extends JView
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
        //-- Get the ECR_COM_NAME
        $ECR_COM_NAME =& $this->get('Data');
        $isNew = ($ECR_COM_NAME->id < 1);

        $text =($isNew) ? JText::_('New') : JText::_('Edit');

        JToolBarHelper::title('ECR_COM_NAME: <small><small>[ '.$text.' ]</small></small>');
        JToolBarHelper::save();

        if($isNew)
        {
            JToolBarHelper::cancel();
        }
        else
        {
            //-- For existing items the button is renamed `close`
            JToolBarHelper::cancel('cancel', JText::_('Close'));
        }

        $this->assignRef('ECR_COM_NAME', $ECR_COM_NAME);

        parent::display($tpl);
    }//function
}//class
