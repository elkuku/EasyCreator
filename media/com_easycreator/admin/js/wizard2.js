/**
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath
 * @author     Created on 11-Oct-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

function submitbutton(command)
{
    var form = document.adminForm;
    if(command == 'wizard3')
    {
        //--do some field validation
        valid = true;
        document.id('req_version').innerHTML = '';
        document.id('req_name').innerHTML = '';

        if(document.id('req_list_postfix') != null)
        {
            document.id('req_list_postfix').innerHTML = '';
        }

        if(form.version.value == '')
        {
            document.id('req_version').innerHTML = '<div style="color: red;">' + jgettext('You must provide a version number') + '</div>';
            document.id('version').focus();
            var div = document.id('req_version').setStyles({
                display:'block',
                opacity:0
            });
            new Fx.Tween(document.id('req_version'), Object.extend({property:'opacity'}), {duration:1500}).start(1);
            valid = false;
        }

        if(form.com_name.value == '')
        {
            document.id('req_name').innerHTML = '<div style="color: red;">' + jgettext('You must provide a name') + '</div>';
            document.id('com_name').focus();
            var div = document.id('req_name').setStyles({
                display:'block',
                opacity:0
            });
            new Fx.Tween(document.id('req_name'), Object.extend({property:'opacity'}), {duration:1500}).start(1);
            valid = false;
        }

        if(form.list_postfix.value == '')
        {
            document.id('req_list_postfix').innerHTML = '<div style="color: red;">' + jgettext('You must provide a list postfix') + '</div>';
            document.id('list_postfix').focus();
            var div = document.id('req_list_postfix').setStyles({
                display:'block',
                opacity:0
            });
            new Fx.Tween(document.id('req_list_postfix'), Object.extend({property:'opacity'}), {duration:1500}).start(1);
            valid = false;
        }

        if(valid)
        {
            document.id('wizard-loader').removeClass('icon-32-wizard');
            document.id('wizard-loader').addClass('ajax-loading-32');
            submitform(command);
        }
    }
    else
    {
        //--user selected 'back' or any other stuff
        document.id('wizard-loader').removeClass('icon-32-wizard');
        document.id('wizard-loader').addClass('ajax-loading-32');
        submitform(command);
    }
}//function
