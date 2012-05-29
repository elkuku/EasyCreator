<?php
##*HEADER*##

jimport('joomla.application.component.model');

/**
 * ECR_COM_NAME Model.
 *
 * @package    ECR_COM_NAME
 * @subpackage Models
 */
class ECR_COM_NAMEECR_LIST_POSTFIXModelECR_COM_NAME extends JModel
{
    /**
     * Constructor that retrieves the ID from the request.
     */
    public function __construct()
    {
        parent::__construct();

        $array = JRequest::getVar('cid', 0, '', 'array');
        $this->setId((int)$array[0]);
    }//function

    /**
     * Method to set the ECR_COM_NAME identifier.
     *
     * @access	public
     * @param	int $id ECR_COM_NAME identifier
     * @return	void
     */
    public function setId($id)
    {
        // Set id and wipe data
        $this->_id = $id;
        $this->_data = null;
    }//function

    /**
     * Method to get a record.
     *
     * @return object with data
     */
    public function &getData()
    {
        // Load the data
        if(empty($this->_data))
        {
            $query = 'SELECT * FROM #__ECR_COM_TBL_NAME'
            . ' WHERE id = '.(int)$this->_id;
            $this->_db->setQuery($query);
            $this->_data = $this->_db->loadObject();
        }

        if( ! $this->_data)
        {
            $this->_data = $this->getTable();
        }

        return $this->_data;
    }//function

    /**
     * Method to store a record
     *
     * @access	public
     * @return	boolean	True on success
     */
    public function store()
    {
        $row =& $this->getTable();

        $data = JRequest::get('post');

        //-- Bind the form fields to the hello table
        if( ! $row->bind($data))
        {
            $this->setError($this->_db->getError());

            return false;
        }

        //-- Make sure the record is valid
        if( ! $row->check())
        {
            $this->setError($this->_db->getError());

            return false;
        }

        //-- Store the table to the database
        if( ! $row->store())
        {
            $this->setError($row->getError());

            return false;
        }

        return true;
    }//function

    /**
     * Method to delete record(s).
     *
     * @access	public
     * @return	boolean	True on success
     */
    public function delete()
    {
        $cids = JRequest::getVar('cid', array(0), 'post', 'array');

        $row =& $this->getTable();

        if(count($cids))
        {
            foreach($cids as $cid)
            {
                if( ! $row->delete($cid))
                {
                    $this->setError($row->getError());

                    return false;
                }
            }//foreach
        }

        return true;
    }//function
}//class
