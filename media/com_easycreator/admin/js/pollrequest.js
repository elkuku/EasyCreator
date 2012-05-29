/**
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath
 * @author     Created on 19-Mar-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

var pollRequest = new Request.JSON({
    method: 'post',
    url: 'index.php?option=com_easycreator&controller=ajax&task=pollLog&tmpl=component&format=raw',
    initialDelay: 100,
    delay: 500,
    limit: 15000,

    onRequest: function(){
        document.id('pollStatus').set('text', 'running...');
    },

    onSuccess: function(response){
        var log = document.id('ecrDebugBox');
        log.set('text', response.message);
        log.scrollTop = log.scrollHeight;
    },

    onFailure: function(){
        document.id('pollStatus').set('text', 'Sorry, your request failed :(');
    }

});

function startPoll()
{
    pollRequest.startTimer();
}

function stopPoll()
{
    pollRequest.stopTimer();

    document.id('pollStatus').set('text', 'idle');
}
