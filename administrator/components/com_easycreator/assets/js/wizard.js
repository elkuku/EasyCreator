/**
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 11-Oct-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

function goWizard(num)
{
    $('wizard-loader').removeClass('icon-32-wizard');
    $('wizard-loader').addClass('ajax-loading-32');
    submitbutton('wizard' + num);
}//function

function setTemplate(type, name)
{
    $('tpl_type').value = type;
    $('tpl_name').value = name;
}//function

function getExtensionTemplateInfo(extType, folder, e)
{
    if(e.open)
    {
        e.toggle();

        return;
    }

    var htmlId = extType + '_' + folder;

    $('btn_' + htmlId).className = 'ecr_button ajax_loading16';

    url = ecrAJAXLink + '&controller=starter&task=ajGetExtensionTemplateInfo';
    url += '&extType=' + extType;
    url += '&folder=' + folder;

    new Request({
        url: url,

        'onRequest' : function()
        {
            //$('ecr_title_pic').innerHTML = jgettext('Loading...');
        }

        , 'onComplete' : function(response)
        {
            var resp = JSON.decode(response);

            if(resp.status)
            {
            }
            else
            {
                //-- Error
            }

            $(htmlId + '_files').innerHTML = resp.text;

            e.toggle();

            $('btn_' + htmlId).className = 'ecr_button img icon-16-add';
        }
    }).send();

}//function

function changeJVersion(version)
{
	$$('div.jcompat_'+version).each(function(e){
		var style =(e.getStyle('display') == 'none') ? 'block' : 'none';
		e.setStyle('display', style);
		});
}
