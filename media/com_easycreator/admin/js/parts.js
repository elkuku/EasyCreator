/**
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath
 * @author     Created on 03-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

function showPart(group, part)
{
    document.id('addPartShow').className = ' img ajax_loading16';

    document.id('addPartShow').innerHTML = jgettext('Loading...');
    document.id('addElementMessage').innerHTML = '';

    new Request({
        url:ecrAJAXLink + '&controller=ajax' + '&group=' + group + '&part=' + part,
        update:'addPartShow',
        onComplete:function()
        {
            document.id('addPartShow').className = '';
            document.id('addElementMessage').innerHTML = '';
            div_new_element.show();
        }
    }).send();

    return false;
}//function
