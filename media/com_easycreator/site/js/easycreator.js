/**
 * @package    EasyCreator
 * @subpackage Frontend-JavaScript
 * @author     Nikolai Plath
 * @author     Created on 24-Sep-2008
 */

function drawProject(project)
{
    frm = document.adminForm;
    frm.ecr_project.value = project;
    frm.submit();
}
