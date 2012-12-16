/**
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath
 * @author     Created on 03-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

window.addEvent('domready', function()
{
    var doccommentTip = new Tips($$('.doccommentTip'),
        {
            className:'doccomment', // this is the prefix for the CSS class
            initialize:function()
            {
                this.fx = new Fx.Tween(this.toolTip, $extend({property:'opacity'}),
                    {
                        duration:500,
                        wait:false
                    }).set(0);
            },
            onShow:function(toolTip)
            {
                this.fx.start(1);
            },
            onHide:function(toolTip)
            {
                this.fx.start(0);
            }
        });
});
