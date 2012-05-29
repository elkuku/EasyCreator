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
    if(document.id('package-to') == null) {
        submitbutton(task);

        return;
    }

    var elements = document.id('package-to').getElements('li');

    var inserts = new Array();

    elements.each(function(el) {
        inserts.push(el.id);
    });

    document.id('packageElements').value = inserts.join(',');

    submitbutton(task);
}

window.addEvent('domready', function() {
    var mySortables = new Sortables('#package-from, #package-to', {
        constrain : false,
        clone : true,
        revert : true
    });
});

var ecrStuffer = new Class({
    cnt_update : 0,

    addUpdateServer : function(name, url, type, priority) {
        var container = document.id('updateServers');
        var html = '';

        html += jgettext('URL') + ': <input type="text" name="updateServers[url][]" value="' + url + '" /><br />';
        html += jgettext('Name') + ': <input type="text" name="updateServers[name][]" value="' + name + '" /><br />';
        html += jgettext('Priority') + ': <input type="text" class="span1" name="updateServers[priority][]" value="' + priority + '" /> ';
        html += jgettext('Type') + ': <input type="text" class="span1" name="updateServers[type][]" value="' + type + '" /><br />';
        html += '<br /><span class="btn btn-mini" onclick="this.getParent().dispose();">';

        html += jgettext('Delete');
        html += '</span>';

        new Element('div', {'style' : 'border: 1px solid silver; padding: 0.4em; margin: 0.2em;'})
            .set('html', html)
            .inject(container);
    },

    newAction : function() {
        var e = document.id('sel_actions');

        var type = e.options[e.selectedIndex].value;

        if('' == type) {
            alert(jgettext('Please select a type'));

            return false;
        }

        trigger = '';

        Stuffer.addAction(type, trigger, {});
    },

    addAction : function(type, trigger, options) {

        this.cnt_update += 1;
        trigger = (undefined == trigger) ? '' : trigger;
        options.trigger = trigger;

        var container = document.id('container_actions');

        var html = '';
        var cnt = this.cnt_update;

        html += '<input type="hidden" name="actions[' + this.cnt_update + ']" value="' + type + '" />';

        html += '<div class="actionTitle">'+type+'</div>';

        html += '<label class="inline" for="fields_' + cnt + '_trigger">' + jgettext('Trigger') + '</label>'
            //+ '<input class="span2" type="text" name="fields[' + cnt + '][trigger]"'
            //+ ' id="fields_' + cnt + '_trigger" value="' + trigger + '">' +
            + '<select class="span2" name="fields[' + cnt + '][trigger]">'
            + '<option value="precopy"' + ('precopy' == trigger ? ' selected="selected"' : '') + '>Pre Copy</option>'
            + '<option value="postcopy"' + ('postcopy' == trigger ? ' selected="selected"' : '') + '>Post Copy</option>'
            + '</select>'
            + '<br />';

        new Request({
            url : ecrAJAXLink + '&controller=ajax&task=getAction'
                + '&type=' + type
                + '&options=' + JSON.stringify(options)
                + '&cnt=' + this.cnt_update,

            onComplete : function(response) {
                var r = JSON.decode(response);

                if(r.status) {
                    //-- Error
                    alert(r.message + r.debug);

                    return false;
                }

                html += r.message;

                html += '<br /><span class="btn btn-mini" onclick="this.getParent().dispose();">';
                html += jgettext('Delete');
                html += '</span>';

                new Element('div', {'style' : 'border: 1px solid silver; padding: 0.4em; margin: 0.2em;'})
                    .set('html', html)
                    .inject(container);
            }
        }).send();
    },

    loadFilenameDefaults : function() {
        new Request({
            url : ecrAJAXLink + '&controller=ajax&task=getEcrParams',

            onComplete : function(response) {
                var resp = JSON.decode(response);

                if(resp.status) {
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
