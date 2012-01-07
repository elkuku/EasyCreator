/**
 * @package    EasyCreator
 * @subpackage Frontend-JavaScript
 * @author     Nikolai Plath
 * @author     Created on 24-Sep-2008
 */

function drawProject(project)
{
    frm = document.adminForm;
    frm.ebc_project.value = project;
    frm.submit();
}//function
