/**
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 21-May-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

function jgettext(string)
{
    return string;
}//function

function jngettext(singular, plural, count)
{
    return (1 == count) ? singular : plural;
}//function
