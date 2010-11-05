/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 11-Oct-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

function submitbutton(command)
{
    var form = document.adminForm;
    if (command == 'wizard3')
    {
        //--do some field validation
        valid = true;
        $('req_version').innerHTML = '';
        $('req_name').innerHTML = '';

        if($('req_list_postfix') != null)
        {
            $('req_list_postfix').innerHTML = '';
        }

        if(form.version.value == '')
        {
            $('req_version').innerHTML = '<div style="color: red;">' + jgettext('You must provide a version number') + '</div>';
            $('version').focus();
            var div = $('req_version').setStyles({
                display:'block',
                opacity: 0
            });
            new Fx.Style($('req_version'), 'opacity', {duration: 1500} ).start(1);
            valid = false;
        }

        if(form.com_name.value == '')
        {
            $('req_name').innerHTML = '<div style="color: red;">' + jgettext('You must provide a name') + '</div>';
            $('com_name').focus();
            var div = $('req_name').setStyles({
                display:'block',
                opacity: 0
            });
            new Fx.Style($('req_name'), 'opacity', {duration: 1500} ).start(1);
            valid = false;
        }

        if(form.list_postfix.value == '')
        {
            $('req_list_postfix').innerHTML = '<div style="color: red;">' + jgettext('You must provide a list postfix') + '</div>';
            $('list_postfix').focus();
            var div = $('req_list_postfix').setStyles({
                display:'block',
                opacity: 0
            });
            new Fx.Style($('req_list_postfix'), 'opacity', {duration: 1500} ).start(1);
            valid = false;
        }

        if(valid)
        {
            $('wizard-loader').removeClass('icon-32-wizard');
            $('wizard-loader').addClass('ajax-loading-32');
            submitform(command);
        }
    }
    else
    {
        //--user selected 'back' or any other stuff
        $('wizard-loader').removeClass('icon-32-wizard');
        $('wizard-loader').addClass('ajax-loading-32');
        submitform(command);
    }
}//function
