/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 03-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

function switchProject()
{
    frm = document.adminForm;
    frm.controller.value = 'stuffer';
    
    $('ecr_stat_project').className = 'ajax_loading16';
    
    if (frm.file_name != undefined)
    {
        frm.file_name.value = '';
    }

    if (frm.ecr_project.value == 'ecr_new_project')
    {
        frm.controller.value = 'starter';
        $('ecr_stat_project').innerHTML = jgettext('New project...');
        submitform('starter');
        return;
    }

    if (frm.ecr_project.value == 'ecr_register_project')
    {
        frm.controller.value = 'register';
        $('ecr_stat_project').innerHTML = jgettext('Register project...');
        submitform('register');
        return;
    }
    
    var project = $('ecr_project').value;

    if(project)
    {
        document.id('ecr_stat_project').set('html', jgettext('Loading project...'));
    }

    submitform('stuffer');
}//function

function easySubmit(task, controller)
{
    document.adminForm.controller.value = controller;
    submitform(task);
}//function

function configureProject(name)
{
    document.adminForm.ecr_project.value = name;
    document.adminForm.controller.value = 'stuffer';
    submitbutton('stuffer');
}//function

function packProject(name)
{
    document.adminForm.ecr_project.value = name;
    document.adminForm.controller.value = 'ziper';
    submitbutton('ziper');
}//function

function registerProject(type, name, scope)
{
    form=document.adminForm;
    form.ecr_project_type.value=type;
    form.ecr_project_name.value=name;
    form.ecr_project_scope.value=scope;
    form.controller.value='starter';
    submitbutton('register_project');
}//function

function translateProject(name)
{
    document.adminForm.ecr_project.value = name;
    document.adminForm.controller.value = 'languages';
    submitbutton('languages');
}//function

var lastId;

function ecr_loadFile(task, file_path, file_name, link_id)
{
    url = 'index.php?option=com_easycreator&format=raw&tmpl=component&controller=ajax';
    url += '&file_path=' + file_path + '&file_name=' + file_name;

    legal_exts = ['php', 'xml', 'ini', 'css', 'js', 'html', 'txt', 'sql', 'brainfuck', 'po', 'pot'];
    legal_pics = ['jpg', 'png', 'gif', 'ico'];
    ext = file_name.substr(file_name.lastIndexOf('.') + 1);

    found = false;

    // --Search for files
    for (key in legal_exts)
    {
        if (legal_exts[key] == ext)
        {
            found = 'file';
            break;
        }
    }// for

    // --Search for images
    if ( ! found)
    {
        for (key in legal_pics)
        {
            if (legal_pics[key] == ext)
            {
                found = 'pic';
                break;
            }
        }// for

        if ( ! found)
        {
            alert('Unsupported extension: ' + ext);
            return;
        }
    }

    switch (found)
    {
    case 'file':
        cl = $('ecr_title_file').className;
     //   $('ecr_title_file').innerHTML = jgettext('Loading...');
        $('ecr_title_file').className = cl + ' ajax_loading16';
        
        if (FBPresent)
        {
            console.log('set lastId(' + lastId + ') to: ' + link_id);
        }

        new Request({
            url: url + '&task=loadFile',
            'onRequest' : function()
            {
                $('ecr_title_file').innerHTML = jgettext('Loading...');
            },
            'onComplete' : function(response)
            {
                var resp = JSON.decode(response, true);
                
                if( ! resp.status) {
                    //-- Error
                    $('ecr_title_file').innerHTML = resp.text;
                }
                else
                {
                    editAreaLoader.setValue('ecr_code_area', '');
                    editAreaLoader.setValue('ecr_code_area', resp.text);
                    editAreaLoader.execCommand('ecr_code_area', 'change_syntax', ext);
                    editAreaLoader.show('ecr_code_area');
                    editAreaLoader.setSelectionRange('ecr_code_area', 0, 0);
                    $('ecr_title_file').innerHTML = file_name;
                }
                
                if (lastId != undefined)
                {
                    $(lastId).setStyle('color', 'black');
                    $(link_id).setStyle('color', 'blue');
                } else
                {
                    $(link_id).setStyle('color', 'blue');
                }

                lastId = link_id;

                frm = document.adminForm;
                frm.file_path.value = file_path;
                frm.file_name.value = file_name;

                $('ecr_title_file').className = cl;
                sld_picture.hide();
                sld_edit_area.show();
            }
        }).send();

        break;
    case 'pic':
        new Request({
            url: url + '&task=loadPic',
            'onRequest' : function()
            {
                $('ecr_title_pic').innerHTML = jgettext('Loading...');
            },
            'onComplete' : function(response)
            {
                if (FBPresent)
                {
                    console.log('set lastId(' + lastId + ') to: ' + link_id);
                }

                if (lastId != undefined)
                {
                    $(lastId).setStyle('color', 'black');
                }

                $(link_id).setStyle('color', 'red');
                lastId = link_id;

                frm = document.adminForm;
                frm.file_path.value = file_path;
                frm.file_name.value = file_name;

                var resp = JSON.decode(response);

                if( ! resp.status) {
                    //-- Error
                    $('ecr_title_file').innerHTML = resp.text;
                }
                else
                {
                    $('ecr_title_pic').innerHTML = file_name;
                    $('container_pic').innerHTML = resp.text;
                    sld_picture.show();
                }
            }
        }).send();
        break;
    }// switch

}//function

