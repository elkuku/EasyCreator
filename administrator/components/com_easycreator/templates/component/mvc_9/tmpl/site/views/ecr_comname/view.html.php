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
     * _ECR_COM_NAME__ECR_LIST_POSTFIX_ view display method.
     *
     * @param string $tpl The name of the template file to parse;
     *
     * @return void
     */
    public function display($tpl = null)
    {
        $data = $this->get('Data');

        $this->assignRef('data', $data);

        parent::display($tpl);
    }//function
}//class
