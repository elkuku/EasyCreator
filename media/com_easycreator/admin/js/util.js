/**
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath
 * @author     Created on 11-Okt-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

if(document.layers)
{
    document.captureEvents(Event.MOUSEMOVE);
    document.onmousemove = captureMousePosition;
} else if(document.all)
{
    document.onmousemove = captureMousePosition;
} else if(document.getElementById)
{
    document.onmousemove = captureMousePosition;
}

// -- Declare the variables that will store the coordinates
var xMousePos = 0; // abscissa
var yMousePos = 0; // ordinate

// -- Capture the position, again depending on client's browser
function captureMousePosition(e)
{
    if(document.layers)
    {
        xMousePos = e.pageX;
        yMousePos = e.pageY;
    } else if(document.all)
    {
        xMousePos = window.event.x + document.body.scrollLeft;
        yMousePos = window.event.y + document.body.scrollTop;
    } else if(document.getElementById)
    {
        xMousePos = e.pageX;
        yMousePos = e.pageY;
    }
}
