<?php
##*HEADER*##

if($this->random)
{
    $response['html'] = $this->random;
    $response['msg'] = JText::_('RECIEVED_OK');
}
else
{
    $response['html'] = 'ERROR';
    $response['msg'] = JText::_('RECIEVED_ERROR');
}

if(function_exists('json_encode'))
{
    echo json_encode($response);
}
else
{
    //--seems we are in PHP < 5.2... or json_encode() is disabled
    require_once JPATH_COMPONENT.DS.'helpers'.DS.'json.php';
    $json = new Services_JSON();
    echo $json->encode($response);
}