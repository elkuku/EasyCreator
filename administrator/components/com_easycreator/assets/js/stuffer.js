/**
 * @version SVN: $Id$
 * @package EasyCreator
 * @subpackage Javascript
 * @author Nikolai Plath {@link http://www.nik-it.de}
 * @author Created on 31-May-2010
 * @license GNU/GPL, see JROOT/LICENSE.php
 */

function createFile(type1, type2)
{
	$('type1').value = type1;
	$('type2').value = type2;

	submitbutton('create_install_file');
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
