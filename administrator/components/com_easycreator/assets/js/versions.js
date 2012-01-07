/**
 * @package EasyCreator
 * @subpackage Javascript
 * @author Nikolai Plath
 * @author Created on 11-Oct-2009
 * @license GNU/GPL, see JROOT/LICENSE.php
 */

function showVersion(number)
{
    document.adminForm.selected_version.value = number;
    submitform('show_versions');
}
