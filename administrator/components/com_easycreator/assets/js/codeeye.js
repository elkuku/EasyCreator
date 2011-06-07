/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 03-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

var ecr_act_field;

/**
 * 
 * @param folder
 * @param file
 * @return
 */
function setPath(folder, file)
{
    $('dspl_sniff_folder').innerHTML = folder;
    
    if(file)
    {
        $('dspl_sniff_file').innerHTML = (file) ? file : '';
    }
}//function

/**
 * Run a PHPUnit test.
 * 
 * @param folder
 * @param test
 * @param timeStamp
 * @param id
 * @return
 */
function doPHPUnit(folder, test, timeStamp, id)
{
   //format my f*** date
   var dx = new Date();
   
   if(ecr_act_field)
   {
       $(ecr_act_field).setStyle('color', 'black');
   }

   $(id).setStyle('color', 'red');
   ecr_act_field = id;
    
//   console.log(dx.toString());
   //Ausgabe: Sun Oct 04 2009 14:00:09 GMT-0500 (ECT)

    y = dx.getFullYear();

    m = dx.getMonth().toString();
    m =(m.length == 1) ? '0'+m : m;

    d = dx.getDay().toString();
    d =(d.length == 1) ? '0'+d : d;
    
    h = dx.getHours().toString();
    h =(h.length == 1) ? '0'+h : h;
    
    i = dx.getMinutes().toString();
    i =(i.length == 1) ? '0'+i : i;
    
    s = dx.getSeconds().toString();
    s =(s.length == 1) ? '0'+s : s;

    timeStamp = ''+y+m+d+'_'+h+i+s;
    
//    console.log(timeStamp);
    //Ausgabe: 200990_1409
    
    // getMonth() und getDay() liefern falsche Werte ...
    
    // ?
    
    url = ecrAJAXLink+'&controller=codeeyeajax';
    url += '&task=phpunit';
//    url += '&ecr_project=' + project;
//    url += '&path='+$('dspl_sniff_folder').innerHTML;
    url += '&folder='+folder;
    url += '&test='+test;
    url += '&time_stamp='+timeStamp;
    url += '&results_base=' + $('results_base').value;

    new Request({
        url: url,
        'onRequest' : function()
        {
            $('ecr_title_file').innerHTML = 'CodeEye is looking PHPUnit...';
            $('ecr_title_file').className = 'ajax_loading16';
            $('ecr_codeeye_output').innerHTML = '';
        },
        'onComplete' : function(response)
        {
            $('ecr_title_file').innerHTML = '';
            $('ecr_title_file').className = '';

            var resp = Json.evaluate(response);
            
            if( ! resp.status) {
                //-- Error
            }

            $('ecr_codeeye_output').innerHTML = resp.text;
            $('ecr_codeeye_console').innerHTML = resp.console;
        }
    }).send();
}//function

/**
 * Run a Selenium test.
 * 
 * @param folder
 * @param test
 * @param timeStamp
 * @param id
 * @return
 */
function doSelenium(folder, test, timeStamp, id)
{
    url = ecrAJAXLink+'&controller=codeeyeajax';
    url += '&task=selenium';
//    url += '&ecr_project=' + project;
//    url += '&path='+$('dspl_sniff_folder').innerHTML;
    url += '&folder='+folder;
    url += '&test='+test;
    url += '&time_stamp='+timeStamp;
    url += '&results_base=' + $('results_base').value;

    new Request({
        url: url,
        'onRequest' : function()
        {
            $('ecr_title_file').innerHTML = 'CodeEye is looking Selenium...';
            $('ecr_title_file').className = 'ajax_loading16';
            $('ecr_codeeye_output').innerHTML = '';
        },
        'onComplete' : function(response)
        {
            $('ecr_title_file').innerHTML = '';
            $('ecr_title_file').className = '';

            var resp = Json.evaluate(response);
            
            if( ! resp.status) {
                //-- Error
            }

            $('ecr_codeeye_output').innerHTML = resp.text;
            $('ecr_codeeye_console').innerHTML = resp.console;
        }
    }).send();

}//function
    
