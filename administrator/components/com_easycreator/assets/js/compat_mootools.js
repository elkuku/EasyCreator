/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 28-Sep-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

var XHR = new Class({

    Extends: Request,

    options: {
        update: false
    },

    initialize: function(url, options){
        this.parent(options);
        this.url = url;
    },

    request: function(data){
        return this.send(this.url, data || this.options.data);
    },

    send: function(url, data){
        if (!this.check(arguments.callee, url, data)) return this;
        return this.parent({url: url, data: data});
    },

    success: function(text, xml){
        text = this.processScripts(text);
        if (this.options.update) $(this.options.update).empty().set('html', text);
        this.onSuccess(text, xml);
    },

    failure: function(){
        this.fireEvent('failure', this.xhr);
    }

});

var Ajax = XHR;

var Json = JSON;

JSON.toString = JSON.encode;
JSON.evaluate = JSON.decode;

Fx.Style = function(element, property, options){
    return new Fx.Tween(element, $extend({property: property}, options));
};
