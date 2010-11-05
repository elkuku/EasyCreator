<?php
##*HEADER*##

jimport('joomla.application.component.modellist');

/**
 * _ECR_COM_NAME__ECR_LIST_POSTFIX_ Model.
 *
 * @package _ECR_COM_NAME_
 */
class _ECR_COM_NAME_Model_ECR_COM_NAME__ECR_LIST_POSTFIX_ extends JModelList
{
    /**
     * Model context string.
     *
     * @var string
     */
    protected $_context = '_ECR_COM_COM_NAME_._ECR_COM_NAME__ECR_LIST_POSTFIX_';

    /**
     * Method to remove the selected items.
     *
     * @return boolean true on success
     */
    public function remove()
    {
        // Get the selected items
        $selected = $this->getState('selected');

        // Get a weblink row instance
        $table = $this->getTable('_ECR_COM_NAME_');

        foreach($selected as $id)
        {
            // Load the row and check for an error.
            if( ! $table->load($id))
            {
                $this->setError($table->getError());

                return false;
            }

            // Delete the row and check for an error.
            if( ! $table->delete())
            {
                $this->setError($table->getError());

                return false;
            }
        }//foreach

        return true;
    }//function

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return string SQL query
     */
    protected function getListQuery()
    {
//        $db = JFactory::getDBO();

        //-- Get a JDatabasequery object.
        $query = $this->_db->getQuery(true);

        //-- Select the fields
        $query->select('id, greeting');

        //-- From the _ECR_COM_TBL_NAME_ table
        $query->from('#___ECR_COM_TBL_NAME_');

        return $query;
    }//function

    /**
     * Method to auto-populate the model state.
     *
     * This method should only be called once per instantiation and is designed
     * to be called on the first call to the getState() method unless the model
     * configuration flag to ignore the request is set.
     *
     * @return void
     */
    protected function populateState()
    {
        // Initialize variables.
        $app = JFactory::getApplication('administrator');

        // Load the list state.
        $this->setState('list.start', $app->getUserStateFromRequest($this->_context
        . '.list.start', 'limitstart', 0, 'int'));

        $this->setState('list.limit', $app->getUserStateFromRequest($this->_context
        . '.list.limit', 'limit', $app->getCfg('list_limit', 25), 'int'));

        $this->setState('selected', JRequest::getVar('cid', array()));
    }//function
}//class
