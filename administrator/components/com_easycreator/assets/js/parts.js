/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 03-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

function showPart(group, part)
{
    $('addPartShow').className = ' img ajax_loading16';

    $('addPartShow').innerHTML = jgettext('Loading...');
    $('addElementMessage').innerHTML = '';
    uri = ecrAJAXLink+'&controller=ajax'+'&group='+group+'&part='+part;

    new Ajax(uri, {
        update: 'addPartShow',
        onComplete: function()
        {
            $('addPartShow').className = '';
            $('addElementMessage').innerHTML = '';
            div_new_element.show();
        }
    }).request();

    return false;
}//function
