/**
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath
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
}

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
    m = (m.length == 1) ? '0' + m : m;

    d = dx.getDay().toString();
    d = (d.length == 1) ? '0' + d : d;

    h = dx.getHours().toString();
    h = (h.length == 1) ? '0' + h : h;

    i = dx.getMinutes().toString();
    i = (i.length == 1) ? '0' + i : i;

    s = dx.getSeconds().toString();
    s = (s.length == 1) ? '0' + s : s;

    timeStamp = '' + y + m + d + '_' + h + i + s;

//    console.log(timeStamp);
    //Ausgabe: 200990_1409
    // getMonth() und getDay() liefern falsche Werte ...
    // ?
    url = ecrAJAXLink + '&controller=codeeyeajax';
    url += '&task=phpunit';
    url += '&folder=' + folder;
    url += '&test=' + test;
    url += '&time_stamp=' + timeStamp;
    url += '&results_base=' + $('results_base').value;

    new Request({
        url:url,
        'onRequest':function()
        {
            $('ecr_title_file').innerHTML = 'CodeEye is looking PHPUnit...';
            $('ecr_title_file').className = 'ajax_loading16';
            $('ecr_codeeye_output').innerHTML = '';
        },
        'onComplete':function(response)
        {
            $('ecr_title_file').innerHTML = '';
            $('ecr_title_file').className = '';

            var resp = JSON.decode(response);

            if(!resp.status)
            {
                //-- Error
            }

            $('ecr_codeeye_output').innerHTML = resp.text;
            $('ecr_codeeye_console').innerHTML = resp.console;
        }
    }).send();
}

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
    url = ecrAJAXLink + '&controller=codeeyeajax';
    url += '&task=selenium';
    url += '&folder=' + folder;
    url += '&test=' + test;
    url += '&time_stamp=' + timeStamp;
    url += '&results_base=' + $('results_base').value;

    new Request({
        url:url,
        'onRequest':function()
        {
            $('ecr_title_file').innerHTML = 'CodeEye is looking Selenium...';
            $('ecr_title_file').className = 'ajax_loading16';
            $('ecr_codeeye_output').innerHTML = '';
        },
        'onComplete':function(response)
        {
            $('ecr_title_file').innerHTML = '';
            $('ecr_title_file').className = '';

            var resp = JSON.decode(response);

            if(!resp.status)
            {
                //-- Error
            }

            $('ecr_codeeye_output').innerHTML = resp.text;
            $('ecr_codeeye_console').innerHTML = resp.console;
        }
    }).send();
}

/**
 *
 * @param project
 * @return
 */
function doPHPCPD(ecr_project)
{
    url = ecrAJAXLink + '&controller=codeeyeajax';
    url += '&task=phpcpd';
    url += '&ecr_project=' + ecr_project;
    url += '&path=' + $('dspl_sniff_folder').innerHTML;
    url += '&min-lines=' + $('phpcpd_min_lines').value;
    url += '&min-tokens=' + $('phpcpd_min_tokens').value;

    new Request({
        url:url,
        'onRequest':function()
        {
            $('ecr_title_file').innerHTML = 'CodeEye is looking PHPCPD...';
            $('ecr_title_file').className = 'ajax_loading16';
            $('ecr_codeeye_output').innerHTML = '';
            $('ecr_codeeye_console').innerHTML = '';
        },
        'onComplete':function(response)
        {
            var resp = JSON.decode(response);

            if(!resp.status)
            {
                //-- Error
            }

            $('ecr_title_file').innerHTML = '';
            $('ecr_title_file').className = '';
            $('ecr_codeeye_output').innerHTML = resp.text;
            $('ecr_codeeye_console').innerHTML = resp.console;
        }
    }).send();
}

/**
 *
 * @param dirs
 * @param files
 * @return
 */
