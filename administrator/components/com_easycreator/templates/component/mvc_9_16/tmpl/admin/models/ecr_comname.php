<?php
##*HEADER*##

jimport('joomla.application.component.modeladmin');

/**
 * _ECR_COM_NAME_ Model.
 *
 * @package    _ECR_COM_NAME_
 * @subpackage Models
 */
class _ECR_COM_NAME_Model_ECR_COM_NAME_ extends JModelAdmin
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param type    The table type to instantiate
     * @param string  A prefix for the table class name. Optional.
     * @param array   Configuration array for model. Optional.
     *
     * @return JTable A database object
     */
    public function getTable($type = '_ECR_COM_NAME_', $prefix = '_ECR_COM_NAME_Table', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }//function

    /**
     * Method to get the record form.
     *
     * @param array $data Data for the form.
     * @param boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return mixed A JForm object on success, false on failure
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('_ECR_COM_COM_NAME_._ECR_LOWER_COM_NAME_', '_ECR_LOWER_COM_NAME_'
        , array('control' => 'jform', 'load_data' => $loadData));

        if(empty($form))
        {
            return false;
        }

        return $form;
    }//function

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return mixed The data for the form.
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()
        ->getUserState('_ECR_COM_COM_NAME_.edit._ECR_LOWER_COM_NAME_.data');

        if(empty($data))
        {
            $data = $this->getItem();
        }

        return $data;
    }//function
}//class
