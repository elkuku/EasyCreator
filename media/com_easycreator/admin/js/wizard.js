/**
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath
 * @author     Created on 11-Oct-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

function goWizard(num)
{
    document.id('wizard-loader').removeClass('icon-32-wizard');
    document.id('wizard-loader').addClass('ajax-loading-32');
    submitbutton('wizard' + num);
}

function setTemplate(type, name)
{
    document.id('tpl_type').value = type;
    document.id('tpl_name').value = name;
}

function getExtensionTemplateInfo(extType, folder, e)
{
    var htmlId = extType + '_' + folder;

    if(e.open)
    {
        e.toggle();
        document.id('btn_' + htmlId).className = 'btn btn-info';
        return;
    }

    //document.id('btn_' + htmlId).addClass = 'ajax_loading16';
    document.id('btn_' + htmlId).className = 'btn btn-warning';

    url = ecrAJAXLink + '&controller=starter&task=ajGetExtensionTemplateInfo';
    url += '&extType=' + extType;
    url += '&folder=' + folder;

    new Request({
        url:url,

        'onRequest':function()
        {
            //$('ecr_title_pic').innerHTML = jgettext('Loading...');
        },

        'onComplete':function(response)
        {
            var resp = JSON.decode(response);

            document.id(htmlId + '_files').innerHTML = resp.text;

            e.toggle();

            document.id('btn_' + htmlId).className = 'btn btn-success';
        }
    }).send();
}

function changeJVersion(version)
{
    $$('div.jcompat_' + version).each(function(e)
    {
        var style = (e.getStyle('display') == 'none') ? 'block' : 'none';
        e.setStyle('display', style);
    });
}
