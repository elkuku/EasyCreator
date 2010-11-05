<?php
##*HEADER*##

jimport('joomla.application.component.controller');

/**
 * Enter description here ...@todo class doccomment.
 *
 */
class _ECR_COM_NAME_sController_ECR_ELEMENT_NAME_ extends JController
{
    var $cid;

    public function __construct()
    {
        parent::__construct();
        // Register Extra tasks
        $this->registerTask('add', 'edit');
        $this->registerTask('unpublish', 'publish');

        $this->cid = JRequest::getVar('cid', array(0), '', 'array');
        JArrayHelper::toInteger($this->cid, array(0));
    }

    private function _buildQuery()
    {
        $this->_query = 'UPDATE #___ECR_TABLE_NAME_'
        .' SET published = '.(int)$this->publish
        .' WHERE id IN ( '.$this->cids.')';

        return $this->_query;
    }

    function edit()
    {
        JRequest::setVar('view', '_ECR_LOWER_ELEMENT_NAME_');
        JRequest::setVar('layout', 'form');
        JRequest::setVar('hidemainmenu', 1);
        parent::display();
    }

    function cancel()
    {
        $msg = JText::_('Operation Cancelled');
        $this->setRedirect('index.php?option=com__ECR_COM_NAME_&view=_ECR_LOWER_ELEMENT_NAME_s', $msg);
    }//function

    function publish()
    {
        $cid		= JRequest::getVar('cid', array(), '', 'array');
        $this->publish	= ($this->getTask() == 'publish' ? 1 : 0);

        JArrayHelper::toInteger($cid);

        if(count($cid) < 1)
        {
            $action = $publish ? 'publish' : 'unpublish';
            JError::raiseError(500, JText::_('Select an item to'.$action, true));
        }

        $this->cids = implode(',', $cid);

        $query = $this->_buildQuery();
        $db = &JFactory::getDBO();
        $db->setQuery($query);

        if( ! $db->query())
        {
            JError::raiseError(500, $db->getError());
        }

        $link = 'index.php?option=com__ECR_COM_NAME_&view=_ECR_LOWER_ELEMENT_NAME_s';
        $this->setRedirect($link, $msg);
    }

    function save()
    {
        $post	= JRequest::get('post');
        $cid	= JRequest::getVar('cid', array(0), 'post', 'array');
        #_ECR_SMAT_DESCRIPTION_CONTROLLER1_
        $post['id'] = (int)$cid[0];

        $model = $this->getModel('_ECR_ELEMENT_NAME_');

        if($model->store($post))
        {
            $msg = JText::_('Item Saved');
        }
        else
        {
            $msg = JText::_('Error Saving Item');
        }

        $link = 'index.php?option=com__ECR_COM_NAME_&view=_ECR_LOWER_ELEMENT_NAME_s';
        $this->setRedirect($link, $msg);
    }

    function remove()
    {
        $model = $this->getModel('_ECR_ELEMENT_NAME_');

        if( ! $model->delete())
        {
            $msg = JText::_('Error Deleting Item');
        }
        else
        {
            $cids = JRequest::getVar('cid', array(0), 'post', 'array');

            foreach($cids as $cid)
            {
                $msg .= JText::_('Item Deleted '.' : '.$cid);
            }
        }

        $this->setRedirect('index.php?option=com__ECR_COM_NAME_&view=_ECR_LOWER_ELEMENT_NAME_s', $msg);
    }//function
    ##ECR_MAT_ORDERING_CONTROLLER1##
}//class
