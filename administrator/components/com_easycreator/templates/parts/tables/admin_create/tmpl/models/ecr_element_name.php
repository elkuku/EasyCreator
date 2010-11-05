<?php
##*HEADER*##

jimport('joomla.application.component.model');

/**
 * Enter description here ...@todo class doccomment.
 *
 */
class _ECR_COM_NAME_sModel_ECR_ELEMENT_NAME_ extends JModel
{
    public function __construct()
    {
        $foo = 'Do something here..';

        parent::__construct();
    }

    function store($data)
    {
        $row =& $this->getTable('_ECR_ELEMENT_NAME_');

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
        $row =& $this->getTable('_ECR_ELEMENT_NAME_');

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
    }

    function getData()
    {
        $id = JRequest::getVar('cid');
        $row =& $this->getTable('_ECR_ELEMENT_NAME_');
        $row->load($id[0]);
        return $row;
    }
}
