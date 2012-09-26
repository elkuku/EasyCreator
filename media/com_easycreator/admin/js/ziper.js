/**
 * @package EasyCreator
 * @subpackage Javascript
 * @author Nikolai Plath
 * @author Created on 11-Oct-2009
 * @license GNU/GPL, see JROOT/LICENSE.php
 */

var EcrZiper = new Class({
    url:ecrAJAXLink,

    createPackage:function()
    {
        document.id('zipResult').setStyle('display', 'block');

        var message = document.id('ajaxMessage');
        var result = document.id('zipResultLinks');

        startPoll();

        new Request({
            url:this.url
                + '&' + document.id('adminForm').toQueryString()
                + '&controller=ziper&task=createPackage',

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
                    result.innerHTML = response.debug;
                }
                else
                {
                    result.innerHTML = response.message;
                }

                stopPoll();
            }
        }).send();
    },

    deleteZipFile:function(path, file)
    {
        var box = document.id('ajaxMessage');
        var debug = document.id('ajaxDebug');

        var fx = new Fx.Morph(box, {});

        new Request({
            url:ecrAJAXLink
                + '&controller=ziper&task=delete'
                + '&file_path=' + path
                + '&file_name=' + file,

            onRequest:function()
            {
                box.innerHTML = jgettext('Deleting...');
            },

            onComplete:function(response)
            {
                resp = JSON.decode(response);
                box.set('text', resp.message);

                box.style.color = 'green';

                if(resp.status)
                {
                    box.style.color = 'red';
                    debug = resp.debug;

                    return;
                } else
                {
                    $('row' + file).setStyle('display', 'none');
                }

                fx.start({}).chain(
                    function()
                    {
                        this.start.delay(1000, this, {
                            'opacity':0
                        });
                    }).chain(function()
                    {
                        box.style.display = "none";
                        this.start.delay(100, this, {
                            'opacity':1
                        });
                    });
            }
        }).send();
    },

    updateName:function()
    {
        var el = document.id('ajName');
        var loadStat = document.id('loadStat_filename');

        //var cst_format = $$('input[name=opt_format]:checked')[0].get('value');
        var cst_format = document.id('cst_format').value;

        //document.id('cst_format').value = cst_format;

        new Request.JSON({
            url:ecrAJAXLink
                + '&controller=ziper&task=updateProjectName'
                + '&ecr_project=' + document.id('ecr_project').value
                + '&cst_format=' + cst_format,

            onRequest:function()
            {
                loadStat.addClass('ajax_loading16');
            },

            onFailure:function()
            {
                loadStat.removeClass('ajax_loading16');
            },

            onComplete:function(request)
            {
                loadStat.removeClass('ajax_loading16');

                el.innerHTML = request.message;
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

                for(var v in data)
                {
                    if('actions' == v)
                    {
                        EcrZiper.loadActions(data[v]);

                        continue;
                    }

                    console.log(v);
                    var elTest = document.id(v);

                    if(null == elTest)
                        continue;

                    switch(elTest.type)
                    {
                        /*
                         case 'text' :
                         elTest.value = data[v];
                         break;
                         */

                        case 'checkbox' :
                            elTest.checked = ('1' == data[v]) ? 'checked' : '';
                            break;

                        case 'radio' :
                            elTest.value = data[v];

                            var lbl = document.id('lbl_' + elTest.id);

                            if(undefined != lbl)
                                lbl.set('text', data[v]);
                            break;

                        case undefined : // a label ?
                            elTest.innerHTML = data[v];
                            break;

                        default :
                            console.warn('UNKNOWN: ' + elTest.type);
                    }
                }

                EcrZiper.updateName();
            }
        }).send();
    },

    loadActions:function(actions)
    {
        var list = document.id('actionList');

        list.empty();

        var ev = '';

        for(var i = 0; i < actions.length; i++)
        {
            var action = JSON.decode(actions[i]);

            var li = new Element('li');

            if('' == ev || ev != action.event)
            {
                ev = action.event;

                new Element('li')
                    .set('html', '<strong>' + ev + '</strong>')
                    .inject(list);
            }

            new Element('input', {
                'type':'checkbox',
                'name':'actions[]',
                'id':'action_' + i,
                'value':i,
                'checked':'checked'
            }).inject(li);

            new Element('label', {
                'class':'inline',
                'for':'action_' + i
            }).set('html', action.type).inject(li);

            if('script' == action.type)
            {
                /*
                 $s = (strlen($action->script) > 30)
                 ? '<span class="hasTip" title="'.$action->script.'">...'
                 .substr($action->script, strlen($action->script) - 30)
                 .'</span>'
                 : $action->script;
                 */
                //html += '<code class="scriptName">'+action.script+'</code>';
                new Element('code', {
                    'class':'scriptName'
                }).set('html', action.script).inject(li);
            }

            li.inject(list);
        }
    }
});

var EcrZiper = new EcrZiper;
