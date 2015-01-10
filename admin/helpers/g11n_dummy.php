<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 21-May-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * PHP dummy function.
 *
 * @param string $string The string.
 *
 * @return string
 */
function jgettext($string)
{
    return $string;
}

/**
 * PHP dummy function.
 *
 * @param string  $singular Singular string.
 * @param string  $plural   Plural string.
 * @param integer $count    Count.
 *
 * @return string
 */
function jngettext($singular, $plural, $count)
{
    return (1 == $count) ? $singular : $plural;
}
