<?php
##*HEADER*##

jimport('joomla.application.component.model');

/**
 * ECR_ELEMENT_NAME model.
 *
 * @package    ECR_COM_NAME
 * @subpackage Models
 */
class ECR_COM_NAMEECR_LIST_POSTFIXModelECR_ELEMENT_NAME extends JModel
{
    public function __construct()
    {
        $foo = 'Do something here..';

        parent::__construct();
    }

    function store($data)
    {
        $row =& $this->getTable('ECR_ELEMENT_NAME');

        if( ! $row->bind($data))
        {
            return false;
        }

        if( ! $row->check())
        {
            return false;
        }

        if( ! $row->store())
        {
            return false;
        }

        return true;
    }

    function delete()
    {
        $cids = JRequest::getVar('cid', array(0), 'post', 'array');
        $row =& $this->getTable('ECR_ELEMENT_NAME');

        if(count($cids))
        {
            foreach($cids as $cid)
            {
                if( ! $row->delete($cid))
                {
                    $this->setError($row->getError());

                    return false;
                }
            }
        }

        return true;
    }

    function getData()
    {
        $id = JRequest::getVar('cid');
        $row =& $this->getTable('ECR_ELEMENT_NAME');
        $row->load($id[0]);

        return $row;
    }
}
