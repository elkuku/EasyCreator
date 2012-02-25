/**
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath
 * @author     Created on 03-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 *
 */
//function registerTable(ecr_project, name, id)
//{
//    url = 'index.php?option=com_easycreator&tmpl=component&format=raw&controller=stuffer';
//    url += '&table_name='+name;
//    url += '&ecr_project='+ecr_project;
//
//    if(FBPresent) console.log('adding table'+name);
//
//    A = new Request({
//url: url + '&task=registerTable',
//    {
//       'onRequest': function()
//           {
//               $(id).innerHTML = 'Registering...';
//           },
//       'onComplete': function(request)
//       {
//
//           if(request == '*OK*')
//           {
//               alert('BLI');
//           }
//           else
//           {
//               $(id).innerHTML = request;
//           }
//       }
//    }).send();
//}//function

function checkTableEditForm(theForm) {
    var atLeastOneField = 0;
    var i, elm, elm2, elm3, val, id;

    for (i = obCountOrig; i < obCount; i++) {
        id = "fields[" + i + "][type]";
        elm = theForm.elements[id];

        if (undefined == elm) continue;

        if (elm.value == 'VARCHAR' || elm.value == 'CHAR'
            || elm.value == 'BIT' || elm.value == 'VARBINARY'
            || elm.value == 'BINARY') {
            elm2 = theForm.elements["fields[" + i + "][length]"];
            val = parseInt(elm2.value);
            elm3 = theForm.elements["fields[" + i + "][name]"];

            if (isNaN(val) && elm3.value != "") {
                elm2.select();
                alert(jgettext('This is not a number'));
                elm2.focus();

                return false;
            }
        }

        if (atLeastOneField == 0) {
            id = "fields[" + i + "][name]";

            if (!emptyCheckTheField(theForm, id)) {
                atLeastOneField = 1;
            }
        }
    }

    if (atLeastOneField == 0) {
        alert(jgettext('Please add at least one field'));

        return false;
    }

    return true;
}//function

/**
 * @param elName
 * @return
 */
function newRow(elName) {
    var div = new Element('div', {'class':'ecr_dbRow active', 'id':'dbRow' + obCount});

    var size = '12';

    //-- Name / Field --- @todo change ?
    var fieldName = new Element('div', {'class':'ecr_dbRowCell'}).injectInside(div);
    new Element('input', {'type':'text', 'name':'fields[' + obCount + '][name]', 'size':size})
        .injectInside(fieldName);

    //-- Label
    var fieldDisplay = new Element('div', {'class':'ecr_dbRowCell'}).injectInside(div);
    new Element('input', {'type':'text', 'name':'fields[' + obCount + '][label]', 'size':size})
        .injectInside(fieldDisplay);
    //  fieldDisplay.injectInside(div);

    //-- Type
    dbGetSelectTypes().injectInside(div);

    //-- Length / Set
    var fieldLength = new Element('div', {'class':'ecr_dbRowCell'}).injectInside(div);
    new Element('input', {'type':'text', 'name':'fields[' + obCount + '][length]', 'size':size})
        .injectInside(fieldLength);

    //-- Attributes
    var fieldAttribs = new Element('div', {'class':'ecr_dbRowCell'}).injectInside(div);
    var fieldAttribsSelect = new Element('select', {'name':'fields[' + obCount + '][attributes]'})
        .injectInside(fieldAttribs);

    new Element('option', {'value':''}).appendText('').injectInside(fieldAttribsSelect);
    new Element('option', {'value':'UNSIGNED'}).appendText('UNSIGNED').injectInside(fieldAttribsSelect);
    new Element('option', {'value':'UNSIGNED ZEROFILL'}).appendText('UNSIGNED ZEROFILL').injectInside(fieldAttribsSelect);

    //-- Null
    var fieldNull = new Element('div', {'class':'ecr_dbRowCell'}).injectInside(div);
    var fieldNullSelect = new Element('select', {'name':'fields[' + obCount + '][null]'})
        .injectInside(fieldNull);

    new Element('option', {'value':'NOT_NULL'}).appendText('NOT NULL').injectInside(fieldNullSelect);
    new Element('option', {'value':'NULL'}).appendText('NULL').injectInside(fieldNullSelect);

    //-- Default
    var fieldDefault = new Element('div', {'class':'ecr_dbRowCell'}).injectInside(div);
    new Element('input', {'type':'text', 'name':'fields[' + obCount + '][default]', 'size':size})
        .injectInside(fieldDefault);

    //-- Extra
    var fieldExtra = new Element('div', {'class':'ecr_dbRowCell'}).injectInside(div);
    var fieldExtraSelect = new Element('select', {'name':'fields[' + obCount + '][extra]'})
        .injectInside(fieldExtra);

    new Element('option', {'value':''}).appendText('').injectInside(fieldExtraSelect);
    new Element('option', {'value':'AUTO_INCREMENT'}).appendText('AUTO INCREMENT').injectInside(fieldExtraSelect);

    //-- Comment
    var fieldComment = new Element('div', {'class':'ecr_dbRowCell'}).injectInside(div);
    new Element('input', {'type':'text', 'name':'fields[' + obCount + '][comment]', 'size':size})
        .injectInside(fieldComment);

    var onclick = 'document.id(\'dbRow' + obCount + '\').dispose()';
    new Element('span', {'class':'ecr_button', 'onclick':onclick, 'text':jgettext('Delete')})
        .injectInside(div);

    div.injectInside($(elName));

    obCount++;

    $(elName).setStyle('width', obCount * 100)
}//function

