/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 31-May-2010
 * @license GNU/GPL, see JROOT/LICENSE.php
 */

function createFile(type1, type2)
{
    $('type1').value = type1;
    $('type2').value = type2;

    submitbutton('create_install_file');
}//function

function addUpdateServer(name, url, type, priority)
{
	var container = document.id('updateServers');
	var div = new Element('div', {'style' : 'border: 1px dashed gray; padding: 0.4em; margin: 0.2em;'});
	var html = '';

	html += 'Name: <input type="text" name="updateServers[name][]" value="'+name+'" /><br />';
	html += 'Priority: <input type="text" name="updateServers[priority][]" value="'+priority+'" /><br />';
	html += 'Type: <input type="text" name="updateServers[type][]" value="'+type+'" /><br />';
	html += 'URL: <input type="text" name="updateServers[url][]" value="'+url+'" /><br />';

	html += '<br /><span class="ecr_button" onclick="this.getParent().dispose();">';
	html += jgettext('Delete');
	html += '</span>';

	div.set('html', html);

	div.inject(container);
}//function

function submitStuffer(task)
{
    if ($('package-to') == null)
    {
        submitbutton(task);

        return;
    }

    var elements = $('package-to').getElements('li');

    var inserts = new Array();

    elements.each(function(el)
    {
        inserts.push(el.id);
    });

    $('packageElements').value = inserts.join(',');

    submitbutton(task);
}

window.addEvent('domready', function()
{
    var mySortables = new Sortables('#package-from, #package-to', {
        constrain : false,
        clone : true,
        revert : true
    });
});