function doPHPDoc(dirs, files)
{
    url = ecrAJAXLink + '&controller=codeeyeajax';
    url += '&task=phpdoc';

    if($('phpdoc_quiet').checked == true)
    {
        url += '&options[]=q';
    }

    if($('phpdoc_undocumented').checked == true)
    {
        url += '&options[]=ue';
    }

    if($('phpdoc_sourcecode').checked == true)
    {
        url += '&options[]=s';
    }

    if($('phpdoc_converter').value)
    {
        url += '&converter=' + $('phpdoc_converter').value;
    }

    url += '&target_dir=' + $('target_dir').value.replace('\\', '/');

    dirs = dirs.replace('\\', '/');
    url += '&parse_dirs=' + dirs;

    files = files.replace('\\', '/');
    url += '&parse_files=' + files;

    new Request({
        url:url,
        'onRequest':function()
        {
            $('ecr_title_file').innerHTML = 'PhpDocumentor is generating documentation...';
            $('ecr_title_file').className = 'ajax_loading16';
            $('ecr_codeeye_output').innerHTML = '';
            $('ecr_codeeye_console').innerHTML = '';
        },
        'onComplete':function(response)
        {
            var resp = JSON.decode(response);

            if(!resp.status)
            {
                //-- Error
            }

            $('ecr_title_file').innerHTML = '';
            $('ecr_title_file').className = '';
            $('ecr_codeeye_output').innerHTML = resp.text;
            $('ecr_codeeye_console').innerHTML = resp.console;
        }
    }).send();
}

/**
 *
 * @return
 */
function sniffFolder()
{
    folder = $('dspl_sniff_folder').innerHTML;

    if(!folder)
    {
        alert('Please select a folder.');
        return false;
    }

    loadSniff(folder);
}

/**
 *
 * @param folder
 * @param file
 * @return
 */
function loadSniff(folder, file)
{
    $('dspl_sniff_folder').innerHTML = folder;
    $('dspl_sniff_file').innerHTML = (file) ? file : '';

    folder = (folder) ? folder : $('dspl_sniff_folder').innerHTML;
    file = (file) ? file : $('dspl_sniff_file').innerHTML;

    url = ecrAJAXLink + '&controller=codeeyeajax';
    url += '&task=phpcs';
    url += '&path=' + folder;
    url += '&file=' + file;
    url += '&sniff_standard=' + $('sniff_standard').value;
    url += '&sniff_format=' + $('sniff_format').value;
    url += '&sniff_verbose=' + $('sniff_verbose').checked;

    var sniffs = '';

    if(document.adminForm.sniff_sniffs)
    {
        for(var i = 0; i < document.adminForm.sniff_sniffs.length; i++)
        {
            if(document.adminForm.sniff_sniffs[i].checked)
            {
                sniffs += document.adminForm.sniff_sniffs[i].value + ',';
            }
        }
    }

    if(sniffs != '')
    {
        url += '&sniff_sniffs=' + sniffs;
    }

    //////////////

    var data = {};//this._getCredentials(deployTarget, 'deployPackages');

    var containers = {
        status:document.id('ecr_codeeye_output'),
        debug:document.id('ecr_codeeye_console'),
        display:document.id('ecr_codeeye_output')
    };

    startPoll();

    /*
     this._send(containers, data
     , jgettext('CodeSniffer sniffing...')
     , 'getPackageList', deployTarget
     );
     */

    //EcrLogconsole = new EcrLogconsole;

    EcrLogconsole.url = url;

    //   alert(EcrLogconsole.url);

    EcrLogconsole.send(containers, data
        , jgettext('CodeSniffer sniffing...'));

    return;

    new Request({
        url:url,
        'onRequest':function()
        {
            $('ecr_title_file').innerHTML = 'CodeSniffer sniffing...';
            $('ecr_title_file').className = 'ajax_loading16';
            $('ecr_codeeye_output').innerHTML = '';
            $('ecr_codeeye_console').innerHTML = '';
        },
        'onComplete':function(response)
        {
            $('ecr_title_file').innerHTML = '';
            $('ecr_title_file').className = '';
            var resp = JSON.decode(response);

            if(!resp.status)
            {
                //-- Error
            }

            $('ecr_codeeye_output').innerHTML = resp.message;
            $('ecr_codeeye_console').innerHTML = resp.debug;
        }
    }).send();
}

function create_skeleton(folder, file)
{
    var url = ecrAJAXLink + '&controller=codeeyeajax&task=create_skeleton';
    url += '&ecr_project=' + $('ecr_project').value;
    url += '&folder=' + folder;
    url += '&file=' + file;

    new Request({
        url:url,
        'onRequest':function()
        {
            $('ecr_title_file').innerHTML = 'CodeEye is creating a skeleton...';
            $('ecr_title_file').className = 'ajax_loading16';
            $('ecr_codeeye_output').innerHTML = '';
        },
        'onComplete':function(response)
        {
            $('ecr_title_file').innerHTML = '';
            $('ecr_title_file').className = '';

            var resp = JSON.decode(response);

            if(!resp.status)
            {
                //-- Error
                $('test_tree').innerHTML = resp.text;
                $('ecr_codeeye_console').innerHTML = resp.console;
            }
            else
            {
                $('ecr_codeeye_output').innerHTML = resp.text;
                $('ecr_codeeye_console').innerHTML = resp.console;

                var url = ecrAJAXLink + '&controller=codeeyeajax&task=draw_test_dir';
                url += '&ecr_project=' + $('ecr_project').value;

                new Request({
                    url:url,
                    'onRequest':function()
                    {
                        $('test_tree').innerHTML = 'Redraw tree...';
                    },
                    'onComplete':function(response)
                    {
                        var resp = JSON.decode(response);

                        if(!resp.status)
                        {
                            //-- Error
                            $('test_tree').innerHTML = resp.text;
                        }

                        $('test_tree').innerHTML = resp.text;
                    }
                }).send();
            }
        }//onComplete
    }).send();
}