/**
 * 
 * @param project
 * @return
 */
function doPHPCPD(ecr_project)
{
    url = ecrAJAXLink+'&controller=codeeyeajax';
    url += '&task=phpcpd';
    url += '&ecr_project=' + ecr_project;
    url += '&path='+$('dspl_sniff_folder').innerHTML;
    url += '&min-lines='+$('phpcpd_min_lines').value;
    url += '&min-tokens='+$('phpcpd_min_tokens').value;

    new Request({
        url: url,
        'onRequest' : function()
        {
            $('ecr_title_file').innerHTML = 'CodeEye is looking PHPCPD...';
            $('ecr_title_file').className = 'ajax_loading16';
            $('ecr_codeeye_output').innerHTML = '';
            $('ecr_codeeye_console').innerHTML = '';
        },
        'onComplete' : function(response)
        {
            var resp = Json.evaluate(response);
            
            if( ! resp.status) {
                //-- Error
            }
            $('ecr_title_file').innerHTML = '';
            $('ecr_title_file').className = '';
            $('ecr_codeeye_output').innerHTML = resp.text;
            $('ecr_codeeye_console').innerHTML = resp.console;
        }
    }).send();
}//function

/**
 * 
 * @param dirs
 * @param files
 * @return
 */
function doPHPDoc(dirs, files)
{
    url = ecrAJAXLink+'&controller=codeeyeajax';
    url += '&task=phpdoc';

    if($('phpdoc_quiet').checked == true) {
        url += '&options[]=q';
    }

    if($('phpdoc_undocumented').checked == true) {
        url += '&options[]=ue';
    }

    if($('phpdoc_sourcecode').checked == true) {
        url += '&options[]=s';
    }

    if($('phpdoc_converter').value) {
        url += '&converter='+$('phpdoc_converter').value;
    }
    
    url += '&target_dir='+$('target_dir').value.replace('\\', '/');

    dirs = dirs.replace('\\', '/');
    url += '&parse_dirs='+dirs;

    files = files.replace('\\', '/');
    url += '&parse_files='+files;

    new Request({
        url: url,
        'onRequest' : function()
        {
            $('ecr_title_file').innerHTML = 'PhpDocumentor is generating documentation...';
            $('ecr_title_file').className = 'ajax_loading16';
            $('ecr_codeeye_output').innerHTML = '';
            $('ecr_codeeye_console').innerHTML = '';
        },
        'onComplete' : function(response)
        {
            var resp = Json.evaluate(response);
            
            if( ! resp.status) {
                //-- Error
            }
            $('ecr_title_file').innerHTML = '';
            $('ecr_title_file').className = '';
            $('ecr_codeeye_output').innerHTML = resp.text;
            $('ecr_codeeye_console').innerHTML = resp.console;
        }
    }).send();
}//function

/**
 * 
 * @return
 */
function sniffFolder()
{
    folder = $('dspl_sniff_folder').innerHTML;

    if( ! folder) {
        alert('Please select a folder.');
        return false;
    }
    
    loadSniff(folder);
}//function

/**
 * 
 * @param folder
 * @param file
 * @return
 */
function loadSniff(folder, file)
{
    $('dspl_sniff_folder').innerHTML = folder;
    $('dspl_sniff_file').innerHTML =(file) ? file : '';

    folder = (folder) ? folder : $('dspl_sniff_folder').innerHTML;
    file = (file) ? file : $('dspl_sniff_file').innerHTML;

    url = ecrAJAXLink+'&controller=codeeyeajax';
    url += '&task=load_sniff';
    url += '&path=' + folder;
    url += '&file=' + file;
    url += '&sniff_standard=' + $('sniff_standard').value;
    url += '&sniff_format=' + $('sniff_format').value;
    url += '&sniff_verbose=' + $('sniff_verbose').checked;

    var sniffs = '';
    for(var i=0; i < document.adminForm.sniff_sniffs.length; i++)
    {
        if(document.adminForm.sniff_sniffs[i].checked) {
            sniffs += document.adminForm.sniff_sniffs[i].value + ',';
        }
    }//for

    if(sniffs != '') {
        url += '&sniff_sniffs=' + sniffs;
    }

    new Request({
        url: url,
        'onRequest' : function()
        {
            $('ecr_title_file').innerHTML = 'CodeSniffer sniffing...';
            $('ecr_title_file').className = 'ajax_loading16';
            $('ecr_codeeye_output').innerHTML = '';
            $('ecr_codeeye_console').innerHTML = '';
        },
        'onComplete' : function(response)
        {
            $('ecr_title_file').innerHTML = '';
            $('ecr_title_file').className = '';
            var resp = Json.evaluate(response);
            
            if( ! resp.status) {
                //-- Error
            }
            $('ecr_codeeye_output').innerHTML = resp.text;
            $('ecr_codeeye_console').innerHTML = resp.console;
        }
    }).send();
}//function

