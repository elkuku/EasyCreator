/**
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath
 * @author     Created on 16-Oct-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

function addElement()
{
    // Get the value into the input text field
    var elementText = $('newElement').value;
    var msg = $('msg');

    if(elementText == "")
    {
        // Show an error message if the field is blank;
        msg.style.display = 'block';
        msg.innerHTML = jgettext('Please enter a description for the element');
        $('newElement').focus();
//        $('newElement').highlight('#ddf');
    }
    else
    {
        var inp = new Element('input', {
            'type':'hidden',
            'name':'langfile[]',
            'value':'#' + elementText
        });

        var sp = new Element('span', {'style':'color: orange;', 'text':'#' + elementText});

        // Show a message if the element has been added;
        msg.style.display = 'block';
        msg.innerHTML = jgettext('The element has been added');

        // Clean input field
        $('newElement').value = '';
        var li = new Element('li', {'class':'handle'}).inject($('orderMe'));

        inp.inject(li);
        sp.inject(li);

        var sortList = new Sortables($('orderMe'));
    }
}//function
