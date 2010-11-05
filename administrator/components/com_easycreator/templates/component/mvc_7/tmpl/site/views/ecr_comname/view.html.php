<?php
##*HEADER*##

jimport('joomla.application.component.view');

/**
 * HTML View class for the _ECR_COM_NAME_ Component.
 *
 * @package    _ECR_COM_NAME_
 * @subpackage Views
 */
class _ECR_COM_NAME_View_ECR_COM_NAME_ extends JView
{
    /**
     * _ECR_COM_NAME_ view display method.
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
        $this->assignRef('editlink', JRoute::_('index.php?option=_ECR_COM_COM_NAME_&view=_ECR_COM_NAME_&task=edit'));

        parent::display($tpl);
    }//function
}//class
