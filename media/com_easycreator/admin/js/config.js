var EConfig = new Class({
    submitForm:function(task, el)
    {
        el.addClass('ajax_loading16');

        Joomla.submitform(task);
    },

    maintain:function(action, el)
    {
        var msg = document.id('maintainResponse');

        new Request.JSON({
            url:ecrAJAXLink
                + '&controller=config&task=' + action,

            onRequest:function()
            {
                el.addClass('ajax_loading16');
            },

            onFailure:function()
            {
                el.removeClass('ajax_loading16');
            },

            onComplete:function(request)
            {
                el.removeClass('ajax_loading16');

                msg.innerHTML = request.message;
            }
        }).send();
    }
});

var EcrConfig = new EConfig;
