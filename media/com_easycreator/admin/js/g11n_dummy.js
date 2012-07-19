/**
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath
 * @author     Created on 21-May-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

var g11n = {};
g11n.debug = '';
function jgettext(string)
{
    return string;
}

function jngettext(singular, plural, count)
{
    return (1 == count) ? singular : plural;
}
