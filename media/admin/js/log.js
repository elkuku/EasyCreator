/**
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath
 * @author     Created on 16-Oct-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

var lastId;

var EcrLog = new Class({
    /**
     * Loads a log file.
     *
     * @param string name
     * @param string id
     *
     * @return void
     */
    loadLog:function(name, id)
    {
        if(lastId != undefined)
        {
            document.id(lastId).setStyles({border:'0px'});
        }

        lastId = id;
        var logView = document.id('ecr_logView');
        var cl = logView.className;

        new Request.JSON({
            url:ecrAJAXLink + '&controller=logfiles'
                + '&task=showLogfile'
                + '&fileName=' + name,

            'onRequest':function()
            {
                logView.addClass('ajax_loading16');
                logView.innerHTML = jgettext('Loading...');
                document.id(id).setStyles({ border:'1px solid #000' });
            },
            'onComplete':function(resp)
            {
                logView.removeClass('ajax_loading16');

                if(resp.status)
                {
                    //-- Error
                    logView.innerHTML = '<strong style="color: red;">' + resp.message + '</strong>'
                        + '<br />' + resp.debug;
                } else
                {
                    logView.innerHTML = '<pre>' + resp.message + '</pre>';
                }

                logView.className = cl;
            },

            onFailure:function()
            {
                logView.removeClass('ajax_loading16');
            }
        }).send();
    }
});

var EcrLog = new EcrLog();
