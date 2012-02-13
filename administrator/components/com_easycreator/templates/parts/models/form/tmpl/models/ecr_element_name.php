<?php
##*HEADER*##

jimport('joomla.application.component.model');

/**
 * _ECR_ELEMENT_NAME_ model.
 *
 * @package    _ECR_COM_NAME_
 * @subpackage Models
 */
class _ECR_COM_NAME_sModel_ECR_ELEMENT_NAME_ extends JModel
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $foo = 'Do something here..';

        parent::__construct();
    }//function

    /**
     * Store a record
     *
     * @param array $data The data to be stored.
     *
     * @return bool
     */
    function store($data)
    {
        $table =& $this->getTable('_ECR_ELEMENT_NAME_');

        if( ! $table->bind($data))
        {
            return false;
        }

        if( ! $table->check())
        {
            return false;
        }

        if( ! $table->store())
        {
            return false;
        }

        return true;
    }//function

    /**
     * Delete a record.
     *
     * @return bool
     */
    function delete()
    {
        $cids = JRequest::getVar('cid', array(0), 'post', 'array');
        $table =& $this->getTable('_ECR_ELEMENT_NAME_');

        if(count($cids))
        {
            foreach($cids as $cid)
            {
                if( ! $table->delete($cid))
                {
                    $this->setError($table->getError());
                    return false;
                }
            }//foreach
        }

        return true;
    }//function

    /**
     * Retrieve the dta.
     *
     * @return JTable
     */
    function getData()
    {
        $id = JRequest::getVar('cid');
        $table = $this->getTable('_ECR_ELEMENT_NAME_');
        $table->load($id[0]);

        return $table;
    }//function
}//class
