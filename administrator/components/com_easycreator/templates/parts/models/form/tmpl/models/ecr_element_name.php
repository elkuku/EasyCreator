<?php
##*HEADER*##

jimport('joomla.application.component.model');

/**
 * ECR_ELEMENT_NAME model.
 *
 * @package    ECR_COM_NAME
 * @subpackage Models
 */
class ECR_COM_NAMEsModelECR_ELEMENT_NAME extends JModel
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
        $table =& $this->getTable('ECR_ELEMENT_NAME');

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
        $table =& $this->getTable('ECR_ELEMENT_NAME');

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
        $table = $this->getTable('ECR_ELEMENT_NAME');
        $table->load($id[0]);

        return $table;
    }//function
}//class