/** @todo DUP ?? */
function addField() {
    add_field_name = 'Name <input type="text" name="new_field_name[]" />';

    s = '';
    s += 'Type';
    s += '<select name="new_field_type[]">';
    s += '<option value="int">INT</option>';
    s += '<option value="varchar">VARCHAR</option>';
    s += '</select>';

    var div = new Element('div');
    div.innerHTML = add_field_name + s;
    $('addField').adopt(div);
}//function

/**
 *
 * @param tableName
 * @return
 */
function addRelation(tableName) {
    $('table_name').value = tableName;

    submitform('new_relation');
}//function

/**
 *
 * @param selected
 * @return
 */
function dbGetSelectTypes(selected) {
    var names = [
        'VARCHAR'
        , 'TINYINT'
        , 'TEXT'
        , 'DATE'
        , 'SMALLINT'
        , 'MEDIUMINT'
        , 'INT'
        , 'BIGINT'
        , 'FLOAT'
        , 'DOUBLE'
        , 'DECIMAL'
        , 'DATETIME'
        , 'TIMESTAMP'
        , 'TIME'
        , 'YEAR'
        , 'CHAR'
        , 'TINYBLOB'
        , 'TINYTEXT'
        , 'MEDIUMBLOB'
        , 'MEDIUMTEXT'
        , 'LONGBLOB'
        , 'LONGTEXT'
        , 'ENUM'
        , 'SET'
        , 'BIT'
        , 'BOOL'
        , 'BINARY'
        , 'VARBINARY'
    ];

    var div = new Element('div', { 'class':'ecr_dbRowCell'});
    var select = new Element('select', { 'name':'fields[' + obCount + '][type]'}).injectInside(div);

    for (var i = 0; i < names.length; i++) {
        new Element('option', {'value':names[i]}).appendText(names[i]).injectInside(select);
    }//for

    return div;
}//function

function getTableFieldSelector(tableName, fieldName) {
    url = ecrAJAXLink + '&controller=ajax';
    url += '&task=get_table_field_selector';
    url += '&table=' + tableName;
    url += '&field_name=' + fieldName;

    new Request({
        url:url,

        'onRequest':function () {
            $(fieldName + '_container').innerHTML = jgettext('Loading...');
            $(fieldName + '_container').className = 'ajax_loading16';
        },

        'onComplete':function (response) {
            var resp = JSON.decode(response);

            if (!resp.status) {
                //-- Error
            }
            $(fieldName + '_container').innerHTML = resp.text;
            $(fieldName + '_container').className = '';
        }
    }).send();
}//function
