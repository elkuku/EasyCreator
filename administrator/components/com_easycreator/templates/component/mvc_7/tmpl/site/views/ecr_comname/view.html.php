<?php
##*HEADER*##

jimport('joomla.application.component.view');

/**
 * HTML View class for the ECR_COM_NAME Component.
 *
 * @package    ECR_COM_NAME
 * @subpackage Views
 */
class ECR_COM_NAMEViewECR_COM_NAME extends JView
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
        $data = $this->get('Data');

        $this->assignRef('data', $data);

        //-- Creating a link to the edit form
        $this->assignRef('editlink', JRoute::_('index.php?option=ECR_COM_COM_NAME&view=ECR_COM_NAME&task=edit'));

        parent::display($tpl);
    }//function
}//class
