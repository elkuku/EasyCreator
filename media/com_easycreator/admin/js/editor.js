/**
 * @package EasyCreator
 * @subpackage Javascript
 * @author Nikolai Plath
 * @author Created on 03-Mar-2008
 * @license GNU/GPL, see JROOT/LICENSE.php
 */

function save_file()
{
    url = 'index.php?option=com_easycreator&tmpl=component&format=raw';
    url += '&controller=ajax&task=save';

    code = encodeURIComponent(editAreaLoader.getValue('ecr_code_area'));

    post = '';
    post += 'file_path=' + $('file_path').value;
    post += '&file_name=' + $('file_name').value;
    post += '&c_insertstring=' + code;

    var box = $('ecr_status_msg');
    var title = $('ecr_title_file');

    var fx = new Fx.Morph(box, {});

    new Request({
        url:url,

        'onRequest':function()
        {
            oldTitle = $('ecr_title_file').innerHTML;

            title.innerHTML = jgettext('Saving...');
            title.addClass('ajax_loading16-red');
        },

        'onComplete':function(response)
        {
            resp = JSON.decode(response);

            title.innerHTML = oldTitle;
            title.removeClass('ajax_loading16-red');

            box.innerHTML = resp.text;
            box.style.display = "inline";

            if(resp.status)
            {
                box.addClass('img icon16-cancel');
                box.style.color = 'red';
            } else
            {
                box.removeClass('img icon16-cancel')
                box.addClass('img icon16-apply');
                box.style.color = 'green';
            }

            $('ajaxDebug').innerHTML = resp.debug;

            fx.start({}).chain(function()
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
        },

        'onFailure':function(item)
        {
            alert(item.responseText);
        }
    }).send(post);
}//function
