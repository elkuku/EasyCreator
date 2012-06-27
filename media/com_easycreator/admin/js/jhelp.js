/**
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath
 * @author     Created on 03-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * Creates a class list for the current Joomla! installation
 */
function create_class_list()
{
    var xref = location.href.substring(0, location.href.indexOf('index.php'));

    xref += 'components/com_easycreator/helpers/jmethodlister.php';

    new Request({
        url:xref,
        'onComplete':function(response)
        {
            if(response.indexOf('{') != 0)
            {
                //-- Not JSON - must be an error
                $('jsonDebug').innerHTML = response;
            }
            else
            {
                var resp = JSON.decode(response);

                if(resp.status)
                {
                    //-- Error happened
                    $('jsonDebug').innerHTML = resp.status + '<br />' + resp.text + resp.debug;
                }
                else
                {
                    //-- The sun is shining :)
                    location.href = 'index.php?option=com_easycreator&controller=help&task=jhelp';
                    return; //where ? =;)
                }
            }
        }
    }).send();
}//function

function changeFrame(className, methodName, packageName)
{
    if(FBPresent) console.log(className, methodName, packageName);

    if(className)
    {
        $('className').value = className;
    }
    else
    {
        className = $('className').value;
    }

    if(methodName)
    {
        $('methodName').value = methodName;
    }
    else
    {
        methodName = $('methodName').value;
    }

    if(packageName)
    {
        $('packageName').value = packageName;
    }
    else
    {
        packageName = $('packageName').value;
    }

    $('linkDisplay').className = 'ajax_loading16';

    if($('out_link').value == 'source')
    {
        $('jhelpDisplay').setStyle('display', 'none');
        $('jsourceDisplay').setStyle('display', 'block');

        link = 'index.php?option=com_easycreator&controller=ajax&tmpl=component&format=raw&task=show_source';
        link += '&class=' + className + '&method=' + methodName;

        new Request({
            url:link,
            'onComplete':function(request)
            {
                $('jsourceDisplay').innerHTML = request;
                $('linkDisplay').className = '';

                JTooltips2 = new Tips($$('.hasTip'),
                    {
                        maxTitleChars:50,
                        fixed:true
                    });
            }
        }).send();
    }
    else
    {
        $('jhelpDisplay').setStyle('display', 'block');
        $('jsourceDisplay').setStyle('display', 'none');

        if($('out_link').value)
        {
            iframeLink = parseLink('http://' + $('out_link').value, className, methodName, packageName);
            $('jhelpDisplay').src = iframeLink;
        }
    }

    if(ECR_DEBUG) console.log('Fetching: ' + iframeLink);
}//function

function changeOutFormat(name, link)
{
    $(fId).setProperty('class', 'btn');
    $(name).setProperty('class', 'btn active');
    fId = name;
    $('out_format').value = name;
    $('out_link').value = link;
    changeFrame();
}//function

function parseLink(string, className, methodName, packageName)
{
    s = string;

    if(string.indexOf('http://api.joomla.org') === 0)
    {
        if(packageName == 'Base')
        {
            if(className == 'JFactory')
            {
                packageName = '';
            }
        }
    }

    if(methodName == 'NULL')
    {
        s = str_replace('[class]/', className, s);
        s = str_replace('[class]', className, s);
        s = str_replace('[method]', '', s);
    }
    else
    {
        s = str_replace('[class]', className, s);
        s = str_replace('[method]', methodName, s);
    }

    sRep = (packageName) ? '[package]' : '[package]/';
    s = str_replace(sRep, packageName, s);

    if(s.lastIndexOf('/') == s.length - 1)
    {
        s = s.substring(0, s.length - 1);
    }

    return s;
}//function
