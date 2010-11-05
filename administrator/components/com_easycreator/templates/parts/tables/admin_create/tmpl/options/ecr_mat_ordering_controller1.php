<?php
function _reOrder($direction)
{
    // Check for request forgeries
    JRequest::checkToken() || jexit('Invalid Token');

    // Initialize variables
    $db	= & JFactory::getDBO();
    $cid	= JRequest::getVar('cid', array(), 'post', 'array');

    if(isset($cid[0]))
    {
        $row = & JTable::getInstance('_ECR_ELEMENT_NAME_', 'Table');
        $row->load((int)$cid[0]);
        $row->move($direction);

        $cache = & JFactory::getCache('com__ECR_COM_NAME_');
        $cache->clean();
    }

    $application = JFactory::getApplication();
    $mainframe->redirect('index.php?option=com__ECR_COM_NAME_&view=_ECR_LOWER_ELEMENT_NAME_s');
}

function saveorder()
{
    // Check for request forgeries
    JRequest::checkToken() || jexit('Invalid Token');

    // Initialize variables
    $db =& JFactory::getDBO();
    $cid = JRequest::getVar('cid', array(), 'post', 'array');

    $total = count($cid);
    $order = JRequest::getVar('order', array(0), 'post', 'array');
    JArrayHelper::toInteger($order, array(0));

    $row =& JTable::getInstance('".$element_name."', 'Table');

    // update ordering values
    for($i = 0; $i < $total; $i++)
    {
        $row->load((int)$cid[$i]);
        // track sections
        if($row->ordering != $order[$i])
        {
            $row->ordering = $order[$i];

            if( ! $row->store())
            {
                JError::raiseError(500, $db->getError());
            }
        }
    }

    $row->reorder();

    $application = JFactory::getApplication();
    $msg = JText::_('New ordering saved');
    $application->redirect('index.php?option=com__ECR_COM_NAME_&view=_ECR_LOWER_ELEMENT_NAME_s', $msg);
}

function orderup()
{
    if($this->order == 'desc')
    $this->_reOrder(1);
    else
    $this->_reOrder(-1);
}

function orderdown()
{
    if ($this->order == 'desc')
    $this->_reOrder(-1);
    else
    $this->_reOrder(1);
}
