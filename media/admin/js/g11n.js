/**
 * @package    EasyCreator
 * @subpackage JavaScript
 * @author     Nikolai Plath
 * @author     Created on 15-Aug-2011
 * @license    GNU/GPL
 */

function g11nCreate(type, lang, scope)
{
    document.adminForm.langTag.value = lang;
    document.adminForm.scope.value = scope;

    submitform(type);
}//function
