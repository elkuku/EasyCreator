/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath {@link http://www.nik-it.de}
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

function checkTableEditForm(theForm)
{
    var atLeastOneField = 0;
    var i, elm, elm2, elm3, val, id;

    for (i=obCountOrig; i<obCount; i++)
    {
        id = "fields[" + i + "][type]";
        elm = theForm.elements[id];
        
        if (elm.value == 'VARCHAR' || elm.value == 'CHAR' || elm.value == 'BIT' || elm.value == 'VARBINARY' || elm.value == 'BINARY')
        {
            elm2 = theForm.elements["fields[" + i + "][length]"];
            val = parseInt(elm2.value);
            elm3 = theForm.elements["fields[" + i + "][name]"];
            
            if (isNaN(val) && elm3.value != "")
            {
                elm2.select();
                alert(jgettext('This is not a number'));
                elm2.focus();
                
                return false;
            }
        }

        if (atLeastOneField == 0)
        {
            id = "fields[" + i + "][name]";
            if( ! emptyCheckTheField(theForm, id))
            {
                atLeastOneField = 1;
            }
        }
    }
    
    if (atLeastOneField == 0)
    {
        alert(jgettext('Please add at least one field'));
    
        return false;
    }

    return true;
}//function

/**
 * @param elName
 * @return
 */
function newRow(elName)
{
    var div = new Element('div', {'class': 'ecr_dbRow'});
    
    var size = '12';

    //-- Name / Field --- @todo change ?
    var fieldName = new Element('div', {'class': 'ecr_dbRowCell'});
    var fieldNameInput = new Element('input',
    		{'type':'text', 'name':'fields['+obCount+'][name]', 'size':size}).injectInside(fieldName);
    fieldName.injectInside(div);
    
    //-- Label
    var fieldDisplay = new Element('div', {'class': 'ecr_dbRowCell'});
    var fieldDisplayInput = new Element('input',
    		{'type':'text', 'name':'fields['+obCount+'][label]', 'size':size}).injectInside(fieldDisplay);
    fieldDisplay.injectInside(div);
    
    //-- Type
    selectTypes = dbGetSelectTypes();
    selectTypes.injectInside(div);
    
    //-- Length / Set
    var fieldLength = new Element('div', {'class': 'ecr_dbRowCell'});
    var fieldLengthInput = new Element('input',
    		{'type':'text', 'name':'fields['+obCount+'][length]', 'size':size}).injectInside(fieldLength);
    fieldLength.injectInside(div);
    
    //-- Attributes
    var fieldAttribs = new Element('div', {'class': 'ecr_dbRowCell'});
    var fieldAttribsSelect = new Element('select',
            { 'name': 'fields['+obCount+'][attributes]' }).injectInside(fieldAttribs);
    var option = new Element('option', {'value': ''});
    option.appendText('');
    option.injectInside(fieldAttribsSelect);
    var option = new Element('option', {'value': 'UNSIGNED'});
    option.appendText('UNSIGNED');
    option.injectInside(fieldAttribsSelect);
    var option = new Element('option', {'value': 'UNSIGNED ZEROFILL'});
    option.appendText('UNSIGNED ZEROFILL');
    option.injectInside(fieldAttribsSelect);
    fieldAttribs.injectInside(div);

    //-- Null
    var fieldNull = new Element('div', {'class': 'ecr_dbRowCell'});
    var fieldNullSelect = new Element('select',
    		{ 'name': 'fields['+obCount+'][null]' }).injectInside(fieldNull);
    var option = new Element('option', {'value': 'NOT_NULL'});
    option.appendText('NOT NULL');
    option.injectInside(fieldNullSelect);
    var option = new Element('option', {'value': 'NULL'});
    option.appendText('NULL');
    option.injectInside(fieldNullSelect);
    fieldNull.injectInside(div);

    //-- Default
    var fieldLength = new Element('div', {'class': 'ecr_dbRowCell'});
    var fieldLengthInput = new Element('input',
            {'type':'text', 'name':'fields['+obCount+'][default]', 'size':size}).injectInside(fieldLength);
    fieldLength.injectInside(div);
    
    //-- Extra
    var fieldExtra = new Element('div', {'class': 'ecr_dbRowCell'});
    var fieldExtraSelect = new Element('select',
            { 'name': 'fields['+obCount+'][extra]' }).injectInside(fieldExtra);
    var option = new Element('option', {'value': ''});
    option.appendText('');
    option.injectInside(fieldExtraSelect);
    var option = new Element('option', {'value': 'AUTO_INCREMENT'});
    option.appendText('AUTO INCREMENT');
    option.injectInside(fieldExtraSelect);
    fieldExtra.injectInside(div);

    //-- Comment
    var fieldComment = new Element('div', {'class': 'ecr_dbRowCell'});
    var fieldCommentInput = new Element('input',
            {'type':'text', 'name':'fields['+obCount+'][comment]', 'size':size}).injectInside(fieldComment);
    fieldComment.injectInside(div);

    var cl = new Element('div', {'styles': {'clear':'both'}}).injectInside(div);

    div.injectInside($(elName));
    
    obCount ++;
}//function

/** @todo DUP ?? */
function addField()
{
    add_field_name = 'Name <input type="text" name="new_field_name[]" />';

    s = '';
    s += 'Type';
    s += '<select name="new_field_type[]">';
    s += '<option value="int">INT</option>';
    s += '<option value="varchar">VARCHAR</option>';
    s += '</select>';
    
    var div = new Element('div');
    div.innerHTML = add_field_name+s;
    $('addField').adopt(div);
}//function

/**
 * 
 * @param tableName
 * @return
 */
function addRelation(tableName)
{
    $('table_name').value = tableName;

    submitform('new_relation');
}//function

/**
 * 
 * @param selected
 * @return
 */
function dbGetSelectTypes(selected)
{
    var names = [
        'VARCHAR' 
        ,'TINYINT'
        ,'TEXT'
        ,'DATE'
        ,'SMALLINT'
        ,'MEDIUMINT'
        ,'INT'
        ,'BIGINT'
        ,'FLOAT'
        ,'DOUBLE'
        ,'DECIMAL'
        ,'DATETIME'
        ,'TIMESTAMP'
        ,'TIME'
        ,'YEAR'
        ,'CHAR'
        ,'TINYBLOB'
        ,'TINYTEXT'
        ,'MEDIUMBLOB'
        ,'MEDIUMTEXT'
        ,'LONGBLOB'
        ,'LONGTEXT'
        ,'ENUM'
        ,'SET'
        ,'BIT'
        ,'BOOL'
        ,'BINARY'
        ,'VARBINARY'
    ];

    var div = new Element('div', { 'class': 'ecr_dbRowCell'});
    var select = new Element('select', { 'name': 'fields['+obCount+'][type]'}).injectInside(div);
    
    for (var i = 0; i < names.length; i ++)
    {
        var option = new Element('option', {'value': names[i]});
        option.appendText(names[i]);
        option.injectInside(select);
    }//for
    
    return div;
}//function

function getTableFieldSelector(tableName, fieldName)
{
    url = ecrAJAXLink + '&controller=ajax';
    url += '&task=get_table_field_selector';
    url += '&table=' + tableName;
    url += '&field_name=' + fieldName;

    new Request({
    	url: url,

    	'onRequest' : function()
        {
            $(fieldName+'_container').innerHTML = jgettext('Loading...');
            $(fieldName+'_container').className = 'ajax_loading16';
        },

        'onComplete' : function(response)
        {
            var resp = Json.evaluate(response);
            
            if( ! resp.status) {
                //-- Error
            }
            $(fieldName+'_container').innerHTML = resp.text;
            $(fieldName+'_container').className = '';
        }
    }).send();
}//function