function create_skeleton(folder, file)
{
    var url = ecrAJAXLink+'&controller=codeeyeajax&task=create_skeleton';
    url += '&ecr_project=' + $('ecr_project').value;
    url += '&folder='+folder;
    url += '&file='+file;

    new Request({
        url: url,
        'onRequest' : function()
        {
            $('ecr_title_file').innerHTML = 'CodeEye is creating a skeleton...';
            $('ecr_title_file').className = 'ajax_loading16';
            $('ecr_codeeye_output').innerHTML = '';
        },
        'onComplete' : function(response)
        {
            $('ecr_title_file').innerHTML = '';
            $('ecr_title_file').className = '';

            var resp = Json.evaluate(response);
            
            if( ! resp.status) {
                //-- Error
                $('test_tree').innerHTML = resp.text;
                $('ecr_codeeye_console').innerHTML = resp.console;
            }
            else
            {

            $('ecr_codeeye_output').innerHTML = resp.text;
            $('ecr_codeeye_console').innerHTML = resp.console;
            
            var url = ecrAJAXLink+'&controller=codeeyeajax&task=draw_test_dir';
            url += '&ecr_project=' + $('ecr_project').value;

            new Request({
                url: url,
                    'onRequest' : function()
                    {
                        $('test_tree').innerHTML = 'Redraw tree...';
                    },
                    'onComplete' : function(response)
                    {
                        var resp = Json.evaluate(response);
                        
                        if( ! resp.status) {
                            //-- Error
                            $('test_tree').innerHTML = resp.text;
                        }

                        $('test_tree').innerHTML = resp.text;
                    }
                }).send();
            }
        }//onComplete
    }).send();
}//function

function draw_test_dir(testDir)
{
    new Request({
        url: ecrAJAXLink+'&controller=codeeyeajax&task=draw_test_dir',

        'onRequest' : function()
        {
            $('ecr_title_file').innerHTML = 'CodeEye is checking your environment...';
            $('ecr_title_file').className = 'ajax_loading16';
            $('ecr_codeeye_output').innerHTML = '';
        },

        'onComplete' : function(response)
        {
            var resp = Json.evaluate(response);
            
            if( ! resp.status) {
                //-- Error
            }
            $('ecr_title_file').innerHTML = '';
            $('ecr_title_file').className = '';
            $('test_tree').innerHTML = resp.text;
            $('ecr_codeeye_console').innerHTML = resp.console;
        }
    }).send();
}//function

/**
 * 
 * @return
 */
function checkEnvironment()
{
    new Request({
        url: ecrAJAXLink+'&controller=codeeyeajax&task=check_environment',

        'onRequest' : function()
        {
            $('ecr_title_file').innerHTML = jgettext('CodeEye is checking your environment...');
            $('ecr_title_file').className = 'ajax_loading16';
            $('ecr_codeeye_output').innerHTML = '';
        },

        'onComplete' : function(response)
        {
            var resp = Json.evaluate(response, true);
            
            if( ! resp.status) {
                //-- Error
            }
            $('ecr_title_file').innerHTML = '';
            $('ecr_title_file').className = '';
            $('ecr_codeeye_output').innerHTML = resp.text;
            $('ecr_codeeye_console').innerHTML = resp.console;
        }
    }).send();
}//function
