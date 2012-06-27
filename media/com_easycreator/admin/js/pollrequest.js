/**
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath
 * @author     Created on 19-Mar-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

var pollRequest = new Request.JSON({
    method:'post',
    url:ecrAJAXLink + '&controller=logfiles&task=pollLog',
    initialDelay:100,
    delay:300,
    limit:15000,

    onRequest:function()
    {
        document.id('pollStatus').set('text', 'running...');

        var progress = document.id('ecrProgressBar');

        if(null != progress)
            progress.getParent().addClass('active');
    },

    onSuccess:function(response)
    {
        var log = document.id('ecrDebugBox');
        var progress = document.id('ecrProgressBar');

        if(null != progress)
            progress.setStyle('width', response.progress + '%');

        log.set('html', response.message.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br />$2'));
        log.scrollTop = log.scrollHeight;
    },

    onFailure:function()
    {
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
