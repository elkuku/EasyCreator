/**
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath
 * @author     Created on 03-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

function loadAutoCode(ecr_project, action, group, element, scope, part)
{
    if(FBPresent) console.log(action + ' - ' + group + ' - ' + element + ' - ' + scope + ' xpos' + xMousePos + ' X ' + yMousePos + ' acElement: ' + part);

    url = ecrAJAXLink + '&controller=autocode';
    url += '&ecr_project=' + ecr_project;
    switch(action)
    {
        case 'new':
            url += '&task=show';
            break;
        case 'edit':
            url += '&task=edit';

            break;
        default:
            alert('Undefined: ' + action);
            return false;
            break;
    }

    oDiv = $('addBox');
    oDiv.style.display = "inline";
    oDiv.style.position = "absolute";
    oDiv.style.top = yMousePos + "px";
    oDiv.style.left = xMousePos + "px";

    dDiv = $('addPartShow');
    dDiv.className = ' img ajax_loading16';
    dDiv.innerHTML = 'Loading...';

    url += '&group=' + group + '&part=' + part + '&element=' + element + '&scope=' + scope;

    new Request.HTML({
        url:url,
        update:'addPartShow',
        onComplete:function()
        {
            dDiv.className = '';
        }
    }).send();

    return false;
}

function loadPart(ecr_project, action, type, element, scope)
{
    $link = 'index.php?option=com_easycreator&controller=ajax&tmpl=component&format=raw';

    if(FBPresent) console.log(action + ' - ' + type + ' - ' + element + ' - ' + scope + ' xpos' + xMousePos + ' X ' + yMousePos);

    url = ecrAJAXLink + '&controller=ajax';
    url += '&ecr_project=' + ecr_project;

    switch(action)
    {
        case 'new':
            url += '&task=show_part';
            break;
        case 'edit':
            url += '&task=edit_part';

            break;
        default:
            alert('Undefined: ' + action);
            break;
    }

    dDiv = $('addPartShow');

    oDiv = $('addBox');
    oDiv.style.display = "inline";
    oDiv.style.position = "absolute";
    oDiv.style.top = yMousePos + "px";
    oDiv.style.left = xMousePos + "px";

    dDiv.className = ' img ajax_loading16';

    dDiv.innerHTML = jgettext('Loading...');

    switch(type)
    {
        case 'tableclass':
            group = 'tableclass';
            part = 'classvar';
            break;

        case 'controller':
            group = 'controllers';
            part = 'data';
            break;

        case 'modelForm':
            group = 'models';
            part = 'form';
            break;
        case 'modelList':
            group = 'models';
            part = 'list';
            break;
        case 'viewList':
            group = 'views';
            part = 'data_list';
            break;
        case 'viewForm':
            group = 'views';
            part = 'data_form';
            break;

        default:
            alert('NOT defined - ' + type);
            return false;
            break;
    }

    url += '&group=' + group + '&part=' + part + '&element=' + element + '&scope=' + scope;

    new Request({
        url:url,
        update:'addPartShow',
        onComplete:function()
        {
            dDiv.className = '';
        }
    }).send();

    return false;
}

function updateAutoCode()
{
    submitform('autocode_update');
}