function draw_test_dir(testDir)
{
    new Request({
        url:ecrAJAXLink + '&controller=codeeyeajax&task=draw_test_dir',

        'onRequest':function()
        {
            $('ecr_title_file').innerHTML = 'CodeEye is checking your environment...';
            $('ecr_title_file').className = 'ajax_loading16';
            $('ecr_codeeye_output').innerHTML = '';
        },

        'onComplete':function(response)
        {
            var resp = JSON.decode(response);

            if(!resp.status)
            {
                //-- Error
            }

            $('ecr_title_file').innerHTML = '';
            $('ecr_title_file').className = '';
            $('test_tree').innerHTML = resp.text;
            $('ecr_codeeye_console').innerHTML = resp.console;
        }
    }).send();
}

/**
 *
 * @return
 */
function checkEnvironment()
{
    new Request({
        url:ecrAJAXLink + '&controller=codeeyeajax&task=check_environment',

        'onRequest':function()
        {
            $('ecr_title_file').innerHTML = jgettext('CodeEye is checking your environment...');
            $('ecr_title_file').className = 'ajax_loading16';
            $('ecr_codeeye_output').innerHTML = '';
        },

        'onComplete':function(response)
        {
            var resp = JSON.decode(response, true);

            if(!resp.status)
            {
                //-- Error
            }

            $('ecr_title_file').innerHTML = '';
            $('ecr_title_file').className = '';
            $('ecr_codeeye_output').innerHTML = resp.text;
            $('ecr_codeeye_console').innerHTML = resp.console;
        }
    }).send();
}

/**
 * Git stuff
 */
function gitStatus()
{
    url = ecrAJAXLink + '&controller=codeeyeajax';
    url += '&task=gitStatus';

    new Request({
        url:url,
        'onRequest':function()
        {
            document.id('ecr_codeeye_output').innerHTML = '';
            document.id('ecr_codeeye_console').innerHTML = '';
        },
        'onComplete':function(response)
        {
            var resp = JSON.decode(response);

            if(!resp.status)
            {
                //-- Error
            }

            document.id('ecr_codeeye_output').innerHTML = resp.text;
            document.id('ecr_codeeye_console').innerHTML = resp.console;
        }
    }).send();
}

/**
 * Run a PHP CLI script
 */
function runCli(ecr_project)
{
    url = ecrAJAXLink
        + '&controller=codeeyeajax'
        + '&task=runCli'
        + '&ecr_project=' + ecr_project
        + '&args=' + document.id('cliargs').value;

    new Request({
        url:url,
        'onRequest':function()
        {
            document.id('ecr_codeeye_output').innerHTML = '';
            document.id('ecr_codeeye_console').innerHTML = '';
        },
        'onComplete':function(response)
        {
            var resp = JSON.decode(response);

            if(!resp.status)
            {
                //-- Error
            }

            document.id('ecr_codeeye_output').innerHTML = resp.text;
            document.id('ecr_codeeye_console').innerHTML = resp.console;
        }
    }).send();
}

/**
 * PHP lines of code.
 */
function phploc(dir)
{
    url = ecrAJAXLink + '&controller=codeeyeajax';
    url += '&task=phploc';
    url += '&dir=' + dir;

    new Request({
        url:url,
        'onRequest':function()
        {
            document.id('ecr_codeeye_output').innerHTML = '';
            document.id('ecr_codeeye_console').innerHTML = '';
        },
        'onComplete':function(response)
        {
            var resp = JSON.decode(response);

            if(!resp.status)
            {
                //-- Error
            }

            document.id('ecr_codeeye_output').innerHTML = resp.text;
            document.id('ecr_codeeye_console').innerHTML = resp.console;
        }
    }).send();
}
