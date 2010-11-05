<?php
##*HEADER*##

//-- Import the Joomla! view library
jimport('joomla.application.component.view');

/**
 * Standard view.
 *
 * @package _ECR_COM_NAME_
 *
 */
class _ECR_COM_NAME_View_ECR_COM_NAME_ extends JView
{
    /**
     * The item
     *
     * @var object
     */
    protected $item = null;

    /**
     * The category.
     *
     * @var object
     */
    protected $category = null;

    /**
     * _ECR_COM_NAME_ view display method.
     *
     * @param string $tpl The name of the template file to parse;
     *
     * @return void
     */
    public function display($tpl = null)
    {
        //-- Get the record
        $this->item = $this->get('Item');

        //-- Get the category
        $this->category = $this->get('Category');

        //-- Display the view
        parent::display($tpl);
    }//function
}//class
