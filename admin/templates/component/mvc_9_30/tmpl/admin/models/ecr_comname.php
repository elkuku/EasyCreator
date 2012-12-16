<?php
##*HEADER*##

jimport('joomla.application.component.modeladmin');

/**
 * ECR_COM_NAME Model.
 *
 * @package    ECR_COM_NAME
 * @subpackage Models
 */
class ECR_COM_NAMEModelECR_COM_NAME extends JModelAdmin
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param string     $type  The table type to instantiate
     * @param string     $prefix A prefix for the table class name.
     * @param array      $config Configuration array for model.
     *
     * @internal param \The $type table type to instantiate
     * @return JTable A database object
     */
    public function getTable($type = 'ECR_COM_NAME', $prefix = 'ECR_COM_NAMETable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

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
        $form = $this->loadForm('ECR_COM_COM_NAME.ECR_LOWER_COM_NAME', 'ECR_LOWER_COM_NAME'
        , array('control' => 'jform', 'load_data' => $loadData));

        if(empty($form))
        {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return mixed The data for the form.
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()
        ->getUserState('ECR_COM_COM_NAME.edit.ECR_LOWER_COM_NAME.data');

        if(empty($data))
        {
            $data = $this->getItem();
        }

        return $data;
    }
}
