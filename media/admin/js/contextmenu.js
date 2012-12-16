/**
 * Simple Context Menu.
 *
 * @package EasyCreator
 * @subpackage Javascript
 * @author webtoolkit {@link http://www.webtoolkit.info/}
 * @author Nikolai Plath
 * @author Created on 03-Mar-2008
 * @license GNU/GPL, see JROOT/LICENSE.php
 */

var SimpleContextMenu = {

    // private attributes
    _menus:new Array,
    _attachedElement:null,
    _menuElement:null,
    _preventDefault:true,
    _preventForms:true,


    // public method. Sets up whole context menu stuff..
    setup:function(conf)
    {
        if(document.all && document.getElementById && !window.opera)
        {
            SimpleContextMenu.IE = true;
        }

        if(!document.all && document.getElementById && !window.opera)
        {
            SimpleContextMenu.FF = true;
        }

        if(document.all && document.getElementById && window.opera)
        {
            SimpleContextMenu.OP = true;
        }

        if(SimpleContextMenu.IE || SimpleContextMenu.FF)
        {
            document.oncontextmenu = SimpleContextMenu._show;
            document.onclick = SimpleContextMenu._hide;

            if(conf && typeof(conf.preventDefault) != "undefined")
            {
                SimpleContextMenu._preventDefault = conf.preventDefault;
            }

            if(conf && typeof(conf.preventForms) != "undefined")
            {
                SimpleContextMenu._preventForms = conf.preventForms;
            }
        }
    },

    // public method. Attaches context menus to specific class names
    attach:function(classNames, menuId)
    {
        if(typeof(classNames) == "string")
        {
            SimpleContextMenu._menus[classNames] = menuId;
        }

        if(typeof(classNames) == "object")
        {
            for(x = 0; x < classNames.length; x++)
            {
                SimpleContextMenu._menus[classNames[x]] = menuId;
            }
        }
    },

    // private method. Get which context menu to show
    _getMenuElementId:function(e)
    {
        if(SimpleContextMenu.IE)
        {
            SimpleContextMenu._attachedElement = event.srcElement;
        } else
        {
            SimpleContextMenu._attachedElement = e.target;
        }

        while(SimpleContextMenu._attachedElement != null)
        {
            var className = SimpleContextMenu._attachedElement.className;

            if(typeof(className) != "undefined")
            {
                className = className.replace(/^\s+/g, "").replace(/\s+$/g, "")
                var classArray = className.split(/[ ]+/g);

                for(i = 0; i < classArray.length; i++)
                {
                    if(SimpleContextMenu._menus[classArray[i]])
                    {
                        return SimpleContextMenu._menus[classArray[i]];
                    }
                }
            }

            if(SimpleContextMenu.IE)
            {
                SimpleContextMenu._attachedElement = SimpleContextMenu._attachedElement.parentElement;
            } else
            {
                SimpleContextMenu._attachedElement = SimpleContextMenu._attachedElement.parentNode;
            }
        }

        return null;
    },

    // private method. Shows context menu
    _getReturnValue:function(e)
    {
        var returnValue = true;
        var evt = SimpleContextMenu.IE ? window.event : e;

        if(evt.button != 1)
        {
            if(evt.target)
            {
                var el = evt.target;
            } else if(evt.srcElement)
            {
                var el = evt.srcElement;
            }

            var tname = el.tagName.toLowerCase();

            if((tname == "input" || tname == "textarea"))
            {
                if(!SimpleContextMenu._preventForms)
                {
                    returnValue = true;
                } else
                {
                    returnValue = false;
                }
            } else
            {
                if(!SimpleContextMenu._preventDefault)
                {
                    returnValue = true;
                } else
                {
                    returnValue = false;
                }
            }
        }

        return returnValue;
    },


    // private method. Shows context menu
    _show:function(e)
    {
        SimpleContextMenu._hide();
        var menuElementId = SimpleContextMenu._getMenuElementId(e);

        if(menuElementId)
        {
            var m = SimpleContextMenu._getMousePosition(e);
            var s = SimpleContextMenu._getScrollPosition(e);

            SimpleContextMenu._menuElement = document.getElementById(menuElementId);
            SimpleContextMenu._menuElement.style.left = m.x + s.x + 'px';
            SimpleContextMenu._menuElement.style.top = m.y + s.y + 'px';
            SimpleContextMenu._menuElement.style.display = 'block';
            return false;
        }

        return SimpleContextMenu._getReturnValue(e);
    },

    // private method. Hides context menu
    _hide:function()
    {
        if(SimpleContextMenu._menuElement)
        {
            SimpleContextMenu._menuElement.style.display = 'none';
        }
    },

    // private method. Returns mouse position
    _getMousePosition:function(e)
    {
        e = e ? e : window.event;
        var position = {
            'x':e.clientX,
            'y':e.clientY
        }

        return position;
    },

    // private method. Get document scroll position
    _getScrollPosition:function()
    {
        var x = 0;
        var y = 0;

        if(typeof( window.pageYOffset ) == 'number')
        {
            x = window.pageXOffset;
            y = window.pageYOffset;
        } else if(document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ))
        {
            x = document.documentElement.scrollLeft;
            y = document.documentElement.scrollTop;
        } else if(document.body && ( document.body.scrollLeft || document.body.scrollTop ))
        {
            x = document.body.scrollLeft;
            y = document.body.scrollTop;
        }

        var position = {
            'x':x,
            'y':y
        }

        return position;
    }
}

/**
 * setAction ...
 *
 * @param event
 * @param folder
 * @param file
 * @param fieldName
 * @return
 */
var ecr_act_field;

function setAction(event, folder, file, fieldName)
{
    if(FBPresent) console.log(folder, file, fieldName);

    var button;

    if(event.which == null)
    {
        button = (event.button < 2) ? "LEFT" :
            ((event.button == 4) ? "MIDDLE" : "RIGHT");
    }
    else
    {
        button = (event.which < 2) ? "LEFT" :
            ((event.which == 2) ? "MIDDLE" : "RIGHT");
    }

    if(button == 'RIGHT')
    {
        $('act_folder').value = folder;

        if(FBPresent) console.log('folder set to ' + folder);
        if(file != undefined)
        {
            $('act_file').value = file;
            if(FBPresent) console.log('file set to ' + file);
        }

        if(fieldName)
        {
            if(ecr_act_field)
            {
                $(ecr_act_field).setStyle('color', 'black');
            }

            $(fieldName).setStyle('color', 'blue');

            ecr_act_field = fieldName;
        }
    }
}

function processForm(bla)
{
    alert(bla);
}
