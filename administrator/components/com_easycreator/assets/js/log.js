/**
 * @version $Id$
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 16-Oct-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

var lastId;

/**
 * Loads a log file.
 * 
 * @param string name
 * @param string id
 * @return void
 */
function loadLog(name, id)
{
    if( lastId != undefined )
    {
        $(lastId).setStyles({border: '0px'});
    }

    lastId = id;
    var cl = $('ecr_logView').className;

    var uri = '';
    uri += 'index.php?option=com_easycreator&controller=ajax&tmpl=component&format=raw';
    uri += '&task=show_logfile';
    uri += '&file_name=' + name;

    var a = new Request({
    	url: uri ,
        'onRequest': function()
        {
            $('ecr_logView').className = cl + ' ajax_loading16';
            $('ecr_logView').innerHTML = jgettext('Loading...');
            $(id).setStyles({ border: '1px solid #000' });
        },
        'onComplete': function(response)
        {
             var resp = Json.evaluate(response);

             if(resp.status) {
                 $('ecr_logView').innerHTML = '<pre>' + resp.text + '</pre>';
             } else {
                 //-- Error
                 $('ecr_logView').innerHTML = '<strong style="color: red;">' + resp.message+'</strong>';
             }

             $('ecr_logView').className = cl;
        }
    }).send();
}//function
