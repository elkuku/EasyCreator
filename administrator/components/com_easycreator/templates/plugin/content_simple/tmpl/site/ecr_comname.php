<?php
##*HEADER*##

JApplication::registerEvent('onBeforeDisplayContent', 'plgContentECR_COM_NAME');

/**
 * This plugin will trigger the string {triggerECR_COM_NAME}
 *
 * @param $row
 * @param $params
 * @param $page
 *
 * @return void
 */
function plgContentECR_COM_NAME(&$row, &$params, $page = 0)
{
    if( ! strpos($row->text, '{triggerECR_COM_NAME'))
    {
        //--The tag is not found in content - abort..
        return;
    }

    //--Search for this tag in the content
    $regex = '/{triggerECR_COM_NAME\s*.*?}/i';

    $replacement = JText::_('My replacement');

    //--Replace tag in content
    $row->text = preg_replace($regex, $replacement, $row->text);

    return;
}//function