function toggleDiv(name)
{
    document.id(name).style.display = (document.id(name).style.display == 'none') ? 'block' : 'none';
}//function

function getElement(e,f){
    if(document.layers){
        f=(f)?f:self;
        if(f.document.layers[e]) {
            return f.document.layers[e];
        }
        for(W=0;W<f.document.layers.length;W++) {
            return(getElement(e,f.document.layers[W]));
        }
    }
    if(document.all) {
        return document.all[e];
    }
    return document.getElementById(e);
}

/**
 * Check if a form's element is empty
 * should be
 *
 * @param   object   the form
 * @param   string   the name of the form field to put the focus on
 *
 * @return  boolean  whether the form field is empty or not
 */
function emptyCheckTheField(theForm, theFieldName)
{
    var isEmpty  = 1;
    var theField = theForm.elements[theFieldName];
    // Whether the replace function (js1.2) is supported or not
    var isRegExp = (typeof(theField.value.replace) != 'undefined');

    if (!isRegExp) {
        isEmpty      = (theField.value == '') ? 1 : 0;
    } else {
        var space_re = new RegExp('\\s+');
        isEmpty      = (theField.value.replace(space_re, '') == '') ? 1 : 0;
    }

    return isEmpty;
} // end of the 'emptyCheckTheField()' function


function checkVersion()
{
    var req = new Request.HTML({
    method: 'post',
    url: 'http://joomla.org',
    data: { 'do' : '1' },
    onRequest: function() 
    { 
        $('ecr_versionCheck').innerHTML = jgettext('Checking...');
    },
    onComplete: function(response) { 
        $('ecr_versionCheck').innerHTML = response; 
        }
}).send();

}

function xcheckVersion()
{
    var urlBase = 'http://inkubator.der-beta-server.de/releases';
    //var urlBase = 'http://helios.nik/jejo_web/releases';
    
    url = urlBase + '/easycreator.html';
    url += '?myVersion='+ECR_VERSION;
    
    url = 'http://joomla.org';
//    url += '&format=raw';
//    alert(url);
    new Request({
        url: url,
        'onRequest' : function()
        {
            $('ecr_versionCheck').innerHTML = jgettext('Checking...');
        },
        'onFailure' : function(rr)
        {
//            console.log(rr);
//            var resp = JSON.decode(rr);
//            console.log(resp);
            $('ecr_versionCheck').innerHTML = '<b style="color: red;">'
                +jgettext('Server error')+'</b>'+url;
            
            return;
        },
        'onComplete' : function(response)
        {
            var resp = JSON.decode(response);
            
            if('undefined' == resp.status) {
                //-- Error
                msg = '? bad coder error..';
                
                return;
            }
            
            var cssClass = '';
            var msg = '';
            var alt = '';
            
            switch (resp.status)
            {
                case -1 :
                    cssClass = 'img outdated';
                    msg = phpjs.sprintf(jgettext('The Latest EasyCreator version is: %s'), resp.version);
                break;
                
                case 0 :
                    cssClass = 'img actual';
                    alt = jgettext('Your version is up-to-date')
                break;
                
                default :
                    msg = jgettext('Unknown version - maybe SVN ?');
                break;
            }//switch

            $('ecr_versionCheck').innerHTML = '<span class="'+cssClass+'" title="'+alt+'" alt="'+alt+'">'+msg+'</span>';
        }
    }).send();
}//function
