<?php
##*HEADER*##

JApplication::registerEvent('onBeforeDisplayContent', 'plgContent_ECR_COM_NAME_');

/**
 * This plugin will trigger the string {trigger_ECR_COM_NAME_}
 *
 * @param $row
 * @param $params
 * @param $page
 *
 * @return void
 */
function plgContent_ECR_COM_NAME_(&$row, &$params, $page = 0)
{
    if( ! strpos($row->text, '{trigger_ECR_COM_NAME_'))
    {
        //--The tag is not found in content - abort..
        return;
    }

    //--Search for this tag in the content
    $regex = '/{trigger_ECR_COM_NAME_\s*.*?}/i';

    $replacement = JText::_('My replacement');

    //--Replace tag in content
    $row->text = preg_replace($regex, $replacement, $row->text);

    return;
}//function
