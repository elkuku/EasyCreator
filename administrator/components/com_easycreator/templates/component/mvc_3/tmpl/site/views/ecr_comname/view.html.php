<?php
##*HEADER*##

jimport('joomla.application.component.view');

/**
 * HTML View class for the _ECR_COM_NAME_ Component.
 *
 * @package    _ECR_COM_NAME_
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
        $model = $this->getModel();

        $greeting_model = $model->getGreeting();
        $greeting_view = 'Hello World (view)';

        $this->assignRef('greeting_model', $greeting_model);
        $this->assignRef('greeting_view', $greeting_view);

        parent::display($tpl);
    }//function
}//class
