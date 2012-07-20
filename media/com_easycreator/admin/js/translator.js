/**
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath
 * @author     Created on 03-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

var ecrTranslator = new Class({

    /**
     * Copy the original to the translated field
     */
    copyTrans:function()
    {
        var s = this.stripQuotes(php2js.trim($('default').innerHTML));
        $('translation').value = s;
        $('translation').focus();
    }, // function

    /**
     * Delete a translation
     *
     * @param string link AJAX link
     * @param integer fieldId Id of the parent.document element which will be deleted
     */
    deleteTranslation:function(link, fieldId)
    {
        $('translation').value = jgettext('Deleting...');

        new Request({
            url:link + '&task=delete_translation',

            onComplete:function(response)
            {
                var resp = JSON.decode(response);

                if(resp.status)
                {
                    //-- Error
                    $('ajResult').innerHTML = '<strong style="color: red;">' + resp.text + '</strong>';

                    return false;
                }

                doc = window.parent.document;

                doc.getElementById('trfield_' + fieldId).innerHTML = '<strong style="color: red">' + jgettext('Empty') + '</strong>';

                parent.SqueezeBox.close();
            }
        }).send();
    },

    /**
     *
     */
    stripQuotes:function(s)
    {
        if(s.substr(0, 1) == '"')
        {
            s = s.substr(1);
        }

        if(s.substr(s.length - 1) == '"')
        {
            s = s.substr(0, s.length - 1);
        }

        return s;
    }, // function

    /**
     * Translate with the Google translation API
     *
     * Not working any more :(
     *
     * @todo replace
     */
    google_translate:function(lang)
    {
        $('translation').value = jgettext('Translating...');

        if(!gbranding_displayed)
        {
            google.language.getBranding('gtranslate_branding');
            gbranding_displayed = true;
        }

        var text = this.stripQuotes(php2js.trim($('default').innerHTML));

        google.language.translate(text, 'en', lang, function(result)
        {
            if(!result.error)
            {
                $('translation').value = result.translation;
            }
        });
    }, //function

    translate:function(link, fieldId, lang, retType, adIds)
    {
        new Request({
            url:link + '&task=translate' + '&translation=' + encodeURIComponent($('translation').value),
            //    method : 'post',
//            data : 'translation=' + encodeURIComponent($('translation').value),

            onRequest:function()
            {
                title = $('ajResult');
                title.innerHTML = jgettext('Saving...');
                title.addClass('ajax_loading16-red');
            },

            onComplete:function(response)
            {
                var resp = JSON.decode(response);

                title = $('ajResult');
                title.innerHTML = resp.text;
                title.removeClass('ajax_loading16-red');

                if(resp.status)
                {
                    //-- Error
                    title.addClass('img icon-16-cancel');
                    $('ajaxDebug').innerHTML = resp.debug;
                    return false;
                }

                doc = window.parent.document;

                switch(retType)
                {
                    case 'ini':
                        doc.getElementById('trfield_' + fieldId).innerHTML = $('translation').value;
                        break;

                    case 'phpxml':
                        doc.getElementById('trfield_' + fieldId).innerHTML = lang;
                        doc.getElementById('trfield_' + fieldId).style.color = 'green';

                        if(adIds)
                        {
                            adIds = adIds.split(',');

                            for(var i = 0; i < adIds.length; ++i)
                            {
                                doc.getElementById('trfield_' + adIds[i]).style.display = 'inline';
                            }//for
                        }
                        break;

                    default:
                        alert('Undefined ret type: ' + retType);
                        break;
                }//switch

                parent.SqueezeBox.close();
            }//onComplete
        }).send();
    }//function

});
