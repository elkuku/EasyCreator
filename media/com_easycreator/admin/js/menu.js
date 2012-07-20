/**
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath
 * @author     Created on 16-Oct-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/* Standard Joomla! menu images - css class */
var stdMenuImgs = new Array('archive', 'article', 'category', 'checkin',
    'component', 'config', 'content', 'cpanel', 'default', 'frontpage',
    'help', 'info', 'install', 'language', 'logout', 'massmail', 'media',
    'menu', 'menumgr', 'messages', 'module', 'plugin', 'stats', 'themes',
    'trash', 'user ');

function newSubmenu(a, b, c, d, e, parent)
{
    newLi = addSubmenu('', '', '', '', '', parent);

    sortSubMenu.addItems(newLi);
}

function addSubmenu(text, link, image, ordering, menuid, parent)
{
    var ni = document.id('divSubmenu');
    var numi = document.id('totalSubmenu');
    var num = (document.id('totalSubmenu').value - 1) + 2;

    numi.value = num;

    var divIdName = 'submenu' + num + 'Div';

    var newdiv = document.createElement('li');

    newdiv.setAttribute('class', 'menu');

    var html = '';

    html += '<input type="hidden" name="submenu[' + num + '][menuid]" value="' + menuid + '" />';

    if(parent)
    {
        html += "<input type=\"hidden\" name=\"submenu[" + num
            + "][parent]\" value=\"" + parent + "\" />";
    }

    html += '<i class="img icon16-move" style="cursor: move;"></i>'
        + "<span class=\"ecr_label2\">" + jgettext('Text')
        + "</span><input type=\"text\" name=\"submenu[" + num
        + "][text]\" size=\"15\" value=\"" + text
        + "\" style=\"border: 2px solid blue; font-size: 1.3em;\" />";
    html += '<span class="ecr_label2">' + jgettext('Link') + '</span>';
    html += "<input type=\"text\" name=\"submenu[" + num
        + "][link]\" size=\"25\" value=\"" + link + "\" />";

    html += '<br />';
    html += '<br />';
    html += "<span class=\"ecr_label2\">" + jgettext('Image') + "</span>";
    html += "<div id=\"menuPic-" + num + "\" style=\"display: inline;\"></div>";
    html += "<div id=\"prev-" + num + "\" style=\"display: inline;\"></div>";
    html += "<input type=\"text\" name=\"submenu[" + num
        + "][img]\" size=\"30\" value=\"" + image + "\" id=\"img-" + num + "\" />";
    html += "<div style=\"float: right\" class=\"btn\""
        + " onclick=\"this.getParent().dispose();\"><i class=\"img icon16-delete\"></i>" + jgettext('Delete') + "</div>";

    newdiv.set('html', html);

    ni.appendChild(newdiv);

    drawPicChooser(num, image);

    return newdiv;
}//function

function chgMenuPic(num)
{
    if(num == undefined)
    {
        num = '';
    }

    selection = $('opt-' + num).value;

    switch(selection)
    {
        case '':
            $('img-' + num).value = '';
            $('img-' + num).readOnly = true;
            $('prev-' + num).setAttribute('class', '');
            break;

        case 'user_defined':
            $('img-' + num).readOnly = false;
            $('prev-' + num).setAttribute('class', '');
            break;

        default:
            $('img-' + num).value = selection;
            $('img-' + num).readOnly = true;
            $('prev-' + num).setAttribute('class', 'img icon-16-' + selection);
            break;
    }// switch
}//function

function drawPicChooser(num, selectedImage)
{
    html = '';
    html += "<select name=\"opt-" + num + "\" id=\"opt-" + num
        + "\" onchange=\"chgMenuPic(" + num + ");\">";
    html += '<option value=\"\">' + jgettext('Select...') + '</option>';
    found = false;

    for(var i = 0; i <= stdMenuImgs.length - 1; i++)
    {
        selected = '';

        if(selectedImage == stdMenuImgs[i])
        {
            selected = ' selected=\"selected\"';
            found = true;
        }

        html += "<option" + selected + " class=\"img icon-16-" + stdMenuImgs[i] + "\">" + stdMenuImgs[i] + "</option>";
    }

    selected = '';

    if(selectedImage != '' && found == false)
    {
        selected = ' selected=\"selected\"';
    }

    html += '<option value=\"user_defined\"' + selected + '>'
        + jgettext('User defined') + '</option>';
    html += "</select>";

    $('menuPic-' + num).innerHTML = html;
    chgMenuPic(num);
}//function
