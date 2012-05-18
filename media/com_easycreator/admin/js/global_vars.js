/**
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath
 * @author     Created on 03-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * EasyCreator AJAXLink
 */
var ecrAJAXLink = 'index.php?option=com_easycreator&tmpl=component&format=raw';

/**
 * Test if FireBug is available so we can debug out to the console
 */
var FBPresent = true;

if(window.console == undefined)
{
    FBPresent = false;
}
