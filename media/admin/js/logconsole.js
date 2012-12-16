/**
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath
 * @author     Created on 03-Apr-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

var EcrLogconsole = new Class({
    url:'index.php?option=com_easycreator&tmpl=component&format=raw',
    urlAdd:'',

    /**
     *
     * @param containers Expecting display, status and debug
     * @param data Post data
     * @param message The message
     * @param additional Additional actions
     * @param deployTarget - hrm
     */
    send:function(containers, data, message, additional, deployTarget)
    {
        new Request({
            url:this.url, // + this.urlAdd,
            data:data,

            onRequest:function()
            {
                containers.status.style.color = 'black';
                containers.status.innerHTML = message;
                containers.status.className = 'ajax_loading16';
            },

            onComplete:function(response)
            {
                resp = JSON.decode(response);

                containers.status.className = '';

                if(resp.status)
                {
                    containers.status.innerHTML = resp.message;
                    containers.debug.innerHTML = resp.debug;
                } else
                {
                    containers.status.set('text', '');
                    containers.debug.set('text', '');
                    containers.display.set('html', resp.message);
                }

                stopPoll();

                if(additional)
                {
                    switch(additional)
                    {
                        case 'getPackageList':
                            EcrDeploy.getPackageList(deployTarget, 'preserve');
                            break;

                        case 'getSyncList':
                            EcrDeploy.getSyncList(deployTarget);
                            break;

                        default:
                            console.log('Unknown additinal:' + additional);
                            break;
                    }
                }
            },

            onFailure:function()
            {
                containers.status.style.color = 'red';
                containers.status.set('text', 'The request failed');
                containers.status.className = '';
                //debug.set('html', resp.debug);
            }
        }).send();
    }
});

var EcrLogconsole = new EcrLogconsole;
