/**
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath
 * @author     Created on 03-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

function submitbutton(task, comType, template)
{
    frm = document.adminForm;

    if(comType == undefined)
    {
        comType = frm.com_type.value;
    }

    if(template == undefined)
    {
        template = frm.template.value;
    }

    frm.com_type.value = comType;
    frm.template.value = template;

    submitform(task);
}//function

function submitInstallForm()
{
    if($('install_package').value == '')
    {
        alert(jgettext('Please select a package to upload'));

        return;
    }

    document.installForm.submit();
}//function
