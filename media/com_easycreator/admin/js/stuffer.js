/**
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath
 * @author     Created on 31-May-2010
 * @license GNU/GPL, see JROOT/LICENSE.php
 */

var sortActions;

window.addEvent('domready', function()
{
    var mySortables = new Sortables('#package-from, #package-to', {
        constrain:false,
        clone:true,
        revert:true
    });
});

/**
 * @todo move to class
 * @param type1
 * @param type2
 */
function createFile(type1, type2)
{
    document.id('type1').value = type1;
    document.id('type2').value = type2;

    submitbutton('create_install_file');
}//function

/**
 * @todo move to class
 * @param task
 * @param el
 */
function submitStuffer(task, el)
{
    el.addClass('ajax_loading16');

    if(document.id('package-to') == null)
    {
        submitbutton(task);

        return;
    }

    var elements = document.id('package-to').getElements('li');

    var inserts = new Array();

    elements.each(function(el)
    {
        inserts.push(el.id);
    });

    document.id('packageElements').value = inserts.join(',');

    submitbutton(task);
}

var ecrStuffer = new Class({
    cnt_update:0,
    sortActions:null,

    init:function()
    {
        var elements = document.id('actionButtons').getElements('div.display');

        elements.each(function(el)
        {
            el.setStyle('display', 'none');
            el.inject(document.id('actionWindow'));
        });

        elements[0].setStyle('display', 'block');

        elements = document.id('actionButtons').getElements('a');

        elements.each(function(el)
        {
            el.addEvent('mousedown', function(el)
            {
                Stuffer.setActive(this.get('coords'));

                document.id('actionButtons').getElements('a').each(function(el, index)
                {
                    el.removeClass('active');
                });

                this.addClass('active');
            });
        });

        elements[0].addClass('active');
    },

    setActive:function(name)
    {
        document.id('actionWindow').getElements('div.display').each(function(el)
        {
            var style = (name == el.get('title')) ? 'block' : 'none';
            el.setStyle('display', style);
        });
    },

    addUpdateServer:function(name, url, type, priority)
    {
        var container = document.id('updateServers');
        var html = '';

        html += '<label class="inline">' + jgettext('URL') + '</label>'
            + '<input type="text" name="updateServers[url][]" value="' + url + '" /><br />';
        html += '<label class="inline">' + jgettext('Name') + '</label>'
            + '<input type="text" name="updateServers[name][]" value="' + name + '" /><br />';
        html += '<label class="inline">' + jgettext('Priority') + '</label>'
            + '<input type="text" name="updateServers[priority][]" value="' + priority + '" /><br />';
        html += '<label class="inline">' + jgettext('Type') + '</label>'
            + '<input type="text" name="updateServers[type][]" value="' + type + '" /><br />';

        html += '<br /><span class="btn btn-mini" onclick="this.getParent().dispose();">';
        html += jgettext('Delete');
        html += '</span>';

        new Element('div', {'class':'updateServer'})
            .set('html', html)
            .inject(container);
    },

    newAction:function(el)
    {
        var eAction = document.id('sel_actions');
        var eTrigger = document.id('sel_event');

        var type = eAction.options[eAction.selectedIndex].value;
        var event = eTrigger.options[eTrigger.selectedIndex].value;

        eAction.setStyle('outline', '');

        if('' == type)
        {
            eAction.setStyle('outline', '1px solid red');

            return false;
        }

        el.addClass('ajax_loading16');

        Stuffer.addAction(type, event, {});

        el.removeClass('ajax_loading16');
    },

    addAction:function(type, event, options)
    {
        this.cnt_update += 1;
        event = (undefined == event) ? '' : event;
        options.event = event;

        var container = document.id('container_actions_' + event);

        var html = '';
        var cnt = this.cnt_update;

        html += '<input type="hidden" name="actions[' + event + '][' + cnt + ']" value="' + type + '" />';

        title = type.charAt(0).toUpperCase();
        title += type.substr(1);

        var sAdd = '';

        if('script' == type && undefined != options.script)
        {
            console.log(options);
            sAdd = options.script;

            var max = 30;

            if(sAdd.length > max)
            {
                sAdd = '<span class="hasTip" title="' + sAdd + '">...'
                    + sAdd.substr(sAdd.length - max)
                    + '</span>';
            }

            sAdd = ' - <code class="scriptName">' + sAdd + '</code>'
        }

        var sortActions = this.sortActions;

        new Request.JSON({
            async:false, // !!!
            url:ecrAJAXLink + '&controller=stuffer&task=getAction'
                + '&type=' + type
                + '&options=' + JSON.stringify(options)
                + '&cnt=' + this.cnt_update,

            onComplete:function(r)
            {
                if(r.status)
                {
                    //-- Error
                    alert(r.message + r.debug);

                    return false;
                }

                html += '<div class="actionTitle">'
                    + '<i class="img icon16-move" style="cursor: move;" title="' + jgettext('Move') + '"></i>'
                    + '<i class="img icon16-eye" style="cursor: help;" title="' + jgettext('Show settings') + '"'
                    + ' onclick="Stuffer.toggleContainer(this, \'tgl_action_' + cnt + '\');"></i>'
                    + title + sAdd
                    + '<span style="float: right;" class="btn btn-mini" onclick="this.getParent().getParent().dispose();">'
                    + jgettext('Delete')
                    + '</span>'
                    + '</div>';

                html += '<div id="tgl_action_' + cnt + '" style="display: none;"><fieldset>'
                    + r.message
                    + '</fieldset></div>';

                var li = new Element('li', {'style':'border: 1px solid silver; padding: 0.4em; margin: 0.2em;'})
                    .set('html', html)
                    .inject(container);

                if(sortActions)
                {
                    sortActions.addItems(li);
                }
            }
        }).send();
    },

    initActions:function(container)
    {
        this.sortActions = new Sortables('#' + container + ' UL', {
            constrain:true,
            clone:true,
            revert:true,
            onStart:function(el)
            {
                el.setStyle('background', '#add8e6');
            },
            onComplete:function(el)
            {
                el.setStyle('background', '#fff');
            }
        });
    },

    toggleContainer:function(el, id)
    {
        var imgOff = 'icon16-eye';
        var imgOn = 'icon16-eyeclosed';

        if(el.hasClass(imgOn))
        {
            el.removeClass(imgOn);
            el.addClass(imgOff);
            el.title = jgettext('Show settings');

            document.id(id).setStyle('display', 'none');
        }
        else
        {
            el.removeClass(imgOff);
            el.addClass(imgOn);
            el.title = jgettext('Hide settings');

            document.id(id).setStyle('display', 'block');
        }
    },

    loadFilenameDefaults:function(el)
    {
        el.addClass('ajax_loading16');

        new Request.JSON({
            url:ecrAJAXLink + '&controller=stuffer&task=getEcrParams',

            onComplete:function(resp)
            {
                el.removeClass('ajax_loading16');

                if(resp.status)
                {
                    //-- Error
                    alert(resp.message);

                    return false;
                }

                var params = JSON.decode(resp.message);

                document.id('custom_name_1').set('value', params.custom_name_1);
                document.id('custom_name_2').set('value', params.custom_name_2);
                document.id('custom_name_3').set('value', params.custom_name_3);
                document.id('custom_name_4').set('value', params.custom_name_4);
            }
        }).send();
    },

    loadPreset:function(el)
    {
        new Request.JSON({
            url:ecrAJAXLink + '&' + document.id('adminForm').toQueryString()
                + '&controller=stuffer&task=loadPreset',

            'onRequest':function()
            {
                el.addClass('ajax_loading16');
            },

            'onFailure':function()
            {
                el.removeClass('ajax_loading16');

                alert(jgettext('The request failed'));
            },

            'onComplete':function(response)
            {
                el.removeClass('ajax_loading16');

                if(undefined == response)
                    return;

                if(response.status)
                {
                    //-- Error
                    alert(response.message);

                    return;
                }

                var data = JSON.decode(response.data);

                console.log(data);

                for(v in data)
                {
                    if('actions' == v)
                    {
                        console.log('actions...');
                        console.log(data[v]);

                        $$('#container_action ul').each(function(el)
                        {
                            console.log(el);
                            el.empty();
                        });

                        document.id('container_action').getChildren().each(function(el)
                        {
                        });

                        for(var i = 0; i < data[v].length; i++)
                        {
                            var action = JSON.decode(data[v][i]);
                            console.info(action);
                            Stuffer.addAction(action.type, action.event, action);
                        }
                    }
                    else
                    {
                        var elTest = document.id(v);

                        switch(elTest.type)
                        {
                            case 'text' :
                                elTest.value = data[v];
                                break;

                            case 'checkbox' :
                                elTest.checked = ('1' == data[v]) ? 'checked' : '';
                                break;

                            case 'radio' :
                                elTest.value = data[v];
                                var lbl = document.id('lbl_' + elTest.id);

                                if(undefined != lbl)
                                {
                                    lbl.set('text', data[v]);
                                }
                                break;

                            case undefined :
                                elTest.innerHTML = data[v];
                                break;

                            default :
                                console.log('UNKNOWN: ' + elTest.type);
                        }
                    }
                }
            }
        }).send();
    },

    savePreset:function(el)
    {
        el.addClass('ajax_loading16');

        new Request({
            url:ecrAJAXLink + '&controller=stuffer&task=savePreset'
                + '&' + document.id('adminForm').toQueryString(),

            'onRequest':function()
            {
                message.setStyle('color', 'black');
                message.className = 'ajax_loading16';
                message.innerHTML = jgettext('Creating your package...');
                result.innerHTML = '';
            },

            'onComplete':function(r)
            {
                var response = JSON.decode(r);

                message.innerHTML = '';
                message.className = '';
                document.id('ecrProgressBar').getParent().removeClass('active');

                if(response.status)
                {
                    message.innerHTML = response.message;
                    message.setStyle('color', 'red');
                }
                else
                {
                    result.innerHTML = response.message;
                }

                stopPoll();
            }
        }).send();
    }
});

var Stuffer = new ecrStuffer();
