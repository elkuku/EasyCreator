/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 03-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

function addNnewElement(fields)
{
    aFields = fields.split(',');
    valid = true;
    for ( var i = 0; i < aFields.length; i++)
    {
        eName = aFields[i].replace(/^\s+/, '');
        if (eName)
        {
            val = $(eName).value;
            if ($(eName).value == '')
            {
                $(eName + '_label').setStyles(
                {
                    color : 'red'
                });
                valid = false;
            } else
            {
                $(eName + '_label').setStyles(
                {
                    color : 'black'
                });
            }
        }
    }// for

    if (!valid)
    {
        $('addElementMessage').innerHTML = '<div style="color: red;">' + jgettext('Please review your input') + '</div>';
        
        var div = $('addElementMessage').setStyles(
        {
            display : 'block',
            opacity : 0
        });
        new Fx.Style(div, 'opacity',
        {
            duration : 1500
        }).start(1);
        div_new_element.slideIn();
        return;
    } else
    {
        submitform('new_element');
    }
}//function

function removeElement(divNum, divName)
{
  var d = document.getElementById(divName);
  var olddiv = document.getElementById(divNum);
  d.removeChild(olddiv);
}//function

function getTableFields(tableName)
{

    $('addPartTableFields').innerHTML = 'Loading...';

    link = 'index.php?option=com_easycreator&controller=ajax&task=show_tablefields&tmpl=component';
    new Ajax(link + '&table_name=' + tableName,
    {
        update : 'addPartTableFields',
        onComplete : function()
        {

            div_new_element.show();
        }
    }).request();
}//function

function explode(delimiter, string, limit)
{
    // http://kevin.vanzonneveld.net
    // + original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // + improved by: kenneth
    // + improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // + improved by: d3x
    // + bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // * example 1: explode(' ', 'Kevin van Zonneveld');
    // * returns 1: {0: 'Kevin', 1: 'van', 2: 'Zonneveld'}
    // * example 2: explode('=', 'a=bc=d', 2);
    // * returns 2: ['a', 'bc=d']

    var emptyArray =
    {
        0 : ''
    };

    // third argument is not required
    if (arguments.length < 2 || typeof arguments[0] == 'undefined'
            || typeof arguments[1] == 'undefined')
    {
        return null;
    }

    if (delimiter === '' || delimiter === false || delimiter === null)
    {
        return false;
    }

    if (typeof delimiter == 'function' || typeof delimiter == 'object'
            || typeof string == 'function' || typeof string == 'object')
    {
        return emptyArray;
    }

    if (delimiter === true)
    {
        delimiter = '1';
    }

    if (!limit)
    {
        return string.toString().split(delimiter.toString());
    } else
    {
        // support for limit argument
        var splitted = string.toString().split(delimiter.toString());
        var partA = splitted.splice(0, limit - 1);
        var partB = splitted.join(delimiter.toString());
        partA.push(partB);
        return partA;
    }
}

function addPackageElement(type, client, name, title, position, ordering)
{
    switch (type)
    {
        case 'module':
            var ni = $('divPackageElementsModules');
            var numi = $('totalPackageElementsModules');
            var num = ($('totalPackageElementsModules').value - 1) + 2;
            numi.value = num;
            var divIdName = "divPackageElementsModules" + num + "Div";
            var divMain = 'divPackageElementsModules';
            attrib1 = 'Client';
            break;

        case 'plugin':
            var ni = $('divPackageElementsPlugins');
            var numi = $('totalPackageElementsPlugins');
            var num = ($('totalPackageElementsPlugins').value - 1) + 2;
            numi.value = num;
            var divIdName = "divPackageElementsPlugins" + num + "Div";
            var divMain = 'divPackageElementsPlugins';
            attrib1 = 'Group';
            break;

        default:
            alert('UNDEFINED ' + type);
            return;
            break;
    }//switch
    
    var newdiv = document.createElement('div');
    
    newdiv.setAttribute("id", divIdName);

    text = '';
    link = '';
    image = '';

    html = "<table style=\"border-bottom: 1px solid grey;\"><tr>";
    html += '<td>';
    html += projectSelector(type, num, title);
    html += '</td><td>';
    html += "<div style=\"float: right\" class=\"ecr_button img icon-16-delete\" onclick=\"removeElement(\'"
            + divIdName + "\', '" + divMain + "')\">" + jgettext('Delete') + "</div>";
    html += '</td>';
    html += '</tr><tr>';
    html += '<td>';
    html += '<span class="ecr_label2">'
            + attrib1
            + '</span><input type="text" readonly="readonly" style="background-color: #f0f0f0;" name="package_'
            + type + '[' + num + '][client]" id="package_' + type + '_' + num
            + '_client" value="' + client + '">';
    html += '<span class="ecr_label2">'
            + type
            + '</span><input type="text" readonly="readonly" style="background-color: #f0f0f0;" name="package_'
            + type + '[' + num + '][name]" id="package_' + type + '_' + num
            + '_name" value="' + name + '">';
    html += '</td>';
    html += "</tr><tr>";
    html += '<td>';
    if (type != 'plugin')
    {
        html += '<span class="ecr_label2">Position</span><input type="text" name="package_'
                + type + '[' + num + '][position]" value="' + position + '">';
    }
    html += '<span class="ecr_label2">ordering</span><input type="text" name="package_'
            + type + '[' + num + '][ordering]" value="' + ordering + '">';
    html += '</td>';
    html += "</tr></table>";

    newdiv.innerHTML = html;
    ni.appendChild(newdiv);
}//function

function projectSelector(type, num, selected)
{
    html = '';
    html += '<select name="package_' + type + '[' + num
            + '][title]" id="package_' + type + '_' + num
            + '" onchange="updateProject(\'' + type + '\', \'' + num
            + '\');" style="font-size: 1.3em;">';
    html += "<option value=''>" + jgettext('Select...') + "</option>\n";
    for ( var key in definedProjects[type + 's'])
    {
        html += "<option value='" + key + "'"
        if (key == selected)
        {
            html += " selected='selected'";
        }
        html += ">" + key + "</option>\n";
    }// for
    html += "</select>\n";

    return html;
}//function

function updateProject(type, num)
{
    selection = $('package_' + type + '_' + num).value;
    client = (selection) ? definedProjects[type + 's'][selection]['client'] : '';
    name = (selection) ? definedProjects[type + 's'][selection]['name'] : '';

    $('package_' + type + '_' + num + '_name').value = name;
    $('package_' + type + '_' + num + '_client').value = client;
}//function
