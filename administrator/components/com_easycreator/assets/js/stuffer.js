/**
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath
 * @author     Created on 31-May-2010
 * @license GNU/GPL, see JROOT/LICENSE.php
 */

function createFile(type1, type2) {
    document.id('type1').value = type1;
    document.id('type2').value = type2;

    submitbutton('create_install_file');
}//function

function submitStuffer(task) {
    if (document.id('package-to') == null) {
        submitbutton(task);

        return;
    }

    var elements = document.id('package-to').getElements('li');

    var inserts = new Array();

    elements.each(function (el) {
        inserts.push(el.id);
    });

    document.id('packageElements').value = inserts.join(',');

    submitbutton(task);
}

window.addEvent('domready', function () {
    var mySortables = new Sortables('#package-from, #package-to', {
        constrain:false,
        clone:true,
        revert:true
    });
});

var ecrStuffer = new Class({
    addUpdateServer:function (name, url, type, priority) {
        var container = document.id('updateServers');
        var html = '';

        html += jgettext('URL') + ': <input type="text" name="updateServers[url][]" value="' + url + '" /><br />';
        html += jgettext('Name') + ': <input type="text" name="updateServers[name][]" value="' + name + '" /><br />';
        html += jgettext('Priority') + ': <input type="text" size="2" name="updateServers[priority][]" value="' + priority + '" /> ';
        html += jgettext('Type') + ': <input type="text" size="8" name="updateServers[type][]" value="' + type + '" /><br />';
        html += '<br /><span class="ecr_button" onclick="this.getParent().dispose();">';

        html += jgettext('Delete');
        html += '</span>';

        var div = new Element('div', {'style':'border: 1px dashed gray; padding: 0.4em; margin: 0.2em;'});

        div.set('html', html);

        div.inject(container);
    },

    loadFilenameDefaults:function () {
        new Request({
            url:ecrAJAXLink + '&controller=ajax&task=getEcrParams',

            onComplete:function (response) {
                var resp = JSON.decode(response);

                if (resp.status) {
                    //-- Error
                    alert(resp.text);

                    return false;
                }

                var params = JSON.decode(resp.text);

                document.id('custom_name_1').set('value', params.custom_name_1);
                document.id('custom_name_2').set('value', params.custom_name_2);
                document.id('custom_name_3').set('value', params.custom_name_3);
                document.id('custom_name_4').set('value', params.custom_name_4);
            }
        }).send();
    }
});

var Stuffer = new ecrStuffer();
