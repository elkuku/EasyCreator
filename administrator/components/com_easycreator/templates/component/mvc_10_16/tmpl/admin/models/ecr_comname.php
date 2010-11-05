<?php
##*HEADER*##

//-- Import Joomla modelform library
jimport('joomla.application.component.modelform');

/**
 * _ECR_COM_NAME_ Model.
 *
 * @package _ECR_COM_NAME_
 */
class _ECR_COM_NAME_Model_ECR_COM_NAME_ extends JModelForm
{
    /**
     * @var array data
     */
    protected $data = null;

    /**
     * Method to auto-populate the model state.
     */
    protected function populateState()
    {
        $app = JFactory::getApplication('administrator');

        //-- Load the User state.
        $pk = (int)$app->getUserState('_ECR_COM_COM_NAME_.edit._ECR_COM_TBL_NAME_.id');

        if( ! $pk)
        {
            $pk = JRequest::getInt('id');
        }

        $this->setState('_ECR_COM_TBL_NAME_.id', $pk);
    }//function

    /**
     * Method to get the data.
     *
     * @return array
     */
    public function &getData()
    {
//        if (empty($this->data))
//        {
//            $app = JFactory::getApplication();

            if(empty($this->data))
            {
                $data = JRequest::getVar('jform');

                if(empty($data))
                {
                    $selected = $this->getState('_ECR_COM_TBL_NAME_.id');
                    $data = $this->getTable();
                    $data->load((int)$selected);
                }

                $this->data = $data;
//                $query = $this->_db->getQuery(true);
//
//                // Select all fields from the _ECR_COM_TBL_NAME_ table.
//                $query->select('*');
//                $query->from('`#___ECR_COM_TBL_NAME_`');
//                $query->where('id = ' . (int)$selected);
//
//                $this->_db->setQuery((string)$query);
//                $data = $this->_db->loadAssoc();
            }

//            if (empty($data))
//            {
//                // Check the session for previously entered form data.
//                $data = $app->getUserState('_ECR_COM_COM_NAME_.edit._ECR_COM_NAME_.data', array());
//                unset($data['id']);
//            }
//
//            $app->setUserState('_ECR_COM_COM_NAME_.edit._ECR_COM_NAME_.data', $data);
//            $this->data = $data;
//        }

        return $this->data;
    }//function

    /**
     * Method to get the _ECR_COM_NAME_ form.
     *
     * @return mixed [JForm object on success | false on failure]
     */
    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm('_ECR_COM_COM_NAME_._ECR_COM_NAME_', '_ECR_COM_NAME_'
        , array('control' => 'jform', 'load_data' => $loadData));

        $form->addRulePath(JPATH_COMPONENT.DS.'models'.DS.'rules');

        return $form;
    }//function

    /**
     * Method to get the javascript attached to the form
     *
     * @return string URL to the script.
     */
    public function getScript()
    {
        return 'administrator/components/_ECR_COM_COM_NAME_/models/forms/_ECR_COM_TBL_NAME_.js';
    }//function

    /**
     * Method to save a record.
     *
     * @param array $data array of data
     *
     * @return boolean True on success
     */
    public function save($data)
    {
        //-- Database processing
        $row = $this->getTable();

        //-- Bind the form fields to the _ECR_COM_TBL_NAME_ table
        if( ! $row->save($data))
        {
            $this->setError($row->getErrorMsg());

            return false;
        }

        return true;
    }//function
}//class
